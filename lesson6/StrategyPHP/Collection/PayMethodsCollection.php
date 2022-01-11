<?php


class PayMethodsCollection
{
    public function getPaymentMethod(string $name){
        if ($name === 'Qiwi'){
            return new QiwiStrategy();
        }
        if ($name === 'YandexMoney'){
            return new YandexMoneyStrategy();
        }
        if ($name === 'WebMoney'){
            return new WebMoneyStrategy();
        }
        return  new \Exception('Unknown Payment Method');
    }
}
