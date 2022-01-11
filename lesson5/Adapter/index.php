<?php

require 'Interface/ICircle.php';
require 'Interface/ISquare.php';
require 'Lib/CircleAreaLib.php';
require 'Lib/SquareAreaLib.php';
require 'Adapters/CircleAdapter.php';
require 'Adapters/SquareAdapter.php';

$circumference = 5;
$circle = new CircleAreaLib();
$adapter = new CircleAdapter($circle);
echo 'Площадь круга при длине окружности ' . $circumference . ' = ';
echo $adapter->circleArea($circumference) . PHP_EOL;

$sideSquare = 10;
$square = new SquareAreaLib();
$adapter = new SquareAdapter($square);
echo 'Площадь квадрата при длине стороны ' . $sideSquare . ' = ';
echo $adapter->squareArea($sideSquare);
