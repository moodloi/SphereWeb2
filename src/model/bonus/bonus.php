<?php

namespace Ofey\Logan22\model\bonus;

use Exception;
use Ofey\Logan22\component\alert\board;
use Ofey\Logan22\component\image\client_icon;
use Ofey\Logan22\component\lang\lang;
use Ofey\Logan22\model\admin\validation;
use Ofey\Logan22\model\db\sql;
use Ofey\Logan22\model\log\logTypes;
use Ofey\Logan22\model\user\user;

class bonus
{

    //Создаем коды

    /**
     * @throws Exception
     */
    public static function genereateCode()
    {
        validation::user_protection("admin");

        $minCodeSymbols    = 9;
        $maxCodeSymbols    = 12;
        $countGenBonusCode = $_POST["count_codes"] ?? 100;
        $items             = $_POST['items'];
        $prefix            = $_POST['prefix'] ?? '';
        if ($items == null || count($items) == 0) {
            board::notice(false, "Не указаны предметы");
        }
        $start_date_code = isset($_POST['start_date_code']) && $_POST['start_date_code'] !== "" ? $_POST['start_date_code'] : board::notice(
          false,
          "Не указана начальная дата действия кода"
        );
        $end_date_code   = isset($_POST['end_date_code']) && $_POST['end_date_code'] !== "" ? $_POST['end_date_code'] : board::notice(
          false,
          "Не указана конечная дата действия кода"
        );

        $server_id = user::self()->getServerId();
        $codesReg  = [];

        for ($i = 0; $i < $countGenBonusCode; $i++) {
            $code       = $prefix . self::generateRandomStrings($minCodeSymbols, $maxCodeSymbols);
            $codesReg[] = $code;
            foreach ($items as $item) {
                $itemid  = $item['itemId'];
                $count   = $item['count'];
                $enchant = 0;
                sql::run(
                  "INSERT INTO `bonus_code` (`server_id`, `code`, `item_id`, `count`, `enchant`, `phrase`, `start_date_code`, `end_date_code`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                  [
                    $server_id,
                    $code,
                    $itemid,
                    $count,
                    $enchant,
                    "code_bonus",
                    $start_date_code,
                    $end_date_code,
                  ]
                );
            }
        }
        header('Content-Type: application/json');
        echo json_encode($codesReg);
    }

    public
    static function generateRandomStrings(
      $minLength,
      $maxLength
    ) {
        $characters       = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString     = "";
        $length           = rand($minLength, $maxLength);

        for ($j = 0; $j < $length; $j++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public static function getCode($code)
    {
        try {
            sql::beginTransaction();

            $bonuses = sql::getRows("SELECT * FROM bonus_code WHERE code = ?", [$code]);
            if (!$bonuses) {
                throw new Exception(lang::get_phrase("code_not_found"));
            }

            $bonusNames = "";
            foreach ($bonuses as $bonus) {
                // Проверка даты бонуса
                if (time() < strtotime($bonus['start_date_code'])) {
                    throw new Exception(lang::get_phrase("code_not_active"));
                }

                // Проверка истечения времени даты бонуса
                if (time() > strtotime($bonus['end_date_code'])) {
                    throw new Exception(lang::get_phrase("code_dead"));
                }

                $serverId = $bonus['server_id'];
                if ($serverId != user::self()->getServerId()) {
                    throw new Exception("Этот код создан для другого сервера");
                }

                $itemId  = $bonus['item_id'];
                $count   = $bonus['count'];
                $enchant = $bonus['enchant'] ?? 0;
                $phrase  = $bonus['phrase'];

                user::self()->addToWarehouse($serverId, $itemId, $count, $enchant, $phrase);

                $itemInfo = client_icon::get_item_info($bonus['item_id'], false);
                sql::sql("DELETE FROM bonus_code WHERE id = ?", [$bonus['id']]);

                $enchant = ($enchant > 0) ? "+{$bonus['enchant']} " : "";

                $name       = $enchant . $itemInfo->getAddName() . " " . $itemInfo->getItemName();
                $bonusNames = "<img src='" . $itemInfo->getIcon() . "' width='16' height='16'> " . $name . "({$count}),  " . $bonusNames;
                user::self()->addLog(logTypes::LOG_BONUS_CODE, 'LOG_BONUS_CODE', [$code, $name, $count]);
            }

            sql::commit();

            $message = lang::get_phrase("bonus_code_success", $bonusNames);
            board::success($message);
        } catch (Exception $e) {
            sql::rollback();
            board::notice(false, $e->getMessage());
        }
    }

}