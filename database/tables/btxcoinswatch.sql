-- Table: public.btxcoinswatch

-- DROP TABLE public.btxcoinswatch;

CREATE TABLE public.btxcoinswatch
(
    id integer NOT NULL DEFAULT nextval('btxcoinswatch_sq'::regclass),
    coin character varying(20) COLLATE pg_catalog."default",
    market character varying(20) COLLATE pg_catalog."default",
    isactive integer,
    startedwatch timestamp with time zone,
    endedwatch timestamp with time zone,
    ishodling integer,
    CONSTRAINT btxcoinswatch_pkey PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.btxcoinswatch
    OWNER to btx_user;