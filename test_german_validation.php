<?php
require_once 'vendor/autoload.php';

use vldmir\Tin\TIN;

echo "=== Тест валидации немецких TIN ===\n\n";

// Валидные TIN (должны проходить валидацию)
$valid_tins = [
    '26954371827', // IdNr - цифра 7 встречается дважды
    '86095742719', // IdNr - цифра 9 встречается дважды  
    '65929970489', // StNr - цифра 9 встречается трижды
];

// Невалидные TIN (должны НЕ проходить валидацию)
$invalid_tins = [
    '12345678901', // Все цифры присутствуют (нарушение правил IdNr)
    '48036952129', // Две цифры встречаются дважды (нарушение правил IdNr)
    '11111111111', // Все цифры одинаковые
    '01234567890', // Начинается с 0
];

echo "ВАЛИДНЫЕ TIN:\n";
foreach ($valid_tins as $tin) {
    $tinObj = TIN::from('DE', $tin);
    $result = $tinObj->isValid() ? "✓ VALID" : "✗ INVALID";
    echo "$tin: $result\n";
}

echo "\nНЕВАЛИДНЫЕ TIN:\n";
foreach ($invalid_tins as $tin) {
    $tinObj = TIN::from('DE', $tin);
    $result = $tinObj->isValid() ? "✓ VALID" : "✗ INVALID";
    echo "$tin: $result\n";
}

echo "\n=== Тест завершен ===\n"; 