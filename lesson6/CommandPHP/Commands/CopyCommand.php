<?php


class CopyCommand extends Command
{

    public function execute(): bool
    {
        $this->selectedTextToClipboard();
//        $this->editor->clipboard =
//            substr($this->file->content,
//                $this->file->textField->start,
//                $this->file->textField->getLength());
        return false;
    }
    public function undo(){
        // nothing to do here
    }
}
