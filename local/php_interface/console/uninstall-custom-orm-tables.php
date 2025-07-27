<?php
if (php_sapi_name() != 'cli') {
    die();
}

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_NO_ACCELERATOR_RESET", true);
define("BX_CRONTAB", true);
define("STOP_STATISTICS", true);
define("NO_AGENT_STATISTIC", "Y");
define("DisableEventsCheck", true);
define("NO_AGENT_CHECK", true);

$_SERVER['DOCUMENT_ROOT'] = realpath('/home/bitrix/www');
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

//title: Удаление лишних таблиц

use Bitrix\Main\Application;

$connection = Application::getConnection();

$tableName = 'vpolyakov_training_author';

if ($connection->isTableExists($tableName)) {
    $connection->dropTable($tableName);
}

$tableName = 'vpolyakov_training';

if ($connection->isTableExists($tableName)) {
    $connection->dropTable($tableName);
}

$tableName = 'vpolyakov_procedure_treaning';

if ($connection->isTableExists($tableName)) {
    $connection->dropTable($tableName);
}

$tableName = 'vpolyakov_doctor_treaning';

if ($connection->isTableExists($tableName)) {
    $connection->dropTable($tableName);
}

$tableName = 'vpolyakov_author';

if ($connection->isTableExists($tableName)) {
    $connection->dropTable($tableName);
}
