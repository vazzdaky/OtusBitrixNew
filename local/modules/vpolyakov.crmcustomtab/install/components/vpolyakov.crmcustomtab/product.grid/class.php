<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\PageNavigation;
use Bitrix\Main\Grid\Options as GridOptions;
use Bitrix\Main\UI\Filter\Options as FilterOptions;
use Vpolyakov\Crmcustomtab\Orm\ProductTable;
use Bitrix\Main\Loader;
use Bitrix\Main\ORM\Query\Result;

Loader::includeModule('vpolyakov.crmcustomtab');
class ProductGrid extends \CBitrixComponent implements Controllerable
{
    public function configureActions(): array
    {
        return [];
    }

    private function getElementActions(): array
    {
        return [];
    }

    private function getHeaders(): array
    {
        return [
            [
                'id' => 'ID',
                'name' => 'ID',
                'sort' => 'ID',
                'default' => true,
            ],
            [
                'id' => 'NOMENCLATURE',
                'name' => Loc::getMessage('PRODUCT_GRID_PRODUCT_NOMENCLATURE_LABEL'),
                'sort' => 'NOMENCLATURE',
                'default' => true,
            ],
            [
                'id' => 'INCASH',
                'name' => Loc::getMessage('PRODUCT_GRID_PRODUCT_INCASH_LABEL'),
                'sort' => 'INCASH',
                'default' => true,
            ],
            [
                'id' => 'SHIPMENT_DATA',
                'name' => Loc::getMessage('PRODUCT_GRID_PRODUCT_SHIPMENT_DATA_LABEL'),
                'sort' => 'SHIPMENT_DATA',
                'default' => true,
            ],
            [
                'id' => 'SEASONS',
                'name' => Loc::getMessage('PRODUCT_GRID_PRODUCT_SEASONS_LABEL'),
                'default' => true,
            ],
        ];
    }

    public function executeComponent(): void
    {
        $this->prepareGridData();
        $this->includeComponentTemplate();
    }

    private function prepareGridData(): void
    {
        $this->arResult['HEADERS'] = $this->getHeaders();
        $this->arResult['FILTER_ID'] = 'PRODUCT_GRID';

        $gridOptions = new GridOptions($this->arResult['FILTER_ID']);
        $navParams = $gridOptions->getNavParams();

        $nav = new PageNavigation($this->arResult['FILTER_ID']);
        $nav->allowAllRecords(true)
            ->setPageSize($navParams['nPageSize'])
            ->initFromUri();

        $filterOption = new FilterOptions($this->arResult['FILTER_ID']);
        $filterData = $filterOption->getFilter([]);
        $filter = $this->prepareFilter($filterData);


        $sort = $gridOptions->getSorting([
            'sort' => [
                'ID' => 'DESC',
            ],
            'vars' => [
                'by' => 'by',
                'order' => 'order',
            ],
        ]);

        $productIdsQuery = ProductTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter)
            ->setLimit($nav->getLimit())
            ->setOffset($nav->getOffset())
            ->setOrder($sort['sort']);

        $countQuery = ProductTable::query()
            ->setSelect(['ID'])
            ->setFilter($filter);
        $nav->setRecordCount($countQuery->queryCountTotal());

        $productIds = array_column($productIdsQuery->exec()->fetchAll(), 'ID');

        if (!empty($productIds)) {
            $products = ProductTable::getList([
                'filter' => ['ID' => $productIds] + $filter,
                'select' => [
                    'ID',
                    'NOMENCLATURE',
                    'INCASH',
                    'SHIPMENT_DATA',
                    'SEASON_ID' => 'SEASONS.ID',
                    'SEASON_NAME' => 'SEASONS.NAME',
                ],
                'order' => $sort['sort'],
            ]);

            $this->arResult['GRID_LIST'] = $this->prepareGridList($products);
        } else {
            $this->arResult['GRID_LIST'] = [];
        }

        $this->arResult['NAV'] = $nav;
        $this->arResult['UI_FILTER'] = $this->getFilterFields();
    }

    private function prepareFilter(array $filterData): array
    {
        $filter = [];

        if (!empty($filterData['FIND'])) {
            $filter['%TITLE'] = $filterData['FIND'];
        }

        if (!empty($filterData['NOMENCLATURE'])) {
            $filter['%TITLE'] = $filterData['NOMENCLATURE'];
        }

        if (!empty($filterData['INCASH_from'])) {
            $filter['>=INCASH'] = $filterData['INCASH_from'];
        }

        if (!empty($filterData['SHIPMENT_DATA_from'])) {
            $filter['>=SHIPMENT_DATA'] = $filterData['SHIPMENT_DATA_from'];
        }

        if (!empty($filterData['SHIPMENT_DATA_to'])) {
            $filter['<=SHIPMENT_DATA'] = $filterData['SHIPMENT_DATA_to'];
        }

        return $filter;
    }

    private function prepareGridList(Result $products): array
    {
        $gridList = [];
        $groupedProducts = [];

        while ($product = $products->fetch()) {
            $productId = $product['ID'];

            if (!isset($groupedProducts[$productId])) {
                $groupedProducts[$productId] = [
                    'ID' => $product['ID'],
                    'NOMENCLATURE' => $product['NOMENCLATURE'],
                    'INCASH' => $product['INCASH'],
                    'SHIPMENT_DATA' => $product['SHIPMENT_DATA'],
                    'SEASONS' => []
                ];
            }

            if ($product['SEASON_ID']) {
                $groupedProducts[$productId]['SEASONS'][] = implode(' ', array_filter([
                    $product['SEASON_NAME'],
                ]));
            }
        }

        foreach ($groupedProducts as $product) {
            $gridList[] = [
                'data' => [
                    'ID' => $product['ID'],
                    'NOMENCLATURE' => $product['NOMENCLATURE'],
                    'INCASH' => $product['INCASH'],
                    'SEASONS' => implode(', ', $product['SEASONS']),
                    'SHIPMENT_DATA' => $product['SHIPMENT_DATA']->format('d.m.Y'),
                ],
                'actions' => $this->getElementActions(),
            ];
        }

        return $gridList;
    }

    private function getFilterFields(): array
    {
        return [
            [
                'id' => 'NOMENCLATURE',
                'name' => Loc::getMessage('PRODUCT_GRID_PRODUCT_NOMENCLATURE_LABEL'),
                'type' => 'string',
                'default' => true,
            ],
            [
                'id' => 'INCASH',
                'name' => Loc::getMessage('PRODUCT_GRID_PRODUCT_INCASH_LABEL'),
                'type' => 'number',
                'default' => true,
            ],
            [
                'id' => 'SHIPMENT_DATA',
                'name' => Loc::getMessage('PRODUCT_GRID_PRODUCT_SHIPMENT_DATA_LABEL'),
                'type' => 'date',
                'default' => true,
            ],
        ];
    }
}
