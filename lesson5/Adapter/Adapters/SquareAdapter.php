<?php


class SquareAdapter implements ISquare
{
    private $square;

    /**
     * SquareAdapter constructor.
     * @param $square
     */
    public function __construct(SquareAreaLib $square)
    {
        $this->square = $square;
    }

    /**
     * На вход приходит длина стороны, для внешней библиотеки нужна диагональ
     * вычисляем диагональ
     * @param float $sideSquare
     * @return float|int площадь квадрата
     */
    function squareArea(float $sideSquare)
    {
        $diagonal = $sideSquare * (sqrt(2));
        return $this->square->getSquareArea($diagonal);
    }
}
