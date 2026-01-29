<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;

// Тест нормализации - проверим, что пробелы удаляются
$tin_with_spaces = "48 036 952 129";
$tin_without_spaces = "48036952129";

echo "Исходный TIN с пробелами: '$tin_with_spaces'\n";
echo "Исходный TIN без пробелов: '$tin_without_spaces'\n";

// Создаем объекты TIN используя правильные factory методы
$tin1 = TIN::from('DE', $tin_with_spaces);
$tin2 = TIN::from('DE', $tin_without_spaces);

// Проверяем валидацию
echo "Валидация TIN1: " . ($tin1->isValid() ? "VALID" : "INVALID") . "\n";
echo "Валидация TIN2: " . ($tin2->isValid() ? "VALID" : "INVALID") . "\n";

// Проверяем, что результаты валидации одинаковые
echo "Результаты валидации одинаковы: " . (($tin1->isValid() === $tin2->isValid()) ? "ДА" : "НЕТ") . "\n";

// Проверяем форматирование
echo "Форматирование TIN1: '" . $tin1->formatInput($tin_with_spaces) . "'\n";
echo "Форматирование TIN2: '" . $tin2->formatInput($tin_without_spaces) . "'\n"; 