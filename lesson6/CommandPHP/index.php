<?php
require 'Autoload.php';

$file = 'files/editor.txt';

$application = new MyWordApp();

$applicationEditor = new Editor($application->openFile($file));
$applicationEditor->getSelectedText(0,3);
echo $applicationEditor->clipboard . PHP_EOL;
$applicationEditor->copy();
$applicationEditor->setCarriage(3);
echo $applicationEditor->clipboard . PHP_EOL;
$applicationEditor->paste();
$applicationEditor->setClipboard('To delete');
$applicationEditor->setCarriage(0);
$applicationEditor->paste();
$applicationEditor->undo();
$applicationEditor->undo();


$applicationEditor->getSelectedText(0,5);
$applicationEditor->cut();
$applicationEditor->undo();
$applicationEditor->setCarriage(0);
$applicationEditor->paste();
$applicationEditor->undo();
echo $applicationEditor->file->content;
$application->saveFile($applicationEditor->file);
