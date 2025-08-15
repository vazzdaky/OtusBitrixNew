<?php

namespace Vpolyakov\Crmcustomtab\Orm;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class SeasonTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'vpolyakov_season';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete()
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_SEASON_TABLE_ID')),

            (new StringField('NAME'))
                ->configureSize(100)
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_SEASON_TABLE_NAME')),

            (new ManyToMany('PRODUCTS', ProductTable::class))
                ->configureTableName('vpolyakov_product_season')
                ->configureLocalPrimary('ID', 'SEASON_ID')
                ->configureRemotePrimary('ID', 'PRODUCT_ID')
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_SEASON_TABLE_PRODUCTS'))
        ];
    }
}
