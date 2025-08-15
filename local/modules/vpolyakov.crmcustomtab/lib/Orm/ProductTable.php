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

class ProductTable extends DataManager
{
    public static function getTableName(): string
    {
        return 'vpolyakov_product';
    }

    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete()
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_PRODUCT_TABLE_ID')),

            (new StringField('NOMENCLATURE'))
                ->configureRequired()
                ->configureSize(255)
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_PRODUCT_TABLE_NOMENCLATURE')),

            (new IntegerField('INCASH'))
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_PRODUCT_TABLE_INCASH')),

            (new DateField('SHIPMENT_DATA'))
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_PRODUCT_TABLE_SHIPMENT_DATA')),

            (new ManyToMany('SEASONS', SeasonTable::class))
                ->configureTableName('vpolyakov_product_season')
                ->configureLocalPrimary('ID', 'PRODUCT_ID')
                ->configureRemotePrimary('ID', 'SEASON_ID')
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_PRODUCT_TABLE_SEASONS'))
        ];
    }
}
