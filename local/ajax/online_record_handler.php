<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$request = Bitrix\Main\Context::getCurrent()->getRequest();

if ($request->isPost()) {
    $postData = $request->getPostList()->toArray();

    \Bitrix\Main\Loader::includeModule('iblock');

    $el = new CIBlockElement();

    // форматирование времени
    $newDate = str_replace('T', ' ', $postData['TIME']) . ':00';

    $prop = [
        'PROC_IDS'     => $postData['PROC_ID'],
        'DATE'         => $newDate,
        'PATIENT_NAME' => $postData['NAME'],
        'DOCTOR'       => $postData['DOCTOR_ID'],
    ];

    // массив для добавления в инфоблок
    $arLoadProductArray = [
        'IBLOCK_ID'       => 21,
        'NAME'            => $postData['NAME'],
        'ACTIVE'          => 'Y',
        'PROPERTY_VALUES' => $prop,
    ];

    // добавление элементов в инфоблок
    if ($element_id = $el->Add($arLoadProductArray)) {
        echo "<script>window.onload = function() {alert('Вы успешно записаны на прием.');}</script>";
    } else {
        echo "Error: " . $el->LAST_ERROR;
    }
}

die();
