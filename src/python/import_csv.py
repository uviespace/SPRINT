import sys
import string
import csv
import os
import zip
import locale
import traceback

from db import *
from settings import *

def save_int(t):    
    s = "NULL"
    try:
        s = str(int(t))
    except ValueError:
        pass
    return s

def tag(s):
    s = s.replace("'", "''")
    return "'{0}'".format(s)

def conv(s):
    #if (s == 'NULL' or s == '\N'):
    if s == "NULL":
        return s
    else:
        return u"'{0}'".format(db_escape(s))

def import_const(idStandard, path):
    res = True
    try:
        db = db_open()
        cur = db.cursor()

        f = open(os.path.join(path, "Constants.csv"))
        reader = csv.reader(f, delimiter='|', quotechar='\"', skipinitialspace=True, escapechar="\\")
        next(reader)

        for row in reader:
            values = []
            values.append("NULL") # id
            values.append(str(idStandard))
            values.append(tag(row[0])) # domain
            values.append(tag(row[1])) # name
            values.append(tag(row[2])) # desc
            values.append(tag(row[3])) # value
            db_execute(cur, """INSERT INTO `Constants`(`id`, `idStandard`, `domain`, `name`, `desc`, `value`) VALUES({0});""".format(','.join(values)))

        db.commit()
    except Exception as e:
        print(e)
        db.rollback()
        res = False

    cur.close()
    db.close()
    return res

def import_dp(idStandard, import_kind, path):
    res = True
    try:
        db = db_open()

        # Get types 
        types = {}
        cur = db.cursor()        
        db_execute(cur, """
            SELECT t.id, CONCAT(t.domain, '/', t.name) from type t
            WHERE t.idStandard IS NULL OR t.idStandard={0}""".format(idStandard))
        for row in cur.fetchall():
            types[row[1]] = row[0]

        # kind
        kinds = {}
        kinds["predefined"] = "0"
        kinds["header"] = "1"
        kinds["body"] = "2"
        kinds["par"] = "3"
        kinds["var"] = "4"

        f = open(os.path.join(path, "Datapool.csv"))
        reader = csv.reader(f, delimiter='|', quotechar='\"', skipinitialspace=True, escapechar="\\")
        next(reader)

        if import_kind == "100":
            import_kind = ["3", "4"]

        for row in reader:
            for kind in import_kind:
                if row[0] in kinds and \
                   kinds[row[0]] == kind:
                    idType = types[row[9]] if row[9] in types else "NULL"
                    values = []
                    values.append("NULL") # id
                    values.append(str(idStandard))
                    values.append(str(idType))
                    values.append(kind)
                    values.append(tag(row[1])) # domain
                    values.append(tag(row[2])) # name
                    values.append(tag(row[3])) # shortDesc
                    values.append(tag(row[4])) # desc
                    values.append(tag(row[5])) # value
                    values.append(save_int(row[6])) # size
                    values.append(tag(row[7])) # unit
                    values.append(save_int(row[8])) # multiplicity
                    db_execute(cur, """INSERT INTO `Parameter`(`id`, `idStandard`, `idType`, `kind`, `domain`, `name`, `shortDesc`, `desc`, `value`, `size`, `unit`, `multiplicity`) VALUES({0});""".format(','.join(values)))

        db.commit()
    except Exception as e:
        print(e)
        db.rollback()
        res = False

    cur.close()
    db.close()
    return res

def import_std(idProject, path):
    changes = {}
    changes["Project"] = {}
    changes["Project"]["*"] = idProject
    changes["Process"] = {}
    changes["Process"]["*"] = 'NULL'
    res = True
    try:
        db = db_open()
        import_table(db, changes, path, "Standard", ["Project"], 1)
        import_table(db, changes, path, "Service", ["Standard"], 1)
        import_table(db, changes, path, "Constants", ["Standard"], 1)
        import_table(db, changes, path, "Type", ["Standard"], 1)
        import_table(db, changes, path, "Enumeration", ["Type"], 1)
        import_table(db, changes, path, "Packet", ["Standard", "Packet", "Process"], 1)    
        import_table(db, changes, path, "Parameter", ["Standard", "Type"], 1)    
        import_table(db, changes, path, "ParameterSequence", ["Standard", "Parameter", "Packet"], 1)   
        db.commit()        
    except:
        db.rollback()
        res = False
    db_close(db)   
    return res 
            

def import_proj(idUser, path):
    changes = {}
    changes["Component"] = {}
    res = True
    try:
        db = db_open()
        import_table(db, changes, path, "Project", [], 1)
        import_table(db, changes, path, "Process", ["Project"], 1)
        import_table(db, changes, path, "Standard", ["Project"], 1)
        import_table(db, changes, path, "Service", ["Standard"], 1)
        import_table(db, changes, path, "Constants", ["Standard"], 1)
        import_table(db, changes, path, "Type", ["Standard"], 1)
        import_table(db, changes, path, "Enumeration", ["Type"], 1)
        import_table(db, changes, path, "Packet", ["Standard", "Packet", "Process"], 1)    
        import_table(db, changes, path, "Parameter", ["Standard", "Type"], 1)    
        import_table(db, changes, path, "ParameterSequence", ["Standard", "Parameter", "Packet"], 1)   
        import_table(db, changes, path, "Application", ["Project"], 1)
        import_table(db, changes, path, "ApplicationComponent", ["Application", "Component"], 0)
        import_table(db, changes, path, "ApplicationStandard", ["Application", "Standard"], 0)
        import_table(db, changes, path, "ApplicationPacket", ["Application", "Standard", "Packet"], 0)
        import_table(db, changes, path, "StandardStandard", ["Standard", "Standard"], 0)        
        cur = db.cursor()
        idProject = list(changes["Project"].values())[0]
        idRole = 2 # Role is fixed to 2=Maintainer        
        db_execute(cur, """
            UPDATE project
            SET isPublic=False
            WHERE id={0};""".format(idProject))
        db_execute(cur, """
            INSERT INTO userproject (idUser, idProject, idRole)
            VALUES({0}, {1}, {2});"""
            .format(idUser, idProject, idRole))
        cur.close()        
        db.commit()
        db_close(db)
    except:
        db.rollback()
        res = False
        db_close(db)
    return res

def import_table(db, changes, path, table_name, parent_table_names, num_pk):
    file_name = "{0}\{1}.csv".format(path, table_name)
    #print("file_name: ", file_name)

    settings = get_settings()
    db_name = settings["db_name"]
    #print("db_name: ", db_name)

    if (not os.path.isfile(file_name)):
        return

    f = open(file_name)
    reader = csv.reader(f, delimiter='|', quotechar='\"', skipinitialspace=True, escapechar="\\")

    changes[table_name] = {}
    num_fk = len(parent_table_names)

    col_names = []
    for col_name in next(reader):
        col_names.append("`{0}`".format(col_name))
    num_cols = len(col_names)

    cur = db.cursor()
    for row in reader:
        values = []
        for i in range(num_pk):
            values.append('NULL')
        for i in range(num_fk):
            table_changes = changes[parent_table_names[i]]
            key_old = row[i+num_pk]
            if key_old in table_changes:
                values.append(table_changes[key_old])
            elif '*' in table_changes:
                values.append(table_changes['*'])
            else:
                values.append(key_old)
        for i in range(num_cols-num_pk-num_fk):
            values.append(conv(row[i+num_pk+num_fk]))

        #print(u"""INSERT INTO `{0}` ({2}) VALUES ({1});""".format(table_name, ','.join(values), ','.join(col_names)))
        db_execute(cur, u"""INSERT INTO `{0}` ({2}) VALUES ({1});""".format(table_name, ','.join(values), ','.join(col_names)))

        if num_pk > 0:
            # TODO: call cur.lastrowid
            '''
            db_execute(cur, u"""SELECT last_insert_id() FROM `{0}`;""".format(table_name))
            lastid = len(cur.fetchall())
            '''
            #print(u"""SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '{0}' AND table_schema = '{1}';""".format(table_name, db_name))
            db_execute(cur, u"""SELECT `auto_increment` FROM INFORMATION_SCHEMA.TABLES WHERE table_name = '{0}' AND table_schema = '{1}';""".format(table_name, db_name))
            for sqlres in cur.fetchall():
                lastid = int(sqlres[0]) - 1
            print("table: ", table_name, "lastid: ", lastid)
            #print(settings["db_name"])
            changes[table_name][row[0]] = str(lastid)

    cur.close()
    f.close()    

def help():
    return "Usage: python import_csv.py {project {user_id} | standard {project_id} | datapool {standard_id} {kind} | constants {standard_id}} {path}"

if __name__ == '__main__':
    res = False

    locale.setlocale(locale.LC_ALL, 'en_US.UTF-8')   # was 'en_US.UTF-8'

    try:
        if (len(sys.argv) == 4 and sys.argv[1] == "project"):
            id = sys.argv[2]
            path = sys.argv[3]
            res = import_proj(id, path)
        elif (len(sys.argv) == 4 and sys.argv[1] == "standard"):
            id = sys.argv[2]
            path = sys.argv[3]
            res = import_std(id, path)
        elif (len(sys.argv) == 5 and sys.argv[1] == "datapool"):
            id = sys.argv[2]
            kind = sys.argv[3]
            path = sys.argv[4]
            res = import_dp(id, kind, path)
        elif (len(sys.argv) == 4 and sys.argv[1] == "constants"):
            id = sys.argv[2]
            path = sys.argv[3]
            res = import_const(id, path)
        else:
            print(help())
    except Exception as e:
        print(e)
        print(traceback.format_exc())
        
    if (res):
        print("Import successful")
    else:
        print("Import failed")
