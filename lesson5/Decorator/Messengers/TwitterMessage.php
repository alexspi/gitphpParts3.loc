<?php


class TwitterMessage implements MessageInterface
{

    protected $obj;

    /**
     * FaceBookMassage constructor.
     * @param $obj
     */
    public function __construct(MessageInterface $obj)
    {
        $this->obj = $obj;
    }


    /**
     * @param string $text
     * @return string
     */
    public function send(string $text): string
    {
        echo 'Twitter send : ' . $text . PHP_EOL;
        return $this->obj->send($text);
    }
}
