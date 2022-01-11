<?php
// Поиск элемента массива с известным индексом

$array = [1, 2, 3, 4, 5];

$findNumber = $array[0];

$arrayDuplicate = [];
foreach ($array as $value) { /* O(N) */
    $arrayDuplicate[] = $value; /* O(1) */
//     сложность алгоритма будет равна O(N) * O(1) = O(1N) и так как множитель можно опустить получим сложность равную O(N)
}


function factorial($x) {
    if ($x === 0)
        return 1;
    return $x * factorial($x-1);
}
echo factorial(5);
// сложность алгоитма равную O(N)