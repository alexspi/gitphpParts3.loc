<?php


interface IPayMethod
{
    public function requestPayment(float $price);
    public function responsePayment($phone);
}
