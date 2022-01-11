<?php


class CutCommand extends Command
{
    protected string $removedText;

    public function execute(): bool
    {
        ($this->backup)??$this->saveBackup();
        $this->removedText = $this->selectedTextToClipboard();
        $this->deleteSelectedTextFromFileByLengthText($this->removedText);
        return true;
    }

    public function undo()
    {
        $this->pasteInnerTextToFile($this->removedText);
    }
}
