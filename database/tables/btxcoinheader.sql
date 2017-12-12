-- Table: public.btxcoinheader

-- DROP TABLE public.btxcoinheader;

CREATE TABLE public.btxcoinheader
(
    id integer NOT NULL DEFAULT nextval('btxcoinheader_sq'::regclass),
    coin character varying(20) COLLATE pg_catalog."default",
    market character varying(20) COLLATE pg_catalog."default",
    coinname double precision,
    mintradesize double precision,
    txfee double precision,
    minconfirmation integer,
    isactive integer,
    btxtimestamp integer,
    "timestamp" integer,
    CONSTRAINT btx_coinheader_id_pk PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.btxcoinheader
    OWNER to btx_user;