<?php

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;
use Bitrix\Main\Entity\Base;
use Bitrix\Main\IO\Directory;
use Bitrix\Main\SystemException;
use Bitrix\Main\IO\InvalidPathException;
use Bitrix\Main\DB\SqlQueryException;
use Bitrix\Main\LoaderException;
use Vpolyakov\Crmcustomtab\Orm\ProductTable;
use Vpolyakov\Crmcustomtab\Orm\SeasonTable;
use Vpolyakov\Crmcustomtab\Data\TestDataInstaller;

Loc::getMessage(__FILE__);

class vpolyakov_crmcustomtab extends CModule
{
    public $MODULE_ID = 'vpolyakov.crmcustomtab';
    public $MODULE_SORT = 500;
    public $MODULE_VERSION;
    public $MODULE_DESCRIPTION;
    public $MODULE_VERSION_DATE;
    public $PARTNER_NAME;
    public $PARTNER_URI;

    public function __construct()
    {
        $arModuleVersion = [];
        include __DIR__ . '/version.php';
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        $this->MODULE_DESCRIPTION = Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_INSTALL_MODULE_DESCRIPTION');
        $this->MODULE_NAME = Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_INSTALL_MODULE_NAME');
        $this->PARTNER_NAME = Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_PARTNER_NAME');
        $this->PARTNER_URI = Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_PARTNER_URI');
    }

    /**
     * @throws SystemException
     */
    public function DoInstall(): void
    {
        if ($this->isVersionD7()) {
            ModuleManager::registerModule($this->MODULE_ID);
            $this->InstallFiles();
            $this->InstallDB();
            $this->InstallEvents();
        } else {
            throw new SystemException(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_INSTALL_ERROR_VERSION'));
        }
    }

    /**
     * @throws SqlQueryException
     * @throws LoaderException
     * @throws InvalidPathException
     */
    public function DoUninstall(): void
    {
        $this->UnInstallFiles();
        $this->UnInstallDB();
        $this->UnInstallEvents();

        ModuleManager::unRegisterModule($this->MODULE_ID);
    }

    /**
     * @throws InvalidPathException
     */
    public function InstallFiles($params = []): void
    {
        $component_path = $this->getPath() . '/install/components';

        if (Directory::isDirectoryExists($component_path)) {
            CopyDirFiles($component_path, $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components', true, true);
        } else {
            throw new InvalidPathException($component_path);
        }
    }

    /**
     * @throws LoaderException
     */
    public function InstallDB(): void
    {
        Loader::includeModule($this->MODULE_ID);

        $entities = $this->getEntities();

        foreach ($entities as $entity) {
            if (!Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                Base::getInstance($entity)->createDbTable();
            }
        }

        $this->installManyToManyTable();

        foreach ($entities as $entity) {
            $this->addEntityElements($entity);
        }
    }

    public function InstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->registerEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Vpolyakov\\Crmcustomtab\\Crm\\Handlers',
            'updateTabs'
        );
    }

    public function UnInstallEvents(): void
    {
        $eventManager = EventManager::getInstance();

        $eventManager->unRegisterEventHandler(
            'crm',
            'onEntityDetailsTabsInitialized',
            $this->MODULE_ID,
            '\\Vpolyakov\\Crmcustomtab\\Crm\\Handlers',
            'updateTabs'
        );
    }

    /**
     * @throws SqlQueryException
     * @throws LoaderException
     */
    public function UnInstallDB()
    {
        Loader::includeModule($this->MODULE_ID);

        $connection = Application::getConnection();

        $entities = $this->getEntities();
        $this->unInstallManyToManyTable();

        foreach ($entities as $entity) {
            if (Application::getConnection($entity::getConnectionName())->isTableExists($entity::getTableName())) {
                $connection->dropTable($entity::getTableName());
            }
        }
    }

    /**
     * Удаляет файлы, установленные компонентом
     * @throws InvalidPathException
     */
    public function UninstallFiles(): void
    {
        $component_path = $this->getPath() . '/install/components';

        if (Directory::isDirectoryExists($component_path)) {
            $installed_components = new \DirectoryIterator($component_path);
            foreach ($installed_components as $component) {
                if ($component->isDir() && !$component->isDot()) {
                    $target_path = $_SERVER['DOCUMENT_ROOT'] . '/bitrix/components/' . $component->getFilename();
                    if (Directory::isDirectoryExists($target_path)) {
                        Directory::deleteDirectory($target_path);
                    }
                }
            }
        } else {
            throw new InvalidPathException($component_path);
        }
    }

    private function addEntityElements(string $entityClass): void
    {
        if ($entityClass === SeasonTable::class) {
            TestDataInstaller::addSeasons();
        } elseif ($entityClass === ProductTable::class) {
            TestDataInstaller::addProducts();
        }
    }

    private function installManyToManyTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'vpolyakov_product_season';

        if (!$connection->isTableExists($tableName)) {
            $connection->queryExecute("
            CREATE TABLE {$tableName} (
                PRODUCT_ID int NOT NULL,
                SEASON_ID int NOT NULL,
                PRIMARY KEY (PRODUCT_ID, SEASON_ID)
            )
        ");
        }
    }

    /**
     * @throws SqlQueryException
     */
    private function unInstallManyToManyTable(): void
    {
        $connection = Application::getConnection();
        $tableName = 'vpolyakov_product_season';

        if ($connection->isTableExists($tableName)) {
            $connection->dropTable($tableName);
        }
    }

    private function getEntities(): array
    {
        return [
            SeasonTable::class,
            ProductTable::class,
        ];
    }

    public function getPath($notDocumentRoot = false): string
    {
        if ($notDocumentRoot) {
            return str_ireplace(Application::getDocumentRoot(), '', dirname(__DIR__));
        } else {
            return dirname(__DIR__);
        }
    }

    public function isVersionD7(): bool
    {
        return version_compare(ModuleManager::getVersion('main'), '20.00.00', ">");
    }
}
