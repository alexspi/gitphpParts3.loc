<?php


class CircleAdapter implements ICircle
{
    private $circle;

    /**
     * CircleAdapter constructor.
     * @param $circle
     */
    public function __construct(CircleAreaLib $circle)
    {
        $this->circle = $circle;
    }

    /**
     * На выход приходит длина окружности, вычисляем из нее диаметр
     * т.к. внешняя библиотека работает с диаметром
     * @param float $circumference
     * @return float|int Площадь круга
     */
    function circleArea(float $circumference)
    {
        $diagonal = $circumference / M_PI;
        return $this->circle->getCircleArea($diagonal);
    }
}
