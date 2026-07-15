# Stock Transfer - Manual Testing Guide

## Setup Requirements
1. Two shops configured in database (e.g., Shop 2 and Shop 9)
2. Products with stock in source shop
3. User logged in to source shop

## Pre-Transfer Verification
Before running a transfer, verify initial stock levels:

```sql
-- Check source shop stock
SELECT stock_item_code, stock_item_name, stock_item_qty, stock_mu_qty, stock_shop_id
FROM stock2
WHERE stock_shop_id = 2
AND stock_item_code = 'P0000063'
AND item_s_price = 180;

-- Check destination shop stock
SELECT stock_item_code, stock_item_name, stock_item_qty, stock_mu_qty, stock_shop_id
FROM stock2
WHERE stock_shop_id = 9
AND stock_item_code = 'P0000063'
AND item_s_price = 180;
```

## Test Cases

### Test 1: Basic Transfer - Pieces
**Setup:**
- Source shop_id = 2
- Destination po_shop_id = 9
- Product: P0000063 (Bala Bilwa Shrutha)
- Unit type: pieces
- Product qty: 2

**Initial State:**
```
Source (Shop 2):     stock_item_qty = 10
Destination (Shop 9): stock_item_qty = 5
```

**Steps:**
1. Login to Shop 2
2. Go to newPharmacyTransfer.php
3. Select Shop 9 from dropdown
4. Scan barcode P0000063
5. Select price 180
6. Change quantity to 2
7. Click Checkout

**Expected Result:**
```
Source (Shop 2):     stock_item_qty = 8 ✓ (10 - 2)
Destination (Shop 9): stock_item_qty = 7 ✓ (5 + 2)
```

**Query to Verify:**
```sql
SELECT stock_item_qty FROM stock2 
WHERE stock_item_code = 'P0000063' AND stock_shop_id = 2;
-- Expected: 8

SELECT stock_item_qty FROM stock2 
WHERE stock_item_code = 'P0000063' AND stock_shop_id = 9;
-- Expected: 7
```

---

### Test 2: Transfer with Liquid Unit (ml)
**Setup:**
- Product: MTT30 (MAHA THUWALA THELA)
- Unit type: ml
- UCV: 33
- Product qty: 1

**Initial State:**
```
Source (Shop 2):     stock_item_qty = 50, stock_mu_qty = 1650
Destination (Shop 9): stock_item_qty = 15, stock_mu_qty = 495
```

**Expected Calculation:**
```
transfer_qty = 1 ml
ucv = 33
product_minimum_qty = 1 * 33 = 33

Source (Shop 2):
  stock_item_qty = 50 - 1 = 49
  stock_mu_qty = 1650 - 33 = 1617

Destination (Shop 9):
  stock_item_qty = 15 + 1 = 16
  stock_mu_qty = 495 + 33 = 528
```

---

### Test 3: Transfer Product Not in Destination
**Setup:**
- Product exists in source shop (Shop 2)
- Product does NOT exist in destination shop (Shop 9)

**Expected Behavior:**
1. Action checks if product exists in destination → NO
2. Fetches product details from source shop
3. INSERTS new record in destination with:
   - stock_item_qty = transferred qty
   - unit_cost, unit_s_price, added_discount (from source)
   - stock_shop_id = po_shop_id

**Verification:**
```sql
-- After transfer, verify new record created
SELECT * FROM stock2 
WHERE stock_item_code = '[code]' AND stock_shop_id = 9;
-- Should have 1 record
```

---

### Test 4: Multiple Products Transfer
**Setup:**
- Transfer 3 different products in one batch

**Expected Behavior:**
1. For each product: Add to destination, then deduct from source
2. All updates should succeed
3. Single invoice number generated for batch

**Verification:**
```sql
-- Check all 3 products were updated correctly
SELECT stock_item_code, stock_item_qty, stock_shop_id FROM stock2
WHERE stock_shop_id IN (2, 9)
ORDER BY stock_item_code;
```

---

### Test 5: Transfer with Discount
**Setup:**
- Sub total: 1000
- Discount: 10%
- Net total: 900

**Expected Behavior:**
- Discount is recorded in database
- Stock transfer happens regardless (discount doesn't affect quantities)

**Verification:**
Stock quantities should be updated; discount is for financial tracking

---

## Edge Cases to Test

### Edge Case 1: Quantity Too Large
**Attempt:** Transfer 100 units when only 50 available

**Current Behavior:** 
- Source qty becomes NEGATIVE (50 - 100 = -50)
- ⚠️ No validation to prevent this

**Recommendation:**
Add check before deduction:
```php
SELECT stock_item_qty FROM stock2 WHERE ...;
if ($available_qty < $transfer_qty) {
  echo json_encode(['status' => 'error', 'message' => 'Insufficient stock']);
  exit();
}
```

---

### Edge Case 2: Product Doesn't Exist in Source
**Attempt:** Try to transfer product only found in destination

**Current Behavior:**
- Line 107: Fetches from source, gets NULL
- INSERT fails silently or with NULL values
- ⚠️ No validation

---

### Edge Case 3: Duplicate Product in Cart
**Current Behavior:**
- JavaScript checks for duplicates: `data-barcode` attribute (line 40)
- Shows alert if duplicate
- Prevents double-add ✓

---

### Edge Case 4: Session Timeout
**Current Behavior:**
- Line 10-20: Checks `$_SESSION['store_id']`
- Returns JSON: `status: session_expired`
- JavaScript handles with `handleExpiredSession()` ✓

---

## Data Flow Validation

### Verify Data Integrity
```sql
-- Check transfer happened atomically
SELECT 
  s.stock_item_code,
  s.stock_item_name,
  (SELECT stock_item_qty FROM stock2 WHERE stock_shop_id = 2 AND stock_item_code = s.stock_item_code) AS source_qty,
  (SELECT stock_item_qty FROM stock2 WHERE stock_shop_id = 9 AND stock_item_code = s.stock_item_code) AS dest_qty
FROM stock2 s
WHERE s.stock_item_code IN ('P0000063', 'MTT30')
GROUP BY s.stock_item_code;
```

---

## Expected Console Logs (Browser DevTools)
1. AJAX POST to `/actions/newPharmacyTransfer.php` - Status 200
2. Response JSON with `status: success` or `status: error`
3. If success: `SuccessMessageDisplay()` shows
4. Invoice number displayed
5. Page should auto-reload (commented out on line 309)

---

## Database Monitoring (During Transfer)
Open database in another window:
```sql
-- Monitor changes in real-time
SELECT stock_item_code, stock_shop_id, stock_item_qty, stock_mu_qty, LAST_INSERT_ID()
FROM stock2 
WHERE stock_item_code = 'P0000063'
ORDER BY stock_shop_id;
```

---

## Success Indicators

✓ Stock decreased in source shop (shop_id)
✓ Stock increased in destination shop (po_shop_id)  
✓ Browser shows "Stock transfer successful" message
✓ Invoice number generated and displayed
✓ No negative stock (unless intentional test)
✓ All unit types calculated correctly
✓ Multiple products transferred in same batch

---

## Common Issues & Fixes

| Issue | Cause | Fix |
|-------|-------|-----|
| Stock not updating | No po_shop_id selected | Select destination shop from dropdown |
| Product not found | Product doesn't exist in source | Verify product is in current shop's inventory |
| Error on checkout | Invalid quantity | Use numeric quantity values |
| Negative stock | Transfer qty > available | Implement stock validation |
| Silent failure | Query error not logged | Check PHP error logs |

