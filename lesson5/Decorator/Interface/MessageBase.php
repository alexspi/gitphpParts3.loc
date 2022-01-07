<?php


class MessageBase implements MessageInterface
{

    public function send(string $text): string
    {
        return 'BaseMethod send : ' . $text;
    }
}
