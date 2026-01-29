# Quick Fix Summary for Failing Tests

## Root Cause

The `CountryHandler::validate()` method normalizes the TIN **before** checking the pattern:

```php
$normalizedTin = $this->normalizeTin($tin);  // Removes separators
if (!$this->hasValidPattern($normalizedTin)) {  // Pattern expects separators!
    throw TINException::invalidPattern($tin);
}
```

This causes pattern matching to fail because:
- **Switzerland & SouthKorea:** Patterns expect separators (dots/dashes) but receive normalized TIN without separators
- **Ukraine:** Should work fine (pattern expects digits only, normalization removes non-digits)

---

## 1. Switzerland - Quick Fix

### Problem
- Pattern: `'^756\.\d{4}\.\d{4}\.\d{2}$'` expects dots
- Normalized: `'7561234567890'` has no dots
- Result: Pattern fails ❌

### Solution

**Update patterns to match normalized format:**

```php
// In Switzerland.php, update:
public const PATTERN_AVS = '^756\d{10}$';  // 13 digits: 756 + 10 digits
public const PATTERN_UID = '^CHE\d{9}$';   // CHE + 9 digits
public const PATTERN = '^(756\d{10}|CHE\d{9})$';
```

**OR override validate() to check pattern before normalization:**

```php
public function validate(string $tin): bool
{
    // Check pattern on original TIN (with separators)
    if (!$this->hasValidPattern($tin)) {
        throw TINException::invalidPattern($tin);
    }
    
    $normalizedTin = $this->normalizeTin($tin);
    
    if (!$this->hasValidLength($normalizedTin)) {
        throw TINException::invalidLength($tin);
    }
    
    if (!$this->hasValidDate($normalizedTin)) {
        throw TINException::invalidDate($tin);
    }
    
    if (!$this->hasValidRule($normalizedTin)) {
        throw TINException::invalidSyntax($tin);
    }
    
    return true;
}
```

---

## 2. SouthKorea - Quick Fix

### Problem
- Pattern: `'^\d{6}-?\d{7}$'` expects optional dash
- Normalized: `'9001011234563'` has no dash
- Result: Pattern fails ❌

### Solution

**Update patterns to match normalized format:**

```php
// In SouthKorea.php, update:
public const PATTERN_RRN = '^\d{13}$';  // 13 digits
public const PATTERN_BRN = '^\d{10}$';  // 10 digits
public const PATTERN = '^(\d{13}|\d{10})$';
```

**OR use the same validate() override approach as Switzerland**

---

## 3. Ukraine - Analysis

### Status
- Pattern: `'^\d{10}$'` expects 10 digits only ✓
- Normalization: Removes non-digits, keeps 10 digits ✓
- Checksum: Algorithm verified correct ✓

### Potential Issues
1. If tests fail, verify `normalizeTin()` override is being called correctly
2. Check that pattern matching receives normalized TIN (should work)
3. Verify checksum calculation matches test expectations

### If Still Failing
Check if the issue is with:
- Pattern matching on normalized vs original TIN
- Checksum validation logic
- Edge cases in `hasValidRule()` (all same digits, all zeros)

---

## Recommended Implementation Order

1. **Fix Switzerland** - Update patterns OR override validate()
2. **Fix SouthKorea** - Update patterns OR override validate()  
3. **Debug Ukraine** - If still failing, add debug output to trace validation flow

## Testing After Fix

Run tests:
```bash
vendor/bin/phpspec run spec/vldmir/Tin/CountryHandler/SwitzerlandSpec.php -vvv
vendor/bin/phpspec run spec/vldmir/Tin/CountryHandler/SouthKoreaSpec.php -vvv
vendor/bin/phpspec run spec/vldmir/Tin/CountryHandler/UkraineSpec.php -vvv
```
