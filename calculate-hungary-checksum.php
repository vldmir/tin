<?php

echo "🧮 Calculating correct Hungarian TIN checksum\n";
echo "=============================================\n\n";

// Hungarian checksum algorithm:
// Sum of (digit * position) for first 9 digits, then remainder mod 11

function calculateHungarianChecksum($firstNineDigits) {
    $sum = 0;
    for ($i = 0; $i < 9; $i++) {
        $digit = (int) $firstNineDigits[$i];
        $position = $i + 1; // Positions 1-9
        $sum += $digit * $position;
        echo "Digit $digit at position $position: $digit * $position = " . ($digit * $position) . "\n";
    }
    
    echo "\nTotal sum: $sum\n";
    $checksum = $sum % 11;
    echo "Checksum (sum % 11): $checksum\n";
    
    return $checksum;
}

// Try base number 812345678
$baseNumber = '812345678';
echo "Base number: $baseNumber\n";
echo "Calculation:\n";

$checksumDigit = calculateHungarianChecksum($baseNumber);
$fullTin = $baseNumber . $checksumDigit;

echo "\n✅ Correct Hungarian TIN: $fullTin\n";
echo "   Length: " . strlen($fullTin) . "\n";
echo "   Starts with 8: " . (str_starts_with($fullTin, '8') ? 'YES' : 'NO') . "\n";

// Let's verify with another calculation
echo "\n🔍 Verification:\n";
$sum = 0;
for ($i = 0; $i < 9; $i++) {
    $digit = (int) $fullTin[$i];
    $sum += $digit * ($i + 1);
}
$remainder = $sum % 11;
$lastDigit = (int) $fullTin[9];

echo "Calculated checksum: $remainder\n";
echo "Last digit: $lastDigit\n";
echo "Match: " . ($remainder === $lastDigit ? 'YES ✅' : 'NO ❌') . "\n";

echo "\n🎯 Use this TIN as placeholder: $fullTin\n"; 