CREATE TABLE public.btxhistory
(
    id integer NOT NULL DEFAULT nextval('btxhistory_sq'::regclass),
    coin character varying(20) COLLATE pg_catalog."default",
    market character varying(20) COLLATE pg_catalog."default",
    description character varying(200) COLLATE pg_catalog."default",
    history_ref_key integer,
    "timestamp" integer,
    CONSTRAINT btx_history_id_pk PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.btxhistory
    OWNER to btx_user;
