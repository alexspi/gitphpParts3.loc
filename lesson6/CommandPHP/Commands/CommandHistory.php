<?php


class CommandHistory
{
    private array $history;

    public function push(Command $command){
        $this->history[] = $command;
    }
    public function pop() : Command
    {
        return array_pop($this->history);
    }
}
