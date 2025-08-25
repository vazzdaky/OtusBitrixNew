<?php
// автолоадер проекта Otus
include_once __DIR__ . '/../app/autoload.php';

// вывод данных 
function pr($var, $type = false)
{
    echo '<pre style="font-size:10px; border:1px solid #000; background:#FFF; text-align:left; color:#000;">';
    if ($type)
        var_dump($var);
    else
        print_r($var);
    echo '</pre>';
}


use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();

$eventManager->AddEventHandler(
    'iblock',
    'OnIBlockPropertyBuildList',
    [
        'UserTypes\UserTypeOnlineRecord', // class обработчик пользовательского типа свойства 
        'GetUserTypeDescription'
    ]
);

\Bitrix\Main\UI\Extension::load(['otus.workday_confirm']);
