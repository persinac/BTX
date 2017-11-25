-- Table: public."BTX_LEDGER"

-- DROP TABLE public."BTX_LEDGER";

CREATE TABLE public."BTX_LEDGER"
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

ALTER TABLE public."BTX_LEDGER"
    OWNER to postgres;

GRANT ALL ON TABLE public."BTX_LEDGER" TO postgres;

GRANT ALL ON TABLE public."BTX_LEDGER" TO bittrex_user;