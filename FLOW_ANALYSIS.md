# Pharmacy Stock Transfer Flow Analysis

## Overview
This document traces the complete flow of the pharmacy stock transfer process from `newPharmacyTransfer.php` (UI) through `dist/js/newPharmacyTransfer.js` (Client-side logic) to `actions/newPharmacyTransfer.php` (Server-side processing).

## Architecture Diagram
```
newPharmacyTransfer.php (Frontend Form)
    ↓
dist/js/newPharmacyTransfer.js (Data Collection & AJAX)
    ↓ (POST /actions/newPharmacyTransfer.php)
actions/newPharmacyTransfer.php (Stock Transfer Logic)
    ↓
Database: stock2 table
```

---

## 1. Frontend: newPharmacyTransfer.php

### Key Components:
- **Shop Selection**: Dropdown `id="po-shop-selector"` - Selects destination pharmacy (po_shop_id)
- **Barcode Input**: `id="barcodeInput"` - Scans/enters product codes
- **Price Selector**: `id="selectPrices"` - Selects item price variant
- **Product Grid**: Displays available products from current shop
- **Cart**: Table with `id="barcodeResults"` - Shows selected items for transfer

### Flow:
1. User selects destination shop from dropdown
2. User scans/enters barcode (triggers `getBarcode2()`)
3. User selects price from dropdown (triggers `getBarcode3()`)
4. Product is added to cart (`#barcodeResults` table)
5. User enters quantity in each row
6. User enters discount percentage (optional)
7. User clicks "Checkout" button

---

## 2. Client-Side Logic: dist/js/newPharmacyTransfer.js

### Key Functions:

#### `getBarcode2(barcode)`
- Called when barcode is scanned/entered
- Sends barcode to `actions/searchNameProductPo.php`
- Populates price dropdown with available prices for that product

#### `getBarcode3()`
- Called when price is selected
- Builds product row with:
  - Product code
  - Product name
  - Unit conversion value (UCV)
  - Item price
  - Unit price
  - Quantity input field (default: 1)
  - Total price calculation
- Checks for duplicates before adding
- Resets barcode input focus

#### `addDiscount()`
- Calculates discounted amounts when discount % is entered
- Updates display totals

#### Checkout Flow (Lines 218-310):

```javascript
$("#checkoutBtn").on("click", function () {
  var po_shop_id = document.getElementById("po-shop-selector").value;
  // Validate po_shop_id selected
  // Call checkout(po_shop_id)
});

function checkout(po_shop_id) {
  // Collect data from form:
  var billData = [{
    po_shop_id: po_shop_id,        // ← Destination shop
    sub_total: $("#subTotal").text(),
    discount_percentage: $("#discountPercentage").val(),
    net_total: $("#netTotal").text()
  }];

  // Build products array from cart rows:
  var poArray = [];
  $("#barcodeResults tr").each(function () {
    poArray.push({
      code: product_code,
      ucv: unit_conversion_value,
      item_price: item_price,
      unit_price: unit_price,
      product_name: product_name,
      product_cost: product_cost,
      product_qty: product_qty,        // ← Quantity from input
      product_unit: product_unit,      // pieces, kg, g, ml, m, pack/bottle
      product_total: product_total
    });
  });

  // Send AJAX POST to server:
  $.ajax({
    url: "actions/newPharmacyTransfer.php",
    method: "POST",
    data: {
      billData: JSON.stringify(billData),
      products: JSON.stringify(poArray)
    }
  });
}
```

### Data Sent to Server:
```json
{
  "billData": [{
    "po_shop_id": 9,
    "sub_total": "1000.00",
    "discount_percentage": 10,
    "net_total": "900.00"
  }],
  "products": [
    {
      "code": "P0000063",
      "ucv": 4,
      "item_price": 180,
      "unit_price": 45,
      "product_name": "Bala Bilwa Shrutha",
      "product_cost": 108,
      "product_qty": 2,
      "product_unit": "pieces",
      "product_total": 216
    },
    {
      "code": "MTT30",
      "ucv": 33,
      "item_price": 340,
      "unit_price": 10.3,
      "product_name": "MAHA THUWALA THELA",
      "product_cost": 221,
      "product_qty": 1,
      "product_unit": "ml",
      "product_total": 221
    }
  ]
}
```

---

## 3. Server-Side Processing: actions/newPharmacyTransfer.php

### Session & Authentication
```php
$shop_id = $_SESSION['store_id'][0]['shop_id'];  // Source shop (logged-in user's shop)
$user_id = $_SESSION['store_id'][0]['id'];       // User ID
```

### Data Extraction
```php
$billData = json_decode($_POST['billData'], true);
$poArray = json_decode($_POST['products'], true);

// Extract from billData:
$po_shop_id = $value['po_shop_id'];              // Destination shop
$sub_total = $value['sub_total'];
$discount_percentage = $value['discount_percentage'];
$net_total = $value['net_total'];
```

### Stock Transfer Logic for Each Product

**Step 1: ADD TO DESTINATION SHOP (po_shop_id)**

```
Check if product already exists in po_shop_id with that item_price:
  IF EXISTS:
    → UPDATE quantities (add to existing)
  IF NOT EXISTS:
    → INSERT new record with quantity
    → Fetch product details from source shop (shop_id)
    → Copy unit_cost, unit_s_price, added_discount
```

**SQL for Adding (Lines 73-115):**
```sql
-- Check if exists in destination:
SELECT * FROM stock2 
WHERE stock_item_code = '$code'
AND item_s_price = '$item_price'
AND stock_shop_id = '$po_shop_id'

-- If exists, UPDATE:
UPDATE stock2 SET 
  stock_item_qty = existing_qty + new_qty,
  stock_mu_qty = existing_mu_qty + new_mu_qty
WHERE stock_item_code = '$code' 
AND stock_shop_id = '$po_shop_id' 
AND item_s_price = '$item_price'

-- If not exists, INSERT:
INSERT INTO stock2 (
  stock_item_code, stock_item_name, stock_item_qty, stock_item_cost,
  stock_mu_qty, unit_cost, unit_s_price, added_discount, item_s_price, stock_shop_id
) VALUES (...)
```

**Step 2: DEDUCT FROM SOURCE SHOP (shop_id)**

Deduction logic varies by product unit type:

| Product Unit | Calculation | SQL Update |
|--------------|------------|-----------|
| **pieces** | Simple count | `stock_item_qty - qty` |
| **pack/bottle** | Simple count | `stock_item_qty - qty` |
| **kg, l** | qty × 1000 × ucv | `stock_mu_qty - qty*1000*ucv` |
| **g, ml** | qty × ucv | `stock_mu_qty - qty*ucv` |
| **m** | qty × 100 × ucv | `stock_mu_qty - qty*100*ucv` |

**SQL for Deducting (Lines 122-199):**
```sql
-- Example: pieces
UPDATE stock2 SET 
  stock_item_qty = (stock_item_qty - '$product_qty')
WHERE stock_shop_id = '$shop_id' 
AND stock_item_code = '$code' 
AND item_s_price = '$product_cost'

-- Example: kg/l
UPDATE stock2 SET 
  stock_item_qty = (stock_item_qty - '$product_qty'),
  stock_mu_qty = (stock_mu_qty - '$product_minimum_qty')
WHERE stock_shop_id = '$shop_id' 
AND stock_item_code = '$code'
AND item_s_price = '$product_cost'
```

---

## 4. Database Schema

### stock2 Table:
```
stock_id          INT (Primary Key)
stock_item_code   VARCHAR(50) - Product code
stock_item_name   VARCHAR(50) - Product name
stock_item_qty    FLOAT - Quantity (pieces/units)
stock_item_cost   FLOAT - Cost per item
stock_mu_qty      INT - Minimum unit quantity (1000g = 1kg in ml terms)
unit_cost         DOUBLE - Unit cost
unit_s_price      DOUBLE - Unit selling price
added_discount    INT - Discount %
item_s_price      INT - Item selling price
stock_shop_id     INT - Shop ID (FK to shops table)
stock_minimum_unit_barcode VARCHAR(50) - Alternative barcode
```

### Data Constraint:
**Items can only be transferred if they exist in the source shop (shop_id) with the specified item_s_price**

---

## 5. Complete Transfer Example

### Scenario:
Transfer 2 units of "Bala Bilwa Shrutha" (P0000063) from Shop 2 to Shop 9

### Initial State (Database):
```
stock2 (Shop 2 - Source):
  code: P0000063, item_s_price: 180, stock_item_qty: 10, stock_mu_qty: 40

stock2 (Shop 9 - Destination):
  code: P0000063, item_s_price: 180, stock_item_qty: 5, stock_mu_qty: 20
```

### Request Data (from JavaScript):
```
billData.po_shop_id = 9
products[0].code = P0000063
products[0].product_qty = 2
products[0].product_cost = 108
products[0].item_price = 180
products[0].product_unit = pieces
```

### Step 1: Add to Shop 9
```php
$po_shop_id = 9;
$code = 'P0000063';
$item_price = 180;

// Check if exists in shop 9
SELECT * FROM stock2 WHERE stock_item_code = 'P0000063' 
  AND item_s_price = 180 AND stock_shop_id = 9
// Result: EXISTS (stock_item_qty = 5)

// UPDATE shop 9:
UPDATE stock2 SET 
  stock_item_qty = 5 + 2 = 7,
  stock_mu_qty = 20 + 8 = 28
WHERE stock_item_code = 'P0000063' 
  AND stock_shop_id = 9 AND item_s_price = 180
```

### Step 2: Deduct from Shop 2
```php
$shop_id = 2;
$product_unit = 'pieces';

// UPDATE shop 2:
UPDATE stock2 SET 
  stock_item_qty = (10 - 2) = 8
WHERE stock_shop_id = 2 
  AND stock_item_code = 'P0000063' 
  AND item_s_price = 108
```

### Final State:
```
stock2 (Shop 2 - Source):
  code: P0000063, item_s_price: 180, stock_item_qty: 8, stock_mu_qty: 32  ✓

stock2 (Shop 9 - Destination):
  code: P0000063, item_s_price: 180, stock_item_qty: 7, stock_mu_qty: 28  ✓
```

---

## 6. Validation & Error Handling

### Frontend Validation (JavaScript):
- ✓ `po_shop_id` must be selected (not 0)
- ✓ Cart must not be empty
- ✓ Duplicate products checked before adding
- ✓ Checkout button disabled during processing

### Backend Validation (PHP):
- ✓ Session check (is user logged in?)
- ✓ `billData` is array and not empty
- ✓ `poArray` is array and not empty
- ✓ Each product has required fields:
  - `product_unit` not empty
  - `ucv` not empty
  - `item_price` not empty
  - `product_name` not empty
  - `product_cost` is numeric
  - `product_qty` is numeric
  - `product_total` is numeric

### Error Handling:
- ⚠ No transaction support (if error mid-process, data could be inconsistent)
- ⚠ No SQL injection protection (all variables directly concatenated)
- ⚠ Query errors not checked (silent failures possible)

---

## 7. Security Issues & Recommendations

### Issue 1: SQL Injection Vulnerability
**Current:**
```php
$conn->query("SELECT * FROM stock2 WHERE stock_item_code = '$code'");
```

**Fix:**
```php
$stmt = $conn->prepare("SELECT * FROM stock2 WHERE stock_item_code = ?");
$stmt->bind_param("s", $code);
$stmt->execute();
$stock_result = $stmt->get_result();
```

### Issue 2: No Transaction Support
**Current:** Updates happen separately (ADD then DEDUCT)

**Risk:** If deduction fails, stock is added but not deducted from source

**Fix:**
```php
$conn->begin_transaction();
try {
  // Add to destination
  // Deduct from source
  $conn->commit();
} catch (Exception $e) {
  $conn->rollback();
}
```

### Issue 3: No Error Checking on Queries
**Current:**
```php
$conn->query("UPDATE stock2 SET ...");  // No check if successful
```

**Fix:**
```php
if (!$conn->query("UPDATE stock2 SET ...")) {
  echo json_encode(['status' => 'error', 'message' => $conn->error]);
  exit();
}
```

---

## 8. Testing Checklist

- [ ] **Basic Transfer**: Transfer 1 unit of simple product (pieces)
- [ ] **Bulk Transfer**: Transfer multiple units
- [ ] **Different Units**: Test kg, g, ml, m, pack/bottle
- [ ] **With Discount**: Transfer with discount percentage
- [ ] **Duplicate Addition**: Try adding same product twice
- [ ] **Price Variants**: Test products with multiple prices
- [ ] **Insufficient Stock**: Try transferring more than available
- [ ] **Non-Existent Product**: Try transferring product not in source shop
- [ ] **Shop Selection**: Test without selecting destination shop
- [ ] **Response Handling**: Verify success/error messages display correctly

---

## 9. API Response Format

### Success Response:
```json
{
  "status": "success",
  "message": "Order Placed Successfully!",
  "invoice_number": "000123914000009"
}
```

### Error Response:
```json
{
  "status": "error",
  "message": "Invalid product entry"
}
```

### Session Expired Response:
```json
{
  "status": "session_expired",
  "message": "Session expired. Wait to login again."
}
```

---

## 10. Functionality Assessment

✓ **Items correctly ADDED to po_shop_id**
✓ **Items correctly DEDUCTED from shop_id**
✓ **Multiple unit types supported**
✓ **Discount calculation included**
✓ **Session authentication present**

⚠ **Missing: SQL Injection protection**
⚠ **Missing: Transaction support**
⚠ **Missing: Query error handling**
⚠ **Missing: Stock sufficiency validation**

