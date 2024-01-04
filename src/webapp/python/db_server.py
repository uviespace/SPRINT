import sys
import MySQLdb

def db_open():
    db = MySQLdb.connect(
            host="db_host",
            user="db_user",
            passwd="db_passwd",
            db="db_name")
    return db

def db_close(db):
    db.close()

def db_execute(cur, sql):
    cur.execute(sql)
