-- Table: public.btxmarkethistory

-- DROP TABLE public.btxmarkethistory;

CREATE TABLE public.btxmarkethistory
(
    id integer NOT NULL DEFAULT nextval('btxmarkethistory_sq'::regclass),
    coin character varying(20) COLLATE pg_catalog."default",
    market character varying(20) COLLATE pg_catalog."default",
    volume double precision,
    value double precision,
    "usdValue" double precision,
    high double precision,
    low double precision,
    "lastSell" double precision,
    "currentBid" double precision,
    "openBuyOrders" integer,
    "openSellOrders" integer,
    "btxTimestamp" timestamp with time zone,
    "timestamp" timestamp with time zone,
    CONSTRAINT btx_id_pk PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.btxmarkethistory
    OWNER to bittrex_user;