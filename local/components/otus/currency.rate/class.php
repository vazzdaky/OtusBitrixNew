<?php

namespace Local\Components\Otus\CurrencyRate;

use Bitrix\Currency\CurrencyTable;

class CurrencyRateComponent extends \CBitrixComponent
{
    /**
     * Undocumented function
     *
     * @return void
     */
    public function executeComponent()
    {
        // Получаем выбранный идентификатор валюты из параметров компонента
        $currencyId = $_REQUEST['CURRENCY_ID'] ?? null;

        // Проверяем, выбран ли идентификатор валюты
        if ($currencyId) {
            // Получаем текущий курс выбранной валюты
            $currencyRate = $this->getCurrencyRate($currencyId);

            // Если курс найден, то сохраняем его в переменные шаблона
            if ($currencyRate) {
                $this->arResult['CURRENCY_RATE'] = $currencyRate;
                $this->includeComponentTemplate();
            } else {
                // Если курс не найден, то выводим сообщение об ошибке
                ShowError('Курс для выбранной валюты не найден.');
            }
        } else {
            // Если идентификатор валюты не выбран, то выводим список валют
            $this->arResult['CURRENCIES'] = $this->getCurrencies();
            $this->includeComponentTemplate();
        }
    }


    /**
     * Undocumented function
     *
     * @return void
     */
    protected function getCurrencies()
    {
        $currencies = CurrencyTable::getList([
            'select' => ['CURRENCY'],
        ]);

        $result = [];
        while ($currency = $currencies->fetch()) {
            $result[$currency['CURRENCY']] = $currency['CURRENCY'];
        }

        return $result;
    }

    /**
     * Undocumented function
     *
     * @param string $currencyId
     * @return void
     */
    protected function getCurrencyRate(string $currencyId)
    {
        $currency = CurrencyTable::getByPrimary($currencyId)->fetch();

        if ($currency) {
            return $currency['CURRENT_BASE_RATE'];
        }

        return null;
    }
}
