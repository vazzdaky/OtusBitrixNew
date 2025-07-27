<?php

namespace Models\Lists;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\StringField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\DateField;
use Bitrix\Main\ORM\Fields\Relations\ManyToMany;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\Localization\Loc;
use Bitrix\Iblock\Elements\ElementdoctorsTable;
use Bitrix\Iblock\Elements\ElementprocedurTable;
use Bitrix\Main\ORM\Query\Join;

Loc::loadMessages(__FILE__);

class TrainingTable extends DataManager
{
    /**
     * Undocumented function
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'vpolyakov_training';
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
                ->configureAutocomplete(),

            (new StringField('TITLE'))
                ->configureRequired()
                ->configureSize(255),

            (new TextField('DESCRIPTION')),

            (new DateField('BEGINNING_COURSE')),

            (new ManyToMany('AUTHORS', AuthorTable::class))
                ->configureTableName('vpolyakov_training_author')
                ->configureLocalPrimary('ID', 'TRAINING_ID')
                ->configureRemotePrimary('ID', 'AUTHOR_ID'),

            (new ManyToMany('DOCTOR_TRAINING', ElementdoctorsTable::class))
                ->configureTableName('vpolyakov_doctor_treaning')
                ->configureLocalPrimary('ID', 'TRAINING_ID')
                ->configureRemotePrimary('ID', 'DOCTOR_ID'),

            (new ManyToMany('TRAINING_PROCEDUR', ElementprocedurTable::class))
                ->configureTableName('vpolyakov_procedure_treaning')
                ->configureLocalPrimary('ID', 'TRAINING_ID')
                ->configureRemotePrimary('ID', 'PROC_ID'),

        ];
    }
}
