<?php


class DeveloperObserver implements SplObserver
{
    private $name;
    public $course;

    public function __construct($name, $course)
    {
        $this->name = $name;
        $this->course = $course;
    }


    public function update(SplSubject $subject)
    {
        echo "Оповестить $this->name $this->course подписчика о новой вакансии " .
            $subject->lastAddedJob->name . PHP_EOL;
    }
}
