<?php


class WebMoneyStrategy implements IPayMethod
{
    public function requestPayment(float $price)
    {
        return true;
    }

    public function responsePayment($phone): string
    {
        return 'Send message ' . $phone;
    }
}
