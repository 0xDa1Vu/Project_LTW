-- =====================================================================
-- Migration: styling ho tro nhieu anh + thong tin model
-- Ap len DB DANG CHAY (khong can re-seed). Chay bang:
--   docker exec -i ltw_db psql -U shop -d fashion_shop \
--     < database/migrations/2026_07_01b_styling_multi_images.sql
-- (schema.sql da duoc cap nhat san cho lan re-seed sau)
-- =====================================================================

ALTER TABLE stylings ADD COLUMN IF NOT EXISTS model_info TEXT;
ALTER TABLE stylings DROP COLUMN IF EXISTS image;

CREATE TABLE IF NOT EXISTS styling_images (
    id          SERIAL PRIMARY KEY,
    styling_id  INT NOT NULL REFERENCES stylings(id) ON DELETE CASCADE,
    image_url   VARCHAR(255) NOT NULL,
    is_cover    BOOLEAN NOT NULL DEFAULT false,
    sort_order  INT NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_styling_images_styling ON styling_images(styling_id);
