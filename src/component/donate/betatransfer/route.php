<?php
/**
 * route.php это обязательный файл
 */

$routes = [
    [
        //Метод POST/GET
        "method"  => "POST",
        //Адрес
        "pattern" => "/donate/transfer/betatransfer/createlink",
        //Файл, в которой будет вызвана функция из call
        "file"    => "pay.php",
        //Функция, которая обработкает когда прийдет запрос
        "call"    => function() {
            (new betatransfer())->create_link();
        },
    ],

    [
        "method"  => "POST",
        "pattern" => "/donate/webhook/betatransfer",
        "file"    => "pay.php",
        "call"    => function() {
            (new betatransfer())->webhook();
        },
    ],
];
