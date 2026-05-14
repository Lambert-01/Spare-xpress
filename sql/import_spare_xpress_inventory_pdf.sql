-- SPARE XPRESS LTD inventory import from:
-- spare xpress pricing and images - Inventory + Images.pdf
--
-- This script is safe to run more than once:
-- - missing vehicle models are inserted only when absent
-- - products are inserted only when the PDF part number is not already used as sku

START TRANSACTION;

SET @body_category_id := (
  SELECT id FROM categories_enhanced
  WHERE category_name = 'Body Parts' OR slug = 'body-parts'
  ORDER BY id
  LIMIT 1
);

INSERT INTO categories_enhanced (
  category_name, slug, description, icon_class, display_priority, is_active
)
SELECT 'Body Parts', 'body-parts', 'Exterior and interior body components', 'bi-grid', 'medium', 1
WHERE @body_category_id IS NULL;

SET @body_category_id := (
  SELECT id FROM categories_enhanced
  WHERE category_name = 'Body Parts' OR slug = 'body-parts'
  ORDER BY id
  LIMIT 1
);

DROP TEMPORARY TABLE IF EXISTS spx_pdf_inventory_import;

CREATE TEMPORARY TABLE spx_pdf_inventory_import (
  brand_name varchar(255) NOT NULL,
  model_name varchar(255) NOT NULL,
  model_slug varchar(255) NOT NULL,
  car_model_label varchar(255) NOT NULL,
  product_name varchar(255) NOT NULL,
  product_slug varchar(255) NOT NULL,
  part_number varchar(100) NOT NULL,
  quantity int NOT NULL,
  wholesale_price decimal(10,2) NOT NULL,
  retail_price decimal(10,2) NOT NULL,
  compatible_models text DEFAULT NULL
);

INSERT INTO spx_pdf_inventory_import (
  brand_name, model_name, model_slug, car_model_label, product_name, product_slug,
  part_number, quantity, wholesale_price, retail_price, compatible_models
) VALUES
('Toyota', 'Hilux Vigo/Revo', 'hilux-vigo-revo', 'Hilux Vigo/Revo', 'Toyota Hilux Vigo/Revo Rear Lamp Hilux Revo S.Africa Type LH', 'toyota-hilux-vigo-revo-rear-lamp-hilux-revo-safrica-type-ty-rvr16ww-l', 'TY-RVR16WW-L', 10, 55000.00, 80000.00, 'Hilux Vigo/Revo'),
('Toyota', 'Hilux Vigo/Revo', 'hilux-vigo-revo', 'Hilux Vigo/Revo', 'Toyota Hilux Vigo/Revo Rear Lamp Hilux Revo S.Africa Type RH', 'toyota-hilux-vigo-revo-rear-lamp-hilux-revo-safrica-type-ty-rvr16ww-r', 'TY-RVR16WW-R', 10, 55000.00, 80000.00, 'Hilux Vigo/Revo'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2005-2007 5-Line Lamp Black LH', 'toyota-corolla-side-mirror-2005-2007-5-line-lamp-black-as-2632-504-l', 'AS-2632-504-L', 3, 72000.00, 98000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2005-2007 5-Line Lamp Black RH', 'toyota-corolla-side-mirror-2005-2007-5-line-lamp-black-as-2632-504-r', 'AS-2632-504-R', 3, 72000.00, 98000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2001-2005 3-Line LH', 'toyota-corolla-side-mirror-2001-2005-3-line-as-2632-503-l', 'AS-2632-503-L', 5, 55000.00, 75000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2001-2005 3-Line RH', 'toyota-corolla-side-mirror-2001-2005-3-line-as-2632-503-r', 'AS-2632-503-R', 5, 55000.00, 75000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2008-2010 LED 5-Line LH', 'toyota-corolla-side-mirror-2008-2010-led-5-line-as-2632-501-l', 'AS-2632-501-L', 2, 75000.00, 100000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2008-2010 LED 5-Line RH', 'toyota-corolla-side-mirror-2008-2010-led-5-line-as-2632-501-r', 'AS-2632-501-R', 2, 75000.00, 100000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2008 3-Line Black LH', 'toyota-corolla-side-mirror-2008-3-line-black-as-2632-502-l', 'AS-2632-502-L', 2, 65000.00, 88000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2008 3-Line Black RH', 'toyota-corolla-side-mirror-2008-3-line-black-as-2632-502-r', 'AS-2632-502-R', 2, 65000.00, 88000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Rear Lamp 2008-2009 LH', 'toyota-corolla-rear-lamp-2008-2009-c212-19q3-l', 'C212-19Q3-L', 5, 38000.00, 55000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Rear Lamp 2008-2009 RH', 'toyota-corolla-rear-lamp-2008-2009-c212-19q3-r', 'C212-19Q3-R', 5, 38000.00, 55000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Fog Lamp 2002 LH', 'toyota-corolla-fog-lamp-2002-c212-2022-l', 'C212-2022-L', 2, 20000.00, 28000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Fog Lamp 2002 RH', 'toyota-corolla-fog-lamp-2002-c212-2022-r', 'C212-2022-R', 2, 20000.00, 28000.00, 'Corolla'),
('Toyota', 'Yaris', 'yaris', 'Yaris', 'Toyota Yaris Rear Lamp 2006-2007 LH', 'toyota-yaris-rear-lamp-2006-2007-19-3113-l', '19-3113-L', 4, 52000.00, 72000.00, 'Yaris'),
('Toyota', 'Yaris', 'yaris', 'Yaris', 'Toyota Yaris Rear Lamp 2006-2007 RH', 'toyota-yaris-rear-lamp-2006-2007-19-3113-r', '19-3113-R', 4, 52000.00, 72000.00, 'Yaris'),
('Toyota', 'Corolla', 'corolla', 'Corolla/Altis/Camry', 'Toyota Corolla Altis Camry Fog Lamp 2003-2004 Set', 'toyota-corolla-altis-camry-fog-lamp-2003-2004-ty40270', 'TY40270', 14, 72000.00, 95000.00, 'Corolla, Altis, Camry'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla RunX Head Lamp 2001 LH', 'toyota-corolla-runx-head-lamp-2001-c212-11d1-l', 'C212-11D1-L', 2, 100000.00, 135000.00, 'Corolla RunX'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla RunX Head Lamp 2001 RH', 'toyota-corolla-runx-head-lamp-2001-c212-11d1-r', 'C212-11D1-R', 2, 100000.00, 135000.00, 'Corolla RunX'),
('Toyota', 'Camry', 'camry', 'Camry', 'Toyota Camry Rear Lamp 2012 USA Type LH', 'toyota-camry-rear-lamp-2012-usa-type-at-89002-l', 'AT-89002-L', 2, 68000.00, 92000.00, 'Camry'),
('Toyota', 'Camry', 'camry', 'Camry', 'Toyota Camry Rear Lamp 2012 USA Type RH', 'toyota-camry-rear-lamp-2012-usa-type-at-89002-r', 'AT-89002-R', 2, 70000.00, 95000.00, 'Camry'),
('Toyota', 'Camry', 'camry', 'Camry', 'Toyota Camry Head Lamp 2012 USA White LH', 'toyota-camry-head-lamp-2012-usa-white-at-89001b-l', 'AT-89001B-L', 1, 120000.00, 160000.00, 'Camry'),
('Toyota', 'Camry', 'camry', 'Camry', 'Toyota Camry Head Lamp 2012 USA White RH', 'toyota-camry-head-lamp-2012-usa-white-at-89001b-r', 'AT-89001B-R', 1, 120000.00, 160000.00, 'Camry'),
('Toyota', 'Camry', 'camry', 'Camry', 'Toyota Camry Head Lamp 2010 USA LH', 'toyota-camry-head-lamp-2010-usa-at-22669-11-l', 'AT-22669-11-L', 1, 100000.00, 135000.00, 'Camry'),
('Toyota', 'Camry', 'camry', 'Camry', 'Toyota Camry Head Lamp 2010 USA RH', 'toyota-camry-head-lamp-2010-usa-at-22669-11-r', 'AT-22669-11-R', 1, 100000.00, 135000.00, 'Camry'),
('Toyota', 'Avensis', 'avensis', 'Avensis', 'Toyota Avensis Head Lamp 1998-2000 LH', 'toyota-avensis-head-lamp-1998-2000-c212-1187-l', 'C212-1187-L', 5, 60000.00, 82000.00, 'Avensis'),
('Toyota', 'Avensis', 'avensis', 'Avensis', 'Toyota Avensis Head Lamp 1998-2000 RH', 'toyota-avensis-head-lamp-1998-2000-c212-1187-r', 'C212-1187-R', 5, 60000.00, 82000.00, 'Avensis'),
('Toyota', 'Avensis', 'avensis', 'Avensis/Corolla', 'Toyota Avensis Corolla Corner Lamp 1998 LH', 'toyota-avensis-corolla-corner-lamp-1998-c212-15c7-l', 'C212-15C7-L', 10, 12000.00, 17000.00, 'Avensis, Corolla'),
('Toyota', 'Avensis', 'avensis', 'Avensis/Corolla', 'Toyota Avensis Corolla Corner Lamp 1998 RH', 'toyota-avensis-corolla-corner-lamp-1998-c212-15c7-r', 'C212-15C7-R', 10, 12000.00, 17000.00, 'Avensis, Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Corner Lamp 1998-2000 USA LH', 'toyota-corolla-corner-lamp-1998-2000-usa-c312-1533l-as', 'C312-1533L-AS', 10, 16500.00, 23000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Corner Lamp 1998-2000 USA RH', 'toyota-corolla-corner-lamp-1998-2000-usa-c312-1533r-as', 'C312-1533R-AS', 10, 16500.00, 23000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Corner Lamp 1992 AE100 LH', 'toyota-corolla-corner-lamp-1992-ae100-c212-1575-l', 'C212-1575-L', 10, 11500.00, 16000.00, 'Corolla AE100'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Corner Lamp 1992 AE100 RH', 'toyota-corolla-corner-lamp-1992-ae100-c212-1575-r', 'C212-1575-R', 10, 11500.00, 16000.00, 'Corolla AE100'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2014 7-Line Folding LH', 'toyota-corolla-side-mirror-2014-7-line-folding-as-2632-508-l', 'AS-2632-508-L', 1, 120000.00, 160000.00, 'Corolla'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Side Mirror 2014 7-Line Folding RH', 'toyota-corolla-side-mirror-2014-7-line-folding-as-2632-508-r', 'AS-2632-508-R', 1, 120000.00, 160000.00, 'Corolla'),
('Toyota', 'RAV4', 'rav4', 'RAV4', 'Toyota RAV4 Side Mirror 2014 LED 5-Line LH', 'toyota-rav4-side-mirror-2014-led-5-line-as-2632-320-l', 'AS-2632-320-L', 1, 105000.00, 140000.00, 'RAV4'),
('Toyota', 'RAV4', 'rav4', 'RAV4', 'Toyota RAV4 Side Mirror 2014 LED 5-Line RH', 'toyota-rav4-side-mirror-2014-led-5-line-as-2632-320-r', 'AS-2632-320-R', 1, 105000.00, 140000.00, 'RAV4'),
('Lexus', 'RX330', 'rx330', 'Lexus RX330', 'Lexus RX330 Rear Lamp 2004 LH', 'lexus-rx330-rear-lamp-2004-at-2211-121-l', 'AT-2211-121-L', 1, 68000.00, 92000.00, 'RX330'),
('Lexus', 'RX330', 'rx330', 'Lexus RX330', 'Lexus RX330 Rear Lamp 2004 RH', 'lexus-rx330-rear-lamp-2004-at-2211-121-r', 'AT-2211-121-R', 1, 68000.00, 92000.00, 'RX330'),
('Toyota', 'RAV4', 'rav4', 'RAV4', 'Toyota RAV4 Tailgate Gas Spring 2013-2015 LH', 'toyota-rav4-tailgate-gas-spring-2013-2015-68960-0r010-th', '68960-0R010-TH', 5, 27000.00, 40000.00, 'RAV4'),
('Toyota', 'RAV4', 'rav4', 'RAV4', 'Toyota RAV4 Tailgate Gas Spring 2013-2015 RH', 'toyota-rav4-tailgate-gas-spring-2013-2015-68950-0r010-th', '68950-0R010-TH', 5, 27000.00, 40000.00, 'RAV4'),
('Hyundai', 'H1', 'h1', 'Hyundai H1', 'Hyundai H1 Tailgate Gas Spring 2012-2018 LH', 'hyundai-h1-tailgate-gas-spring-2012-2018-81770-4h030-gk', '81770-4H030-GK', 5, 27000.00, 40000.00, 'H1'),
('Hyundai', 'H1', 'h1', 'Hyundai H1', 'Hyundai H1 Tailgate Gas Spring 2012-2018 RH', 'hyundai-h1-tailgate-gas-spring-2012-2018-81780-4h030-gk', '81780-4H030-GK', 5, 27000.00, 40000.00, 'H1'),
('Toyota', 'Corolla', 'corolla', 'Corolla', 'Toyota Corolla Fog Lamp 2001-2003 Set CASP', 'toyota-corolla-fog-lamp-2001-2003-set-casp-p-212-2022-rl', 'P-212-2022-R/L', 3, 78000.00, 105000.00, 'Corolla'),
('Toyota', 'Hiace', 'hiace', 'Hiace', 'Toyota Hiace Tail Lamp 2005-2013 Noble C10 LH', 'toyota-hiace-tail-lamp-2005-2013-noble-c10-p-212-19k2l-cn', 'P-212-19K2L-CN', 3, 36000.00, 52000.00, 'Hiace'),
('Toyota', 'Hiace', 'hiace', 'Hiace', 'Toyota Hiace Tail Lamp 2005-2013 Noble C10 RH', 'toyota-hiace-tail-lamp-2005-2013-noble-c10-p-212-19k2r-cn', 'P-212-19K2R-CN', 3, 36000.00, 52000.00, 'Hiace');

DELETE FROM vehicle_models_enhanced
WHERE id = 0
  AND compatibility_info LIKE 'Imported from PDF inventory label:%';

DELETE imported_models
FROM vehicle_models_enhanced imported_models
JOIN vehicle_models_enhanced keep_models
  ON keep_models.brand_id = imported_models.brand_id
  AND keep_models.slug = imported_models.slug
  AND keep_models.id < imported_models.id
WHERE imported_models.compatibility_info LIKE 'Imported from PDF inventory label:%';

SET @next_model_id := COALESCE((SELECT MAX(id) FROM vehicle_models_enhanced), 0);

INSERT INTO vehicle_models_enhanced (
  id, brand_id, model_name, slug, compatibility_info, is_active, display_order
)
SELECT
  (@next_model_id := @next_model_id + 1),
  missing_models.brand_id,
  missing_models.model_name,
  missing_models.model_slug,
  missing_models.compatibility_info,
  1,
  999
FROM (
  SELECT DISTINCT
    b.id AS brand_id,
    i.model_name,
    i.model_slug,
    CONCAT('Imported from PDF inventory label: ', MIN(i.car_model_label)) AS compatibility_info
  FROM spx_pdf_inventory_import i
  JOIN vehicle_brands_enhanced b ON b.brand_name = i.brand_name
  WHERE NOT EXISTS (
    SELECT 1
    FROM vehicle_models_enhanced m
    WHERE m.brand_id = b.id
      AND (m.model_name = i.model_name OR m.slug = i.model_slug)
  )
  GROUP BY b.id, i.model_name, i.model_slug
) AS missing_models
ORDER BY missing_models.brand_id, missing_models.model_slug;

UPDATE products_enhanced p
JOIN spx_pdf_inventory_import i ON p.sku = i.part_number
JOIN vehicle_brands_enhanced b ON b.brand_name = i.brand_name
JOIN vehicle_models_enhanced m ON m.brand_id = b.id AND (m.model_name = i.model_name OR m.slug = i.model_slug)
SET
  p.brand_id = b.id,
  p.model_id = m.id,
  p.category_id = @body_category_id,
  p.product_name = i.product_name,
  p.slug = i.product_slug,
  p.description = CONCAT(
    i.product_name,
    '. PDF car model: ', i.car_model_label,
    '. Part number: ', i.part_number,
    '. Wholesale price: ', FORMAT(i.wholesale_price, 0), ' Rwf',
    '. Retail price: ', FORMAT(i.retail_price, 0), ' Rwf.'
  ),
  p.short_description = CONCAT(i.car_model_label, ' ', i.part_number),
  p.price = i.retail_price,
  p.regular_price = i.retail_price,
  p.wholesale_price = i.wholesale_price,
  p.stock_quantity = i.quantity,
  p.low_stock_threshold = 2,
  p.manage_stock = 1,
  p.backorders_allowed = 0,
  p.product_condition = 'new',
  p.visibility = 'public',
  p.warranty_type = 'none',
  p.compatible_models = CONCAT('["', REPLACE(i.compatible_models, ', ', '","'), '"]'),
  p.tags = '["pdf-import","lighting","exterior"]',
  p.specifications = CONCAT(
    '{"source":"spare xpress pricing and images - Inventory + Images.pdf",',
    '"part_number":"', REPLACE(i.part_number, '"', '\\"'), '",',
    '"car_model":"', REPLACE(i.car_model_label, '"', '\\"'), '",',
    '"wholesale_price_rwf":', CAST(i.wholesale_price AS UNSIGNED), ',',
    '"retail_price_rwf":', CAST(i.retail_price AS UNSIGNED), '}'
  ),
  p.stock_status = CASE WHEN i.quantity > 0 THEN 'in_stock' ELSE 'out_of_stock' END,
  p.`condition` = 'new',
  p.is_active = 1;

INSERT INTO products_enhanced (
  brand_id,
  model_id,
  category_id,
  product_name,
  slug,
  sku,
  description,
  short_description,
  price,
  regular_price,
  wholesale_price,
  stock_quantity,
  low_stock_threshold,
  manage_stock,
  backorders_allowed,
  product_condition,
  visibility,
  warranty_type,
  compatible_models,
  tags,
  specifications,
  stock_status,
  `condition`,
  is_featured,
  is_active
)
SELECT
  b.id,
  m.id,
  @body_category_id,
  i.product_name,
  i.product_slug,
  i.part_number,
  CONCAT(
    i.product_name,
    '. PDF car model: ', i.car_model_label,
    '. Part number: ', i.part_number,
    '. Wholesale price: ', FORMAT(i.wholesale_price, 0), ' Rwf',
    '. Retail price: ', FORMAT(i.retail_price, 0), ' Rwf.'
  ),
  CONCAT(i.car_model_label, ' ', i.part_number),
  i.retail_price,
  i.retail_price,
  i.wholesale_price,
  i.quantity,
  2,
  1,
  0,
  'new',
  'public',
  'none',
  CONCAT('["', REPLACE(i.compatible_models, ', ', '","'), '"]'),
  '["pdf-import","lighting","exterior"]',
  CONCAT(
    '{"source":"spare xpress pricing and images - Inventory + Images.pdf",',
    '"part_number":"', REPLACE(i.part_number, '"', '\\"'), '",',
    '"car_model":"', REPLACE(i.car_model_label, '"', '\\"'), '",',
    '"wholesale_price_rwf":', CAST(i.wholesale_price AS UNSIGNED), ',',
    '"retail_price_rwf":', CAST(i.retail_price AS UNSIGNED), '}'
  ),
  CASE WHEN i.quantity > 0 THEN 'in_stock' ELSE 'out_of_stock' END,
  'new',
  0,
  1
FROM spx_pdf_inventory_import i
JOIN vehicle_brands_enhanced b ON b.brand_name = i.brand_name
JOIN vehicle_models_enhanced m ON m.brand_id = b.id AND (m.model_name = i.model_name OR m.slug = i.model_slug)
WHERE NOT EXISTS (
  SELECT 1
  FROM products_enhanced p
  WHERE p.sku = i.part_number
);

DROP TEMPORARY TABLE IF EXISTS spx_pdf_inventory_import;

COMMIT;
