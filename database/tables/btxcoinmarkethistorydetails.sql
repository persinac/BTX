-- Table: public.btxcoinmarkethistorydetails

-- DROP TABLE public.btxcoinmarkethistorydetails;

CREATE TABLE public.btxcoinmarkethistorydetails
(
    id integer NOT NULL DEFAULT nextval('btxcoinmarkethistorydetails_sq'::regclass),
    btxid integer,
    coin character varying(20) COLLATE pg_catalog."default",
    market character varying(20) COLLATE pg_catalog."default",
    quantity double precision,
    value double precision,
    total double precision,
    filltype character varying(50) COLLATE pg_catalog."default",
    ordertype character varying(50) COLLATE pg_catalog."default",
    btxtimestamp integer,
    CONSTRAINT btxcoinmarkethistorydetails_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.btxcoinmarkethistorydetails
    OWNER to btx_user;