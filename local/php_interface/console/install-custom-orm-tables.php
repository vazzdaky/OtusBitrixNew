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

//title: Создание таблицы на основе ORM класса

use Bitrix\Main\Entity\Base;
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Models\Lists\TrainingTable;
use Models\Lists\AuthorTable;

$entities = [
	AuthorTable::class,
	TrainingTable::class,
];

foreach ($entities as $entity) {
	if (!Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
		Base::getInstance($entity)->createDbTable();
	}
}

$connection = Application::getConnection();

$tableName = 'vpolyakov_training_author';

if (!$connection->isTableExists($tableName)) {
	$connection->queryExecute("
		CREATE TABLE {$tableName} (
			TRAINING_ID int NOT NULL,
			AUTHOR_ID int NOT NULL,
			PRIMARY KEY (TRAINING_ID, AUTHOR_ID)
		)
	");
}
$tableName = 'vpolyakov_doctor_treaning';

if (!$connection->isTableExists($tableName)) {
	$connection->queryExecute("
		CREATE TABLE {$tableName} (
			TRAINING_ID int NOT NULL,
			DOCTOR_ID int NOT NULL,
			PRIMARY KEY (TRAINING_ID, DOCTOR_ID)
		)
	");
}
$tableName = 'vpolyakov_procedure_treaning';

if (!$connection->isTableExists($tableName)) {
	$connection->queryExecute("
		CREATE TABLE {$tableName} (
			TRAINING_ID int NOT NULL,
			PROC_ID int NOT NULL,
			PRIMARY KEY (TRAINING_ID, PROC_ID)
		)
	");
}
