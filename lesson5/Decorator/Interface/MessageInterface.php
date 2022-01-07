<?php


interface MessageInterface
{
    /**
     * @param string $text
     * @return string
     */
    public function send(string $text) : string;
}
