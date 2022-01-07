<?php


class Job
{
    public $name;
    public $course;


    public function __construct($name, $course)
    {
        $this->name = $name;
        $this->course = $course;
    }

}
