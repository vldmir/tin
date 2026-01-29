<?php

require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;
use vldmir\Tin\Exception\TINException;

echo "\n=== ФИНАЛЬНАЯ ОТЛАДКА ПРОБЛЕМЫ С ПРОБЕЛАМИ ===\n\n";

$testCases = [
    '48036952129',
    '48 036 952 129',
    '48-036-952-129',
    '48.036.952.129',
];

foreach ($testCases as $input) {
    echo "Вход: '$input'\n";
    
    // Проверим нормализацию напрямую
    $normalized = preg_replace('#[^[:alnum:]]#u', '', $input);
    echo "Нормализованный: '$normalized'\n";
    
    try {
        $tin = TIN::from('DE', $input);
        $isValid = $tin->isValid();
        echo "Валидность: " . ($isValid ? 'ВАЛИДЕН' : 'НЕВАЛИДЕН') . "\n";
        
        if ($isValid) {
            $type = $tin->identifyTinType();
            if ($type) {
                echo "Тип: {$type['code']} - {$type['name']}\n";
            }
        }
        
    } catch (TINException $e) {
        echo "ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== Проверим через fromSlug ===\n\n";

$slugCases = [
    'DE48036952129',
    'DE48 036 952 129',
    'DE48-036-952-129',
];

foreach ($slugCases as $slug) {
    echo "Slug: '$slug'\n";
    
    try {
        $tin = TIN::fromSlug($slug);
        $isValid = $tin->isValid();
        echo "Валидность: " . ($isValid ? 'ВАЛИДЕН' : 'НЕВАЛИДЕН') . "\n";
        
    } catch (TINException $e) {
        echo "ОШИБКА: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
}

echo "=== Отладка завершена ===\n"; 