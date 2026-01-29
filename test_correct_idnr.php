<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

echo "\n=== Создание корректного IdNr ===\n\n";

// Создадим корректный IdNr согласно правилам:
// - 11 цифр
// - Первая цифра не 0
// - Одна цифра встречается дважды
// - Одна цифра отсутствует (не встречается вообще)
// - Остальные 8 цифр встречаются по одному разу

$correctExamples = [
    '12345678917', // нет цифры 0, цифра 7 встречается дважды
    '23456789811', // нет цифры 0, цифра 1 встречается дважды  
    '13456789922', // нет цифры 0, цифра 2 встречается дважды
];

foreach ($correctExamples as $example) {
    echo "Тестируем: $example\n";
    
    // Анализ структуры
    $digits = str_split($example);
    $digitCount = array_count_values($digits);
    
    echo "Распределение цифр:\n";
    $twiceCount = 0;
    $zeroCount = 0;
    $onceCount = 0;
    
    for ($i = 0; $i <= 9; $i++) {
        $count = $digitCount[$i] ?? 0;
        if ($count > 0) {
            echo "  $i: $count раз";
            if ($count === 2) {
                echo " (дважды)";
                $twiceCount++;
            } elseif ($count === 1) {
                $onceCount++;
            }
            echo "\n";
        } else {
            echo "  $i: отсутствует\n";
            $zeroCount++;
        }
    }
    
    echo "Проверка правил:\n";
    echo "- Цифры встречающиеся дважды: $twiceCount (должно быть 1) " . ($twiceCount === 1 ? "✅" : "❌") . "\n";
    echo "- Отсутствующие цифры: $zeroCount (должно быть 1) " . ($zeroCount === 1 ? "✅" : "❌") . "\n";
    echo "- Цифры встречающиеся один раз: $onceCount (должно быть 8) " . ($onceCount === 8 ? "✅" : "❌") . "\n";
    
    // Проверка валидности
    try {
        $tin = TIN::from('DE', $example);
        $isValid = $tin->isValid();
        echo "Валидность: " . ($isValid ? 'ВАЛИДЕН' : 'НЕВАЛИДЕН') . "\n";
        
        $type = $tin->identifyTinType();
        if ($type) {
            echo "Тип: {$type['code']} - {$type['name']}\n";
        }
    } catch (TINException $e) {
        echo "ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("-", 50) . "\n\n";
}

echo "=== Проверка известных валидных номеров из тестов ===\n\n";

$knownValid = [
    '26954371827',
    '86095742719',
];

foreach ($knownValid as $validTin) {
    echo "Проверяем: $validTin\n";
    
    $digits = str_split($validTin);
    $digitCount = array_count_values($digits);
    
    echo "Распределение цифр:\n";
    $twiceCount = 0;
    $zeroCount = 0;
    
    for ($i = 0; $i <= 9; $i++) {
        $count = $digitCount[$i] ?? 0;
        if ($count > 0) {
            echo "  $i: $count раз";
            if ($count === 2) {
                echo " (дважды)";
                $twiceCount++;
            }
            echo "\n";
        } else {
            echo "  $i: отсутствует\n";
            $zeroCount++;
        }
    }
    
    echo "Отсутствующие цифры: $zeroCount, Дважды: $twiceCount\n";
    
    try {
        $tin = TIN::from('DE', $validTin);
        echo "Валидность: " . ($tin->isValid() ? 'ВАЛИДЕН' : 'НЕВАЛИДЕН') . "\n";
    } catch (TINException $e) {
        echo "ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== Тест завершен ===\n"; 