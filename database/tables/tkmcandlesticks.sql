-- Table: public.tkmcandlesticks

-- DROP TABLE public.tkmcandlesticks;

CREATE TABLE public.tkmcandlesticks
(
    id integer NOT NULL DEFAULT nextval('tkmcandlesticks_sq'::regclass),
    coin character varying(15) COLLATE pg_catalog."default",
    market character varying(50) COLLATE pg_catalog."default",
    opener double precision,
    closer double precision,
    high double precision,
    low double precision,
    exchange character varying(255) COLLATE pg_catalog."default",
    timestampintervallow integer,
    timestampintervalhigh integer,
    createdon integer,
    CONSTRAINT tkmcandlesticks_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.tkmcandlesticks
    OWNER to btx_user;