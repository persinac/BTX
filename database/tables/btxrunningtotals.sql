-- Table: public.btxrunningtotals

-- DROP TABLE public.btxrunningtotals;

CREATE TABLE public.btxrunningtotals
(
    id integer NOT NULL DEFAULT nextval('btxrunningtotals_sq'::regclass),
    coin character varying(20) COLLATE pg_catalog."default",
    market character varying(20) COLLATE pg_catalog."default",
    usdpershare double precision,
    percofportfolio double precision,
    quantity double precision,
    valuepershare double precision,
    totalvalue double precision,
    CONSTRAINT btxrunningtotals_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.btxrunningtotals
    OWNER to btx_user;