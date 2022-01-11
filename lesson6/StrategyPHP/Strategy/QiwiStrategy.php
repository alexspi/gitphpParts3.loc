<?php


class QiwiStrategy implements IPayMethod
{
    public function requestPayment(float $price): bool
    {
        return true;
    }

    public function responsePayment($phone): string
    {
        return 'Send message ' . $phone;
    }
}
