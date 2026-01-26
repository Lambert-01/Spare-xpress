# SPARE XPRESS LTD - Advanced Order Management System

## Overview

This is a comprehensive Order Management System designed specifically for automotive spare parts businesses. The system handles both regular stock orders and special on-demand requests, providing complete order lifecycle management from placement to delivery.

## Features

### 🛒 Order Management
- **Stock Orders**: Manage orders for items currently in inventory
- **On-Demand Orders**: Handle special requests for parts not in stock
- **Order Tracking**: Real-time status updates and tracking information
- **Payment Management**: Multiple payment methods and status tracking
- **Customer Management**: Comprehensive customer information storage

### 📊 Analytics & Reporting
- **Revenue Analytics**: Monthly and daily revenue tracking
- **Order Statistics**: Comprehensive order metrics and KPIs
- **Product Performance**: Best-selling products and brand analysis
- **Payment Analytics**: Payment method distribution and trends

### 🧾 Invoice Generation
- **PDF Invoices**: Professional invoice generation with company branding
- **Order Details**: Complete itemized billing with taxes and shipping
- **QR Codes**: Digital verification codes on invoices
- **Multi-language Support**: Ready for internationalization

### 🔄 Order Workflow
- **Status Management**: Complete order lifecycle from pending to delivered
- **Automated Notifications**: Real-time updates for order changes
- **Tracking Integration**: Courier and tracking number management
- **Quality Control**: Order notes and special instructions

## Database Schema

### Core Tables

#### `customers`
```sql
- id (Primary Key)
- customer_name, customer_phone, customer_email
- customer_address, city, sector
- created_at, updated_at
```

#### `orders`
```sql
- id (Primary Key)
- order_number (Unique, SPX-000001 format)
- customer_id (Foreign Key)
- order_type (stock/on_demand)
- order_status (pending/processing/ready/shipped/delivered/cancelled/failed)
- payment_status (unpaid/partial/paid/refunded)
- payment_method (cash/momo/bank/card)
- Financial fields (subtotal, tax_amount, shipping_fee, discount_amount, total_amount)
- Shipping information (address, courier, tracking_number)
- Timestamps
```

#### `order_items`
```sql
- id (Primary Key)
- order_id (Foreign Key)
- product_id (Foreign Key, nullable)
- Product details (name, brand, model, category, image)
- Pricing (unit_price, quantity, subtotal)
```

#### `order_on_demand`
```sql
- id (Primary Key)
- request_number (Unique, SOD-000001 format)
- customer_id (Foreign Key)
- Vehicle information (brand, model, year)
- Part details (name, description, image)
- Pricing (estimated_min, estimated_max, quoted_price)
- Status tracking
```

#### `order_tracking`
```sql
- id (Primary Key)
- order_id (Foreign Key)
- status, status_description
- location, tracking_number, courier_name
- estimated_delivery, actual_delivery
- created_by, created_at
```

#### `order_notes`
```sql
- id (Primary Key)
- order_id / on_demand_id (Foreign Keys)
- note_type (internal/customer/packing/issue/refund)
- note_content, is_visible_to_customer
- created_by, created_at
```

#### `payments`
```sql
- id (Primary Key)
- order_id / on_demand_id (Foreign Keys)
- payment_method, transaction_id, amount
- payment_status, payment_date
- processed_by, created_at
```

## Installation & Setup

### 1. Database Setup
Run the SQL schema file to create all necessary tables:
```bash
mysql -u username -p database_name < sql/order_management_schema.sql
```

Or use the web-based setup:
```
Visit: http://your-domain/admin/setup.php
```

### 2. Dependencies
Ensure you have the following PHP extensions:
- `mysqli` or `pdo_mysql`
- `gd` (for image processing)
- `mbstring` (for UTF-8 support)

Install FPDF for invoice generation:
```bash
# Download FPDF from http://www.fpdf.org/
# Place in lib/fpdf/ directory
```

### 3. File Permissions
Ensure proper permissions for file uploads:
```bash
chmod 755 uploads/
chmod 755 uploads/order_images/
chmod 755 uploads/invoices/
```

### 4. Configuration
Update database connection in `admin/includes/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'sparedb');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

## API Endpoints

### Orders API (`api/get_orders.php`)
```
GET /admin/orders/api/get_orders.php?action=list
GET /admin/orders/api/get_orders.php?action=count
POST /admin/orders/api/update_status.php
POST /admin/orders/api/update_payment.php
DELETE /admin/orders/api/delete_order.php
```

### Parameters
- `page`: Page number for pagination
- `limit`: Number of records per page
- `search`: Search term for filtering
- `status`: Order status filter
- `payment_status`: Payment status filter
- `date_from/date_to`: Date range filters

## Usage Guide

### Managing Orders

#### 1. Viewing Orders
- Navigate to `admin/orders/list_orders.php`
- Use filters to find specific orders
- Click order number to view details
- Export data to CSV/Excel

#### 2. Order Status Updates
- Click on status badge to change order status
- Add tracking information for shipped orders
- System automatically logs status changes

#### 3. Payment Management
- Update payment status and method
- Record transaction IDs
- Track partial payments

#### 4. On-Demand Requests
- View special part requests at `order_demand_list.php`
- Quote prices for requested parts
- Update request status through workflow

### Analytics Dashboard

#### Key Metrics
- **Orders Today**: Daily order count
- **Monthly Revenue**: Current month financial performance
- **Pending Orders**: Orders requiring attention
- **Top Products**: Best-selling items

#### Charts Available
- Monthly revenue trends
- Daily order patterns
- Payment method distribution
- Brand performance analysis

### Invoice Generation

#### Automatic Features
- Company branding and logo
- Complete customer and order details
- Itemized product list with pricing
- Tax and shipping calculations
- QR code for digital verification

#### Customization
- Modify invoice template in `generate_invoice.php`
- Add company-specific terms and conditions
- Customize styling and layout

## Security Features

### Data Protection
- **SQL Injection Prevention**: Prepared statements throughout
- **XSS Protection**: Input sanitization and validation
- **CSRF Protection**: Token-based form validation
- **Session Security**: Secure admin authentication

### Access Control
- **Role-based Access**: Admin-only access to management functions
- **Audit Logging**: All changes tracked with timestamps
- **Data Validation**: Comprehensive input validation
- **File Upload Security**: Safe file handling and validation

## Integration Points

### Frontend Integration
The system integrates with existing frontend components:

#### Shop Page (`pages/shop.php`)
- Order placement triggers admin notifications
- Cart data automatically syncs with order_items table

#### Checkout Process (`pages/checkout.php`)
- Customer data stored in customers table
- Payment information tracked in payments table

#### Order Success (`pages/order_success.php`)
- Order confirmation updates order status
- Generates order number and tracking

### Third-party Integrations

#### Payment Gateways
- **MTN Mobile Money**: Integrated payment processing
- **Bank Transfer**: Manual payment confirmation
- **Card Payments**: External processor integration

#### Shipping Providers
- **DHL Rwanda**: Tracking integration
- **Courier Services**: Status update webhooks
- **Local Delivery**: Custom delivery management

## Customization Guide

### Adding New Order Statuses
1. Update ENUM in database schema
2. Add status handling in PHP logic
3. Update frontend status displays
4. Add status-specific actions

### Custom Invoice Templates
1. Modify `generate_invoice.php`
2. Add custom fields and styling
3. Include additional company information
4. Add custom terms and conditions

### Payment Method Extensions
1. Add new payment method to ENUM
2. Update payment processing logic
3. Add method-specific validation
4. Update analytics calculations

## Troubleshooting

### Common Issues

#### Database Connection Errors
```
Error: No connection could be made
Solution: Check MySQL server status and credentials
```

#### File Upload Failures
```
Error: Upload directory not writable
Solution: chmod 755 uploads/ directory
```

#### PDF Generation Errors
```
Error: FPDF class not found
Solution: Ensure FPDF library is properly installed
```

### Performance Optimization

#### Database Indexes
- Ensure all foreign keys are indexed
- Add composite indexes for common queries
- Regular index maintenance

#### Caching Strategies
- Implement query result caching
- Cache frequently accessed data
- Use CDN for static assets

#### Query Optimization
- Use EXPLAIN to analyze slow queries
- Implement pagination for large datasets
- Optimize complex JOIN operations

## Support & Documentation

### File Structure
```
admin/orders/
├── list_orders.php          # Main orders list
├── view_order.php           # Order details page
├── order_demand_list.php    # On-demand requests
├── analytics.php            # Analytics dashboard
├── generate_invoice.php     # PDF invoice generator
├── api/                     # API endpoints
│   ├── get_orders.php
│   ├── update_status_api.php
│   └── ...
└── README.md               # This documentation
```

### Version History
- **v1.0.0**: Initial release with core order management
- **v1.1.0**: Added analytics dashboard
- **v1.2.0**: Enhanced invoice generation
- **v1.3.0**: On-demand request management

### Future Enhancements
- [ ] Mobile app integration
- [ ] Email notification system
- [ ] Advanced reporting features
- [ ] Multi-currency support
- [ ] Automated order processing

---

**SPARE XPRESS LTD** - Professional Automotive Parts Management System
Contact: support@sparexpress.rw | Phone: +250 792 865 114