<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

echo "\n=== Отладка обработки пробелов ===\n\n";

$inputs = [
    '26954371827',
    '269 543 718 27',
];

foreach ($inputs as $input) {
    echo "Вход: '$input'\n";
    echo "Длина: " . strlen($input) . "\n";
    
    // Проверим нормализацию напрямую
    $normalizedManual = preg_replace('#[^[:alnum:]]#u', '', $input);
    echo "Нормализованный вручную: '$normalizedManual' (длина: " . strlen($normalizedManual) . ")\n";
    
    try {
        $tin = TIN::from('DE', $input);
        echo "TIN создан успешно\n";
        echo "Валидность: " . ($tin->isValid() ? 'ДА' : 'НЕТ') . "\n";
        
        $type = $tin->identifyTinType();
        if ($type) {
            echo "Тип: {$type['code']} - {$type['name']}\n";
        } else {
            echo "Тип: НЕ ОПРЕДЕЛЕН\n";
        }
        
        // Проверим, что возвращает formatInput
        echo "Форматирование: '{$tin->formatInput($input)}'\n";
        
    } catch (TINException $e) {
        echo "ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== Отладка завершена ===\n"; 