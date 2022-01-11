<?php


class File
{
    public string $content;
    public string $filename;
    public TextField $textField;

    public function __construct(string $filename, string $content)
    {
        $this->content = $content;
        $this->filename = $filename;
        $this->textField = new TextField();
    }

    public function getText(): bool|string
    {
        return file_get_contents($this->filename);
    }


    public function getSelectedText($start, $end){
        $this->textField->setAllCoords($start, $end);
    }

}
