<?php


abstract class Command
{
    protected  Editor $editor;
    public File $file;
    protected  string $backup;


    public function __construct(Editor $editor)
    {
        $this->editor = $editor;
        $this->file = $editor->file;
    }

    protected function saveBackup(){
        $this->backup = $this->editor->getAllText();
    }
    protected function selectedTextToClipboard() : string
    {
        return $this->editor->clipboard = substr($this->file->content,
            $this->file->textField->start,
            $this->file->textField->getLength());
    }

    protected function deleteSelectedTextFromFileByLengthText(string $text) : string
    {
        return $this->file->content = substr_replace(
            $this->file->content,
            '',
            $this->file->textField->start,
            strlen($text)
        );
    }

    protected function pasteInnerTextToFile(string $text) : string {
        return $this->file->content = substr_replace(
            $this->file->content,
            $text,
            $this->file->textField->start,
            0);
    }
    // Не решил как лучше сделать возврат польностью перебирать текст или через переопределение в коммандах
//    public function undo(){
//        $this->editor->text = $this->backup;
//    }
    abstract public function undo();
    abstract public function execute();

}
