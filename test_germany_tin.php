<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

echo "\n=== Тест валидации германского ТИН ===\n\n";

// Валидные номера из тестов
$validNumbers = [
    '269 543 718 27',
    '86095742719',
    '65929970489'
];

// Невалидные номера
$invalidNumbers = [
    '26954371828',  // Неправильная контрольная сумма
    '860957427199', // Неправильная длина
    'wwwwwwwwwww',  // Не цифры
    '12345678901',  // Неправильный алгоритм
    '00000000000',  // Начинается с 0
    '11111111111',  // Все одинаковые
    '123456789012' // Больше 11 цифр
];

echo "🟢 Проверка валидных номеров:\n";
foreach ($validNumbers as $number) {
    try {
        $tin = TIN::from('DE', $number);
        $isValid = $tin->isValid();
        $type = $tin->identifyTinType();

        echo "  ✓ $number - ";
        if ($isValid) {
            echo "ВАЛИДЕН";
            if ($type) {
                echo " ({$type['code']}: {$type['name']})";
            }
        } else {
            echo "НЕВАЛИДЕН";
        }
        echo "\n";

        // Проверим маску и плейсхолдер
        echo "    Маска: {$tin->getInputMask()}\n";
        echo "    Плейсхолдер: {$tin->getPlaceholder()}\n";
        echo "    Отформатированный: {$tin->formatInput($number)}\n";

    } catch (TINException $e) {
        echo "  ✗ $number - ОШИБКА: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

echo "\n🔴 Проверка невалидных номеров:\n";
foreach ($invalidNumbers as $number) {
    try {
        $tin = TIN::from('DE', $number);
        $isValid = $tin->isValid();

        echo "  ✗ $number - ";
        if ($isValid) {
            echo "ВАЛИДЕН (неожиданно!)";
        } else {
            echo "НЕВАЛИДЕН (правильно)";
        }
        echo "\n";

    } catch (TINException $e) {
        echo "  ✓ $number - ОТКЛОНЕН: " . $e->getMessage() . "\n";
    }
}

echo "\n🔍 Проверка алгоритма валидации:\n";

// Проверим детали алгоритма для одного валидного номера
$testNumber = '26954371827';
echo "Анализ номера: $testNumber\n";

$tin = TIN::from('DE', $testNumber);
echo "Длина: " . strlen($testNumber) . "\n";
echo "Первая цифра: " . $testNumber[0] . " (не должна быть 0)\n";
echo "Контрольная цифра: " . $testNumber[10] . "\n";

// Проверим распределение цифр
$digits = str_split($testNumber);
$digitCount = array_count_values($digits);
echo "Распределение цифр: ";
foreach ($digitCount as $digit => $count) {
    echo "$digit:$count ";
}
echo "\n";

echo "\n✅ Тест завершен!\n";
