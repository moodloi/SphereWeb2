<?php

$routes = [
  [
    "method"  => "GET",
    "pattern" => "/admin/modify/item",
    "file"    => "itemMaster.php",
    "call"    => function() {
        (new \Ofey\Logan22\component\plugins\itemMaster\itemMaster())->show();
    },
  ],




  [
    "method"  => "GET",
    "pattern" => "/admin/modify/item/get/{chronicle}/add",
    "file"    => "itemMaster.php",
    "call"    => function($chronicle) {
        (new \Ofey\Logan22\component\plugins\itemMaster\itemMaster())->add($chronicle);
    },
  ],


  [
    "method"  => "GET",
    "pattern" => "/admin/modify/item/edit/{chronicle}/id/{itemId}",
    "file"    => "itemMaster.php",
    "call"    => function($chronicle, $itemId) {
        (new \Ofey\Logan22\component\plugins\itemMaster\itemMaster())->edit($chronicle, $itemId);
    },
  ],

  [
    "method"  => "GET",
    "pattern" => "/admin/modify/item/get/{chronicle}",
    "file"    => "itemMaster.php",
    "call"    => function($chronicle) {
        (new \Ofey\Logan22\component\plugins\itemMaster\itemMaster())->show($chronicle);
    },
  ],


  [
    "method"  => "POST",
    "pattern" => "/admin/modify/item/load/icon",
    "file"    => "itemMaster.php",
    "call"    => function() {
        (new \Ofey\Logan22\component\plugins\itemMaster\itemMaster())->addIcon();
    },
  ],

  [
    "method"  => "POST",
    "pattern" => "/admin/modify/item/new/save",
    "file"    => "itemMaster.php",
    "call"    => function() {
        (new \Ofey\Logan22\component\plugins\itemMaster\itemMaster())->addItemSave();
    },
  ],
  [
    "method"  => "POST",
    "pattern" => "/admin/modify/item/update/save",
    "file"    => "itemMaster.php",
    "call"    => function() {
        (new \Ofey\Logan22\component\plugins\itemMaster\itemMaster())->updateItemSave();
    },
  ],


];