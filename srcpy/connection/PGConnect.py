import sys
import logging
sys.path.append('/var/www/html/srcpy/config')
import psycopg2
from config import config

class DBase:
    params = config.config()
    def __init__(self):
        self.conn = psycopg2.connect(
            host=self.params['pgsql']['host']
            , database=self.params['pgsql']['database']
            , user=self.params['pgsql']['user']
            , password=self.params['pgsql']['password']
        )
        self.cur = self.conn.cursor()

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        if self.conn:
            self.conn.close()

    def query(self, sql):
        self.cur.execute(sql)
        return self.cur.fetchall()

    def rows(self):
        return self.cur.rowcount

    def insert(self, sql):
        self.cur.execute(sql)
        self.conn.commit()

    def explicitclose(self):
        if self.conn:
            self.conn.close()