# Laravel Project README

## Project Overview
This project implements full e-commerce backend functionality including:
- User authentication with JWT
- Role-based access control (Admin, Vendor, User)
- Product management with variants & inventory
- Order management with workflow
- PDF invoice generation
- Email notifications
- CSV bulk import
- Queue jobs for async tasks
- Repository + Service architecture
- Events & Listeners

## Features
- Create products with variants
- Inventory tracking and low-stock events
- Order workflow: Pending → Processing → Shipped → Delivered → Cancelled
- Inventory deduction/rollback
- PDF invoice download
- CSV bulk product import
- Email notifications for order updates

## Local Setup Instructions
```bash
git clone https://github.com/dawoodulislam/btf_ecommerce_order_management
cd project
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=ProductSeeder
php artisan serve
```

## API Authentication Guide (JWT)
- Register: POST `/api/v1/auth/register`
- Login: POST `/api/v1/auth/login`
- Refresh Token: POST `/api/v1/auth/refresh`

Header:
```
Authorization: Bearer <token>
```

## Product APIs
### Create Product
POST `/api/v1/products`
```json
{
  "title": "Sample Product",
  "description": "Text",
  "price": 100,
  "variants": [
    { "name": "Black", "sku": "SKU-1", "price": 100, "quantity": 10 }
  ]
}
```

### Update Product
PUT `/api/v1/products/{id}`

### Delete Product
DELETE `/api/v1/products/{id}`

### Add Variant
POST `/api/v1/products/{id}/variants`

### Update Variant
PUT `/api/v1/variants/{id}`

### Delete Variant
DELETE `/api/v1/variants/{id}`

## Orders API
### Create Order
POST `/api/v1/orders`
```json
{
  "items": [
    { "variant_id": 1, "quantity": 2 }
  ]
}
```

### Update Order Status
PUT `/api/v1/orders/{id}/status`
```json
{ "status": "processing" }
```

### Download Invoice PDF
GET `/api/v1/orders/{id}/invoice`

## Testing Instructions
```bash
php artisan test
```

## Technical Notes / Future Improvements

Due to time limitations, the following enhancements were not fully implemented across the entire codebase but are planned as next steps:
 - PSR-12 Coding Style Compliance
 - Consistent try/catch Error Handling
 - Centralized Logging Strategy 
 - Exception Handling Extensions
 - Feature Test and Unit test  

## Developer Info
**Name:** Dawoodul Islam
**Email:** dislam151350@bscse.uiu.ac.bd
**GitHub:** https://github.com/dawoodulislam