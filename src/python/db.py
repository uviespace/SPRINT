import sys
import MySQLdb
from settings import *

def db_open():
    settings = get_settings()
    db = MySQLdb.connect(
            host=settings["db_host"],
            user=settings["db_user"],
            passwd=settings["db_passwd"],
            db=settings["db_name"],
            charset='utf8')
    return db

def db_close(db):
    db.close()

def db_execute(cur, sql):
    cur.execute(sql)

def db_escape(s):
    return MySQLdb.escape_string(s).decode("utf-8")
