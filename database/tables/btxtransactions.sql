-- Table: public.btxtransactions

-- DROP TABLE public.btxtransactions;

CREATE TABLE public.btxtransactions
(
    id integer NOT NULL DEFAULT nextval('btxtransactions_sq'::regclass),
    coin character varying(20) COLLATE pg_catalog."default",
    market character varying(20) COLLATE pg_catalog."default",
    quantity double precision,
    quantityremaining double precision,
    commission double precision,
    value double precision,
    priceperunit double precision,
    orderuuid character varying(255) COLLATE pg_catalog."default",
    simulationid integer,
    btxtimestamp timestamp with time zone,
    "timestamp" timestamp with time zone,
    CONSTRAINT btxtransactions_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.btxtransactions
    OWNER to bittrex_user;