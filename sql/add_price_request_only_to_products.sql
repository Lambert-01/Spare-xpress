ALTER TABLE products_enhanced
    ADD COLUMN IF NOT EXISTS price_request_only TINYINT(1) NOT NULL DEFAULT 0 AFTER backorders_allowed;

CREATE INDEX IF NOT EXISTS idx_products_price_request_only
    ON products_enhanced (price_request_only);
