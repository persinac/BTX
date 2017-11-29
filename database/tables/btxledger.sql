-- Table: public."btxledger"

-- DROP TABLE public."btxledger";

CREATE TABLE public."btxledger"
(
    "BTC_DOLLAR_VALUE" double precision,
    "BTC_VALUE" double precision,
    "BTX_LEDGER_ID" integer NOT NULL DEFAULT nextval('btx_ledger_id_sq'::regclass),
    "COIN_ID" integer,
    "TYPE_ID" integer,
    "TIMESTAMP" timestamp with time zone,
    "ACCT_ID" integer,
    CONSTRAINT "BTX_LEDGER_pkey" PRIMARY KEY ("BTX_LEDGER_ID")
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;

ALTER TABLE public."btxledger"
    OWNER to btx_user;

GRANT ALL ON TABLE public."btxledger" TO btx_user;