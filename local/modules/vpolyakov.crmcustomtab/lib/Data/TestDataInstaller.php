<?php

namespace Vpolyakov\Crmcustomtab\Data;

use Vpolyakov\Crmcustomtab\Orm\ProductTable;
use Vpolyakov\Crmcustomtab\Orm\SeasonTable;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;

class TestDataInstaller
{
    public static function addSeasons(): void
    {
        $seasons = [
            [
                'NAME' => 'Лето 2026',
            ],

            [
                'NAME' => 'Осень 2025',
            ],

            [
                'NAME' => 'Зима 2026-2027',
            ],

            [
                'NAME' => 'Весна 2026',
            ],

        ];

        foreach ($seasons as $seasonData) {
            SeasonTable::add($seasonData);
        }
    }

    /**
     * @throws SystemException
     * @throws \Exception
     */
    public static function addProducts(): void
    {
        $products = [
            [
                'NOMENCLATURE' => 'Палатка Сплав Shelter one Si',
                'INCASH' => 105,
                'SHIPMENT_DATA' => '01.02.2026',
                'SEASONS' => [1, 2, 4] // Лето, Весна, Осень
            ],
            [
                'NOMENCLATURE' => 'Спальный мешок пуховый Сплав Adventure Zero терракот',
                'INCASH' => 25,
                'SHIPMENT_DATA' => '08.10.2025',
                'SEASONS' => [2, 3] // Осень, Зима
            ],
            [
                'NOMENCLATURE' => 'Рюкзак Сплав Gradient 80 v3 L темно-синий',
                'INCASH' => 205,
                'SHIPMENT_DATA' => '10.11.2025',
                'SEASONS' => [1, 2, 3, 4] // Все сезоны
            ],
            [
                'NOMENCLATURE' => 'Коврик туристический надувной Сплав Vision',
                'INCASH' => 185,
                'SHIPMENT_DATA' => '08.09.2025',
                'SEASONS' => [1, 2, 4] // Лето, Осень, Весна
            ],
            [
                'NOMENCLATURE' => 'Кресло складное Сплав Shell light',
                'INCASH' => 87,
                'SHIPMENT_DATA' => '11.01.2026',
                'SEASONS' => [1, 2, 3, 4] // Все сезоны
            ],
            [
                'NOMENCLATURE' => 'Страховочный жилет Phantom салатовый/серый',
                'INCASH' => 28,
                'SHIPMENT_DATA' => '10.03.2026',
                'SEASONS' => [1, 2, 4] // Лето, Осень, Весна
            ],
            [
                'NOMENCLATURE' => 'Снегоступы Husky Track',
                'INCASH' => 18,
                'SHIPMENT_DATA' => '01.09.2025',
                'SEASONS' => [3] // Зима
            ],
            [
                'NOMENCLATURE' => 'Спальный мешок Сплав Селигер-200',
                'INCASH' => 53,
                'SHIPMENT_DATA' => '01.03.2026',
                'SEASONS' => [1, 4] // Лето, Весна
            ],
            [
                'NOMENCLATURE' => 'Очки солнцезащитные Сплав Alpine 4 cat',
                'INCASH' => 685,
                'SHIPMENT_DATA' => '01.07.2026',
                'SEASONS' => [1, 2, 3, 4] // Все сезоны
            ],
            [
                'NOMENCLATURE' => 'Кроссовки-амфибии THB Zimba бежевые',
                'INCASH' => 124,
                'SHIPMENT_DATA' => '01.06.2026',
                'SEASONS' => [1] // Лето
            ],
            [
                'NOMENCLATURE' => 'Горелка с кастрюлей FastBoil Track',
                'INCASH' => 632,
                'SHIPMENT_DATA' => '01.08.2026',
                'SEASONS' => [1, 2, 3, 4] // Все сезоны
            ],
            [
                'NOMENCLATURE' => 'Гермосумка Ranger 100л. синий',
                'INCASH' => 42,
                'SHIPMENT_DATA' => '08.10.2025',
                'SEASONS' => [1, 2, 3, 4] // Все сезоны
            ],
            [
                'NOMENCLATURE' => 'Стол складной Сплав Shell light',
                'INCASH' => 58,
                'SHIPMENT_DATA' => '01.11.2025',
                'SEASONS' => [1, 4] // Лето, Весна
            ],

        ];

        foreach ($products as $productData) {
            $productData['SHIPMENT_DATA'] = DateTime::createFromText($productData['SHIPMENT_DATA']);
            $seasonIds = $productData['SEASONS'];
            unset($productData['SEASONS']);

            $resultAdd = ProductTable::add($productData);
            if (!$resultAdd->isSuccess()) {
                throw new SystemException('Не удалось добавить тестовые данные: ' . implode(', ', $resultAdd->getErrorMessages()));
            }

            $productId = $resultAdd->getId();
            $product = ProductTable::getByPrimary($productId)->fetchObject();

            if ($product) {
                foreach ($seasonIds as $seasonId) {
                    $season = SeasonTable::getByPrimary($seasonId)->fetchObject();
                    if ($season) {
                        $product->addToSeasons($season);
                    }
                }
                $product->save();
            }
        }
    }
}
