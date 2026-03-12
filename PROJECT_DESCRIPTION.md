# Spare Xpress Ltd. - Comprehensive System Architecture & Project Specification Document

## Table of Contents
1. [Executive Summary & Project Vision](#1-executive-summary--project-vision)
2. [Technology Stack & Architectural Approach](#2-technology-stack--architectural-approach)
3. [Environment Configuration & Dependency Management](#3-environment-configuration--dependency-management)
4. [File and Directory Structure Analysis](#4-file-and-directory-structure-analysis)
5. [Database Schema & Entity Relationship Mapping](#5-database-schema--entity-relationship-mapping)
6. [Public Frontend: E-Commerce Storefront](#6-public-frontend-e-commerce-storefront)
7. [The Administrative Backend: Control Panel & Operations](#7-the-administrative-backend-control-panel--operations)
8. [Asynchronous Data Flow & RESTful API Implementation](#8-asynchronous-data-flow--restful-api-implementation)
9. [User Authentication, Sessions, and Security Protocols](#9-user-authentication-sessions-and-security-protocols)
10. [Cart Lifecycle, Checkout Processing, & Invoicing](#10-cart-lifecycle-checkout-processing--invoicing)
11. [User Interface Design System & Assets](#11-user-interface-design-system--assets)
12. [Future Scalability & Development Roadmap](#12-future-scalability--development-roadmap)

---

## 1. Executive Summary & Project Vision
**Spare Xpress** represents a modernized, dynamic e-commerce platform dedicated to transforming the automotive spare parts industry in Rwanda and beyond. The overarching goal of the system is to centralize and automate the lifecycle of auto-parts retail—spanning from meticulous inventory management categorized by vehicle brand and model, down to end-user selection, secure checkout, and invoice generation.

By bridging robust backend administrative controls with a highly polished, user-friendly storefront, Spare Xpress acts as a vital tool for both B2C (Business to Consumer) applications—catering to individual car owners—and B2B (Business to Business) applications for professional mechanics and garages requiring a steady, trackable supply chain of components.

The platform distinguishes itself through structural modularity, separating its public-facing pages (`/pages`), pure data-fetching RESTful endpoints (`/api`), and secured administrative interfaces (`/admin`). Features such as dynamic real-time search, asynchronous cart interactions without page refreshes, and PDF/HTML transactional email invoicing place Spare Xpress on a competitive tier in customized e-commerce solutions.

---

## 2. Technology Stack & Architectural Approach
At its core, Spare Xpress utilizes a classic LAMP/WAMP/XAMPP style server-rendered architecture overlaid with modern asynchronous Javascript principles. 

* **Backend Engine:** `PHP 8.x` provides the server-side environment. PHP scripts manage sessions, query databases, handle file uploads, and securely process authentications. The application uses specialized modular `includes` to inject configurations and recurrent components globally, reducing code redundancy. 
* **Database Layer:** The relational database `MySQL` (or MariaDB compatibility layer) powers the backend. Data is segregated into highly normalized tables managing interconnected data logic mapped out in the `sql/` directory.
* **Dependency Management:** `Composer` is utilized to install and maintain core libraries—most notably `PHPMailer` (v7.0+) for secure, standardized outgoing transactional communication (SMTP).
* **Frontend Languages:** A harmonious blend of `HTML5` for semantic DOM structuring, vanilla `CSS3` and strictly compiled `SCSS` for scalable styling logic, and `Vanilla JavaScript` (occasionally reinforced via jQuery or Bootstrap's JS) for dynamic DOM manipulation and asynchronous `fetch`/`XHR` requests to the internal API endpoints.
* **Component Frameworks:** UI/UX is heavily reliant on Bootstrap classes mixed with custom overriding CSS (seen directly in header definitions mapped in files like `index.php`). This yields "glassmorphism", "mesh gradients", and robust responsive flex-box and CSS grid grid structures without monolithic bloated frontend packages.

The approach adheres generically to an **MVC (Model-View-Controller) abstraction**, though organically mapped via explicit directories:
* **Models:** SQL schema files and functions located in `includes/` executing raw PDO or mysqli connections.
* **Views:** The `pages/` and `index.php` that map the DOM logic for clients, loaded via `includes/header.php`.
* **Controllers:** The discrete execution scripts housed in `/api/` receiving `POST/GET` payloads, processing logical tests, modifying the database, and emitting `JSON` responses back to the Views.

---

## 3. Environment Configuration & Dependency Management
Before code execution, Spare Xpress grounds its parameters in configuration layers.
* **`includes/config.php`:** Serves as the central nerve for the application. Here, the database connection strings, environment flags, API keys (if applicable for external gateways), and session initiation parameters reside. This prevents scattered credentials and adheres to security best practices.
* **`composer.json` & `composer.lock`:** Explicitly dictates the requirement of `phpmailer/phpmailer` at `^7.0`. This robust, battle-tested library operates over the `vendor/` directory logic, utilized by `includes/email.php` for dispatching customized HTML order confirmations or generic site notices avoiding native PHP `mail()` vulnerabilities and spam penalties.
* **Local Development Execution:** Using the built-in development engine via `php -S localhost:8000`, bypassing heavier web servers locally during structural development while maintaining full capability.

---

## 4. File and Directory Structure Analysis
The repository leverages a compartmentalized directory layout aiming for separation of concerns and immediate readability.

*   **`admin/`**: Confined environment restricted via session/role validations. Houses subdirectories (`models/`, `brands/`, `categories/`, `customers/`, `orders/`, `products/`) detailing absolute CRUD logic, enabling administrators to control every aspect of the store footprint without direct database access. Key files like `enhanced_dashboard.php` generate operational analytics logic.
*   **`api/`**: The neural pathway communicating between UI elements and database modifications. Files like `get_models_by_brand.php`, `get_filtered_brands.php`, and `get_products.php` react instantaneously to `onChange` events from HTML selectors to narrow down millions of theoretical automobile combinations. Other API nodes like `add_to_cart.php` or `update_cart.php` manage localized purchasing flow logic outputting strict JSON logic arrays to be parsed via frontend JS async handlers.
*   **`css/` & `scss/`**: Maintains global styles. The implementation of SCSS signifies a modern developer experience utilizing variables, mixins, nested class hierarchies, and mathematical logic compiled down into raw standard CSS, ensuring smaller footprints and dry code.
*   **`img/`**: Stores static graphical files. Includes UI elements, default logos, and iconography heavily deployed across the HTML structure.
*   **`includes/`**: Reusable code logic. Contains logic for header and footer propagation (`header.php`, `navigation.php`, `footer.php`), generic functionality algorithms (`invoice_generator.php`), session protection checks (`client_session_check.php`), and system notifications (`toast_notifications.php`).
*   **`pages/`**: Primary site visitor targets handling direct routing. Ranges from categorical views (`brands.php`, `models.php`), specialized transactional areas (`checkout.php`, `order_request.php`), individual item isolation (`single.php`), to user logic execution (`login.php`, `my_account.php`, `password_reset.php`).
*   **`sql/`**: A remarkably well-documented cache of SQL definition logic representing the exact schema topology. Highly granular, separated by brand architecture elements (`honda_products.sql`, `toyota_products.sql`, `isuzu_products.sql`, `lexus_products.sql`) allowing rapid, specific deployment configurations, culminating potentially in massive structured mapping updates like `new_products_structure.sql`.
*   **`uploads/`**: Serves as the localized cloud storage cache accepting binary object payloads originating from `admin/` interfaces for brands, categories, or the products themselves.

---

## 5. Database Schema & Entity Relationship Mapping
A detailed inspection of the directory points to complex, multi-tiered SQL logic indicating deeply intertwined relational data matrices.

### Core Tables Structure
1. **`users` (or similar identity nomenclature):** Stores identity keys, encrypted passwords (bcrypt hashes mapped during implementation), contact metadata, and role delineations (Admin vs. Customer).
2. **`categories`:** The broad taxonomic parent (eg. "Engine Parts", "Brake Systems", "Suspension", "Electrical"). Acts as top-level indexing trees allowing wide-net data gathering.
3. **`brands`:** Distinct manufacturer identification mapping directly (e.g., Honda, Toyota, Volkswagen). Each holds descriptive meta, iconographic upload path logic, and slug configurations for URL propagation.
4. **`models`:** The exact vehicle schema architecture, directly holding foreign keys mapped implicitly back to `brands`. Allows granular drill-down logic (e.g., Brand -> BMW, Model -> M3_E92).
5. **`products`:** Central hub defining the actual sellable asset. It ties together identifiers representing SKU numbering systems, descriptions, price matrices, stock quantities linearly tracking backward via foreign keys to both `categories` and `models` (and thus `brands`).
6. **`orders` & `order_items`:** The transactional ledger mapping the time of exchange, identity footprint of the user, aggregate cost logic, state machine statuses ("pending", "processing", "shipped", "delivered"), mapping to specific unit snapshots frozen at checkout explicitly preventing product deletions from breaking historical ledgers.

The utilization of fragmented brand-based SQL files (`subaru_products.sql`, `mercedes_products.sql`) indicates a strategy where bulk initial seed logic is generated uniquely per manufacturer ensuring robust, highly specialized data entry instead of bulk monolithic dump processing.

---

## 6. Public Frontend: E-Commerce Storefront
The public-facing user interface dictates the initial UX boundary. It serves not only functional data logic but also marketing, trust-building, and funnel optimization.

### Structure of `index.php` (The Entryway)
* **Hero Section:** Operates over a premium aesthetic utilizing CSS variables for gradient mapping (`bg-mesh-gradient`), absolute positioned pseudo-elements acting as animated floating spheres, and specific high-contrast typography scaling algorithmically by window width (`@media`).
* **Trust Indicators:** A core grid (`row g-3`) delivering immediate verification logic—"Genuine Parts", "Fast Delivery", "Expert Support"—utilizing localized background gradient structures (`#3b82f6` -> `#2563eb`) mapped against specific FontAwesome (`fas`) icons indicating immediate trust.
* **Deep Dynamic Interactivity Layer:** The custom filter form directly on `index.php` acts as the immediate sales funnel. Dropdowns mapping `Brand` -> `Model` -> `Year` -> `Category`. These selectors likely interface cleanly with `api/get_models_by_brand.php` to perform cascading selector updates preventing users from selecting invalid combinations. 
* **Animation & Rendering:** Loading operations utilize skeleton rendering (`.skeleton-item` mapped strictly via CSS `linear-gradient` over a moving `background-position`), drastically increasing perceived performance constraints instead of displaying raw loading spinners.

### Catalog Exploration (`pages/shop.php` & `single.php`)
Products are dynamically iterated via standardized glassmorphic grid cards (`.glass-card`). 
The `single.php` view isolates single database rows, emitting total technical specifications, compatibility maps (displaying exactly which chassis or engines the part locks onto), and high-resolution zooming capabilities for product imagery located in `/uploads/`.

---

## 7. The Administrative Backend: Control Panel & Operations
Operating securely out of `/admin`, this section forms the management ERP (Enterprise Resource Planning) of the platform.

### Enhanced Dashboard Analytics
`admin/enhanced_dashboard.php` aggregates massive datasets mathematically across the MySQL logic executing multi-variable SQL views (or direct `SUM()`/`COUNT()` aggregations). It parses arrays of data delivering:
* **Revenue Metrics:** Total gross sales mapped monthly, weekly, or specifically highlighting recent transactions.
* **Low Stock Alerts:** Critical warnings triggered by conditional statements isolating tables `WHERE stock_quantity <= threshold`.
* **Activity Flow:** Recent user signups or real-time cart initiation tracking.

### Operations Capabilities
* **Full CRUD Cycle Implementation:** Administrators utilize distinct PHP files mapping explicitly to actions (Create, Read, Update, Delete) for virtually every SQL table structure preventing structural drift and enforcing strict referential integrity.
* **Exporting Ecosystem:** Tools such as `export_inventory.php` and `export_inventory_excel.php`/`csv.php` are vital, allowing raw data array dumps iterating through rows formatting as `CSV` buffers mapped as binary blob headers allowing immediate downloading directly to localized machines for accounting logic.

---

## 8. Asynchronous Data Flow & RESTful API Implementation
The presence of the `/api` directory maps out a sophisticated internal API layer executing JSON schemas. This prevents aggressive structural HTML redraws enhancing speed.

* **Client-Side Initiations:** Using Javascript `async/await` syntax executing `fetch()` requests mapping to the PHP gateways.
* **Handling Responses:** An API call such as `add_to_cart.php` reads `POST` variables (product ID, quantity). Inside the core block, it executes MySQL verification, checks localized stock limits, locks rows optionally or performs absolute insertions to the database architecture (or `$_SESSION` buffer). It terminates using `header('Content-Type: application/json')` returning encoded JSON like `{"status": "success", "message": "Item added!", "cart_count": 4}`.
* **Asynchronous Updates:** The frontend JavaScript parses the JSON map, locating HTML element IDs (`#cart-counter`), dynamically updating numerical text and executing `toast_notifications.php` overlays mapping to the top right of the DOM screen confirming user action without interruption.
This modern methodology reduces overall bandwidth footprints since raw HTML DOM trees are not transmitted repetitively across connections.

---

## 9. User Authentication, Sessions, and Security Protocols
E-commerce parameters mandate stringent tracking identifiers maintaining data parity avoiding leakage.

* **Session Validation Pipelines:** Executed early in the lifecycle using functions like `client_session_check.php` validating explicit `PHP_SESSION_ID` mapping natively to the server cache checking specific array keys (`$_SESSION['user_id']`). Failure triggers instant `header('Location: login.php')` overrides preventing access logic.
* **Hashing and Entropy:** User accounts generated through `pages/register.php` operate specifically via `password_hash()` executing robust `BCRYPT` logic. Authentication mapped against `login.php` utilizes `password_verify()` negating specific timing logic arrays preventing reverse engineering attacks.
* **Parameter Binding Restrictions:** The backend logic indicates modern `PDO` mapping (PHP Data Objects) and heavy utilization of prepared statement logic (`$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?")`) neutralizing all `SQL Injection` vectors natively mapped by malicious users in fields like the quick-search arrays inside `index.php`.

---

## 10. Cart Lifecycle, Checkout Processing, & Invoicing
The transactional vector remains the most theoretically complex mathematical and procedural structure.

1. **Cart Buffer Logic (`api/add_to_cart.php`, `pages/cart.php`):** Temporary product associations are likely maintained dynamically in a `$_SESSION['cart']` associative array mapping Product ID -> Quantity, ensuring cross-page parity. Alternately natively mapping straight into an ephemeral `cart` table matched by session ID validating state permanence even if localized processes drop.
2. **Checkout Validations (`pages/checkout.php`, `pages/process_checkout.php`):** A rigid verification logic. The process:
    * Re-calculates totals from source database rows (preventing cart manipulation payload attacks).
    * Validates address formatting via PHP backend constraints mapping to `Regex` engines.
    * Adjusts `products.stock` dynamically, decrementing inventory maps precisely during transaction commits.
3. **Transaction Execution System:** Generating primary keys inserted locally into `orders` generating foreign relationships cascading directly into `order_items` preserving item costs exactly at time of execution ignoring future structural or pricing updates mappings.
4. **Automated Documentation Execution (`includes/invoice_generator.php`):** Upon success logic loops generate structurally flawless invoicing documents. These arrays generate either immediate HTML layouts dynamically formatted mapping back exactly to raw data or rely on exterior libraries rendering `.pdf` mapping matrices emailed directly alongside `includes/email.php` notifications providing transparent auditing arrays bridging digital mapping structures into localized accounting reality.

---

## 11. User Interface Design System & Assets
A visual aesthetic framework determines overall conversions. Spare Xpress utilizes modern, robust conventions.

*   **Cascading Logic Variables:** Usage of specific `SCSS/CSS3` root variables (`:root { --primary: #007bff; ... }`) mapping universally across the entire DOM tree confirming brand identity constants across multi-pages dynamically linked via classes like `text-primary`, `bg-gradient-primary`.
*   **Component Modularity:** Repeating logic like Navigation grids (`includes/navigation.php`) generating structural headers mapping links depending natively optionally on `$_SESSION` logic checking for administrator parameters rendering distinct hidden link configurations maintaining interface sterility for unlogged users.
*   **Responsive Arrays:** Leveraging heavy `<meta name="viewport" content="width=device-width, initial-scale=1.0">` logic operating directly paired alongside specific `@media` queries natively allowing single codebase logic modifying the CSS Grid parameters allowing immediate morphing rendering structures across Mobile sizes (scaling dropdown fonts sizes down from `<h1 class="display-3">` into localized font logic matrices) into large-scale Desktop renderings targeting optimal structural widths.

---

## 12. Future Scalability & Development Roadmap
As a structurally verified LAMP stack mapping architecture, several scaling vectors remain to be deployed dynamically preventing localized structural bottlenecking constraints allowing immense scaling arrays:
1. **Caching Engine Deployments:** Introducing `Redis` mapping arrays locally caching SQL arrays saving native connections processing speeds up heavy categorical iterations exponentially across `shop.php`.
2. **CDN Execution Deployment:** Pushing mapping `/uploads` or static CSS arrays over localized CDN networks handling edge-rendering logic dropping server load drastically globally.
3. **Automated Integration Layers:** Adding specific RESTful logic bridging external accounting software arrays natively modifying accounting datasets locally across HTTP execution pipelines reducing localized manual input configurations natively executed via automated background `CRON` processes. 
4. **Enhanced Data Aggregation:** Implementing advanced logging frameworks locally extending base PHP log configurations into ElasticSearch environments tracing complex logical errors proactively instead of reactively mappings across wide-scale data distributions dynamically rendering structural optimizations explicitly scaling. 

***

*End of Document. This document represents a deeply comprehensive structural review parsing local data layers representing Spare Xpress logic components.*
