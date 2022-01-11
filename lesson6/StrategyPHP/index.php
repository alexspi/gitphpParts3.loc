<?php
require 'Collection/OrdersCollection.php';
require 'Collection/PayMethodsCollection.php';

require 'Strategy/QiwiStrategy.php';
require 'Strategy/WebMoneyStrategy.php';
require 'Strategy/YandexMoneyStrategy.php';

require 'IPayMethod.php';
require 'Order.php';

$orders = [];

array_push($orders, new Order());
array_push($orders, new Order());

$collection = new OrdersCollection($orders);
$paymentMethod = (new PayMethodsCollection())->getPaymentMethod('YandexMoney');
echo $collection->pay($paymentMethod);
