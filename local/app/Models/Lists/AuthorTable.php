<?php

namespace Models\Lists;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class AuthorTable extends DataManager
{
    /**
     * Undocumented function
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'vpolyakov_author';
    }

    /**
     * Undocumented function
     *
     * @return array
     */
    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete()
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_AUTHOR_TABLE_ID')),

            (new StringField('FIRST_NAME'))
                ->configureRequired()
                ->configureSize(100)
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_AUTHOR_TABLE_FIRST_NAME')),

            (new StringField('LAST_NAME'))
                ->configureRequired()
                ->configureSize(100)
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_AUTHOR_TABLE_LAST_NAME')),

            (new StringField('SECOND_NAME'))
                ->configureRequired()
                ->configureSize(100)
                ->configureTitle(Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_AUTHOR_TABLE_SECOND_NAME')),

            (new ManyToMany('TRAINING', TrainingTable::class))
                ->configureTableName('vpolyakov_training_author')
                ->configureLocalPrimary('ID', 'AUTHOR_ID')
                ->configureRemotePrimary('ID', 'TRAINING_ID')
                ->configureTitle(Loc::getMessage('VPOLYKOV_CRMCUSTOMTAB_AUTHOR_TABLE_TRAINING')),
        ];
    }
}
