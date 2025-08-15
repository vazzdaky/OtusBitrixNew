<?php

namespace Vpolyakov\Crmcustomtab\Crm;

use Vpolyakov\Crmcustomtab\Orm\ProductTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
class Handlers
{
    public static function updateTabs(Event $event): EventResult
    {
        $availableEntityIds = Option::get('vpolyakov.crmcustomtab', 'ENTITIES_TO_DISPLAY_TAB');
        $availableEntityIds = explode(',', $availableEntityIds);
        $entityTypeId = $event->getParameter('entityTypeID');
        $entityId = $event->getParameter('entityID');
        $tabs = $event->getParameter('tabs');
        if (in_array($entityTypeId, $availableEntityIds)) {
            $tabs[] = [
                'id' => 'product_tab_' . $entityTypeId . '_' . $entityId,
                'name' => Loc::getMessage('VPOLYAKOV_CRMCUSTOMTAB_TAB_TITLE'),
                'enabled' => true,
                'loader' => [
                    'serviceUrl' => sprintf(
                        '/bitrix/components/vpolyakov.crmcustomtab/product.grid/lazyload.ajax.php?site=%s&%s',
                        \SITE_ID,
                        \bitrix_sessid_get(),
                    ),
                    'componentData' => [
                        'template' => '',
                        'params' => [
                            'ORM' => ProductTable::class,
                            'DEAL_ID' => $entityId,
                        ],
                    ],
                ],
            ];
        }

        return new EventResult(EventResult::SUCCESS, ['tabs' => $tabs,]);
    }
}
