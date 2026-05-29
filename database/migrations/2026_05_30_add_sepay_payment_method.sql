-- =====================================================================
-- Migration: cho phep payment_method = 'sepay'
-- Ap len DB DANG CHAY (khong can re-seed). Chay bang:
--   docker exec -i ltw_db psql -U shop -d fashion_shop \
--     < database/migrations/2026_05_30_add_sepay_payment_method.sql
-- (schema.sql da duoc cap nhat san cho lan re-seed sau)
-- =====================================================================

ALTER TABLE orders DROP CONSTRAINT IF EXISTS orders_payment_method_check;
ALTER TABLE orders ADD CONSTRAINT orders_payment_method_check
    CHECK (payment_method IN ('cod','vnpay','sepay'));
