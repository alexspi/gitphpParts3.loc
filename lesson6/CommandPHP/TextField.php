<?php


class TextField
{
    public int $start = 0;
    public int $end = 0;
    protected int $length = 0;


    public function setCarriage($pos){
        $this->start = $pos;
        $this->end = $pos;
        $this->length = 0;
    }
    public function getLength(): int
    {
        return $this->length;
    }

    public function setAllCoords(int $start, int $end){
        $this->start = $start;
        $this->end = $end;
        $this->length = $end - $start;
    }

}
