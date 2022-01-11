<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Directory Iterator</title>
    <style>
        a {
            text-decoration: none;
            color: red;
        }
    </style>
</head>
<body>

</body>
</html>
<?php

function getDirectory($dir)
{
    $currentDirectory = new DirectoryIterator(realpath($dir));
    getContent($currentDirectory);
}

function getContent($currentDirectory)
{
    foreach ($currentDirectory as $item) {
        if ($item->isDot()) continue;
        if (!$item->isDir()) echo "<img src='image/file.png' alt='#' width='15px'>{$item->getBaseName()}<br>";
        else echo "<img src='image/folder.png' alt='#' width='15px'><a href='?path={$item->getPathname()}'>{$item}</a><br>";
    }
}

if (!empty($_GET['path'])) {
    getDirectory($_GET['path']);
} else {
    getDirectory('/');
}

