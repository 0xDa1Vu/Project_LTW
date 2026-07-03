--
-- PostgreSQL database dump
--

\restrict ppBNZ0FQHtFwWD56dFNboCTpcAVxUz28sbphw8jOv0SD67iXxSY60TTDfQsgWmS

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

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cart_items; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.cart_items (
    id integer NOT NULL,
    cart_id integer NOT NULL,
    variant_id integer NOT NULL,
    quantity integer DEFAULT 1 NOT NULL,
    CONSTRAINT cart_items_quantity_check CHECK ((quantity > 0))
);


ALTER TABLE public.cart_items OWNER TO shop;

--
-- Name: cart_items_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.cart_items_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cart_items_id_seq OWNER TO shop;

--
-- Name: cart_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.cart_items_id_seq OWNED BY public.cart_items.id;


--
-- Name: carts; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.carts (
    id integer NOT NULL,
    user_id integer,
    session_id character varying(120),
    created_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.carts OWNER TO shop;

--
-- Name: carts_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.carts_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.carts_id_seq OWNER TO shop;

--
-- Name: carts_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.carts_id_seq OWNED BY public.carts.id;


--
-- Name: categories; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.categories (
    id integer NOT NULL,
    name character varying(120) NOT NULL,
    slug character varying(140) NOT NULL,
    parent_id integer
);


ALTER TABLE public.categories OWNER TO shop;

--
-- Name: categories_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.categories_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.categories_id_seq OWNER TO shop;

--
-- Name: categories_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.categories_id_seq OWNED BY public.categories.id;


--
-- Name: coupons; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.coupons (
    id integer NOT NULL,
    code character varying(50) NOT NULL,
    type character varying(10) DEFAULT 'percent'::character varying NOT NULL,
    value numeric(12,2) NOT NULL,
    min_order numeric(12,2) DEFAULT 0 NOT NULL,
    max_uses integer,
    used_count integer DEFAULT 0 NOT NULL,
    expires_at timestamp without time zone,
    is_active boolean DEFAULT true NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT coupons_type_check CHECK (((type)::text = ANY ((ARRAY['percent'::character varying, 'fixed'::character varying])::text[])))
);


ALTER TABLE public.coupons OWNER TO shop;

--
-- Name: coupons_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.coupons_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.coupons_id_seq OWNER TO shop;

--
-- Name: coupons_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.coupons_id_seq OWNED BY public.coupons.id;


--
-- Name: order_items; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.order_items (
    id integer NOT NULL,
    order_id integer NOT NULL,
    variant_id integer,
    product_name character varying(200) NOT NULL,
    variant_label character varying(80),
    price numeric(12,2) NOT NULL,
    quantity integer NOT NULL,
    CONSTRAINT order_items_quantity_check CHECK ((quantity > 0))
);


ALTER TABLE public.order_items OWNER TO shop;

--
-- Name: order_items_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.order_items_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.order_items_id_seq OWNER TO shop;

--
-- Name: order_items_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.order_items_id_seq OWNED BY public.order_items.id;


--
-- Name: orders; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.orders (
    id integer NOT NULL,
    user_id integer,
    total numeric(12,2) DEFAULT 0 NOT NULL,
    status character varying(20) DEFAULT 'pending'::character varying NOT NULL,
    payment_method character varying(20) DEFAULT 'cod'::character varying NOT NULL,
    payment_status character varying(20) DEFAULT 'unpaid'::character varying NOT NULL,
    shipping_address text NOT NULL,
    phone character varying(20) NOT NULL,
    customer_name character varying(120) NOT NULL,
    note text,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT orders_payment_method_check CHECK (((payment_method)::text = ANY ((ARRAY['cod'::character varying, 'vnpay'::character varying, 'sepay'::character varying])::text[]))),
    CONSTRAINT orders_payment_status_check CHECK (((payment_status)::text = ANY ((ARRAY['unpaid'::character varying, 'paid'::character varying, 'failed'::character varying])::text[]))),
    CONSTRAINT orders_status_check CHECK (((status)::text = ANY ((ARRAY['pending'::character varying, 'confirmed'::character varying, 'shipping'::character varying, 'completed'::character varying, 'cancelled'::character varying])::text[])))
);


ALTER TABLE public.orders OWNER TO shop;

--
-- Name: orders_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.orders_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.orders_id_seq OWNER TO shop;

--
-- Name: orders_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.orders_id_seq OWNED BY public.orders.id;


--
-- Name: payments; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.payments (
    id integer NOT NULL,
    order_id integer NOT NULL,
    provider character varying(20) NOT NULL,
    txn_ref character varying(100),
    amount numeric(12,2) NOT NULL,
    status character varying(20) DEFAULT 'pending'::character varying NOT NULL,
    raw_response jsonb,
    created_at timestamp without time zone DEFAULT now() NOT NULL
);


ALTER TABLE public.payments OWNER TO shop;

--
-- Name: payments_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.payments_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.payments_id_seq OWNER TO shop;

--
-- Name: payments_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.payments_id_seq OWNED BY public.payments.id;


--
-- Name: product_images; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.product_images (
    id integer NOT NULL,
    product_id integer NOT NULL,
    image_url character varying(255) NOT NULL,
    is_primary boolean DEFAULT false NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.product_images OWNER TO shop;

--
-- Name: product_images_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.product_images_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.product_images_id_seq OWNER TO shop;

--
-- Name: product_images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.product_images_id_seq OWNED BY public.product_images.id;


--
-- Name: products; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.products (
    id integer NOT NULL,
    category_id integer,
    name character varying(200) NOT NULL,
    slug character varying(220) NOT NULL,
    description text,
    price numeric(12,2) DEFAULT 0 NOT NULL,
    sale_price numeric(12,2),
    brand character varying(120),
    status character varying(20) DEFAULT 'active'::character varying NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    is_featured boolean DEFAULT false NOT NULL,
    CONSTRAINT products_status_check CHECK (((status)::text = ANY ((ARRAY['active'::character varying, 'hidden'::character varying])::text[])))
);


ALTER TABLE public.products OWNER TO shop;

--
-- Name: products_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.products_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.products_id_seq OWNER TO shop;

--
-- Name: products_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.products_id_seq OWNED BY public.products.id;


--
-- Name: reviews; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.reviews (
    id integer NOT NULL,
    product_id integer NOT NULL,
    user_id integer NOT NULL,
    rating integer NOT NULL,
    comment text,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT reviews_rating_check CHECK (((rating >= 1) AND (rating <= 5)))
);


ALTER TABLE public.reviews OWNER TO shop;

--
-- Name: reviews_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.reviews_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.reviews_id_seq OWNER TO shop;

--
-- Name: reviews_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.reviews_id_seq OWNED BY public.reviews.id;


--
-- Name: styling_images; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.styling_images (
    id integer NOT NULL,
    styling_id integer NOT NULL,
    image_url character varying(255) NOT NULL,
    is_cover boolean DEFAULT false NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.styling_images OWNER TO shop;

--
-- Name: styling_images_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.styling_images_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.styling_images_id_seq OWNER TO shop;

--
-- Name: styling_images_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.styling_images_id_seq OWNED BY public.styling_images.id;


--
-- Name: stylings; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.stylings (
    id integer NOT NULL,
    title character varying(150) NOT NULL,
    sort_order integer DEFAULT 0 NOT NULL,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    updated_at timestamp without time zone DEFAULT now() NOT NULL,
    model_info text
);


ALTER TABLE public.stylings OWNER TO shop;

--
-- Name: stylings_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.stylings_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.stylings_id_seq OWNER TO shop;

--
-- Name: stylings_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.stylings_id_seq OWNED BY public.stylings.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.users (
    id integer NOT NULL,
    name character varying(120) NOT NULL,
    email character varying(160) NOT NULL,
    password_hash character varying(255) NOT NULL,
    role character varying(20) DEFAULT 'customer'::character varying NOT NULL,
    phone character varying(20),
    address text,
    created_at timestamp without time zone DEFAULT now() NOT NULL,
    CONSTRAINT users_role_check CHECK (((role)::text = ANY ((ARRAY['customer'::character varying, 'admin'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO shop;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.users_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO shop;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: variants; Type: TABLE; Schema: public; Owner: shop
--

CREATE TABLE public.variants (
    id integer NOT NULL,
    product_id integer NOT NULL,
    size character varying(20) NOT NULL,
    color character varying(40) NOT NULL,
    stock integer DEFAULT 0 NOT NULL,
    sku character varying(60),
    CONSTRAINT variants_stock_check CHECK ((stock >= 0))
);


ALTER TABLE public.variants OWNER TO shop;

--
-- Name: variants_id_seq; Type: SEQUENCE; Schema: public; Owner: shop
--

CREATE SEQUENCE public.variants_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.variants_id_seq OWNER TO shop;

--
-- Name: variants_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: shop
--

ALTER SEQUENCE public.variants_id_seq OWNED BY public.variants.id;


--
-- Name: cart_items id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.cart_items ALTER COLUMN id SET DEFAULT nextval('public.cart_items_id_seq'::regclass);


--
-- Name: carts id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.carts ALTER COLUMN id SET DEFAULT nextval('public.carts_id_seq'::regclass);


--
-- Name: categories id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.categories ALTER COLUMN id SET DEFAULT nextval('public.categories_id_seq'::regclass);


--
-- Name: coupons id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.coupons ALTER COLUMN id SET DEFAULT nextval('public.coupons_id_seq'::regclass);


--
-- Name: order_items id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.order_items ALTER COLUMN id SET DEFAULT nextval('public.order_items_id_seq'::regclass);


--
-- Name: orders id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.orders ALTER COLUMN id SET DEFAULT nextval('public.orders_id_seq'::regclass);


--
-- Name: payments id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.payments ALTER COLUMN id SET DEFAULT nextval('public.payments_id_seq'::regclass);


--
-- Name: product_images id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.product_images ALTER COLUMN id SET DEFAULT nextval('public.product_images_id_seq'::regclass);


--
-- Name: products id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.products ALTER COLUMN id SET DEFAULT nextval('public.products_id_seq'::regclass);


--
-- Name: reviews id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.reviews ALTER COLUMN id SET DEFAULT nextval('public.reviews_id_seq'::regclass);


--
-- Name: styling_images id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.styling_images ALTER COLUMN id SET DEFAULT nextval('public.styling_images_id_seq'::regclass);


--
-- Name: stylings id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.stylings ALTER COLUMN id SET DEFAULT nextval('public.stylings_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: variants id; Type: DEFAULT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.variants ALTER COLUMN id SET DEFAULT nextval('public.variants_id_seq'::regclass);


--
-- Data for Name: cart_items; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.cart_items (id, cart_id, variant_id, quantity) FROM stdin;
\.


--
-- Data for Name: carts; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.carts (id, user_id, session_id, created_at) FROM stdin;
1	\N	e8754e2d8b3eea5d8c8d78477ae1660d	2026-05-29 20:07:44.204393
3	2	\N	2026-05-29 20:16:59.843223
4	\N	9fea663a33b20a6927facf8a36f8f258	2026-05-30 18:23:18.39007
5	1	\N	2026-06-25 06:34:31.208725
7	4	\N	2026-06-25 07:06:27.199903
15	5	\N	2026-07-02 07:01:31.02217
\.


--
-- Data for Name: categories; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.categories (id, name, slug, parent_id) FROM stdin;
1	Áo	ao	\N
2	Quần	quan	\N
4	Phụ kiện	phu-kien	\N
9	Hoodie	hoodie	1
13	Mũ	mu	4
14	Túi	tui	4
\.


--
-- Data for Name: coupons; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.coupons (id, code, type, value, min_order, max_uses, used_count, expires_at, is_active, created_at) FROM stdin;
1	WELCOME10	percent	10.00	0.00	\N	0	\N	t	2026-06-26 08:59:23.401092
2	SALE50K	fixed	50000.00	200000.00	\N	0	\N	t	2026-06-26 08:59:23.401092
\.


--
-- Data for Name: order_items; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) FROM stdin;
3	2	\N	Đầm suông linen	M / Be	629000.00	1
7	4	\N	Đầm xòe hoa nhí	M / Trắng	589000.00	1
8	5	\N	Áo khoác bomber	L / Xanh navy	649000.00	1
9	5	\N	Thắt lưng da bò	Free / Đen	299000.00	1
10	6	\N	Quần short kaki	30 / Be	259000.00	1
11	7	\N	Áo hoodie nỉ bông	XL / Đen	549000.00	1
13	8	\N	Khăn lụa vuông	Free / Trắng	249000.00	2
12	8	\N	Giày cao gót 7cm	39 / Đen	529000.00	1
5	3	\N	Giày sneaker trắng	40 / Trắng	659000.00	1
4	2	\N	Túi tote canvas	Free / Đen	199000.00	1
20	15	\N	aaaa	38 / Be	12000.00	1
22	17	\N	aaaa	38 / Be	12000.00	1
23	18	\N	aaaa	38 / Be	12000.00	1
24	19	\N	aaaa	38 / Be	12000.00	1
25	20	\N	Levents® Basic Slub Long Sleeve Relaxed Tee	38 / Be	360000.00	1
26	21	\N	Levents® Basic Slub Long Sleeve Relaxed Tee	38 / Be	360000.00	1
27	22	\N	Levents® Basic Slub Long Sleeve Relaxed Tee	38 / Be	360000.00	1
28	23	\N	Levents® Basic Slub Long Sleeve Relaxed Tee	38 / Be	360000.00	1
29	24	\N	Levents® Basic Slub Long Sleeve Relaxed Tee	38 / Be	360000.00	1
35	30	\N	Levents® Basic Slub Long Sleeve Relaxed Tee	38 / Be	360000.00	1
1	1	\N	Áo thun cotton basic	M / Trắng	159000.00	2
19	14	\N	Áo thun cotton basic	L / Xanh navy	159000.00	1
14	9	\N	Áo thun cotton basic	S / Trắng	159000.00	2
21	16	\N	Áo sơ mi linen dài tay	L / Xám	459000.00	1
36	31	\N	Áo sơ mi linen dài tay	L / Xám	459000.00	1
37	32	\N	Áo sơ mi linen dài tay	L / Xám	459000.00	1
30	25	\N	Áo sơ mi linen dài tay	L / Be	459000.00	1
6	3	\N	Áo polo pique	L / Đen	279000.00	2
17	12	\N	Áo polo pique	L / Đen	279000.00	1
31	26	\N	Áo polo pique	L / Xanh navy	279000.00	1
32	27	\N	Áo polo pique	L / Xanh navy	279000.00	1
16	11	\N	Áo hoodie nỉ bông	L / Xám	549000.00	1
18	13	\N	Áo hoodie nỉ bông	L / Xám	549000.00	1
34	29	\N	Áo hoodie nỉ bông	L / Xám	549000.00	1
33	28	\N	Áo len cổ tròn	L / Xanh navy	399000.00	1
2	1	\N	Quần jean slim fit	30 / Đen	399000.00	1
38	33	334	XL Logo Star Shark Semi-Oversized	1 / Trắng	589000.00	1
39	34	294	Seasonal Slub Semi-Oversized Tee	S / Xanh navy	649000.00	1
40	35	295	Seasonal Slub Semi-Oversized Tee	S / Đen	649000.00	1
41	36	292	Seasonal Slub Semi-Oversized Tee	M / Xanh navy	649000.00	1
42	37	439	Basic Slub Long Sleeve Relaxed Tee	38 / Be	360000.00	1
\.


--
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) FROM stdin;
1	2	717000.00	completed	cod	paid	12 Le Loi, Quan 1, TP.HCM	0911111111	Nguyen Van An	Giao gio hanh chinh	2026-05-18 20:04:48.90682
2	3	828000.00	completed	vnpay	paid	34 Cau Giay, Ha Noi	0922222222	Tran Thi Binh	\N	2026-05-19 20:04:48.90682
3	2	1217000.00	completed	cod	paid	12 Le Loi, Quan 1, TP.HCM	0911111111	Nguyen Van An	\N	2026-05-21 20:04:48.90682
4	3	589000.00	shipping	cod	unpaid	34 Cau Giay, Ha Noi	0922222222	Tran Thi Binh	Goi truoc khi giao	2026-05-23 20:04:48.90682
5	2	948000.00	confirmed	vnpay	paid	12 Le Loi, Quan 1, TP.HCM	0911111111	Nguyen Van An	\N	2026-05-25 20:04:48.90682
6	3	259000.00	pending	cod	unpaid	34 Cau Giay, Ha Noi	0922222222	Tran Thi Binh	\N	2026-05-27 20:04:48.90682
7	2	549000.00	cancelled	cod	unpaid	12 Le Loi, Quan 1, TP.HCM	0911111111	Nguyen Van An	Khach doi y	2026-05-28 20:04:48.90682
8	3	1027000.00	completed	vnpay	paid	34 Cau Giay, Ha Noi	0922222222	Tran Thi Binh	\N	2026-05-20 20:04:48.90682
9	2	318000.00	confirmed	sepay	paid	12 Le Loi Q1	0911111111	Nguyen Van An	test	2026-05-29 20:16:59.892114
11	\N	549000.00	pending	sepay	unpaid	Gia Nghĩa	0336008578	Mai Vũ Đại Vũ		2026-06-25 06:19:00.201693
12	\N	279000.00	pending	sepay	unpaid	1	2	1	1	2026-06-25 06:19:28.00291
13	\N	549000.00	pending	sepay	unpaid	2	2	1	1	2026-06-25 06:21:57.564829
14	\N	159000.00	pending	sepay	unpaid	1	2	1		2026-06-25 06:31:33.072846
15	1	12000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-25 06:40:12.332307
16	1	459000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-25 06:41:06.928163
17	1	42000.00	confirmed	sepay	paid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-25 06:54:03.400722
18	1	42000.00	confirmed	sepay	paid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-25 06:57:49.77988
19	1	42000.00	pending	cod	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-25 06:59:22.187986
20	1	390000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:19:48.875895
21	1	390000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:20:18.579926
22	1	390000.00	pending	cod	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:22:22.183283
23	1	390000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:22:47.595836
24	1	390000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:23:15.186415
25	1	489000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:25:15.137367
26	1	309000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:26:31.098459
27	1	309000.00	pending	cod	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:27:31.116074
28	1	429000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:28:04.313776
29	1	579000.00	pending	cod	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:29:45.453029
30	1	390000.00	pending	sepay	unpaid	Van phong ATELIER, Ha Noi	0900000001	Quan tri vien		2026-06-26 08:34:43.963067
31	1	489000.00	pending	cod	unpaid	adsdads, Phường Bình Hòa, Thành phố Hồ Chí Minh	0900000001	Quan tri vien		2026-06-26 08:51:08.397614
32	1	489000.00	pending	sepay	unpaid	1, Xã Ngọc Đường, Tỉnh Tuyên Quang	0336008578	Quan tri vien		2026-06-26 08:51:43.819859
33	1	619000.00	pending	sepay	unpaid	1234, Phường Ba Đình, Thành phố Hà Nội	0900000001	aaaa		2026-07-01 11:10:35.621318
34	\N	679000.00	pending	cod	unpaid	abcd, Xã Bảo Lâm, Tỉnh Cao Bằng	6853779	Mai Vũ Đại Vũ		2026-07-02 05:22:19.004503
35	\N	679000.00	pending	sepay	unpaid	Gia Nghĩa, Phường Giảng Võ, Thành phố Hà Nội	0336008578	Mai Vũ Đại Vũ		2026-07-02 06:25:47.491816
36	1	679000.00	pending	sepay	unpaid	kiikkkk, Phường Ngọc Hà, Thành phố Hà Nội	0900000001	Quan tri vien		2026-07-02 06:54:42.652802
37	5	390000.00	pending	cod	unpaid	Gia Nghĩa, Xã Phú Linh, Tỉnh Tuyên Quang	0336008578	Mai Vũ Đại Vũ		2026-07-02 07:18:24.733279
\.


--
-- Data for Name: payments; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.payments (id, order_id, provider, txn_ref, amount, status, raw_response, created_at) FROM stdin;
1	2	vnpay	TX1002	828000.00	success	\N	2026-05-29 20:04:48.90682
2	5	vnpay	TX1005	948000.00	success	\N	2026-05-29 20:04:48.90682
3	8	vnpay	TX1008	1027000.00	success	\N	2026-05-29 20:04:48.90682
4	9	sepay	FT123456	318000.00	paid	{"id": 999, "content": "DH9 thanh toan", "transferType": "in", "referenceCode": "FT123456", "transferAmount": 318000}	2026-05-29 20:18:15.900913
6	17	sepay	FT26176242820359	42000.00	paid	{"id": 64973047, "code": null, "content": "134865022846-DH17-CHUYEN TIEN-OQCH000EHvA1-MOMO134865022846MOMO", "gateway": "MBBank", "subAccount": null, "accumulated": 0, "description": "BankAPINotify 134865022846-DH17-CHUYEN TIEN-OQCH000EHvA1-MOMO134865022846MOMO", "transferType": "in", "accountNumber": "64579797977777", "referenceCode": "FT26176242820359", "transferAmount": 42000, "transactionDate": "2026-06-25 13:54:00"}	2026-06-25 06:54:49.625204
7	18	sepay	FT26176502693040	42000.00	paid	{"id": 64973513, "code": null, "content": "DH18", "gateway": "MBBank", "subAccount": null, "accumulated": 0, "description": "BankAPINotify DH18", "transferType": "in", "accountNumber": "64579797977777", "referenceCode": "FT26176502693040", "transferAmount": 42000, "transactionDate": "2026-06-25 13:58:00"}	2026-06-25 06:58:34.532797
\.


--
-- Data for Name: product_images; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.product_images (id, product_id, image_url, is_primary, sort_order) FROM stdin;
85	34	/uploads/3f9c629723986556.webp	t	0
87	34	/uploads/da589ccf6e34ffaa.webp	f	1
86	34	/uploads/97e2df04dec79633.webp	f	2
88	35	/uploads/c7c38679ca10b7da.webp	t	0
89	35	/uploads/12e186597ad53c20.webp	f	0
90	35	/uploads/2ee4454c592c4b07.webp	f	0
72	12	/uploads/edec8aca507d6b3d.webp	t	0
73	12	/uploads/e2dabd686eb1f1e4.webp	f	0
75	11	/uploads/928367dfea1dba36.webp	f	0
76	11	/uploads/fbf7a59a85337305.webp	f	0
74	11	/uploads/4dd3fe2be434c4be.webp	t	0
77	32	/uploads/dfc1596e916d77d5.webp	t	0
78	32	/uploads/0ecf54fcbd6606b3.webp	f	0
81	10	/uploads/f9031978d5e127a6.webp	t	0
80	10	/uploads/fd897f6955d99d37.webp	f	1
79	10	/uploads/358010f88d835bfa.webp	f	2
63	31	/uploads/2924be21042850b1.webp	t	0
64	31	/uploads/4d427cc035f0b338.webp	f	1
65	31	/uploads/d4b26c127bb31d2c.webp	f	2
66	31	/uploads/7ae81eb56a8c06a6.webp	f	3
67	18	/uploads/2fc9e9059eea1226.webp	f	0
68	17	/uploads/60ea37ec316e3aaa.webp	t	0
83	33	/uploads/726f9b9be62645e8.webp	f	1
82	33	/uploads/15e3ea15c00e1fea.webp	f	2
69	16	/uploads/917959e0c0238168.webp	t	0
84	33	/uploads/3458f12e7fdacfcc.webp	t	0
70	15	/uploads/13843acb8badc1d2.webp	t	0
71	14	/uploads/000db1aba3115984.webp	t	0
\.


--
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) FROM stdin;
32	2	Raw Denim Stitch Baggy Jeans	raw-denim-stitch-baggy-jeans-b75c	kh con gi de noi..	360000.00	\N		active	2026-07-01 10:33:07.507293	f
10	1	Travel Cities Long Sleeve Boxy Tee	travel-cities-long-sleeve-boxy-tee-1940	Quần kaki chino mềm, dễ phối, đủ màu cơ bản.	389000.00	329000.00		active	2026-05-29 20:04:48.90682	f
34	14	Stripe Classic Backpack	stripe-classic-backpack-eccb	dung ca? the gioi	789000.00	\N		active	2026-07-01 10:42:44.454721	f
35	4	Triple Star Small Wallet	triple-star-small-wallet-aa9c	muoi hai doi dep mat cung mot dem	363636000.00	\N		active	2026-07-01 10:44:57.635182	f
11	13	Joy Icon Classic Cap	joy-icon-classic-cap-9a77	hip to the hop	259000.00	\N		active	2026-05-29 20:04:48.90682	f
12	2	Emoji Pattern Shortpants	emoji-pattern-shortpants-d616	dep thi vl luon	299000.00	\N		active	2026-05-29 20:04:48.90682	f
31	1	Basic Slub Long Sleeve Relaxed Tee	levents-basic-slub-long-sleeve-relaxed-tee-ec7b		360000.00	\N		active	2026-06-25 06:37:42.441281	t
33	9	Classic Triple Star Zipper Hoodie Boxy	classic-triple-star-zipper-hoodie-boxy-8295	nong lam dung mua	6789000.00	\N		active	2026-07-01 10:39:46.755926	f
18	1	Seasonal Slub Semi-Oversized Tee	seasonal-slub-semi-oversized-tee-e10d	Đầm sơ mi dáng midi thanh lịch, có đai eo.	649000.00	\N	ATELIER	active	2026-05-29 20:04:48.90682	t
17	1	Furry Heart Semi-Oversized Tee	furry-heart-semi-oversized-tee-c8de	Maxi voan mỏng nhẹ, thoáng mát cho mùa hè.	559000.00	459000.00		active	2026-05-29 20:04:48.90682	t
16	1	Blink Blink Signature Logo Semi-Oversized Tee/ White	blink-blink-signature-logo-semi-oversized-tee-white-54cb	dep thi thoi roi	899000.00	\N	Bloom	active	2026-05-29 20:04:48.90682	t
15	1	XL Logo Star Shark Semi-Oversized	xl-logo-star-shark-semi-oversized-93d7	:D	689000.00	589000.00		active	2026-05-29 20:04:48.90682	t
14	1	Signature Logo Long Sleeve Boxy Tee	signature-logo-long-sleeve-boxy-tee-24da	dep thi vl	629000.00	\N	ATELIER	active	2026-05-29 20:04:48.90682	t
\.


--
-- Data for Name: reviews; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.reviews (id, product_id, user_id, rating, comment, created_at) FROM stdin;
4	14	3	5	Đầm xinh, vải mềm, sẽ ủng hộ tiếp.	2026-05-29 20:04:48.90682
7	15	2	4	Đầm hoa dễ thương, đúng mô tả.	2026-05-29 20:04:48.90682
9	35	1	5	dep	2026-07-01 11:01:08.053485
\.


--
-- Data for Name: styling_images; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.styling_images (id, styling_id, image_url, is_cover, sort_order) FROM stdin;
3	2	/uploads/912ef64f1028c413.webp	t	0
4	2	/uploads/cc23cdd428f6bc08.webp	f	0
5	2	/uploads/5803c8cf8a9dd7ec.webp	f	0
6	2	/uploads/cbdb9993142f2096.webp	f	0
10	7	/uploads/40477b16aa1f06b1.webp	t	0
12	7	/uploads/9ad18f8aaded4e42.webp	f	0
13	7	/uploads/633cb359948f4f50.webp	f	0
14	7	/uploads/62d7ef09eba205da.webp	f	0
16	8	/uploads/cec56b64830da252.webp	t	0
17	8	/uploads/1f48f5955b3cff33.webp	f	0
18	8	/uploads/1f9fe84db73a3b8c.webp	f	0
19	8	/uploads/f71af44dee78a131.webp	f	0
23	9	/uploads/46d58a92e32f82c8.webp	t	0
22	9	/uploads/cf0fe6a2dd8059cb.webp	f	1
21	9	/uploads/78c3c01353f9ffc5.webp	f	2
20	9	/uploads/39ccd92b4c10d011.webp	f	3
24	10	/uploads/4815833df4d90008.webp	t	0
25	10	/uploads/57bca55035a6bbc8.webp	f	0
26	10	/uploads/a31bf8c3dfe76879.webp	f	0
27	10	/uploads/502d8ae4de293a4a.webp	f	0
\.


--
-- Data for Name: stylings; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.stylings (id, title, sort_order, created_at, updated_at, model_info) FROM stdin;
7	Striped Oversized Jersey Tee	2	2026-07-01 09:16:51.302823	2026-07-01 09:23:13	Model:\r\n\r\nHeight 1m68, Weight 51kg\r\n\r\nWearing: Size 0
2	Logo Zipper Hoodie Boxy	1	2026-07-01 08:26:43.51202	2026-07-01 09:24:31	Model:\r\n\r\nHeight 1m68, Weight 51kg\r\nWearing: Size 3
8	XL Logo Boxy Sweater	3	2026-07-01 09:23:59.320396	2026-07-01 09:26:14	Model:\r\nHeight 1m68, Weight 51kg\r\nWearing: Size 3
9	Raglan Long Sleeve Boxy Tee	4	2026-07-01 09:35:50.169745	2026-07-01 09:36:48	
10	Striped Fur Knit Boxy Sweater	0	2026-07-01 09:44:33.603032	2026-07-01 09:44:33.603032	
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.users (id, name, email, password_hash, role, phone, address, created_at) FROM stdin;
1	Quan tri vien	admin@shop.test	$2y$10$vcbmDrTl/vr9eYeKveJLmeVigj6jg2FZugFP7Ve5lsERaeCleJHQi	admin	0900000001	Van phong ATELIER, Ha Noi	2026-05-29 20:04:48.90682
2	Nguyen Van An	an@shop.test	$2y$10$xGVQFDzQQwwaEft29cqpBexzIigt9TmHByPZdeQIRDyohQn.IlcHa	customer	0911111111	12 Le Loi, Quan 1, TP.HCM	2026-05-29 20:04:48.90682
3	Tran Thi Binh	binh@shop.test	$2y$10$xGVQFDzQQwwaEft29cqpBexzIigt9TmHByPZdeQIRDyohQn.IlcHa	customer	0922222222	34 Cau Giay, Ha Noi	2026-05-29 20:04:48.90682
4	123456	daivu9c@gmail.com	$2y$10$OSPcE/kmZhOfo5tqcvo7MeiDS6ufGqraXHy0nqHuqD1vYSbFCQePa	customer	12345678	\N	2026-06-25 07:06:27.196277
5	Mai Vũ Đại Vũ	daivu912.dev@gmail.com	$2y$10$mI/av4vXdqO4o5duT98ztOa1GbhVjOtpDQr3fKQbvzJYKQ5Vioc1u	customer	0336008578	\N	2026-07-02 07:01:31.019702
\.


--
-- Data for Name: variants; Type: TABLE DATA; Schema: public; Owner: shop
--

COPY public.variants (id, product_id, size, color, stock, sku) FROM stdin;
290	18	L	Xanh navy	10	18-l-xanh-navy
430	11	onesize	Trắng	5	11-onesize-trang
435	12	1	Xanh navy	12	12-1-xanh-navy
436	12	2	Xanh navy	17	12-2-xanh-navy
437	12	3	Xanh navy	15	12-3-xanh-navy
438	12	4	Xanh navy	20	12-4-xanh-navy
440	33	1	Đen	11	33-1-den
398	32	1	Đen	13	32-1-den
399	32	2	Đen	14	32-2-den
400	32	3	Đen	15	32-3-den
401	32	4	Đen	13	32-4-den
441	33	2	Đen	11	33-2-den
442	33	3	Đen	11	33-3-den
443	33	4	Đen	11	33-4-den
291	18	L	Đen	15	18-l-den
427	34	onesize	xanh	77	34-onesize-xanh
429	35	onesize	Đen	44	35-onesize-den
293	18	M	Đen	12	18-m-den
302	17	L	Be	3	17-l-be
303	17	L	Xám	8	17-l-xam
304	17	M	Be	25	17-m-be
305	17	M	Xám	5	17-m-xam
306	17	S	Be	22	17-s-be
307	17	S	Xám	27	17-s-xam
334	15	1	Trắng	18	15-1-trang
294	18	S	Xanh navy	3	18-s-xanh-navy
410	10	1	Trắng	7	10-1-trang
411	10	2	Trắng	9	10-2-trang
412	10	3	Trắng	10	10-3-trang
413	10	4	Trắng	14	10-4-trang
295	18	S	Đen	8	18-s-den
292	18	M	Xanh navy	6	18-m-xanh-navy
439	31	38	Be	9	31-38-be
320	16	1	Trắng	21	16-1-trang
321	16	2	Xanh navy	26	16-2-xanh-navy
322	16	3	Trắng	18	16-3-trang
323	16	4	Xanh navy	23	16-4-xanh-navy
324	16	2	Trắng	15	16-2-trang
325	16	3	Xanh navy	20	16-3-xanh-navy
335	15	2	Trắng	14	15-2-trang
336	15	3	Trắng	16	15-3-trang
337	15	4	Trắng	11	15-4-trang
356	14	1	Trắng	12	14-1-trang
357	14	2	Xám	7	14-2-xam
358	14	3	Trắng	9	14-3-trang
359	14	4	Xám	4	14-4-xam
\.


--
-- Name: cart_items_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.cart_items_id_seq', 34, true);


--
-- Name: carts_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.carts_id_seq', 15, true);


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

SELECT pg_catalog.setval('public.order_items_id_seq', 42, true);


--
-- Name: orders_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.orders_id_seq', 37, true);


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

SELECT pg_catalog.setval('public.users_id_seq', 5, true);


--
-- Name: variants_id_seq; Type: SEQUENCE SET; Schema: public; Owner: shop
--

SELECT pg_catalog.setval('public.variants_id_seq', 443, true);


--
-- Name: cart_items cart_items_cart_id_variant_id_key; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_cart_id_variant_id_key UNIQUE (cart_id, variant_id);


--
-- Name: cart_items cart_items_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_pkey PRIMARY KEY (id);


--
-- Name: carts carts_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_pkey PRIMARY KEY (id);


--
-- Name: categories categories_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_pkey PRIMARY KEY (id);


--
-- Name: categories categories_slug_key; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_slug_key UNIQUE (slug);


--
-- Name: coupons coupons_code_key; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.coupons
    ADD CONSTRAINT coupons_code_key UNIQUE (code);


--
-- Name: coupons coupons_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.coupons
    ADD CONSTRAINT coupons_pkey PRIMARY KEY (id);


--
-- Name: order_items order_items_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_pkey PRIMARY KEY (id);


--
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id);


--
-- Name: payments payments_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_pkey PRIMARY KEY (id);


--
-- Name: product_images product_images_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_pkey PRIMARY KEY (id);


--
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id);


--
-- Name: products products_slug_key; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_slug_key UNIQUE (slug);


--
-- Name: reviews reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_pkey PRIMARY KEY (id);


--
-- Name: reviews reviews_product_id_user_id_key; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_product_id_user_id_key UNIQUE (product_id, user_id);


--
-- Name: styling_images styling_images_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.styling_images
    ADD CONSTRAINT styling_images_pkey PRIMARY KEY (id);


--
-- Name: stylings stylings_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.stylings
    ADD CONSTRAINT stylings_pkey PRIMARY KEY (id);


--
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: variants variants_pkey; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.variants
    ADD CONSTRAINT variants_pkey PRIMARY KEY (id);


--
-- Name: variants variants_product_id_size_color_key; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.variants
    ADD CONSTRAINT variants_product_id_size_color_key UNIQUE (product_id, size, color);


--
-- Name: variants variants_sku_key; Type: CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.variants
    ADD CONSTRAINT variants_sku_key UNIQUE (sku);


--
-- Name: idx_carts_session; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_carts_session ON public.carts USING btree (session_id);


--
-- Name: idx_carts_user; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_carts_user ON public.carts USING btree (user_id);


--
-- Name: idx_images_product; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_images_product ON public.product_images USING btree (product_id);


--
-- Name: idx_order_items_order; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_order_items_order ON public.order_items USING btree (order_id);


--
-- Name: idx_orders_status; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_orders_status ON public.orders USING btree (status);


--
-- Name: idx_orders_user; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_orders_user ON public.orders USING btree (user_id);


--
-- Name: idx_payments_order; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_payments_order ON public.payments USING btree (order_id);


--
-- Name: idx_products_category; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_products_category ON public.products USING btree (category_id);


--
-- Name: idx_products_status; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_products_status ON public.products USING btree (status);


--
-- Name: idx_reviews_product; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_reviews_product ON public.reviews USING btree (product_id);


--
-- Name: idx_styling_images_styling; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_styling_images_styling ON public.styling_images USING btree (styling_id);


--
-- Name: idx_variants_product; Type: INDEX; Schema: public; Owner: shop
--

CREATE INDEX idx_variants_product ON public.variants USING btree (product_id);


--
-- Name: cart_items cart_items_cart_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_cart_id_fkey FOREIGN KEY (cart_id) REFERENCES public.carts(id) ON DELETE CASCADE;


--
-- Name: cart_items cart_items_variant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.cart_items
    ADD CONSTRAINT cart_items_variant_id_fkey FOREIGN KEY (variant_id) REFERENCES public.variants(id) ON DELETE CASCADE;


--
-- Name: carts carts_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: categories categories_parent_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.categories
    ADD CONSTRAINT categories_parent_id_fkey FOREIGN KEY (parent_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: order_items order_items_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: order_items order_items_variant_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.order_items
    ADD CONSTRAINT order_items_variant_id_fkey FOREIGN KEY (variant_id) REFERENCES public.variants(id) ON DELETE SET NULL;


--
-- Name: orders orders_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: payments payments_order_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.payments
    ADD CONSTRAINT payments_order_id_fkey FOREIGN KEY (order_id) REFERENCES public.orders(id) ON DELETE CASCADE;


--
-- Name: product_images product_images_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.product_images
    ADD CONSTRAINT product_images_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: products products_category_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_category_id_fkey FOREIGN KEY (category_id) REFERENCES public.categories(id) ON DELETE SET NULL;


--
-- Name: reviews reviews_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- Name: reviews reviews_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE CASCADE;


--
-- Name: styling_images styling_images_styling_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.styling_images
    ADD CONSTRAINT styling_images_styling_id_fkey FOREIGN KEY (styling_id) REFERENCES public.stylings(id) ON DELETE CASCADE;


--
-- Name: variants variants_product_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: shop
--

ALTER TABLE ONLY public.variants
    ADD CONSTRAINT variants_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id) ON DELETE CASCADE;


--
-- PostgreSQL database dump complete
--

\unrestrict ppBNZ0FQHtFwWD56dFNboCTpcAVxUz28sbphw8jOv0SD67iXxSY60TTDfQsgWmS

