-- New simplified products_enhanced table structure
CREATE TABLE products_enhanced (
  id INT AUTO_INCREMENT PRIMARY KEY,
  brand_id INT NOT NULL,
  model_id INT DEFAULT NULL,
  category_id INT NOT NULL,
  product_name VARCHAR(255) NOT NULL,
  slug VARCHAR(255) UNIQUE,
  sku VARCHAR(100) UNIQUE,
  description TEXT,
  short_description VARCHAR(500),
  main_image VARCHAR(255),
  gallery_images TEXT COMMENT 'JSON array of image paths',
  price DECIMAL(10,2) NOT NULL,
  sale_price DECIMAL(10,2) DEFAULT NULL,
  stock_quantity INT DEFAULT 0,
  stock_status ENUM('in_stock','out_of_stock') DEFAULT 'in_stock',
  `condition` ENUM('new','used') DEFAULT 'new',
  is_featured TINYINT(1) DEFAULT 0,
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Indexes for performance
ALTER TABLE products_enhanced ADD KEY idx_brand_id (brand_id);
ALTER TABLE products_enhanced ADD KEY idx_model_id (model_id);
ALTER TABLE products_enhanced ADD KEY idx_category_id (category_id);
ALTER TABLE products_enhanced ADD KEY idx_stock_status (stock_status);
ALTER TABLE products_enhanced ADD KEY idx_is_featured (is_featured);
ALTER TABLE products_enhanced ADD KEY idx_is_active (is_active);
ALTER TABLE products_enhanced ADD FULLTEXT KEY idx_search (product_name, description, short_description);

-- Insert all spare parts from the PDF document
-- Note: Using placeholder IDs. In actual database, use correct brand_id, model_id, category_id
-- Prices are estimated, stock_quantity set to 10 for demo

-- TOYOTA PARTS

-- Toyota Corolla / Corolla Cross
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 1, 1, 'Toyota Corolla Ignition Coils', 'toyota-corolla-ignition-coils', 'SPX-TOY-IGN-001', 'High-quality ignition coils for Toyota Corolla engines', 'Ignition coils replacement', 15000.00, 10, 'in_stock', 'new', 0, 1),
(1, 1, 1, 'Toyota Corolla Oxygen Sensor', 'toyota-corolla-oxygen-sensor', 'SPX-TOY-OXY-001', 'Oxygen sensor for Toyota Corolla catalytic converter', 'Oxygen sensor replacement', 12000.00, 10, 'in_stock', 'new', 0, 1),
(1, 1, 3, 'Toyota Corolla CV Joints', 'toyota-corolla-cv-joints', 'SPX-TOY-CV-001', 'CV joints for Toyota Corolla', 'CV joints replacement', 25000.00, 10, 'in_stock', 'new', 0, 1),
(1, 1, 1, 'Toyota Corolla Engine Mounts', 'toyota-corolla-engine-mounts', 'SPX-TOY-EM-001', 'Engine mounts for Toyota Corolla', 'Engine mounts replacement', 18000.00, 10, 'in_stock', 'new', 0, 1),
(1, 1, 3, 'Toyota Corolla Steering Rack Bushings', 'toyota-corolla-steering-rack-bushings', 'SPX-TOY-SRB-001', 'Steering rack bushings for Toyota Corolla', 'Steering rack bushings replacement', 8000.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota Hilux (Vigo / Revo)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 3, 3, 'Toyota Hilux Shock Absorbers', 'toyota-hilux-shock-absorbers', 'SPX-TOY-SA-001', 'Shock absorbers for Toyota Hilux heavy load use', 'Shock absorbers replacement', 35000.00, 10, 'in_stock', 'new', 0, 1),
(1, 3, 3, 'Toyota Hilux Ball Joints & Tie Rods', 'toyota-hilux-ball-joints-tie-rods', 'SPX-TOY-BJTR-001', 'Ball joints and tie rods for Toyota Hilux', 'Ball joints and tie rods replacement', 22000.00, 10, 'in_stock', 'new', 0, 1),
(1, 3, 1, 'Toyota Hilux Diesel Injectors', 'toyota-hilux-diesel-injectors', 'SPX-TOY-DI-001', 'Diesel injectors for 2.4 & 2.8 D-4D engines', 'Diesel injectors replacement', 45000.00, 10, 'in_stock', 'new', 0, 1),
(1, 3, 5, 'Toyota Hilux Clutch Plates', 'toyota-hilux-clutch-plates', 'SPX-TOY-CP-001', 'Clutch plates for manual variants', 'Clutch plates replacement', 28000.00, 10, 'in_stock', 'new', 0, 1),
(1, 3, 3, 'Toyota Hilux Leaf Spring Bushes', 'toyota-hilux-leaf-spring-bushes', 'SPX-TOY-LSB-001', 'Leaf spring bushes for Toyota Hilux', 'Leaf spring bushes replacement', 12000.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota Land Cruiser (Prado, LC200/300, LC70)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 4, 3, 'Toyota Land Cruiser Suspension Bushes', 'toyota-land-cruiser-suspension-bushes', 'SPX-TOY-SB-001', 'Suspension bushes for heavy vehicles', 'Suspension bushes replacement', 30000.00, 10, 'in_stock', 'new', 0, 1),
(1, 4, 3, 'Toyota Land Cruiser Shock Absorbers', 'toyota-land-cruiser-shock-absorbers', 'SPX-TOY-SA-002', 'Shock absorbers for Land Cruiser', 'Shock absorbers replacement', 40000.00, 10, 'in_stock', 'new', 0, 1),
(1, 4, 3, 'Toyota Prado Upper Ball Joints', 'toyota-prado-upper-ball-joints', 'SPX-TOY-UBJ-001', 'Upper ball joints for Prado TX-L', 'Upper ball joints replacement', 25000.00, 10, 'in_stock', 'new', 0, 1),
(1, 4, 1, 'Toyota Land Cruiser Diesel Injectors', 'toyota-land-cruiser-diesel-injectors', 'SPX-TOY-DI-002', 'Diesel fuel injectors for 1KD / 1GD engines', 'Diesel injectors replacement', 50000.00, 10, 'in_stock', 'new', 0, 1),
(1, 4, 4, 'Toyota Land Cruiser Alternator', 'toyota-land-cruiser-alternator', 'SPX-TOY-ALT-001', 'Alternator due to dust & heat', 'Alternator replacement', 35000.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota RAV4
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 5, 3, 'Toyota RAV4 Rear Shock Absorbers', 'toyota-rav4-rear-shock-absorbers', 'SPX-TOY-RSA-001', 'Rear shock absorbers for Toyota RAV4', 'Rear shock absorbers replacement', 32000.00, 10, 'in_stock', 'new', 0, 1),
(1, 5, 3, 'Toyota RAV4 Wheel Bearings', 'toyota-rav4-wheel-bearings', 'SPX-TOY-WB-001', 'Wheel bearings for Toyota RAV4', 'Wheel bearings replacement', 18000.00, 10, 'in_stock', 'new', 0, 1),
(1, 5, 3, 'Toyota RAV4 Stabilizer Link Rods', 'toyota-rav4-stabilizer-link-rods', 'SPX-TOY-SLR-001', 'Stabilizer link rods for Toyota RAV4', 'Stabilizer link rods replacement', 10000.00, 10, 'in_stock', 'new', 0, 1),
(1, 5, 1, 'Toyota RAV4 Throttle Body', 'toyota-rav4-throttle-body', 'SPX-TOY-TB-001', 'Throttle body cleaning needed', 'Throttle body replacement', 25000.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota Fortuner
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 6, 3, 'Toyota Fortuner Front Suspension Bushes', 'toyota-fortuner-front-suspension-bushes', 'SPX-TOY-FSB-001', 'Front suspension bushes for Toyota Fortuner', 'Front suspension bushes replacement', 15000.00, 10, 'in_stock', 'new', 0, 1),
(1, 6, 1, 'Toyota Fortuner Diesel Injectors', 'toyota-fortuner-diesel-injectors', 'SPX-TOY-DI-003', 'Diesel injectors for 2.4 & 2.8 D-4D', 'Diesel injectors replacement', 48000.00, 10, 'in_stock', 'new', 0, 1),
(1, 6, 2, 'Toyota Fortuner Brake Pads', 'toyota-fortuner-brake-pads', 'SPX-TOY-BP-001', 'Brake pads that wear quickly', 'Brake pads replacement', 20000.00, 10, 'in_stock', 'new', 0, 1),
(1, 6, 3, 'Toyota Fortuner Rear Shock Absorbers', 'toyota-fortuner-rear-shock-absorbers', 'SPX-TOY-RSA-002', 'Rear shock absorbers for Toyota Fortuner', 'Rear shock absorbers replacement', 33000.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota Hiace / Coaster
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 7, 5, 'Toyota Hiace Clutch Plates', 'toyota-hiace-clutch-plates', 'SPX-TOY-CP-002', 'Clutch plates for public transport heavy use', 'Clutch plates replacement', 30000.00, 10, 'in_stock', 'new', 0, 1),
(1, 7, 2, 'Toyota Hiace Brake Pads & Rotors', 'toyota-hiace-brake-pads-rotors', 'SPX-TOY-BPR-001', 'Brake pads and rotors for Toyota Hiace', 'Brake pads and rotors replacement', 25000.00, 10, 'in_stock', 'new', 0, 1),
(1, 7, 3, 'Toyota Hiace Shock Absorbers', 'toyota-hiace-shock-absorbers', 'SPX-TOY-SA-003', 'Shock absorbers for Toyota Hiace', 'Shock absorbers replacement', 28000.00, 10, 'in_stock', 'new', 0, 1),
(1, 7, 1, 'Toyota Hiace Diesel Pump & Injectors', 'toyota-hiace-diesel-pump-injectors', 'SPX-TOY-DPI-001', 'Diesel pump and injectors for Toyota Hiace', 'Diesel pump and injectors replacement', 55000.00, 10, 'in_stock', 'new', 0, 1),
(1, 7, 1, 'Toyota Hiace Cooling System', 'toyota-hiace-cooling-system', 'SPX-TOY-CS-001', 'Cooling issues - radiator maintenance required', 'Cooling system replacement', 20000.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota Prius / Aqua / Prius α
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 8, 4, 'Toyota Prius Hybrid Battery', 'toyota-prius-hybrid-battery', 'SPX-TOY-HB-001', 'Hybrid battery after high mileage', 'Hybrid battery replacement', 150000.00, 10, 'in_stock', 'new', 0, 1),
(1, 8, 1, 'Toyota Prius Water Pump', 'toyota-prius-water-pump', 'SPX-TOY-WP-001', 'Water pump (electric)', 'Water pump replacement', 22000.00, 10, 'in_stock', 'new', 0, 1),
(1, 8, 1, 'Toyota Prius Inverter Coolant Pump', 'toyota-prius-inverter-coolant-pump', 'SPX-TOY-ICP-001', 'Inverter coolant pump', 'Inverter coolant pump replacement', 18000.00, 10, 'in_stock', 'new', 0, 1),
(1, 8, 1, 'Toyota Prius Oxygen Sensor', 'toyota-prius-oxygen-sensor', 'SPX-TOY-OXY-002', 'Oxygen sensor', 'Oxygen sensor replacement', 13000.00, 10, 'in_stock', 'new', 0, 1),
(1, 8, 2, 'Toyota Prius ABS Actuator', 'toyota-prius-abs-actuator', 'SPX-TOY-ABA-001', 'ABS actuator (older models)', 'ABS actuator replacement', 35000.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota Camry
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 2, 1, 'Toyota Camry Engine Mounts', 'toyota-camry-engine-mounts', 'SPX-TOY-EM-002', 'Engine mounts for Toyota Camry', 'Engine mounts replacement', 19000.00, 10, 'in_stock', 'new', 0, 1),
(1, 2, 4, 'Toyota Camry AC Compressor', 'toyota-camry-ac-compressor', 'SPX-TOY-ACC-001', 'AC compressor for Toyota Camry', 'AC compressor replacement', 40000.00, 10, 'in_stock', 'new', 0, 1),
(1, 2, 3, 'Toyota Camry Shock Absorbers', 'toyota-camry-shock-absorbers', 'SPX-TOY-SA-004', 'Shock absorbers for Toyota Camry', 'Shock absorbers replacement', 30000.00, 10, 'in_stock', 'new', 0, 1),
(1, 2, 3, 'Toyota Camry Control Arm Bushes', 'toyota-camry-control-arm-bushes', 'SPX-TOY-CAB-001', 'Control arm bushes for Toyota Camry', 'Control arm bushes replacement', 14000.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota Yaris / Vitz / Vios
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 9, 1, 'Toyota Yaris Ignition Coils', 'toyota-yaris-ignition-coils', 'SPX-TOY-IGN-002', 'Ignition coils for Toyota Yaris', 'Ignition coils replacement', 16000.00, 10, 'in_stock', 'new', 0, 1),
(1, 9, 3, 'Toyota Yaris CV Joints', 'toyota-yaris-cv-joints', 'SPX-TOY-CV-002', 'CV joints for Toyota Yaris', 'CV joints replacement', 24000.00, 10, 'in_stock', 'new', 0, 1),
(1, 9, 3, 'Toyota Yaris Rear Shocks', 'toyota-yaris-rear-shocks', 'SPX-TOY-RS-001', 'Rear shocks for Toyota Yaris', 'Rear shocks replacement', 26000.00, 10, 'in_stock', 'new', 0, 1),
(1, 9, 1, 'Toyota Yaris Engine Mounts', 'toyota-yaris-engine-mounts', 'SPX-TOY-EM-003', 'Engine mounts for Toyota Yaris', 'Engine mounts replacement', 17000.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota Highlander / Harrier
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 10, 3, 'Toyota Highlander Strut Mounts', 'toyota-highlander-strut-mounts', 'SPX-TOY-SM-001', 'Strut mounts for Toyota Highlander', 'Strut mounts replacement', 12000.00, 10, 'in_stock', 'new', 0, 1),
(1, 10, 3, 'Toyota Highlander CV Joints', 'toyota-highlander-cv-joints', 'SPX-TOY-CV-003', 'CV joints for Toyota Highlander', 'CV joints replacement', 27000.00, 10, 'in_stock', 'new', 0, 1),
(1, 10, 3, 'Toyota Highlander Steering Rack', 'toyota-highlander-steering-rack', 'SPX-TOY-SR-001', 'Steering rack leaks', 'Steering rack replacement', 45000.00, 10, 'in_stock', 'new', 0, 1),
(1, 10, 4, 'Toyota Highlander AC Compressor', 'toyota-highlander-ac-compressor', 'SPX-TOY-ACC-002', 'AC compressor for Toyota Highlander', 'AC compressor replacement', 42000.00, 10, 'in_stock', 'new', 0, 1);

-- Premio / Allion / Noah / Voxy / Wish
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 20, 3, 'Toyota Premio CV Joints', 'toyota-premio-cv-joints', 'SPX-TOY-CV-004', 'CV joints for Toyota Premio', 'CV joints replacement', 26000.00, 10, 'in_stock', 'new', 0, 1),
(1, 20, 1, 'Toyota Premio Engine Mounts', 'toyota-premio-engine-mounts', 'SPX-TOY-EM-004', 'Engine mounts for Toyota Premio', 'Engine mounts replacement', 18500.00, 10, 'in_stock', 'new', 0, 1),
(1, 23, 4, 'Toyota Noah Hybrid Battery', 'toyota-noah-hybrid-battery', 'SPX-TOY-HB-002', 'Hybrid battery for Noah hybrid', 'Hybrid battery replacement', 140000.00, 10, 'in_stock', 'new', 0, 1),
(1, 22, 3, 'Toyota Wish Suspension Bushings', 'toyota-wish-suspension-bushings', 'SPX-TOY-SB-002', 'Suspension bushings for Toyota Wish', 'Suspension bushings replacement', 13000.00, 10, 'in_stock', 'new', 0, 1),
(1, 21, 1, 'Toyota Allion Oxygen Sensors', 'toyota-allion-oxygen-sensors', 'SPX-TOY-OXY-003', 'Oxygen sensors for Toyota Allion', 'Oxygen sensors replacement', 13500.00, 10, 'in_stock', 'new', 0, 1);

-- Toyota Passo / Ist / Aqua (small cars)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(1, 27, 1, 'Toyota Passo Ignition Coils', 'toyota-passo-ignition-coils', 'SPX-TOY-IGN-003', 'Ignition coils for Toyota Passo', 'Ignition coils replacement', 15500.00, 10, 'in_stock', 'new', 0, 1),
(1, 27, 3, 'Toyota Passo CV Joints', 'toyota-passo-cv-joints', 'SPX-TOY-CV-005', 'CV joints for Toyota Passo', 'CV joints replacement', 23500.00, 10, 'in_stock', 'new', 0, 1),
(1, 27, 3, 'Toyota Passo Rear Shocks', 'toyota-passo-rear-shocks', 'SPX-TOY-RS-002', 'Rear shocks for Toyota Passo', 'Rear shocks replacement', 25500.00, 10, 'in_stock', 'new', 0, 1),
(1, 27, 3, 'Toyota Passo Wheel Bearings', 'toyota-passo-wheel-bearings', 'SPX-TOY-WB-002', 'Wheel bearings for Toyota Passo', 'Wheel bearings replacement', 17500.00, 10, 'in_stock', 'new', 0, 1);

-- HONDA PARTS

-- Honda CR-V
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(2, 11, 2, 'Honda CR-V Brake Pads Front', 'honda-cr-v-brake-pads-front', 'SPX-HON-BP-001', 'High-performance brake pads for Honda CR-V', 'Brake pads replacement', 28000.00, 15, 'in_stock', 'new', 1, 1),
(2, 11, 3, 'Honda CR-V Shock Absorbers', 'honda-cr-v-shock-absorbers', 'SPX-HON-SA-001', 'Shock absorbers for Honda CR-V', 'Shock absorbers replacement', 35000.00, 12, 'in_stock', 'new', 0, 1),
(2, 11, 1, 'Honda CR-V Engine Mounts', 'honda-cr-v-engine-mounts', 'SPX-HON-EM-001', 'Engine mounts for Honda CR-V', 'Engine mounts replacement', 20000.00, 10, 'in_stock', 'new', 0, 1),
(2, 11, 4, 'Honda CR-V AC Compressor', 'honda-cr-v-ac-compressor', 'SPX-HON-ACC-001', 'AC compressor for Honda CR-V', 'AC compressor replacement', 38000.00, 8, 'in_stock', 'new', 0, 1),
(2, 11, 7, 'Honda CR-V Air Filter', 'honda-cr-v-air-filter', 'SPX-HON-AF-001', 'Air filter for Honda CR-V', 'Air filter replacement', 8500.00, 20, 'in_stock', 'new', 0, 1);

-- Honda Civic
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(2, 18, 2, 'Honda Civic Brake Discs', 'honda-civic-brake-discs', 'SPX-HON-BD-001', 'Brake discs for Honda Civic', 'Brake discs replacement', 25000.00, 15, 'in_stock', 'new', 0, 1),
(2, 18, 1, 'Honda Civic Spark Plugs', 'honda-civic-spark-plugs', 'SPX-HON-SP-001', 'Spark plugs for Honda Civic', 'Spark plugs replacement', 14000.00, 25, 'in_stock', 'new', 0, 1),
(2, 18, 3, 'Honda Civic CV Joints', 'honda-civic-cv-joints', 'SPX-HON-CV-001', 'CV joints for Honda Civic', 'CV joints replacement', 22000.00, 12, 'in_stock', 'new', 0, 1),
(2, 18, 7, 'Honda Civic Oil Filter', 'honda-civic-oil-filter', 'SPX-HON-OF-001', 'Oil filter for Honda Civic', 'Oil filter replacement', 6500.00, 30, 'in_stock', 'new', 0, 1);

-- NISSAN PARTS

-- Nissan X-Trail
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(3, 19, 2, 'Nissan X-Trail Brake Pads', 'nissan-x-trail-brake-pads', 'SPX-NIS-BP-001', 'Brake pads for Nissan X-Trail', 'Brake pads replacement', 26000.00, 15, 'in_stock', 'new', 1, 1),
(3, 19, 3, 'Nissan X-Trail Shock Absorbers', 'nissan-x-trail-shock-absorbers', 'SPX-NIS-SA-001', 'Shock absorbers for Nissan X-Trail', 'Shock absorbers replacement', 32000.00, 10, 'in_stock', 'new', 0, 1),
(3, 19, 1, 'Nissan X-Trail Diesel Injectors', 'nissan-x-trail-diesel-injectors', 'SPX-NIS-DI-001', 'Diesel injectors for X-Trail', 'Diesel injectors replacement', 45000.00, 8, 'in_stock', 'new', 0, 1),
(3, 19, 4, 'Nissan X-Trail Alternator', 'nissan-x-trail-alternator', 'SPX-NIS-ALT-001', 'Alternator for Nissan X-Trail', 'Alternator replacement', 35000.00, 12, 'in_stock', 'new', 0, 1);

-- Nissan Navara
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(3, 21, 2, 'Nissan Navara Brake Pads', 'nissan-navara-brake-pads', 'SPX-NIS-BP-002', 'Heavy-duty brake pads for Nissan Navara', 'Brake pads replacement', 29000.00, 12, 'in_stock', 'new', 0, 1),
(3, 21, 3, 'Nissan Navara Leaf Springs', 'nissan-navara-leaf-springs', 'SPX-NIS-LS-001', 'Leaf springs for Nissan Navara', 'Leaf springs replacement', 40000.00, 8, 'in_stock', 'new', 0, 1),
(3, 21, 1, 'Nissan Navara Diesel Pump', 'nissan-navara-diesel-pump', 'SPX-NIS-DP-001', 'Diesel pump for Navara', 'Diesel pump replacement', 55000.00, 6, 'in_stock', 'new', 0, 1),
(3, 21, 5, 'Nissan Navara Clutch Kit', 'nissan-navara-clutch-kit', 'SPX-NIS-CK-001', 'Clutch kit for manual Navara', 'Clutch kit replacement', 35000.00, 10, 'in_stock', 'new', 0, 1);

-- MITSUBISHI PARTS

-- Mitsubishi Pajero (Full-size)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 50, 3, 'Mitsubishi Pajero Front Upper Control Arm Bushes', 'mitsubishi-pajero-front-upper-control-arm-bushes', 'SPX-MIT-PAJ-FUCB-001', 'Front upper control arm bushes for Mitsubishi Pajero heavy vehicles', 'Front upper control arm bushes replacement', 15000.00, 10, 'in_stock', 'new', 0, 1),
(9, 50, 3, 'Mitsubishi Pajero Front Lower Control Arm Bushes', 'mitsubishi-pajero-front-lower-control-arm-bushes', 'SPX-MIT-PAJ-FLCB-001', 'Front lower control arm bushes for Mitsubishi Pajero', 'Front lower control arm bushes replacement', 14000.00, 10, 'in_stock', 'new', 0, 1),
(9, 50, 3, 'Mitsubishi Pajero Pitman Arm', 'mitsubishi-pajero-pitman-arm', 'SPX-MIT-PAJ-PA-001', 'Pitman arm for Mitsubishi Pajero steering', 'Pitman arm replacement', 22000.00, 8, 'in_stock', 'new', 0, 1),
(9, 50, 3, 'Mitsubishi Pajero Idler Arm', 'mitsubishi-pajero-idler-arm', 'SPX-MIT-PAJ-IA-001', 'Idler arm for Mitsubishi Pajero steering play issues', 'Idler arm replacement', 18000.00, 8, 'in_stock', 'new', 0, 1),
(9, 50, 3, 'Mitsubishi Pajero Rear Suspension Trailing Arm Bushes', 'mitsubishi-pajero-rear-suspension-trailing-arm-bushes', 'SPX-MIT-PAJ-RSTAB-001', 'Rear suspension trailing arm bushes for Mitsubishi Pajero', 'Rear suspension trailing arm bushes replacement', 12000.00, 12, 'in_stock', 'new', 0, 1),
(9, 50, 1, 'Mitsubishi Pajero Diesel Pump Seals', 'mitsubishi-pajero-diesel-pump-seals', 'SPX-MIT-PAJ-DPS-001', 'Diesel pump seals for Mitsubishi Pajero 3.2 Di-D', 'Diesel pump seals replacement', 8500.00, 15, 'in_stock', 'new', 0, 1),
(9, 50, 1, 'Mitsubishi Pajero Radiator', 'mitsubishi-pajero-radiator', 'SPX-MIT-PAJ-RAD-001', 'Radiator for Mitsubishi Pajero - cracks in hot regions', 'Radiator replacement', 35000.00, 6, 'in_stock', 'new', 0, 1),
(9, 50, 1, 'Mitsubishi Pajero Turbo Actuator', 'mitsubishi-pajero-turbo-actuator', 'SPX-MIT-PAJ-TA-001', 'Turbo actuator for Mitsubishi Pajero sticking issues', 'Turbo actuator replacement', 28000.00, 8, 'in_stock', 'new', 0, 1),
(9, 50, 2, 'Mitsubishi Pajero Brake Booster', 'mitsubishi-pajero-brake-booster', 'SPX-MIT-PAJ-BB-001', 'Brake booster for Mitsubishi Pajero V70/V80', 'Brake booster replacement', 25000.00, 7, 'in_stock', 'new', 0, 1),
(9, 50, 3, 'Mitsubishi Pajero Wheel Bearings', 'mitsubishi-pajero-wheel-bearings', 'SPX-MIT-PAJ-WB-001', 'Wheel bearings for Mitsubishi Pajero', 'Wheel bearings replacement', 16000.00, 12, 'in_stock', 'new', 0, 1),
(9, 50, 4, 'Mitsubishi Pajero AC Compressor Clutch', 'mitsubishi-pajero-ac-compressor-clutch', 'SPX-MIT-PAJ-ACC-001', 'AC compressor clutch for Mitsubishi Pajero', 'AC compressor clutch replacement', 22000.00, 9, 'in_stock', 'new', 0, 1);

-- Mitsubishi Pajero Sport (Montero Sport / Challenger)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 51, 1, 'Mitsubishi Pajero Sport Timing Chain Tensioner', 'mitsubishi-pajero-sport-timing-chain-tensioner', 'SPX-MIT-PJS-TCT-001', 'Timing chain tensioner for Mitsubishi Pajero Sport 2.5 Di-D', 'Timing chain tensioner replacement', 18000.00, 10, 'in_stock', 'new', 0, 1),
(9, 51, 1, 'Mitsubishi Pajero Sport Turbo Seals', 'mitsubishi-pajero-sport-turbo-seals', 'SPX-MIT-PJS-TS-001', 'Turbo seals for Mitsubishi Pajero Sport', 'Turbo seals replacement', 12000.00, 15, 'in_stock', 'new', 0, 1),
(9, 51, 3, 'Mitsubishi Pajero Sport Leaf Spring Bushes', 'mitsubishi-pajero-sport-leaf-spring-bushes', 'SPX-MIT-PJS-LSB-001', 'Leaf spring bushes for Mitsubishi Pajero Sport rear', 'Leaf spring bushes replacement', 9500.00, 18, 'in_stock', 'new', 0, 1),
(9, 51, 3, 'Mitsubishi Pajero Sport Front Shocks', 'mitsubishi-pajero-sport-front-shocks', 'SPX-MIT-PJS-FS-001', 'Front shock absorbers for Mitsubishi Pajero Sport', 'Front shocks replacement', 28000.00, 12, 'in_stock', 'new', 0, 1),
(9, 51, 1, 'Mitsubishi Pajero Sport Idle Valve', 'mitsubishi-pajero-sport-idle-valve', 'SPX-MIT-PJS-IV-001', 'Idle valve for Mitsubishi Pajero Sport', 'Idle valve replacement', 8500.00, 20, 'in_stock', 'new', 0, 1);

-- Mitsubishi Pajero iO / Pinin
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 225, 1, 'Mitsubishi Pajero iO GDI Fuel Pump', 'mitsubishi-pajero-io-gdi-fuel-pump', 'SPX-MIT-PIO-GFP-001', 'GDI fuel pump for Mitsubishi Pajero iO 1.8/2.0 GDI', 'GDI fuel pump replacement', 32000.00, 8, 'in_stock', 'new', 0, 1),
(9, 225, 1, 'Mitsubishi Pajero iO Idle Control Valve', 'mitsubishi-pajero-io-idle-control-valve', 'SPX-MIT-PIO-ICV-001', 'Idle control valve for Mitsubishi Pajero iO', 'Idle control valve replacement', 6500.00, 15, 'in_stock', 'new', 0, 1),
(9, 225, 1, 'Mitsubishi Pajero iO Ignition Coils', 'mitsubishi-pajero-io-ignition-coils', 'SPX-MIT-PIO-IC-001', 'Ignition coils for Mitsubishi Pajero iO', 'Ignition coils replacement', 14000.00, 12, 'in_stock', 'new', 0, 1),
(9, 225, 3, 'Mitsubishi Pajero iO CV Joints', 'mitsubishi-pajero-io-cv-joints', 'SPX-MIT-PIO-CVJ-001', 'CV joints for Mitsubishi Pajero iO', 'CV joints replacement', 22000.00, 10, 'in_stock', 'new', 0, 1),
(9, 225, 3, 'Mitsubishi Pajero iO Suspension Top Mounts', 'mitsubishi-pajero-io-suspension-top-mounts', 'SPX-MIT-PIO-STM-001', 'Suspension top mounts for Mitsubishi Pajero iO', 'Suspension top mounts replacement', 9500.00, 16, 'in_stock', 'new', 0, 1),
(9, 225, 1, 'Mitsubishi Pajero iO Timing Belt', 'mitsubishi-pajero-io-timing-belt', 'SPX-MIT-PIO-TB-001', 'Timing belt for Mitsubishi Pajero iO - frequent replacement needed', 'Timing belt replacement', 18000.00, 14, 'in_stock', 'new', 0, 1);

-- Mitsubishi Outlander
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 52, 5, 'Mitsubishi Outlander CVT Transmission', 'mitsubishi-outlander-cvt-transmission', 'SPX-MIT-OUT-CVT-001', 'CVT transmission for Mitsubishi Outlander', 'CVT transmission replacement', 450000.00, 2, 'in_stock', 'new', 0, 1),
(9, 52, 3, 'Mitsubishi Outlander Rear Diff Bushing', 'mitsubishi-outlander-rear-diff-bushing', 'SPX-MIT-OUT-RDB-001', 'Rear differential bushing for Mitsubishi Outlander', 'Rear diff bushing replacement', 8500.00, 18, 'in_stock', 'new', 0, 1),
(9, 52, 1, 'Mitsubishi Outlander Engine Mounts', 'mitsubishi-outlander-engine-mounts', 'SPX-MIT-OUT-EM-001', 'Engine mounts for Mitsubishi Outlander', 'Engine mounts replacement', 16000.00, 12, 'in_stock', 'new', 0, 1),
(9, 52, 3, 'Mitsubishi Outlander Wheel Bearings', 'mitsubishi-outlander-wheel-bearings', 'SPX-MIT-OUT-WB-001', 'Wheel bearings for Mitsubishi Outlander', 'Wheel bearings replacement', 14500.00, 15, 'in_stock', 'new', 0, 1),
(9, 52, 1, 'Mitsubishi Outlander Electric Motor Cooling Pump', 'mitsubishi-outlander-electric-motor-cooling-pump', 'SPX-MIT-OUT-EMCP-001', 'Electric motor cooling pump for Mitsubishi Outlander PHEV', 'Electric motor cooling pump replacement', 38000.00, 6, 'in_stock', 'new', 0, 1),
(9, 52, 3, 'Mitsubishi Outlander Front Shock Absorber Mounts', 'mitsubishi-outlander-front-shock-absorber-mounts', 'SPX-MIT-OUT-FSAM-001', 'Front shock absorber mounts for Mitsubishi Outlander', 'Front shock absorber mounts replacement', 7500.00, 20, 'in_stock', 'new', 0, 1);

-- Mitsubishi RVR / ASX / Outlander Sport
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 53, 3, 'Mitsubishi RVR Front Shock Absorbers', 'mitsubishi-rvr-front-shock-absorbers', 'SPX-MIT-RVR-FSA-001', 'Front shock absorbers for Mitsubishi RVR/ASX', 'Front shock absorbers replacement', 26000.00, 14, 'in_stock', 'new', 0, 1),
(9, 53, 3, 'Mitsubishi RVR Ball Joints', 'mitsubishi-rvr-ball-joints', 'SPX-MIT-RVR-BJ-001', 'Ball joints for Mitsubishi RVR/ASX', 'Ball joints replacement', 14000.00, 16, 'in_stock', 'new', 0, 1),
(9, 53, 1, 'Mitsubishi RVR Throttle Body', 'mitsubishi-rvr-throttle-body', 'SPX-MIT-RVR-TB-001', 'Throttle body for Mitsubishi RVR/ASX', 'Throttle body replacement', 22000.00, 10, 'in_stock', 'new', 0, 1),
(9, 53, 3, 'Mitsubishi RVR CV Joints', 'mitsubishi-rvr-cv-joints', 'SPX-MIT-RVR-CVJ-001', 'CV joints for Mitsubishi RVR/ASX', 'CV joints replacement', 21000.00, 12, 'in_stock', 'new', 0, 1),
(9, 53, 2, 'Mitsubishi RVR Brake Pads', 'mitsubishi-rvr-brake-pads', 'SPX-MIT-RVR-BP-001', 'Brake pads for Mitsubishi RVR/ASX', 'Brake pads replacement', 18000.00, 18, 'in_stock', 'new', 0, 1),
(9, 53, 1, 'Mitsubishi RVR Engine Mounts', 'mitsubishi-rvr-engine-mounts', 'SPX-MIT-RVR-EM-001', 'Engine mounts for Mitsubishi RVR/ASX', 'Engine mounts replacement', 15000.00, 15, 'in_stock', 'new', 0, 1);

-- Mitsubishi Lancer / Lancer EX
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 54, 5, 'Mitsubishi Lancer CVT Transmission', 'mitsubishi-lancer-cvt-transmission', 'SPX-MIT-LAN-CVT-001', 'CVT transmission for Mitsubishi Lancer EX models', 'CVT transmission replacement', 380000.00, 3, 'in_stock', 'new', 0, 1),
(9, 54, 1, 'Mitsubishi Lancer Ignition Coils', 'mitsubishi-lancer-ignition-coils', 'SPX-MIT-LAN-IC-001', 'Ignition coils for Mitsubishi Lancer', 'Ignition coils replacement', 13500.00, 20, 'in_stock', 'new', 0, 1),
(9, 54, 3, 'Mitsubishi Lancer Control Arm Bushings', 'mitsubishi-lancer-control-arm-bushings', 'SPX-MIT-LAN-CAB-001', 'Control arm bushings for Mitsubishi Lancer', 'Control arm bushings replacement', 11000.00, 22, 'in_stock', 'new', 0, 1),
(9, 54, 1, 'Mitsubishi Lancer Timing Belt', 'mitsubishi-lancer-timing-belt', 'SPX-MIT-LAN-TB-001', 'Timing belt or chain for Mitsubishi Lancer', 'Timing belt/chain replacement', 25000.00, 16, 'in_stock', 'new', 0, 1),
(9, 54, 2, 'Mitsubishi Lancer Brake Caliper Pins', 'mitsubishi-lancer-brake-caliper-pins', 'SPX-MIT-LAN-BCP-001', 'Brake caliper pins for Mitsubishi Lancer - rust issues', 'Brake caliper pins replacement', 4500.00, 30, 'in_stock', 'new', 0, 1),
(9, 54, 1, 'Mitsubishi Lancer Radiator', 'mitsubishi-lancer-radiator', 'SPX-MIT-LAN-RAD-001', 'Radiator for Mitsubishi Lancer - leaks common', 'Radiator replacement', 28000.00, 12, 'in_stock', 'new', 0, 1);

-- Mitsubishi Canter (Fuso)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 55, 5, 'Mitsubishi Canter Clutch Plate', 'mitsubishi-canter-clutch-plate', 'SPX-MIT-CAN-CP-001', 'Clutch plate for Mitsubishi Canter heavy use', 'Clutch plate replacement', 35000.00, 8, 'in_stock', 'new', 0, 1),
(9, 55, 5, 'Mitsubishi Canter Clutch Pressure Plate', 'mitsubishi-canter-clutch-pressure-plate', 'SPX-MIT-CAN-CPP-001', 'Clutch pressure plate for Mitsubishi Canter', 'Clutch pressure plate replacement', 28000.00, 10, 'in_stock', 'new', 0, 1),
(9, 55, 2, 'Mitsubishi Canter Wheel Cylinder', 'mitsubishi-canter-wheel-cylinder', 'SPX-MIT-CAN-WC-001', 'Wheel cylinder for Mitsubishi Canter - leaks common', 'Wheel cylinder replacement', 6500.00, 25, 'in_stock', 'new', 0, 1),
(9, 55, 3, 'Mitsubishi Canter Rear Leaf Spring Bushes', 'mitsubishi-canter-rear-leaf-spring-bushes', 'SPX-MIT-CAN-RLSB-001', 'Rear leaf spring bushes for Mitsubishi Canter', 'Rear leaf spring bushes replacement', 8500.00, 20, 'in_stock', 'new', 0, 1),
(9, 55, 2, 'Mitsubishi Canter Brake Drums', 'mitsubishi-canter-brake-drums', 'SPX-MIT-CAN-BD-001', 'Brake drums for Mitsubishi Canter', 'Brake drums replacement', 22000.00, 12, 'in_stock', 'new', 0, 1),
(9, 55, 1, 'Mitsubishi Canter Fuel Pump', 'mitsubishi-canter-fuel-pump', 'SPX-MIT-CAN-FP-001', 'Fuel pump for Mitsubishi Canter', 'Fuel pump replacement', 18000.00, 15, 'in_stock', 'new', 0, 1),
(9, 55, 1, 'Mitsubishi Canter Radiator', 'mitsubishi-canter-radiator', 'SPX-MIT-CAN-RAD-001', 'Radiator for Mitsubishi Canter - cracking issues', 'Radiator replacement', 32000.00, 10, 'in_stock', 'new', 0, 1);

-- Mitsubishi L200 (Triton)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 56, 1, 'Mitsubishi L200 Turbo', 'mitsubishi-l200-turbo', 'SPX-MIT-L200-TUR-001', 'Turbo for Mitsubishi L200 2.5 Di-D - failure common', 'Turbo replacement', 65000.00, 5, 'in_stock', 'new', 0, 1),
(9, 56, 1, 'Mitsubishi L200 Injector Seals', 'mitsubishi-l200-injector-seals', 'SPX-MIT-L200-IS-001', 'Injector seals for Mitsubishi L200', 'Injector seals replacement', 4500.00, 35, 'in_stock', 'new', 0, 1),
(9, 56, 3, 'Mitsubishi L200 Upper Ball Joints', 'mitsubishi-l200-upper-ball-joints', 'SPX-MIT-L200-UBJ-001', 'Upper ball joints for Mitsubishi L200', 'Upper ball joints replacement', 12000.00, 18, 'in_stock', 'new', 0, 1),
(9, 56, 3, 'Mitsubishi L200 Rear Leaf Springs', 'mitsubishi-l200-rear-leaf-springs', 'SPX-MIT-L200-RLS-001', 'Rear leaf springs for Mitsubishi L200', 'Rear leaf springs replacement', 45000.00, 6, 'in_stock', 'new', 0, 1),
(9, 56, 1, 'Mitsubishi L200 Fuel Pump', 'mitsubishi-l200-fuel-pump', 'SPX-MIT-L200-FP-001', 'Fuel pump for Mitsubishi L200', 'Fuel pump replacement', 16000.00, 14, 'in_stock', 'new', 0, 1),
(9, 56, 1, 'Mitsubishi L200 Engine Mounts', 'mitsubishi-l200-engine-mounts', 'SPX-MIT-L200-EM-001', 'Engine mounts for Mitsubishi L200', 'Engine mounts replacement', 14000.00, 16, 'in_stock', 'new', 0, 1);

-- Mitsubishi Mirage / Attrage
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 57, 1, 'Mitsubishi Mirage Engine Mounts', 'mitsubishi-mirage-engine-mounts', 'SPX-MIT-MIR-EM-001', 'Engine mounts for Mitsubishi Mirage/Attrage', 'Engine mounts replacement', 12000.00, 20, 'in_stock', 'new', 0, 1),
(9, 57, 3, 'Mitsubishi Mirage CV Joints', 'mitsubishi-mirage-cv-joints', 'SPX-MIT-MIR-CVJ-001', 'CV joints for Mitsubishi Mirage/Attrage', 'CV joints replacement', 19000.00, 15, 'in_stock', 'new', 0, 1),
(9, 57, 1, 'Mitsubishi Mirage Coil Packs', 'mitsubishi-mirage-coil-packs', 'SPX-MIT-MIR-CP-001', 'Coil packs for Mitsubishi Mirage/Attrage', 'Coil packs replacement', 9500.00, 22, 'in_stock', 'new', 0, 1),
(9, 57, 4, 'Mitsubishi Mirage AC Compressor', 'mitsubishi-mirage-ac-compressor', 'SPX-MIT-MIR-ACC-001', 'AC compressor for Mitsubishi Mirage/Attrage', 'AC compressor replacement', 28000.00, 10, 'in_stock', 'new', 0, 1),
(9, 57, 1, 'Mitsubishi Mirage Throttle Body', 'mitsubishi-mirage-throttle-body', 'SPX-MIT-MIR-TB-001', 'Throttle body for Mitsubishi Mirage/Attrage', 'Throttle body replacement', 18000.00, 12, 'in_stock', 'new', 0, 1),
(9, 57, 2, 'Mitsubishi Mirage Brake Pads', 'mitsubishi-mirage-brake-pads', 'SPX-MIT-MIR-BP-001', 'Brake pads for Mitsubishi Mirage/Attrage', 'Brake pads replacement', 15000.00, 18, 'in_stock', 'new', 0, 1);

-- Mitsubishi Airtrek
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 58, 1, 'Mitsubishi Airtrek GDI Fuel System', 'mitsubishi-airtrek-gdi-fuel-system', 'SPX-MIT-AIR-GFS-001', 'GDI fuel system components for Mitsubishi Airtrek', 'GDI fuel system replacement', 35000.00, 8, 'in_stock', 'new', 0, 1),
(9, 58, 1, 'Mitsubishi Airtrek Oxygen Sensors', 'mitsubishi-airtrek-oxygen-sensors', 'SPX-MIT-AIR-OS-001', 'Oxygen sensors for Mitsubishi Airtrek', 'Oxygen sensors replacement', 12000.00, 16, 'in_stock', 'new', 0, 1),
(9, 58, 3, 'Mitsubishi Airtrek CV Joints', 'mitsubishi-airtrek-cv-joints', 'SPX-MIT-AIR-CVJ-001', 'CV joints for Mitsubishi Airtrek', 'CV joints replacement', 21000.00, 12, 'in_stock', 'new', 0, 1),
(9, 58, 3, 'Mitsubishi Airtrek Shock Absorbers', 'mitsubishi-airtrek-shock-absorbers', 'SPX-MIT-AIR-SA-001', 'Shock absorbers for Mitsubishi Airtrek', 'Shock absorbers replacement', 27000.00, 10, 'in_stock', 'new', 0, 1),
(9, 58, 3, 'Mitsubishi Airtrek Front Suspension Arms', 'mitsubishi-airtrek-front-suspension-arms', 'SPX-MIT-AIR-FSA-001', 'Front suspension arms for Mitsubishi Airtrek', 'Front suspension arms replacement', 32000.00, 8, 'in_stock', 'new', 0, 1);

-- Mitsubishi Delica / Delica D:5
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 59, 3, 'Mitsubishi Delica Rear Suspension Arm Bushings', 'mitsubishi-delica-rear-suspension-arm-bushings', 'SPX-MIT-DEL-RSAB-001', 'Rear suspension arm bushings for Mitsubishi Delica', 'Rear suspension arm bushings replacement', 11000.00, 18, 'in_stock', 'new', 0, 1),
(9, 59, 3, 'Mitsubishi Delica Driveshaft', 'mitsubishi-delica-driveshaft', 'SPX-MIT-DEL-DS-001', 'Driveshaft for Mitsubishi Delica', 'Driveshaft replacement', 45000.00, 6, 'in_stock', 'new', 0, 1),
(9, 59, 4, 'Mitsubishi Delica Power Steering Pump', 'mitsubishi-delica-power-steering-pump', 'SPX-MIT-DEL-PSP-001', 'Power steering pump for Mitsubishi Delica', 'Power steering pump replacement', 25000.00, 10, 'in_stock', 'new', 0, 1),
(9, 59, 1, 'Mitsubishi Delica Diesel Injector', 'mitsubishi-delica-diesel-injector', 'SPX-MIT-DEL-DI-001', 'Diesel injector for Mitsubishi Delica - clogging issues', 'Diesel injector replacement', 28000.00, 12, 'in_stock', 'new', 0, 1),
(9, 59, 1, 'Mitsubishi Delica Radiator', 'mitsubishi-delica-radiator', 'SPX-MIT-DEL-RAD-001', 'Radiator for Mitsubishi Delica - leaks common', 'Radiator replacement', 30000.00, 9, 'in_stock', 'new', 0, 1),
(9, 59, 4, 'Mitsubishi Delica Alternator', 'mitsubishi-delica-alternator', 'SPX-MIT-DEL-ALT-001', 'Alternator for Mitsubishi Delica', 'Alternator replacement', 22000.00, 14, 'in_stock', 'new', 0, 1);

-- Mitsubishi Colt / Colt Plus
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 60, 1, 'Mitsubishi Colt Ignition Coils', 'mitsubishi-colt-ignition-coils', 'SPX-MIT-COL-IC-001', 'Ignition coils for Mitsubishi Colt/Colt Plus', 'Ignition coils replacement', 12500.00, 20, 'in_stock', 'new', 0, 1),
(9, 60, 3, 'Mitsubishi Colt CV Joints', 'mitsubishi-colt-cv-joints', 'SPX-MIT-COL-CVJ-001', 'CV joints for Mitsubishi Colt/Colt Plus', 'CV joints replacement', 18500.00, 16, 'in_stock', 'new', 0, 1),
(9, 60, 3, 'Mitsubishi Colt Shock Absorbers', 'mitsubishi-colt-shock-absorbers', 'SPX-MIT-COL-SA-001', 'Shock absorbers for Mitsubishi Colt/Colt Plus', 'Shock absorbers replacement', 24000.00, 14, 'in_stock', 'new', 0, 1),
(9, 60, 4, 'Mitsubishi Colt Electric Window Motor', 'mitsubishi-colt-electric-window-motor', 'SPX-MIT-COL-EWM-001', 'Electric window motor for Mitsubishi Colt/Colt Plus', 'Electric window motor replacement', 8500.00, 18, 'in_stock', 'new', 0, 1),
(9, 60, 1, 'Mitsubishi Colt Timing Belt Tensioner', 'mitsubishi-colt-timing-belt-tensioner', 'SPX-MIT-COL-TBT-001', 'Timing belt tensioner for Mitsubishi Colt/Colt Plus', 'Timing belt tensioner replacement', 9500.00, 22, 'in_stock', 'new', 0, 1);

-- Mitsubishi Space Wagon / Space Star / Grandis
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 61, 1, 'Mitsubishi Space Wagon Engine Mounts', 'mitsubishi-space-wagon-engine-mounts', 'SPX-MIT-SW-EM-001', 'Engine mounts for Mitsubishi Space Wagon/Grandis', 'Engine mounts replacement', 13500.00, 16, 'in_stock', 'new', 0, 1),
(9, 61, 3, 'Mitsubishi Space Wagon Steering Rack', 'mitsubishi-space-wagon-steering-rack', 'SPX-MIT-SW-SR-001', 'Steering rack for Mitsubishi Space Wagon/Grandis', 'Steering rack replacement', 38000.00, 8, 'in_stock', 'new', 0, 1),
(9, 61, 3, 'Mitsubishi Space Wagon Rear Shocks', 'mitsubishi-space-wagon-rear-shocks', 'SPX-MIT-SW-RS-001', 'Rear shock absorbers for Mitsubishi Space Wagon/Grandis', 'Rear shocks replacement', 26000.00, 12, 'in_stock', 'new', 0, 1),
(9, 61, 1, 'Mitsubishi Space Wagon Timing Belt', 'mitsubishi-space-wagon-timing-belt', 'SPX-MIT-SW-TB-001', 'Timing belt for Mitsubishi Space Wagon/Grandis', 'Timing belt replacement', 22000.00, 14, 'in_stock', 'new', 0, 1),
(9, 61, 4, 'Mitsubishi Space Wagon Heater Core', 'mitsubishi-space-wagon-heater-core', 'SPX-MIT-SW-HC-001', 'Heater core for Mitsubishi Space Wagon/Grandis - leaks common', 'Heater core replacement', 18000.00, 10, 'in_stock', 'new', 0, 1);

-- Mitsubishi Rosa (Minibus)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 226, 5, 'Mitsubishi Rosa Clutch System', 'mitsubishi-rosa-clutch-system', 'SPX-MIT-ROS-CS-001', 'Complete clutch system for Mitsubishi Rosa minibus', 'Clutch system replacement', 45000.00, 6, 'in_stock', 'new', 0, 1),
(9, 226, 2, 'Mitsubishi Rosa Brake Master Cylinder', 'mitsubishi-rosa-brake-master-cylinder', 'SPX-MIT-ROS-BMC-001', 'Brake master cylinder for Mitsubishi Rosa', 'Brake master cylinder replacement', 12000.00, 15, 'in_stock', 'new', 0, 1),
(9, 226, 3, 'Mitsubishi Rosa Wheel Bearings', 'mitsubishi-rosa-wheel-bearings', 'SPX-MIT-ROS-WB-001', 'Wheel bearings for Mitsubishi Rosa', 'Wheel bearings replacement', 14000.00, 18, 'in_stock', 'new', 0, 1),
(9, 226, 1, 'Mitsubishi Rosa Injector Pump', 'mitsubishi-rosa-injector-pump', 'SPX-MIT-ROS-IP-001', 'Injector pump for Mitsubishi Rosa diesel', 'Injector pump replacement', 35000.00, 8, 'in_stock', 'new', 0, 1),
(9, 226, 1, 'Mitsubishi Rosa Radiator', 'mitsubishi-rosa-radiator', 'SPX-MIT-ROS-RAD-001', 'Radiator for Mitsubishi Rosa', 'Radiator replacement', 28000.00, 12, 'in_stock', 'new', 0, 1),
(9, 226, 3, 'Mitsubishi Rosa Suspension Leaf Springs', 'mitsubishi-rosa-suspension-leaf-springs', 'SPX-MIT-ROS-SLS-001', 'Suspension leaf springs for Mitsubishi Rosa', 'Suspension leaf springs replacement', 42000.00, 7, 'in_stock', 'new', 0, 1);

-- Mitsubishi Pajero (original entry - keeping for compatibility)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 50, 2, 'Mitsubishi Pajero Brake Pads', 'mitsubishi-pajero-brake-pads', 'SPX-MIT-BP-001', 'Off-road brake pads for Pajero', 'Brake pads replacement', 29000.00, 12, 'in_stock', 'new', 1, 1),
(9, 50, 3, 'Mitsubishi Pajero Shock Absorbers', 'mitsubishi-pajero-shock-absorbers', 'SPX-MIT-SA-001', 'Heavy-duty shock absorbers for Pajero', 'Shock absorbers replacement', 38000.00, 10, 'in_stock', 'new', 0, 1),
(9, 50, 1, 'Mitsubishi Pajero Diesel Injectors', 'mitsubishi-pajero-diesel-injectors', 'SPX-MIT-DI-001', 'Diesel injectors for 4M41 engine', 'Diesel injectors replacement', 48000.00, 8, 'in_stock', 'new', 0, 1),
(9, 50, 4, 'Mitsubishi Pajero Starter Motor', 'mitsubishi-pajero-starter-motor', 'SPX-MIT-SM-001', 'Starter motor for Pajero', 'Starter motor replacement', 25000.00, 15, 'in_stock', 'new', 0, 1);

-- Mitsubishi L200 (updated with proper brand_id=9 and model_id=56)
INSERT INTO products_enhanced (brand_id, model_id, category_id, product_name, slug, sku, description, short_description, price, stock_quantity, stock_status, `condition`, is_featured, is_active) VALUES
(9, 56, 2, 'Mitsubishi L200 Brake Pads', 'mitsubishi-l200-brake-pads', 'SPX-MIT-BP-002', 'Brake pads for Mitsubishi L200', 'Brake pads replacement', 24000.00, 18, 'in_stock', 'new', 0, 1),
(9, 56, 8, 'Mitsubishi L200 Drive Belt', 'mitsubishi-l200-drive-belt', 'SPX-MIT-DB-001', 'Drive belt for L200 Triton', 'Drive belt replacement', 16000.00, 20, 'in_stock', 'new', 0, 1),
(9, 56, 3, 'Mitsubishi L200 Ball Joints', 'mitsubishi-l200-ball-joints', 'SPX-MIT-BJ-001', 'Ball joints for L200', 'Ball joints replacement', 18000.00, 15, 'in_stock', 'new', 0, 1),
(9, 56, 7, 'Mitsubishi L200 Fuel Filter', 'mitsubishi-l200-fuel-filter', 'SPX-MIT-FF-001', 'Fuel filter for diesel L200', 'Fuel filter replacement', 12000.00, 25, 'in_stock', 'new', 0, 1);


-- Note: Foreign key constraints removed for simplified standalone table
-- Add FK constraints manually if integrating with existing database