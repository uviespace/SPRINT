#!/usr/bin/python

import sys
import string
import csv
import locale
import traceback

from db import *
from common import *

def new_file(path, filename):
    f = open("{0}/{1}".format(path, filename), "w", encoding="utf-8")
    return f

def close_file(f):
    f.close    

def quote(s):
    if s is None:
        s = u"NULL"
    else:
        #if not isinstance(s, unicode):   # Python 2.x
        #    s = unicode(s)
        if not isinstance(s, str):
            s = str(s)
    
    s = s.replace("\\", "\\\\")
    s = s.replace("\"", "\\\"")    
    if ('\r' in s) or ('\n' in s):
        s = u"\"{0}\"".format(s)
    return s

def export_data(path, filename, cur, sql, columns=None):
    db_execute(cur, sql)
    f = new_file(path, filename)
    if columns != None:
        f.write(u"{0}\n".format(u"|".join(columns)))
    for row in cur.fetchall():
        u = u"{0}\n".format("|".join([quote(item) for item in row]))
        #f.write(u.encode("utf8"))   # Python 2.x 
        #f.write(str(u.encode("utf8")))
        f.write(u)    
    close_file(f)

def get_table_columns(db, table_name):
    cur = db.cursor()
    db_execute(cur, """
        SELECT DISTINCT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_NAME='{0}'""".format(table_name.lower()))  # TODO: WHY get column names two times without DISTINCT?
    col_names = []
    for col_name in cur.fetchall():
        #print("COL: ", col_name[0])  # TODO: WHY two times?
        col_names.append("{0}".format(col_name[0]))
    return col_names

def export_const(path, standard_id):
    db = db_open()
    cur = db.cursor()
    export_data(path, "Constants.csv", cur, """
        SELECT c.domain, c.name, c.desc, c.value FROM constants c
        WHERE c.idStandard in (
            SELECT id FROM standard s
            WHERE s.id={0}
        )""".format(standard_id),
        ["Domain", "Name", "Desc", "Value"])
    return gen_zip(path)

def export_datapool(path, standard_id, kind):    
    db = db_open()
    cur = db.cursor()
    kind_a = kind
    kind_b = kind
    kind_c = kind
    kind_d = kind
    if kind == "100":
        # Special: Export both, datapool parameters and variables
        kind_a = 3  # PAR
        kind_b = 4  # VAR
        kind_c = 5  # PAR IMP
        kind_d = 6  # VAR IMP

    export_data(path, "Datapool.csv", cur, """
        SELECT if(p.kind=0, "predefined", if(p.kind=1, "header", if(p.kind=2, "body", if(p.kind=3 or p.kind=5, "par", "var")))) as kind, p.domain, p.name, p.shortDesc, p.`desc`, p.value, p.size, p.unit, p.multiplicity, CONCAT(t.domain, '/', t.name) FROM parameter p
        JOIN type t ON p.idType=t.id
        WHERE (p.kind={1} OR p.kind={2} OR p.kind={3} OR p.kind={4}) AND p.idStandard={0}
        ORDER BY p.kind, p.domain, p.name
        """.format(standard_id, kind_a, kind_b, kind_c, kind_d),
        ["Kind", "Domain", "Name", "ShortDesc", "Desc", "Value", "Size", "Unit", "Multiplicity", "Type"])
    return gen_zip(path)

def export_std(path, id):
    db = db_open()
    cur = db.cursor()    
    export_data(path, "Standard.csv", cur, """
        SELECT * FROM standard s
        WHERE s.id={0}""".format(id),
        get_table_columns(db, "Standard"))
    export_data(path, "Service.csv", cur, """
        SELECT * FROM service s
        WHERE s.idStandard in (
            SELECT id FROM standard s
            WHERE s.id={0}
        )""".format(id),
        get_table_columns(db, "Service"))
    export_data(path, "Packet.csv", cur, """
        SELECT * FROM packet p
        WHERE p.idStandard in (
            SELECT id FROM standard s
            WHERE s.id={0}
        )""".format(id),
        get_table_columns(db, "Packet"))
    export_data(path, "ParameterSequence.csv", cur, """
        SELECT * FROM parametersequence ps
        WHERE ps.idStandard in (
            SELECT id FROM standard s
            WHERE s.id={0}
        )""".format(id),
        get_table_columns(db, "ParameterSequence"))
    export_data(path, "Parameter.csv", cur, """
        SELECT * FROM parameter p
        WHERE p.idStandard in (
            SELECT id FROM standard s
            WHERE s.id={0}
        )""".format(id),
        get_table_columns(db, "Parameter"))
    export_data(path, "Type.csv", cur, """
        SELECT * FROM type t
        WHERE t.idStandard in (
            SELECT id FROM standard s
            WHERE s.id={0}
        )""".format(id),
        get_table_columns(db, "Type"))
    export_data(path, "Enumeration.csv", cur, """
        SELECT * FROM enumeration e
        WHERE e.idType in (
            SELECT id FROM type t
            WHERE t.idStandard in (
                SELECT id FROM standard s
                WHERE s.id={0}
            )
        )""".format(id),
        get_table_columns(db, "Enumeration"))
    export_data(path, "Constants.csv", cur, """
        SELECT * FROM constants c
        WHERE c.idStandard in (
            SELECT id FROM standard s
            WHERE s.id={0}
        )""".format(id),
        get_table_columns(db, "Constants"))
    db_close(db)    
    return gen_zip(path)

def export_proj(path, id):
    db = db_open()
    cur = db.cursor()

    export_data(path, "Project.csv", cur, """
        SELECT * FROM project p
        WHERE p.id={0}""".format(id),
        get_table_columns(db, "Project"))
    export_data(path, "Process.csv", cur, """
        SELECT * FROM process p
        WHERE p.idProject={0}""".format(id),
        get_table_columns(db, "Process"))
    export_data(path, "Application.csv", cur, """
        SELECT * FROM application a
        WHERE a.idProject={0}""".format(id),
        get_table_columns(db, "Application"))
    export_data(path, "ApplicationComponent.csv", cur, """
        SELECT * FROM applicationcomponent ac
        WHERE ac.idApplication in (
            SELECT id FROM application a
            WHERE a.idProject={0}
        )""".format(id),
        get_table_columns(db, "ApplicationComponent"))        
    export_data(path, "ApplicationStandard.csv", cur, """
        SELECT * FROM applicationstandard s
        WHERE s.idApplication in (
            SELECT id FROM application a
            WHERE a.idProject={0}
        )""".format(id),
        get_table_columns(db, "ApplicationStandard"))        
    export_data(path, "ApplicationPacket.csv", cur, """
        SELECT * FROM applicationpacket ap
        WHERE ap.idApplication in (
            SELECT id FROM application a
            WHERE a.idProject={0}
        )""".format(id),
        get_table_columns(db, "ApplicationPacket"))
    export_data(path, "Standard.csv", cur, """
        SELECT * FROM standard s
        WHERE s.idProject={0}""".format(id),
        get_table_columns(db, "Standard"))
    export_data(path, "standardstandard.csv", cur, """
        SELECT * FROM standardstandard s
        WHERE s.idStandardParent in (
            SELECT id FROM standard s
            WHERE s.idProject={0}) AND
          s.idStandardChild in (
            SELECT id FROM standard s
            WHERE s.idProject={0}
        )""".format(id),
        get_table_columns(db, "StandardStandard"))
    export_data(path, "Service.csv", cur, """
        SELECT * FROM service s
        WHERE s.idStandard in (
            SELECT id FROM standard s
            WHERE s.idProject={0}
        )""".format(id),
        get_table_columns(db, "Service"))
    export_data(path, "Packet.csv", cur, """
        SELECT * FROM packet p
        WHERE p.idStandard in (
            SELECT id FROM standard s
            WHERE s.idProject={0}
        )
        ORDER BY idParent ASC""".format(id),
        get_table_columns(db, "Packet"))
    export_data(path, "ParameterSequence.csv", cur, """
        SELECT * FROM parametersequence ps
        WHERE ps.idStandard in (
            SELECT id FROM standard s
            WHERE s.idProject={0}
        )""".format(id),
        get_table_columns(db, "ParameterSequence"))
    export_data(path, "Parameter.csv", cur, """
        SELECT * FROM parameter p
        WHERE p.idStandard in (
            SELECT id FROM standard s
            WHERE s.idProject={0}
        )""".format(id),
        get_table_columns(db, "Parameter"))
    export_data(path, "Type.csv", cur, """
        SELECT * FROM type t
        WHERE t.idStandard in (
            SELECT id FROM standard s
            WHERE s.idProject={0}
        )""".format(id),
        get_table_columns(db, "Type"))
    export_data(path, "enumeration.csv", cur, """
        SELECT * FROM enumeration e
        WHERE e.idType in (
            SELECT id FROM type t
            WHERE t.idStandard in (
                SELECT id FROM standard s
                WHERE s.idProject={0}
            )
        )""".format(id),
        get_table_columns(db, "Enumeration"))
    export_data(path, "Constants.csv", cur, """
        SELECT * FROM constants c
        WHERE c.idStandard in (
            SELECT id FROM standard s
            WHERE s.idProject={0}
        )""".format(id),
        get_table_columns(db, "Constants"))
    db_close(db)
    return gen_zip(path)

def help():
    return "Usage: python export_csv.py {project {project_id} | standard {standard_id}} | datapool {standard_id} {kind} | constants {standard_id}"

if __name__ == '__main__':
    res = help()

    locale.setlocale(locale.LC_ALL, '')  # was 'en_US.UTF-8'

    try:
        if (len(sys.argv) == 3 and sys.argv[1] == "project"):
            id = sys.argv[2]
            res = export_proj(create_path(), id)   
        elif (len(sys.argv) == 3 and sys.argv[1] == "standard"):
            id = sys.argv[2]
            res = export_std(create_path(), id)
        elif (len(sys.argv) == 4 and sys.argv[1] == "datapool"):
            id = sys.argv[2]
            kind = sys.argv[3]
            res = export_datapool(create_path(), id, kind)
        elif (len(sys.argv) == 3 and sys.argv[1] == "constants"):
            id = sys.argv[2]
            res = export_const(create_path(), id)
    except Exception as e:
        print("Something went wrong...")
        print(traceback.format_exc())

    print(res)
