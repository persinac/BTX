CREATE TABLE public.btxhistoryref
(
    id integer NOT NULL DEFAULT nextval('btxhistoryref_sq'::regclass),
    type character varying(20) COLLATE pg_catalog."default",
    subtype character varying(20) COLLATE pg_catalog."default",
    name character varying(200) COLLATE pg_catalog."default",
    isactive integer,
    CONSTRAINT btx_historyref_id_pk PRIMARY KEY (id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public.btxhistoryref
    OWNER to btx_user;
