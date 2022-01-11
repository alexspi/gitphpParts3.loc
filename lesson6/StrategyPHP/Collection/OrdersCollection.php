<?php


class OrdersCollection
{
    protected $orders;
    protected $buyerPhone = 123;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }
    public function renderOrders(){}


    public function getTotalPrice(): float
    {
        return 99.99;
    }
    public function pay(IPayMethod $method){
        if($method->requestPayment($this->getTotalPrice())){
            return $method->responsePayment($this->buyerPhone);
        };
        return 'Оплата не прошла';
    }
}
