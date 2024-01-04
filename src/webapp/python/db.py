
try:

    import pymysql.cursors
    from settings import *

    def db_open():
        settings = get_settings()
        db = pymysql.connect(host=settings["db_host"],
                             user=settings["db_user"],
                             password=settings["db_passwd"],
                             db=settings["db_name"],
                             charset="utf8")
        return db

    def db_close(db):
        db.close()

    def db_execute(cur, sql):
        cur.execute(sql);

    def db_escape(s):
        return bytes(s, "utf-8").decode("unicode_escape")
        

    # raise ModuleNotFoundError  # Python 2.7: raise ImportError; Python 3.x: raise ModuleNotFoundError
    # import MySQLdb
    # from settings import *

    # def db_open():
    #     settings = get_settings()
    #     db = MySQLdb.connect(
    #         host=settings["db_host"],
    #         user=settings["db_user"],
    #         passwd=settings["db_passwd"],
    #         db=settings["db_name"],
    #         charset='utf8')
    #     return db

    # def db_close(db):
    #     db.close()

    # def db_execute(cur, sql):
    #     cur.execute(sql)

    # def db_escape(s):
    #     return MySQLdb.escape_string(s).decode("utf-8")

except ModuleNotFoundError:  # Python 2.7: except ImportError:; Python 3.x: except ModuleNotFoundError:
    #print('MySQLdb not found, using PHP workaround.')
    import json
    import requests
    from collections import OrderedDict

    def db_open():
        return Cursor()

    def db_close(db):
        return

    def db_execute(cur, sql):
        # print(sql)
        cur.execute(sql)

    def db_escape(s):
        decoded_string = bytes(s, "utf-8").decode("unicode_escape")  # python3
        #decoded_string = s.decode('string_escape')  # python2
        #raise NotImplementedError
        return decoded_string

    class Cursor:
        def __init__(self):
            self.data = None
            self.url = 'http://localhost/dbeditor/api/db_sqlquery.php'
        def execute(self, sql):
            sql = str(sql)
            printIt = False
            #if "ELECT s.idApplication, s.idComponent, s.setting, c.id, c.shortName, c.name FROM applicationcomponent s" in sql:
            #    printIt = True
            if printIt: print("sql: ", sql)
            result1 = requests.post(self.url, data={'sql': sql})
            if printIt: print("result1: ", result1.content)
            result = result1.text
            if printIt: print("result: ", result)
            try:
                '''json_result = json.loads(result, object_pairs_hook=OrderedDict)  # !!! you can't guarantee the order.
                print("json_result: ", json_result)
                json_data = json.loads(result, object_pairs_hook=OrderedDict)['data']'''
                json_result = json.loads(result)  # !!! you can't guarantee the order.
                if printIt: print("json_result: ", json_result)
                json_data = json.loads(result)['data']
                if printIt: print("json_data: ", json_data)
                # for dd in json_data:
                #    print("value: ", dd.values())
                data = [tuple(d.values()) for d in json_data]
                self.data = data
            except Exception as err:
                print(err)
                self.data = None

        def fetchall(self):
            return self.data

        def fetchone(self):
            if self.data is not None:
                return self.data[0]
            else:
                return

        def cursor(self):
            return self

        def commit(self):
            return self

        def rollback(self):
            return self

        def lastrowid(self):
            '''curid = self.cursor()
            db_execute(curid, u"""SELECT last_insert_id() FROM `{0}`;""".format(table_name))
            lastid = 0
            for rowid in curid.fetchall():
                print("row: ", rowid[0])
                lastid += 1
            curid.close()
            return lastid'''
            return 1

        def close(self):
            self.data = None
            return self
