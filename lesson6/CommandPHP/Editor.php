<?php


class Editor // Invoker
{
    private CommandHistory $history;
    public File $file;
    public  string $clipboard ='';

    public function __construct(File $file)
    {
        $this->file = $file;
        $this->history = new CommandHistory();
    }

    public function copy(){
        $this->executeCommand(new CopyCommand($this));
    }

    public function cut(){
        $this->executeCommand(new CutCommand($this));
    }
    public function paste()
    {
        $this->executeCommand(new PasteCommand($this));
    }

    public function executeCommand(Command $command){
        if ($command->execute()){
            $this->history->push($command);
        }
    }
    public function undo(){
        $lastCommand = $this->history->pop();
        if (isset($lastCommand)){
            $lastCommand->undo();
        }else {
            echo 'Nothing to undo';
        }
    }

    public function setClipboard(string $text){
        $this->clipboard = $text;
    }

    public function getAllText(): string
    {
        return $this->file->getText();

    }

    public function getSelectedText(int $start, int $end) : void
    {
        $this->file->getSelectedText($start, $end);
    }

    public function setCarriage(int $pos){
        $this->file->textField->setCarriage($pos);
    }

}
