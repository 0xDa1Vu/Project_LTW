-- =====================================================================
-- Migration: them bang stylings (anh lookbook phoi do o trang chu)
-- Ap len DB DANG CHAY (khong can re-seed). Chay bang:
--   docker exec -i ltw_db psql -U shop -d fashion_shop \
--     < database/migrations/2026_07_01_add_stylings_table.sql
-- (schema.sql da duoc cap nhat san cho lan re-seed sau)
-- =====================================================================

CREATE TABLE IF NOT EXISTS stylings (
    id         SERIAL PRIMARY KEY,
    title      VARCHAR(150) NOT NULL,
    image      VARCHAR(255) NOT NULL,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT now(),
    updated_at TIMESTAMP NOT NULL DEFAULT now()
);
