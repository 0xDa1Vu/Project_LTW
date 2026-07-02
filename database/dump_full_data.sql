pg_dump: warning: there are circular foreign-key constraints on this table:
pg_dump: detail: categories
pg_dump: hint: You might not be able to restore the dump without using --disable-triggers or temporarily dropping the constraints.
pg_dump: hint: Consider using a full dump instead of a --data-only dump to avoid this problem.
--
-- PostgreSQL database dump
--

\restrict aP80NTNO24heXC32ORdM7CZ3UbHPMt2acsqptt8VW6JdDuVbqKD0yxmQPTQHhXv

-- Dumped from database version 16.14
-- Dumped by pg_dump version 16.14

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.users (id, name, email, password_hash, role, phone, address, created_at) VALUES (1, 'Quan tri vien', 'admin@shop.test', '$2y$10$vcbmDrTl/vr9eYeKveJLmeVigj6jg2FZugFP7Ve5lsERaeCleJHQi', 'admin', '0900000001', 'Van phong ATELIER, Ha Noi', '2026-05-29 20:04:48.90682');
INSERT INTO public.users (id, name, email, password_hash, role, phone, address, created_at) VALUES (2, 'Nguyen Van An', 'an@shop.test', '$2y$10$xGVQFDzQQwwaEft29cqpBexzIigt9TmHByPZdeQIRDyohQn.IlcHa', 'customer', '0911111111', '12 Le Loi, Quan 1, TP.HCM', '2026-05-29 20:04:48.90682');
INSERT INTO public.users (id, name, email, password_hash, role, phone, address, created_at) VALUES (3, 'Tran Thi Binh', 'binh@shop.test', '$2y$10$xGVQFDzQQwwaEft29cqpBexzIigt9TmHByPZdeQIRDyohQn.IlcHa', 'customer', '0922222222', '34 Cau Giay, Ha Noi', '2026-05-29 20:04:48.90682');


--
-- Data for Name: carts; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.carts (id, user_id, session_id, created_at) VALUES (1, NULL, 'e8754e2d8b3eea5d8c8d78477ae1660d', '2026-05-29 20:07:44.204393');
INSERT INTO public.carts (id, user_id, session_id, created_at) VALUES (3, 2, NULL, '2026-05-29 20:16:59.843223');
INSERT INTO public.carts (id, user_id, session_id, created_at) VALUES (4, NULL, '9fea663a33b20a6927facf8a36f8f258', '2026-05-30 18:23:18.39007');
INSERT INTO public.carts (id, user_id, session_id, created_at) VALUES (5, 1, NULL, '2026-06-25 06:34:31.208725');


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.categories (id, name, slug, parent_id) VALUES (1, 'Áo', 'ao', NULL);
INSERT INTO public.categories (id, name, slug, parent_id) VALUES (2, 'Quần', 'quan', NULL);
INSERT INTO public.categories (id, name, slug, parent_id) VALUES (4, 'Phụ kiện', 'phu-kien', NULL);
INSERT INTO public.categories (id, name, slug, parent_id) VALUES (9, 'Hoodie', 'hoodie', 1);
INSERT INTO public.categories (id, name, slug, parent_id) VALUES (13, 'Mũ', 'mu', 4);
INSERT INTO public.categories (id, name, slug, parent_id) VALUES (14, 'Túi', 'tui', 4);


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (32, 2, 'Raw Denim Stitch Baggy Jeans', 'raw-denim-stitch-baggy-jeans-b75c', 'kh con gi de noi..', 360000.00, NULL, '', 'active', '2026-07-01 10:33:07.507293', false);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (10, 1, 'Travel Cities Long Sleeve Boxy Tee', 'travel-cities-long-sleeve-boxy-tee-1940', 'Quần kaki chino mềm, dễ phối, đủ màu cơ bản.', 389000.00, 329000.00, '', 'active', '2026-05-29 20:04:48.90682', false);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (34, 14, 'Stripe Classic Backpack', 'stripe-classic-backpack-eccb', 'dung ca? the gioi', 789000.00, NULL, '', 'active', '2026-07-01 10:42:44.454721', false);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (35, 4, 'Triple Star Small Wallet', 'triple-star-small-wallet-aa9c', 'muoi hai doi dep mat cung mot dem', 363636000.00, NULL, '', 'active', '2026-07-01 10:44:57.635182', false);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (11, 13, 'Joy Icon Classic Cap', 'joy-icon-classic-cap-9a77', 'hip to the hop', 259000.00, NULL, '', 'active', '2026-05-29 20:04:48.90682', false);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (12, 2, 'Emoji Pattern Shortpants', 'emoji-pattern-shortpants-d616', 'dep thi vl luon', 299000.00, NULL, '', 'active', '2026-05-29 20:04:48.90682', false);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (31, 1, 'Basic Slub Long Sleeve Relaxed Tee', 'levents-basic-slub-long-sleeve-relaxed-tee-ec7b', '', 360000.00, NULL, '', 'active', '2026-06-25 06:37:42.441281', true);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (33, 9, 'Classic Triple Star Zipper Hoodie Boxy', 'classic-triple-star-zipper-hoodie-boxy-8295', 'nong lam dung mua', 6789000.00, NULL, '', 'active', '2026-07-01 10:39:46.755926', false);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (18, 1, 'Seasonal Slub Semi-Oversized Tee', 'seasonal-slub-semi-oversized-tee-e10d', 'Đầm sơ mi dáng midi thanh lịch, có đai eo.', 649000.00, NULL, 'ATELIER', 'active', '2026-05-29 20:04:48.90682', true);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (17, 1, 'Furry Heart Semi-Oversized Tee', 'furry-heart-semi-oversized-tee-c8de', 'Maxi voan mỏng nhẹ, thoáng mát cho mùa hè.', 559000.00, 459000.00, '', 'active', '2026-05-29 20:04:48.90682', true);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (16, 1, 'Blink Blink Signature Logo Semi-Oversized Tee/ White', 'blink-blink-signature-logo-semi-oversized-tee-white-54cb', 'dep thi thoi roi', 899000.00, NULL, 'Bloom', 'active', '2026-05-29 20:04:48.90682', true);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (15, 1, 'XL Logo Star Shark Semi-Oversized', 'xl-logo-star-shark-semi-oversized-93d7', ':D', 689000.00, 589000.00, '', 'active', '2026-05-29 20:04:48.90682', true);
INSERT INTO public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES (14, 1, 'Signature Logo Long Sleeve Boxy Tee', 'signature-logo-long-sleeve-boxy-tee-24da', 'dep thi vl', 629000.00, NULL, 'ATELIER', 'active', '2026-05-29 20:04:48.90682', true);


--
-- Data for Name: variants; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (290, 18, 'L', 'Xanh navy', 10, '18-l-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (430, 11, 'onesize', 'Trắng', 5, '11-onesize-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (435, 12, '1', 'Xanh navy', 12, '12-1-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (436, 12, '2', 'Xanh navy', 17, '12-2-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (437, 12, '3', 'Xanh navy', 15, '12-3-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (438, 12, '4', 'Xanh navy', 20, '12-4-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (440, 33, '1', 'Đen', 11, '33-1-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (398, 32, '1', 'Đen', 13, '32-1-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (399, 32, '2', 'Đen', 14, '32-2-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (400, 32, '3', 'Đen', 15, '32-3-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (401, 32, '4', 'Đen', 13, '32-4-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (441, 33, '2', 'Đen', 11, '33-2-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (442, 33, '3', 'Đen', 11, '33-3-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (443, 33, '4', 'Đen', 11, '33-4-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (291, 18, 'L', 'Đen', 15, '18-l-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (427, 34, 'onesize', 'xanh', 77, '34-onesize-xanh');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (429, 35, 'onesize', 'Đen', 44, '35-onesize-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (292, 18, 'M', 'Xanh navy', 7, '18-m-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (293, 18, 'M', 'Đen', 12, '18-m-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (294, 18, 'S', 'Xanh navy', 4, '18-s-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (295, 18, 'S', 'Đen', 9, '18-s-den');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (439, 31, '38', 'Be', 10, '31-38-be');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (302, 17, 'L', 'Be', 3, '17-l-be');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (303, 17, 'L', 'Xám', 8, '17-l-xam');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (304, 17, 'M', 'Be', 25, '17-m-be');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (305, 17, 'M', 'Xám', 5, '17-m-xam');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (306, 17, 'S', 'Be', 22, '17-s-be');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (307, 17, 'S', 'Xám', 27, '17-s-xam');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (334, 15, '1', 'Trắng', 18, '15-1-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (410, 10, '1', 'Trắng', 7, '10-1-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (411, 10, '2', 'Trắng', 9, '10-2-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (412, 10, '3', 'Trắng', 10, '10-3-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (413, 10, '4', 'Trắng', 14, '10-4-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (320, 16, '1', 'Trắng', 21, '16-1-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (321, 16, '2', 'Xanh navy', 26, '16-2-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (322, 16, '3', 'Trắng', 18, '16-3-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (323, 16, '4', 'Xanh navy', 23, '16-4-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (324, 16, '2', 'Trắng', 15, '16-2-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (325, 16, '3', 'Xanh navy', 20, '16-3-xanh-navy');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (335, 15, '2', 'Trắng', 14, '15-2-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (336, 15, '3', 'Trắng', 16, '15-3-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (337, 15, '4', 'Trắng', 11, '15-4-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (356, 14, '1', 'Trắng', 12, '14-1-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (357, 14, '2', 'Xám', 7, '14-2-xam');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (358, 14, '3', 'Trắng', 9, '14-3-trang');
INSERT INTO public.variants (id, product_id, size, color, stock, sku) VALUES (359, 14, '4', 'Xám', 4, '14-4-xam');


--
-- Data for Name: cart_items; Type: TABLE DATA; Schema: public; Owner: shop
--



--
-- Data for Name: coupons; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.coupons (id, code, type, value, min_order, max_uses, used_count, expires_at, is_active, created_at) VALUES (1, 'WELCOME10', 'percent', 10.00, 0.00, NULL, 0, NULL, true, '2026-06-26 08:59:23.401092');
INSERT INTO public.coupons (id, code, type, value, min_order, max_uses, used_count, expires_at, is_active, created_at) VALUES (2, 'SALE50K', 'fixed', 50000.00, 200000.00, NULL, 0, NULL, true, '2026-06-26 08:59:23.401092');


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (1, 2, 717000.00, 'completed', 'cod', 'paid', '12 Le Loi, Quan 1, TP.HCM', '0911111111', 'Nguyen Van An', 'Giao gio hanh chinh', '2026-05-18 20:04:48.90682');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (2, 3, 828000.00, 'completed', 'vnpay', 'paid', '34 Cau Giay, Ha Noi', '0922222222', 'Tran Thi Binh', NULL, '2026-05-19 20:04:48.90682');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (3, 2, 1217000.00, 'completed', 'cod', 'paid', '12 Le Loi, Quan 1, TP.HCM', '0911111111', 'Nguyen Van An', NULL, '2026-05-21 20:04:48.90682');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (4, 3, 589000.00, 'shipping', 'cod', 'unpaid', '34 Cau Giay, Ha Noi', '0922222222', 'Tran Thi Binh', 'Goi truoc khi giao', '2026-05-23 20:04:48.90682');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (5, 2, 948000.00, 'confirmed', 'vnpay', 'paid', '12 Le Loi, Quan 1, TP.HCM', '0911111111', 'Nguyen Van An', NULL, '2026-05-25 20:04:48.90682');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (6, 3, 259000.00, 'pending', 'cod', 'unpaid', '34 Cau Giay, Ha Noi', '0922222222', 'Tran Thi Binh', NULL, '2026-05-27 20:04:48.90682');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (7, 2, 549000.00, 'cancelled', 'cod', 'unpaid', '12 Le Loi, Quan 1, TP.HCM', '0911111111', 'Nguyen Van An', 'Khach doi y', '2026-05-28 20:04:48.90682');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (8, 3, 1027000.00, 'completed', 'vnpay', 'paid', '34 Cau Giay, Ha Noi', '0922222222', 'Tran Thi Binh', NULL, '2026-05-20 20:04:48.90682');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (9, 2, 318000.00, 'confirmed', 'sepay', 'paid', '12 Le Loi Q1', '0911111111', 'Nguyen Van An', 'test', '2026-05-29 20:16:59.892114');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (11, NULL, 549000.00, 'pending', 'sepay', 'unpaid', 'Gia Nghĩa', '0336008578', 'Mai Vũ Đại Vũ', '', '2026-06-25 06:19:00.201693');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (12, NULL, 279000.00, 'pending', 'sepay', 'unpaid', '1', '2', '1', '1', '2026-06-25 06:19:28.00291');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (13, NULL, 549000.00, 'pending', 'sepay', 'unpaid', '2', '2', '1', '1', '2026-06-25 06:21:57.564829');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (14, NULL, 159000.00, 'pending', 'sepay', 'unpaid', '1', '2', '1', '', '2026-06-25 06:31:33.072846');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (15, 1, 12000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:40:12.332307');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (16, 1, 459000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:41:06.928163');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (17, 1, 42000.00, 'confirmed', 'sepay', 'paid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:54:03.400722');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (18, 1, 42000.00, 'confirmed', 'sepay', 'paid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:57:49.77988');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (19, 1, 42000.00, 'pending', 'cod', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:59:22.187986');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (20, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:19:48.875895');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (21, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:20:18.579926');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (22, 1, 390000.00, 'pending', 'cod', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:22:22.183283');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (23, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:22:47.595836');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (24, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:23:15.186415');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (25, 1, 489000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:25:15.137367');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (26, 1, 309000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:26:31.098459');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (27, 1, 309000.00, 'pending', 'cod', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:27:31.116074');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (28, 1, 429000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:28:04.313776');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (29, 1, 579000.00, 'pending', 'cod', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:29:45.453029');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (30, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:34:43.963067');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (31, 1, 489000.00, 'pending', 'cod', 'unpaid', 'adsdads, Phường Bình Hòa, Thành phố Hồ Chí Minh', '0900000001', 'Quan tri vien', '', '2026-06-26 08:51:08.397614');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (32, 1, 489000.00, 'pending', 'sepay', 'unpaid', '1, Xã Ngọc Đường, Tỉnh Tuyên Quang', '0336008578', 'Quan tri vien', '', '2026-06-26 08:51:43.819859');
INSERT INTO public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES (33, 1, 619000.00, 'pending', 'sepay', 'unpaid', '1234, Phường Ba Đình, Thành phố Hà Nội', '0900000001', 'aaaa', '', '2026-07-01 11:10:35.621318');


--
-- Data for Name: order_items; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (3, 2, NULL, 'Đầm suông linen', 'M / Be', 629000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (7, 4, NULL, 'Đầm xòe hoa nhí', 'M / Trắng', 589000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (8, 5, NULL, 'Áo khoác bomber', 'L / Xanh navy', 649000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (9, 5, NULL, 'Thắt lưng da bò', 'Free / Đen', 299000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (10, 6, NULL, 'Quần short kaki', '30 / Be', 259000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (11, 7, NULL, 'Áo hoodie nỉ bông', 'XL / Đen', 549000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (13, 8, NULL, 'Khăn lụa vuông', 'Free / Trắng', 249000.00, 2);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (12, 8, NULL, 'Giày cao gót 7cm', '39 / Đen', 529000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (5, 3, NULL, 'Giày sneaker trắng', '40 / Trắng', 659000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (4, 2, NULL, 'Túi tote canvas', 'Free / Đen', 199000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (20, 15, NULL, 'aaaa', '38 / Be', 12000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (22, 17, NULL, 'aaaa', '38 / Be', 12000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (23, 18, NULL, 'aaaa', '38 / Be', 12000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (24, 19, NULL, 'aaaa', '38 / Be', 12000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (25, 20, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (26, 21, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (27, 22, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (28, 23, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (29, 24, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (35, 30, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (1, 1, NULL, 'Áo thun cotton basic', 'M / Trắng', 159000.00, 2);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (19, 14, NULL, 'Áo thun cotton basic', 'L / Xanh navy', 159000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (14, 9, NULL, 'Áo thun cotton basic', 'S / Trắng', 159000.00, 2);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (21, 16, NULL, 'Áo sơ mi linen dài tay', 'L / Xám', 459000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (36, 31, NULL, 'Áo sơ mi linen dài tay', 'L / Xám', 459000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (37, 32, NULL, 'Áo sơ mi linen dài tay', 'L / Xám', 459000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (30, 25, NULL, 'Áo sơ mi linen dài tay', 'L / Be', 459000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (6, 3, NULL, 'Áo polo pique', 'L / Đen', 279000.00, 2);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (17, 12, NULL, 'Áo polo pique', 'L / Đen', 279000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (31, 26, NULL, 'Áo polo pique', 'L / Xanh navy', 279000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (32, 27, NULL, 'Áo polo pique', 'L / Xanh navy', 279000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (16, 11, NULL, 'Áo hoodie nỉ bông', 'L / Xám', 549000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (18, 13, NULL, 'Áo hoodie nỉ bông', 'L / Xám', 549000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (34, 29, NULL, 'Áo hoodie nỉ bông', 'L / Xám', 549000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (33, 28, NULL, 'Áo len cổ tròn', 'L / Xanh navy', 399000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (2, 1, NULL, 'Quần jean slim fit', '30 / Đen', 399000.00, 1);
INSERT INTO public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES (38, 33, 334, 'XL Logo Star Shark Semi-Oversized', '1 / Trắng', 589000.00, 1);


--
-- Data for Name: payments; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.payments (id, order_id, provider, txn_ref, amount, status, raw_response, created_at) VALUES (1, 2, 'vnpay', 'TX1002', 828000.00, 'success', NULL, '2026-05-29 20:04:48.90682');
INSERT INTO public.payments (id, order_id, provider, txn_ref, amount, status, raw_response, created_at) VALUES (2, 5, 'vnpay', 'TX1005', 948000.00, 'success', NULL, '2026-05-29 20:04:48.90682');
INSERT INTO public.payments (id, order_id, provider, txn_ref, amount, status, raw_response, created_at) VALUES (3, 8, 'vnpay', 'TX1008', 1027000.00, 'success', NULL, '2026-05-29 20:04:48.90682');
INSERT INTO public.payments (id, order_id, provider, txn_ref, amount, status, raw_response, created_at) VALUES (4, 9, 'sepay', 'FT123456', 318000.00, 'paid', '{"id": 999, "content": "DH9 thanh toan", "transferType": "in", "referenceCode": "FT123456", "transferAmount": 318000}', '2026-05-29 20:18:15.900913');
INSERT INTO public.payments (id, order_id, provider, txn_ref, amount, status, raw_response, created_at) VALUES (6, 17, 'sepay', 'FT26176242820359', 42000.00, 'paid', '{"id": 64973047, "code": null, "content": "134865022846-DH17-CHUYEN TIEN-OQCH000EHvA1-MOMO134865022846MOMO", "gateway": "MBBank", "subAccount": null, "accumulated": 0, "description": "BankAPINotify 134865022846-DH17-CHUYEN TIEN-OQCH000EHvA1-MOMO134865022846MOMO", "transferType": "in", "accountNumber": "64579797977777", "referenceCode": "FT26176242820359", "transferAmount": 42000, "transactionDate": "2026-06-25 13:54:00"}', '2026-06-25 06:54:49.625204');
INSERT INTO public.payments (id, order_id, provider, txn_ref, amount, status, raw_response, created_at) VALUES (7, 18, 'sepay', 'FT26176502693040', 42000.00, 'paid', '{"id": 64973513, "code": null, "content": "DH18", "gateway": "MBBank", "subAccount": null, "accumulated": 0, "description": "BankAPINotify DH18", "transferType": "in", "accountNumber": "64579797977777", "referenceCode": "FT26176502693040", "transferAmount": 42000, "transactionDate": "2026-06-25 13:58:00"}', '2026-06-25 06:58:34.532797');


--
-- Data for Name: product_images; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (85, 34, '/uploads/3f9c629723986556.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (87, 34, '/uploads/da589ccf6e34ffaa.webp', false, 1);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (86, 34, '/uploads/97e2df04dec79633.webp', false, 2);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (88, 35, '/uploads/c7c38679ca10b7da.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (89, 35, '/uploads/12e186597ad53c20.webp', false, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (90, 35, '/uploads/2ee4454c592c4b07.webp', false, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (72, 12, '/uploads/edec8aca507d6b3d.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (73, 12, '/uploads/e2dabd686eb1f1e4.webp', false, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (75, 11, '/uploads/928367dfea1dba36.webp', false, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (76, 11, '/uploads/fbf7a59a85337305.webp', false, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (74, 11, '/uploads/4dd3fe2be434c4be.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (77, 32, '/uploads/dfc1596e916d77d5.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (78, 32, '/uploads/0ecf54fcbd6606b3.webp', false, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (81, 10, '/uploads/f9031978d5e127a6.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (80, 10, '/uploads/fd897f6955d99d37.webp', false, 1);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (79, 10, '/uploads/358010f88d835bfa.webp', false, 2);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (63, 31, '/uploads/2924be21042850b1.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (64, 31, '/uploads/4d427cc035f0b338.webp', false, 1);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (65, 31, '/uploads/d4b26c127bb31d2c.webp', false, 2);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (66, 31, '/uploads/7ae81eb56a8c06a6.webp', false, 3);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (67, 18, '/uploads/2fc9e9059eea1226.webp', false, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (68, 17, '/uploads/60ea37ec316e3aaa.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (83, 33, '/uploads/726f9b9be62645e8.webp', false, 1);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (82, 33, '/uploads/15e3ea15c00e1fea.webp', false, 2);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (69, 16, '/uploads/917959e0c0238168.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (84, 33, '/uploads/3458f12e7fdacfcc.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (70, 15, '/uploads/13843acb8badc1d2.webp', true, 0);
INSERT INTO public.product_images (id, product_id, image_url, is_primary, sort_order) VALUES (71, 14, '/uploads/000db1aba3115984.webp', true, 0);


--
-- Data for Name: reviews; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.reviews (id, product_id, user_id, rating, comment, created_at) VALUES (4, 14, 3, 5, 'Đầm xinh, vải mềm, sẽ ủng hộ tiếp.', '2026-05-29 20:04:48.90682');
INSERT INTO public.reviews (id, product_id, user_id, rating, comment, created_at) VALUES (7, 15, 2, 4, 'Đầm hoa dễ thương, đúng mô tả.', '2026-05-29 20:04:48.90682');
INSERT INTO public.reviews (id, product_id, user_id, rating, comment, created_at) VALUES (9, 35, 1, 5, 'dep', '2026-07-01 11:01:08.053485');


--
-- Data for Name: stylings; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.stylings (id, title, sort_order, created_at, updated_at, model_info) VALUES (7, 'Striped Oversized Jersey Tee', 2, '2026-07-01 09:16:51.302823', '2026-07-01 09:23:13', 'Model:

Height 1m68, Weight 51kg

Wearing: Size 0');
INSERT INTO public.stylings (id, title, sort_order, created_at, updated_at, model_info) VALUES (2, 'Logo Zipper Hoodie Boxy', 1, '2026-07-01 08:26:43.51202', '2026-07-01 09:24:31', 'Model:

Height 1m68, Weight 51kg
Wearing: Size 3');
INSERT INTO public.stylings (id, title, sort_order, created_at, updated_at, model_info) VALUES (8, 'XL Logo Boxy Sweater', 3, '2026-07-01 09:23:59.320396', '2026-07-01 09:26:14', 'Model:
Height 1m68, Weight 51kg
Wearing: Size 3');
INSERT INTO public.stylings (id, title, sort_order, created_at, updated_at, model_info) VALUES (9, 'Raglan Long Sleeve Boxy Tee', 4, '2026-07-01 09:35:50.169745', '2026-07-01 09:36:48', '');
INSERT INTO public.stylings (id, title, sort_order, created_at, updated_at, model_info) VALUES (10, 'Striped Fur Knit Boxy Sweater', 0, '2026-07-01 09:44:33.603032', '2026-07-01 09:44:33.603032', '');


--
-- Data for Name: styling_images; Type: TABLE DATA; Schema: public; Owner: shop
--

INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (3, 2, '/uploads/912ef64f1028c413.webp', true, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (4, 2, '/uploads/cc23cdd428f6bc08.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (5, 2, '/uploads/5803c8cf8a9dd7ec.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (6, 2, '/uploads/cbdb9993142f2096.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (10, 7, '/uploads/40477b16aa1f06b1.webp', true, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (12, 7, '/uploads/9ad18f8aaded4e42.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (13, 7, '/uploads/633cb359948f4f50.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (14, 7, '/uploads/62d7ef09eba205da.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (16, 8, '/uploads/cec56b64830da252.webp', true, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (17, 8, '/uploads/1f48f5955b3cff33.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (18, 8, '/uploads/1f9fe84db73a3b8c.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (19, 8, '/uploads/f71af44dee78a131.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (23, 9, '/uploads/46d58a92e32f82c8.webp', true, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (22, 9, '/uploads/cf0fe6a2dd8059cb.webp', false, 1);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (21, 9, '/uploads/78c3c01353f9ffc5.webp', false, 2);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (20, 9, '/uploads/39ccd92b4c10d011.webp', false, 3);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (24, 10, '/uploads/4815833df4d90008.webp', true, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (25, 10, '/uploads/57bca55035a6bbc8.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (26, 10, '/uploads/a31bf8c3dfe76879.webp', false, 0);
INSERT INTO public.styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES (27, 10, '/uploads/502d8ae4de293a4a.webp', false, 0);


--
-- Name: cart_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.cart_items_id_seq', 29, true);


--
-- Name: carts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.carts_id_seq', 11, true);


--
-- Name: categories_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.categories_id_seq', 15, true);


--
-- Name: coupons_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.coupons_id_seq', 2, true);


--
-- Name: order_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.order_items_id_seq', 38, true);


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.orders_id_seq', 33, true);


--
-- Name: payments_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.payments_id_seq', 7, true);


--
-- Name: product_images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.product_images_id_seq', 90, true);


--
-- Name: products_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.products_id_seq', 35, true);


--
-- Name: reviews_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.reviews_id_seq', 9, true);


--
-- Name: styling_images_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.styling_images_id_seq', 27, true);


--
-- Name: stylings_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.stylings_id_seq', 10, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.users_id_seq', 4, true);


--
-- Name: variants_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.variants_id_seq', 443, true);


--
-- PostgreSQL database dump complete
--

\unrestrict aP80NTNO24heXC32ORdM7CZ3UbHPMt2acsqptt8VW6JdDuVbqKD0yxmQPTQHhXv

