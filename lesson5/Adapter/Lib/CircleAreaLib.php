<?php


class CircleAreaLib
{
    public function getCircleArea(float $diagonal)
    {
        return (M_PI * $diagonal**2)/4;
   }

}
