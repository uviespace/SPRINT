#!/usr/bin/python
#
# This script offers functions to build the data structures used by other generator
# scripts.  
# The data structures take the form of either a list or a dictionary. In some
# cases, the data structure consists of a dictionary with two entries:
# - "list": the set of objects as a list
# - "hash": the set of objects as a dictionary with the object identifier as key
#
# A standard can extend another standard. For set of objects which are attached
# to a standard, the return data structure holds the objects in a standard and
# and in its parent standard. The field "ownerStandardId" can be used to discriminate
# between items which are attached to the standard itself or to its parent. This
# mechanism is used for the following kinds of objects:
# - parameters
# - constants
# - types
# - packets
# - services
#
import sys
# import MySQLdb
from db import *
import json
from collections import OrderedDict
import re
import traceback


def save_str(s):
    if s is not None:
        return s
    return ""


def save_int(s):
    try:
        return int(s.replace(" ", ""), 0)
    except ValueError:
        return 0


def is_int(s):
    try:
        int(s)
        return True
    except ValueError:
        return False


def get_data(idProject):
    project = None
    db = db_open()
    try:
        project = get_project(db, idProject)

        # Merging the standards together
        for standard in project["standards"]["list"]:
            for standard_base in standard["extends"]:
                if len(standard["headers"]["TC"]) == 0:
                    standard["headers"]["TC"] = standard_base["headers"]["TC"]
                if len(standard["headers"]["TM"]) == 0:
                    standard["headers"]["TM"] = standard_base["headers"]["TM"]

        # Adding redundancy
        for standard in project["standards"]["list"]:
            standard["headers"]["TC_length"] = get_param_sequence_length(standard["headers"]["TC"])
            standard["headers"]["TM_length"] = get_param_sequence_length(standard["headers"]["TM"])

            standard["packets"]["TC"] = {}
            standard["packets"]["TC"]["list"] = []
            standard["packets"]["TC"]["params"] = {}
            standard["packets"]["TM"] = {}
            standard["packets"]["TM"]["list"] = []
            standard["packets"]["TM"]["params"] = {}

            for packet in standard["packets"]["list"]:

                # Adding header length
                if packet["kind"] == "TC":
                    packet["_header_length"] = standard["headers"]["TC_length"]
                else:
                    packet["_header_length"] = standard["headers"]["TM_length"]

                # Adding this packet to the standard's TC/TM list
                standard["packets"][packet["kind"]]["list"].append(packet)

                # Creating the link between a packet and its service
                if packet["type"] is not None:
                    packet["service"] = standard["services"]["hash"][packet["type"]]
                    packet["service"]["packets"].append(packet)
                else:
                    packet["service"] = None

                # 
                param_list = {}
                for param_i in packet["body"]:
                    param = param_i["param"]
                    param_list[param["id"]] = param
                for derived in packet["derivations"]["list"]:
                    for param_i in derived["body"]:
                        param = param_i["param"]
                        param_list[param["id"]] = param
                # standard["packets"][packet["kind"]]["params"] = param_list
                standard["packets"][packet["kind"]]["params"].update(param_list)

                #
                if len(packet["derivations"]["list"]) > 0:
                    for derived in packet["derivations"]["list"]:
                        derived["_disc"] = None
                        if is_int(derived["disc"]):
                            derived["_disc"] = int(derived["disc"])
                        else:
                            param_i = packet["_param_derived"]
                            enum = None
                            if param_i != None:
                                t = param_i["param"]["type"]
                                if t["setting"] != None:
                                    for enum in t["setting"]["Enumerations"]:
                                        if enum["Name"] == derived["disc"]:
                                            derived["_disc"] = int(enum["_dec"])
                                            break

        '''
        # #71: Reuse Hashtag in Specification Definition Table
        def copy_spec_field_no_disc(spec, field, type_, subtype):
            for spec_ in spec["app"]["specifications"]:
                if spec_ != spec:
                    packet = spec_["packet"]
                    if "parent" not in packet and \
                        packet["type"] == type_ and \
                        packet["subtype"] == subtype:

                        spec[field] = spec_[field]
                        return

        def copy_spec_field_disc(spec, field, type_, subtype, disc):
            for spec_ in spec["app"]["specifications"]:
                if spec_ != spec:
                    packet = spec_["packet"]
                    if "parent" in packet and \
                        packet["parent"]["type"] == type_ and \
                        packet["parent"]["subtype"] == subtype and \
                        packet["disc"] == disc:

                        spec[field] = spec_[field]
                        return

        def check_spec_field_no_disc(spec, kind, field):
            pattern = "#T[MC]\(\s*\d+\s*,\s*\d+\s*\)"
            text = spec[field]
            m = re.search(pattern, text)
            if m != None:
                idx_comma = text.find(",", m.start(), m.end())
                type_ = int(text[m.start()+4:idx_comma])
                subtype = int(text[idx_comma+1:m.end()-1])
                copy_spec_field_no_disc(spec, field, type_, subtype)

        def check_spec_field_disc(spec, kind, field):
            pattern = "#T[MC]\(\s*\d+\s*,\s*\d+\s*,\s*\w+\s*\)"
            text = spec[field]
            m = re.search(pattern, text)
            if m != None:
                idx_comma_1 = text.find(",", m.start(), m.end())
                idx_comma_2 = text.find(",", idx_comma_1+1, m.end())
                type_ = int(text[m.start()+4:idx_comma_1].strip())
                subtype = int(text[idx_comma_1+1:idx_comma_2].strip())
                discriminant = text[idx_comma_2+1:m.end()-1].strip()
                copy_spec_field_disc(spec, field, type_, subtype, discriminant)
        
        def check_spec_field(spec, kind, field):
             check_spec_field_no_disc(spec, kind, field)
             check_spec_field_disc(spec, kind, field)

        def check_spec_71(spec):
            check_spec_field(spec, 'TC', 'cmdPrvActionStart')
            check_spec_field(spec, 'TC', 'cmdPrvActionProgress')
            check_spec_field(spec, 'TC', 'cmdPrvActionAbort')
            check_spec_field(spec, 'TC', 'cmdPrvActionTermination')
            check_spec_field(spec, 'TC', 'cmdPrvCheckAcceptance')
            check_spec_field(spec, 'TC', 'cmdPrvCheckReady')
            check_spec_field(spec, 'TC', 'cmdUsrActionUpdate')
            check_spec_field(spec, 'TC', 'cmdUsrCheckEnable')
            check_spec_field(spec, 'TC', 'cmdUsrCheckRepeat')
            check_spec_field(spec, 'TC', 'cmdUsrCheckReady')
            check_spec_field(spec, 'TM', 'repPrvActionUpdate')
            check_spec_field(spec, 'TM', 'repPrvCheckEnable')
            check_spec_field(spec, 'TM', 'repPrvCheckRepeat')
            check_spec_field(spec, 'TM', 'repPrvCheckReady')
            check_spec_field(spec, 'TM', 'repUsrCheckAcceptance')
            check_spec_field(spec, 'TM', 'repUsrActionUpdate')

        for app in project["apps"]["list"]:
            for spec in app["specifications"]:
                check_spec_71(spec)
        '''

    except Exception as e:
        print(e)
        print(traceback.format_exc())

    db_close(db)

    return project


# -------------------------------------------------------------------------------
def get_project(db, idProject):
    cur = db.cursor()
    db_execute(cur, """
        SELECT p.* FROM project p 
        WHERE id={0} 
        ORDER BY p.name""".format(str(idProject)))

    row = cur.fetchone()
    if row is None:
        return

    project = {}
    project["_nr_std"] = 1
    project["_nr_param"] = 1
    project["_nr_type"] = 1
    project["_nr_packet"] = 1
    project["_nr_elem"] = 1
    project["id"] = idProject
    project["name"] = row[0]
    project["settings"] = row[4]
    project["processes"] = get_processes(db, project)
    project["standards"] = get_standards(db, project)
    project["apps"] = get_apps(db, project)
    return project


# -------------------------------------------------------------------------------
def get_processes(db, project):
    cur = db.cursor()
    db_execute(cur, """
        SELECT p.* FROM process p 
        WHERE p.idProject={0} 
        ORDER BY p.address""".format(project["id"]))

    processes = {}
    for row in cur.fetchall():
        process = {}
        process["id"] = row[0]
        process["project"] = project
        process["name"] = row[2]
        process["desc"] = row[3]
        process["address"] = row[4]
        processes[process["id"]] = process
    return processes


# -------------------------------------------------------------------------------
def get_standards(db, project):
    cur = db.cursor()
    db_execute(cur, """
        SELECT s.id, s.name, s.setting FROM standard s 
        WHERE idProject={0} 
        ORDER BY s.name""".format(str(project["id"])))

    as_hash = {}
    as_list = []
    for row in cur.fetchall():
        standard = {}
        standard["datapool"] = {}
        standard["datapool"]["params"] = []
        standard["datapool"]["vars"] = []
        standard["project"] = project
        standard["id"] = int(row[0]) if row[0] is not None else 0
        standard["name"] = row[1]
        standard["setting"] = json.loads(row[2]) if row[2] else None
        standard["constants"] = get_constants(db, standard)
        standard["types"] = get_types(db, standard)
        standard["params"] = get_params(db, standard)
        standard["headers"] = get_standard_header(db, standard)
        standard["services"] = get_services(db, standard)
        standard["packets"] = get_packets(db, standard)
        standard["conforms"] = []
        standard["conforms_base"] = []
        standard["extends"] = []
        standard["extends_base"] = []
        standard["_nr"] = project["_nr_std"]
        project["_nr_std"] = project["_nr_std"] + 1
        as_list.append(standard)
        as_hash[standard["id"]] = standard

        if standard["setting"]:
            dpid_p = standard["setting"]["datapool"]["parameter"]["offset"]
        else:
            dpid_p = 0

        for param in standard["datapool"]["params"]:
            param["_dpid"] = dpid_p
            # print("PAR - DP ID: ", dpid_p, " Param: ", param["name"])
            dpid_p += 1  # for dp2
            # if param["_multi"] == None:  # for dp
            #    dpid_p += 1
            # else:
            #    dpid_p += param["_multi"]
            # print("PAR - DP ID: ", dpid_p, " Param: ", param["name"])
        dpid_v = dpid_p
        for param in standard["datapool"]["vars"]:
            param["_dpid"] = dpid_v
            # print("VAR - DP ID: ", dpid_v, " Param: ", param["name"])
            dpid_v += 1  # for dp2
            # if param["_multi"] == None:  # for dp
            #    dpid_v += 1
            # else:
            #    dpid_v += param["_multi"]
            # print("VAR - DP ID: ", dpid_v, " Param: ", param["name"])

    for standard in as_list:
        cur = db.cursor()
        db_execute(cur, """
            SELECT ss.idStandardParent, ss.relation, ss.setting FROM standardstandard ss
            WHERE ss.idStandardChild={0}""".format(str(standard["id"])))
        for row in cur.fetchall():
            s_id = row[0]
            s_relation = int(row[1])
            s_parent = as_hash[int(row[0])]

            if s_relation == 0:
                standard["conforms"].append(s_parent)
                s_parent["conforms_base"].append(standard)
            elif s_relation == 1:
                standard["extends"].append(s_parent)
                s_parent["extends_base"].append(standard)

    standards = {}
    standards["list"] = as_list
    standards["hash"] = as_hash
    return standards


# -------------------------------------------------------------------------------
# Return a dictionary with two entries:
# - "list": a list of the parameters: in the argument standard, in the parent
#           standard, and not in any standard
# - "hash": a dictionary of the same set of parameters with the parameter
#           identifier serving as key
#
def get_params(db, standard):
    global a_dict
    project = standard["project"]

    cur = db.cursor()

    # get maximal number of parameter identifier from db
    db_execute(cur, """
        SELECT MAX(nrParameter) FROM parameteridentifier WHERE idProject={0}""".format(project["id"]))
    param_max_nr_fetch = cur.fetchall()
    param_max_nr = param_max_nr_fetch[0][0]
    if param_max_nr is None:
        param_max_nr = 0
    else:
        param_max_nr = int(param_max_nr)
    # print("Project ID = ", project["id"], ", Parameter Max Nr = ", param_max_nr)

    # get all tupel (idParameter, nrParameter) from db
    db_execute(cur, """
        SELECT idParameter, nrParameter FROM parameteridentifier WHERE idProject={0}""".format(project["id"]))
    param_db = cur.fetchall()
    # print("Number of params in DB: ", len(param_db))
    # print("param in DB: ", param_db)

    # get all the information from all parameters from db
    db_execute(cur, """
        SELECT p.* FROM parameter p 
        WHERE p.idStandard={0} OR p.idStandard IS NULL or p.idStandard in 
            (SELECT ss.idStandardParent FROM standardstandard ss WHERE ss.idStandardChild={0} and ss.relation=1)
        ORDER BY p.domain,p.name""".format(standard["id"]))

    as_list = []
    as_hash = {}
    for row in cur.fetchall():
        #print("param: ", row[5], " (", int(row[0]), ")")
        param = {}
        param["id"] = int(row[0])
        param["standard"] = standard
        param["ownerStandardId"] = int(row[1]) if row[1] is not None else 0
        if row[2] != None:
            param["type"] = standard["types"][row[2]]
            standard["types"][row[2]]["params"].append(param)
        else:
            param["type"] = None
        param["kind"] = int(row[3])
        param["domain"] = row[4]
        param["name"] = row[5]
        param["shortDesc"] = row[6]
        param["desc"] = row[7]
        param["value"] = row[8]
        param["size"] = int(row[9]) if row[9] is not None else None
        param["unit"] = row[10]
        param["multi"] = row[11]  # int(row[11]) if (row[11] is not None and row[11] != '') else None
        param["role"] = int(row[13]) if row[13] is not None else None
        # None := size unknown / undefined
        # -1 := variable size
        param["_size"] = (
            param["type"]["size"] if param["type"] != None and param["type"]["size"] != None else
            param["size"])
        param["_value"] = (
            param["value"] if len(param["value"]) > 0 else
            param["type"]["value"] if param["type"] is not None else  # TODO: uncomment if needed
            None)

        # None := multiplicity unknown / undefined
        if param["multi"] != None and param["multi"] == '':  # handle empty strings
            param["multi"] = None
        if param["multi"] != None:
            try:
                param["_multi"] = int(param["multi"])
                if param["_multi"] < 0:
                    param["_multi"] = None
            except ValueError:
                if param["multi"] in standard["constants"]:
                    try:
                        param["_multi"] = int(standard["constants"][param["multi"]]["value"])
                        if param["_multi"] < 0:
                            param["_multi"] = None
                    except ValueError:
                        param["_multi"] = None
                else:
                    param["_multi"] = None
        else:
            # When multiplicity field is left empty, assume one.
            param["_multi"] = 1

        # -1 := length is variable
        # None := length is unknown / undefined
        if param["_size"] != None and param["_multi"] != None:
            if param["_size"] != -1:
                param["_length"] = param["_size"] * param["_multi"]
            else:
                param["_length"] = -1
        else:
            param["_length"] = None

        param["_desc"] = (
            param["desc"] if param["desc"] else
            param["shortDesc"] if param["shortDesc"] else
            param["type"]["desc"] if param["type"] else "")

        # OLD
        # param["_nr"] = project["_nr_param"] # TODO: get _nr from db table
        # project["_nr_param"] = project["_nr_param"]+1
        # NEW
        param_id = param["id"]
        value_found = [item for item in param_db if int(item[0]) == int(param_id)]
        '''value_found = []
        for item in param_db:
            if int(item[0]) == int(param_id):
                value_found.append(item)'''
        # print("value_of_key: ", value_found)
        if len(value_found) == 0:
            # insert param in DB table
            param_max_nr += 1
            # print("new parameter (", param_id, ") found: insert param in DB table with new number: ", param_max_nr)
            db_execute(cur, """
                INSERT INTO parameteridentifier (idProject, nrParameter, idParameter) VALUES ({0}, {1}, {2})""".format(
                project["id"], param_max_nr, param_id))
            db.commit()
            param["_nr"] = int(param_max_nr)
        elif len(value_found) == 1:
            # param found in DB table
            # print("parameter (", value_found[0][0], ") found in DB: set number from DB table: ", value_found[0][1])
            param["_nr"] = int(value_found[0][1])
        else:
            # error
            print("error ", value_found)
        project["_nr_param"] = project["_nr_param"] + 1

        param["_sequence"] = []
        param["_limits"] = get_limits(db, standard, param["id"])  # ["list"]

        as_list.append(param)
        as_hash[param["id"]] = param

        if param["kind"] == 3:
            standard["datapool"]["params"].append(param)
        if param["kind"] == 4:
            standard["datapool"]["vars"].append(param)
        if param["kind"] == 5:
            standard["datapool"]["params"].append(param)
        if param["kind"] == 6:
            standard["datapool"]["vars"].append(param)

        # print("Parameter: ID=", param["id"], ", Name='", param["name"],"', Nr=", param["_nr"])

    params = {}
    params["list"] = as_list
    params["hash"] = as_hash
    return params


# -------------------------------------------------------------------------------
def get_standard_header(db, standard):
    headers = {}
    headers["TC"] = get_param_sequence(db, standard, 0, None)
    headers["TM"] = get_param_sequence(db, standard, 1, None)
    return headers


# -------------------------------------------------------------------------------
def get_constants(db, standard):
    cur = db.cursor()
    db_execute(cur, """
        SELECT c.* FROM constants c
        WHERE c.idStandard={0} or c.idStandard in 
            (SELECT ss.idStandardParent FROM standardstandard ss WHERE ss.idStandardChild={0} and ss.relation=1)
        ORDER BY c.domain, c.name""".format(standard["id"]))

    constants = {}
    for row in cur.fetchall():
        c = {}
        c["id"] = row[0]
        c["standard"] = standard if (row[1] is not None) else None
        c["ownerStandardId"] = row[1] if (row[1] is not None) else 0
        c["domain"] = row[2]
        c["name"] = row[3]
        c["desc"] = row[4]
        c["value"] = row[5]
        constants[c["name"]] = c
    return constants


# -------------------------------------------------------------------------------
# Return a dictionary holding: (a) the types defined by the argument standard,
# the general-purpose types not attached to any standard and (c) the types
# defined by standards which are extended by the argument standard.
# The dictionary key is the type identifier.
# 
def get_types(db, standard):
    # Return (0,0) if the enums has no entries and otherwise return (min,max) where 'min'
    # and 'max' are the Names of the enumerated entries with minimum and maximum values
    def get_enums_min_max(enums):
        minMax = {}
        minVal = 4294967295
        maxVal = 0
        maxName = ""  # NEW INSERTED, du to error message: UnboundLocalError: local variable 'maxName' referenced before assignment
        if len(enums) > 0:
            for enum in enums:
                if enum["_dec"] < minVal:
                    minVal = enum["_dec"]
                    minName = enum["Name"]
                if enum["_dec"] > maxVal:
                    maxVal = enum["_dec"]
                    maxName = enum["Name"]
            minMax["min"] = minName
            minMax["max"] = maxName
        else:
            minMax["min"] = 0
            minMax["max"] = 0
        return minMax

    project = standard["project"]

    cur = db.cursor()

    # get maximal number of parameter identifier from db
    db_execute(cur, """
        SELECT MAX(nrType) FROM typeidentifier WHERE idProject={0}""".format(project["id"]))
    type_max_nr_fetch = cur.fetchall()
    #print("type_max_nr_fetch: ", type_max_nr_fetch)
    if type_max_nr_fetch[0][0] is not None:
        #print("...: ", type(type_max_nr_fetch[0][0]))
        type_max_nr = int(type_max_nr_fetch[0][0])
    else:
        type_max_nr = 0
    #print("type_max_nr: ", type_max_nr)
    if type_max_nr is None: type_max_nr = 0
    # print("Project ID = ", project["id"], ", Type Max Nr = ", type_max_nr)

    # get all tupel (idType, nrType) from db
    db_execute(cur, """
        SELECT idType, nrType FROM typeidentifier WHERE idProject={0}""".format(project["id"]))
    type_db = cur.fetchall()
    # print("Number of types in DB: ", len(type_db))
    # print("type in DB: ", type_db)

    # get all the information from all types from db
    db_execute(cur, """
        SELECT t.* FROM type t
        WHERE t.idStandard={0} or t.idStandard IS NULL or t.idStandard in 
            (SELECT ss.idStandardParent FROM standardstandard ss WHERE ss.idStandardChild={0} and ss.relation=1)
        ORDER BY t.domain, t.name""".format(standard["id"]))

    types = {}
    for row in cur.fetchall():
        type_ = {}
        type_["id"] = row[0]
        type_["standard"] = standard if (row[1] is not None) else None
        type_["ownerStandardId"] = int(row[1]) if (row[1] is not None) else 0
        type_["domain"] = row[2]
        type_["name"] = row[3]
        type_["nativeType"] = row[4]
        type_["desc"] = row[5]
        type_["size"] = int(row[6]) if row[6] is not None else None
        type_["value"] = row[7]
        #print("get_types: name = ", type_["name"])
        #print(" | schema: ", json.loads(row[9], object_pairs_hook=OrderedDict) if row[9] else None)
        # type_["setting"] = {}
        # print("get_types: setting = ", json.loads(row[8]) if row[8] else None)
        type_["datatype"] = get_datatype(db, row[8])
        # type_["setting"] = json.loads(row[8], object_pairs_hook=OrderedDict) if row[8] else None
        type_["schema"] = json.loads(row[9], object_pairs_hook=OrderedDict) if row[9] else None
        type_["enums"] = get_enumerations(db, type_)
        minMax = get_enums_min_max(type_["enums"])
        type_["enumsMin"] = minMax[
            "min"]  # Either 0 (if not enumerated) or the symbolic name of the enumerated constant with minimum value
        type_["enumsMax"] = minMax[
            "max"]  # Either 0 (if not enumerated) or the symbolic name of the enumerated constant with maximum value
        type_["params"] = []  # Will hold all parameters with this type

        # OLD
        # type_["_nr"] = project["_nr_type"] # TODO: get _nr from db table
        # project["_nr_type"] = project["_nr_type"]+1
        # NEW
        type_id = type_["id"]
        value_found = [item for item in type_db if item[0] == type_id]
        # print("value_of_key: ", value_found)
        if len(value_found) == 0:
            # insert type in DB table
            type_max_nr += 1
            # print("new type (", type_id, ") found: insert param in DB table with new number: ", type_max_nr)
            db_execute(cur, """
                INSERT INTO typeidentifier (idProject, nrType, idType) VALUES ({0}, {1}, {2})""".format(
                project["id"], type_max_nr, type_id))
            db.commit()
            type_["_nr"] = int(type_max_nr)
        elif len(value_found) == 1:
            # type found in DB table
            # print("type (", value_found[0][0], ") found in DB: set number from DB table: ", value_found[0][1])
            type_["_nr"] = int(value_found[0][1])
        else:
            # error
            print("error ", value_found)
        project["_nr_type"] = project["_nr_type"] + 1

        types[type_["id"]] = type_
    return types


# -------------------------------------------------------------------------------
def get_datatype(db, row):
    as_list = []
    as_hash = {}

    # as_list.append(json.loads(row, object_pairs_hook=OrderedDict) if row else None)
    as_list.append(json.loads(row) if row else None)

    # datatype = {}
    #  datatype["Datatype"] = as_list
    # type_["setting"] = datatype

    return as_list


# -------------------------------------------------------------------------------
# The argument type_ is a descriptor of a data type.
# If the data type is an enumeration, then this function returns a modified
# version of it which also includes the decimal values of the descriptors.
# The return value is a list with one entry for each enumerated value to
# which an extra entry "_dec" is added to represent that entry's decimal value
# (or zero if the enumerated value cannot be converted to an integer)
#
def get_enumerations(db, type_):
    cur = db.cursor()
    db_execute(cur, """
        SELECT e.* FROM `enumeration` e 
        WHERE e.idType={0}""".format(type_["id"]))
    # ORDER BY l.id

    as_list = []
    as_hash = {}
    for row in cur.fetchall():
        # print("Enumeration: ", row[2])
        enum = {}
        enum["id"] = row[0]
        enum["idType"] = row[1]
        enum["Name"] = row[2]
        enum["Value"] = str(row[3])
        enum["desc"] = row[4]
        enum["setting"] = row[5]
        enum["schema"] = row[6]
        enum["_dec"] = None
        as_list.append(enum)
        as_hash[enum["id"]] = enum
    enums = {}
    enums["Enumerations"] = as_list
    # enums["hash"] = as_hash

    # enum_t = {}
    # enum_t["Enumerations"] = enums
    type_["setting"] = enums

    # s_enums = []
    if type_["setting"] != None:
        if type_["setting"]["Enumerations"] != None:
            s_enums = type_["setting"]["Enumerations"]
            for s_enum in s_enums:
                value = s_enum["Value"]
                if value[0:1] == "0x":
                    # Entered as hex value with preceding 0x
                    s_enum["_dec"] = save_int(value)
                elif value[len(value) - 1] == "h":
                    # Entered as hex value with h at the end
                    s_enum["_dec"] = save_int("0x" + value[:len(value) - 1])
                else:
                    s_enum["_dec"] = save_int(value)

    return as_list


# -------------------------------------------------------------------------------
def get_limits(db, standard, param_id):
    cur = db.cursor()
    db_execute(cur, """
        SELECT l.* FROM `limit` l 
        WHERE l.idParameter={0}""".format(param_id))
    # ORDER BY l.id

    # print("get_limits: row length = "+str(cur.rowcount))

    as_list = []
    as_hash = {}
    for row in cur.fetchall():
        limits = {}
        limits["id"] = row[0]
        # limits["standard"] = standard
        limits["idParameter"] = row[1]
        limits["type"] = row[2]
        limits["lvalue"] = row[3]
        limits["hvalue"] = row[4]
        limits["setting"] = row[5]
        # print("(lvalue/hvalue) = (" + row[3] + "/" + row[4] + ")")
        as_list.append(limits)
        as_hash[limits["id"]] = limits
    res = {}
    res["list"] = as_list
    res["hash"] = as_hash
    return res


# -------------------------------------------------------------------------------
def get_services(db, standard):
    cur = db.cursor()
    db_execute(cur, """
        SELECT s.* FROM service s 
        WHERE s.idStandard={0} or s.idStandard in 
            (SELECT ss.idStandardParent FROM standardstandard ss WHERE ss.idStandardChild={0} and ss.relation=1)
        ORDER BY s.type""".format(standard["id"]))

    as_list = []
    as_hash = {}
    for row in cur.fetchall():
        service = {}
        service["id"] = row[0]
        service["standard"] = standard
        service["ownerStandardId"] = int(row[1]) if row[1] is not None else 0
        service["name"] = row[2]
        service["desc"] = row[3]
        service["type"] = row[4]
        service["packets"] = []
        as_list.append(service)
        as_hash[service["type"]] = service
    res = {}
    res["list"] = as_list
    res["hash"] = as_hash
    return res


# -------------------------------------------------------------------------------
# Return the derived packets attached to the argument base packet.
# The return value is a dictionary with two entries:
# - "list": the list of derived packets attached to the argument base packet
# - "hash": the set of derived packets organized as a dictionary where the packet 
#           discriminant is the dictionary key 
# NB: The discriminant in entry "disc" is taken from the Packet table and is not 
#     necessarily an integer (it could also be an enumerated constant).
#     The integer value of the discriminant (if it exists) is stored in
#     entry "_disc".
# NB: The name of a derived packet is the same as the name of its parent with the
#     discriminant appended to it
# NB: The domain of a derived packet is the same as the domain of its parent
#
def get_derived_packets(db, parent):
    def get_int_value_of_disc(packet):
        int_value_of_disc = 0
        if is_int(packet["disc"]):
            int_value_of_disc = int(packet["disc"])
        else:
            param_i = packet['parent']["_param_derived"]
            enum = None
            if param_i != None:
                t = param_i["param"]["type"]
                if t["setting"] != None:
                    for enum in t["setting"]["Enumerations"]:
                        if enum["Name"] == packet["disc"]:
                            int_value_of_disc = int(enum["_dec"])
                            break
        return int_value_of_disc

    standard = parent["standard"]
    project = standard["project"]

    cur = db.cursor()

    # get maximal number of packet identifier from db
    db_execute(cur, """
        SELECT MAX(nrPacket) FROM packetidentifier WHERE idProject={0}""".format(project["id"]))
    packet_max_nr_fetch = cur.fetchall()
    if packet_max_nr_fetch[0][0] is not None:
        packet_max_nr = int(packet_max_nr_fetch[0][0])
    else:
        packet_max_nr = 0
    if packet_max_nr is None: packet_max_nr = 0
    # print("Project ID = ", project["id"], ", Packet Max Nr = ", packet_max_nr)

    # get all tupel (idPacket, nrPacket) from db
    db_execute(cur, """
        SELECT idPacket, nrPacket FROM packetidentifier WHERE idProject={0}""".format(project["id"]))
    packet_db = cur.fetchall()
    # print("Number of types in DB: ", len(packet_db))
    # print("type in DB: ", packet_db)

    # get all the information from all packets from db
    db_execute(cur, """
        SELECT p.* FROM packet p
        WHERE p.idParent={0}
        ORDER BY p.discriminant""".format(parent["id"]))

    as_list = []
    as_hash = {}
    for row in cur.fetchall():
        packet = {}
        packet["id"] = row[0]
        packet['parent'] = parent
        packet['disc'] = row[7]

        if int(row[4]) > 0:  # FIXME: otherwise KeyErr 'kind'
            packet["kind"] = "TM"
        else:
            packet["kind"] = "TC"
        packet["type"] = row[5]  # FIXME: otherwise KeyError 'type'
        packet["subtype"] = row[6]  # FIXME: otherwise KeyError 'subtype'
        packet['derivations'] = {}
        packet['derivations']["list"] = []
        packet['derivations']["hash"] = {}
        packet["_param_derived"] = None

        packet['domain'] = parent['domain']
        packet['name'] = parent['name'] + '_' + packet['disc']
        packet["shortDesc"] = save_str(row[10])
        packet["desc"] = save_str(row[11])
        packet["descParam"] = save_str(row[12])
        packet["descDest"] = save_str(row[13])
        packet["body"] = get_param_sequence(db, parent["standard"], None, packet)
        packet["spec"] = None
        packet["_disc"] = get_int_value_of_disc(packet)
        packet["_desc"] = packet["shortDesc"] + packet["desc"] + packet["descParam"] + packet["descDest"]
        packet["_length"] = get_param_sequence_length(packet["body"])
        # print("Desc: ", packet["id"], packet["type"], packet["subtype"], " _nr = ", project["_nr_packet"])

        # OLD
        # packet["_nr"] = project["_nr_packet"] # TODO: get _nr from db table
        # project["_nr_packet"] = project["_nr_packet"]+1
        # NEW
        packet_id = packet["id"]
        value_found = [item for item in packet_db if item[0] == packet_id]
        # print("value_of_key: ", value_found)
        if len(value_found) == 0:
            # insert packet in DB table
            packet_max_nr += 1
            # print("new derived packet (", packet_id, ") found: insert param in DB table with new number: ", packet_max_nr)
            db_execute(cur, """
                INSERT INTO packetidentifier (idProject, nrPacket, idPacket) VALUES ({0}, {1}, {2})""".format(
                project["id"], packet_max_nr, packet_id))
            db.commit()
            packet["_nr"] = int(packet_max_nr)
        elif len(value_found) == 1:
            # packet found in DB table
            # print("derived packet (", value_found[0][0], ") found in DB: set number from DB table: ", value_found[0][1])
            packet["_nr"] = int(value_found[0][1])
        else:
            # error
            print("error ", value_found)
        project["_nr_packet"] = project["_nr_packet"] + 1
        # print("Packet: ", packet["name"], ", ", packet["_nr"])

        as_list.append(packet)
        as_hash[packet['disc']] = packet
    res = {}
    res["list"] = as_list
    res["hash"] = as_hash
    return res


# -------------------------------------------------------------------------------
# Return all base packets in the argument standard.
# The return value is a dictionary with two entries:
# - "list": the list of base packets in the argument standard
# - "hash": the set of base packets organized as a dictionary where the packet 
#           identifier is the dictionary key 
#
# Each packet is represented by a dictionary with the following keys:
# - "id": the identifier of the packet
# - "standard": the standard to which the packet belongs
# - "ownerStandardId: the id of the parent standard where the packet is defined (TBC) or else 0 
# - "spec": this is left blank here but will presumably be filled in with the specification-leve information for the packet
# - "process": the APID of the packet (TBC)
# - "kind": the packet kind (either TM or TC)
# - "type": the service type of the packet
# - "subtype": the service sub-type of the packet
# - "domain": the domain to which the packet belongs
# - "name": the name of the packet
# - "shortDesc": the short description of the packet
# - "desc": the description of the packet
# - "descParam": the description of the packet parameters
# - "descDest": the description of the packet destination
# - "header": TBD
# - "body": the sequence of parameters in the packet
# - "derivations": the sequence of packets derived from this packet 
# - "disc": 0 if the packet has no derived packets (otherwise, this field is filled in with the symbolic value of the discriminant)
# - "_disc": 0 if the packet has no derived packets (otherwise, this field is filled in with the integer value of the discriminant)
# - "_nr": TBD
# - "_nr_packet": TBD
# - "_param_derived": the parameter in the packet with the role of discriminant (or else: "none")
# - "_desc": the concatenation of short description, description, parameter description and destination description
# - "_length": the length of the packet body
# 
def get_packets(db, standard):
    project = standard["project"]

    cur = db.cursor()

    # get all the information from all packets from db
    db_execute(cur, """
        SELECT p.* FROM packet p 
        WHERE p.idParent IS NULL and (p.idStandard={0} or p.idStandard in 
            (SELECT ss.idStandardParent FROM standardstandard ss WHERE ss.idStandardChild={0} and ss.relation=1))
        ORDER BY p.type, p.subtype, p.discriminant""".format(standard["id"]))

    as_list = []
    as_hash = {}
    for row in cur.fetchall():

        # get maximal number of packet identifier from db
        db_execute(cur, """
            SELECT MAX(nrPacket) FROM packetidentifier WHERE idProject={0}""".format(project["id"]))
        packet_max_nr_fetch = cur.fetchall()
        if packet_max_nr_fetch[0][0] is not None:
            packet_max_nr = int(packet_max_nr_fetch[0][0])
        else:
            packet_max_nr = 0
        if packet_max_nr is None: packet_max_nr = 0
        # print("Project ID = ", project["id"], ", Packet Max Nr = ", packet_max_nr)

        # get all tupel (idPacket, nrPacket) from db
        db_execute(cur, """
            SELECT idPacket, nrPacket FROM packetidentifier WHERE idProject={0}""".format(project["id"]))
        packet_db = cur.fetchall()
        # print("Number of types in DB: ", len(packet_db))
        # print("type in DB: ", packet_db)

        packet = {}
        packet["id"] = row[0]
        packet["standard"] = standard
        packet["ownerStandardId"] = int(row[1]) if row[1] is not None else 0
        packet["spec"] = None
        if row[3] != None:
            packet["process"] = project["processes"][row[3]]
        else:
            packet["process"] = {}
            packet["process"]["address"] = ""
        if (int(row[4]) > 0):
            packet["kind"] = "TM"
        else:
            packet["kind"] = "TC"
        packet["type"] = row[5]
        packet["subtype"] = row[6]
        packet["domain"] = row[8]
        packet["name"] = row[9]
        packet["shortDesc"] = save_str(row[10])
        packet["desc"] = save_str(row[11])
        packet["descParam"] = save_str(row[12])
        packet["descDest"] = save_str(row[13])
        packet["header"] = standard["headers"][packet["kind"]]
        packet["body"] = get_param_sequence(db, packet["standard"], None, packet)
        packet["_param_derived"] = None
        for param in packet["body"]:
            if int(param["role"]) == 3:
                packet["_param_derived"] = param
                break
        packet["_desc"] = packet["shortDesc"] + packet["desc"] + packet["descParam"] + packet["descDest"]
        packet["_length"] = get_param_sequence_length(packet["body"])
        packet['derivations'] = get_derived_packets(db, packet)
        packet["disc"] = 0
        packet["_disc"] = 0

        if len(packet["derivations"]["list"]) == 0 or packet["kind"] == "TC":  # The packet has no derived packets
            # OLD
            # packet["_nr"] = project["_nr_packet"] # TODO: get _nr from db table
            # project["_nr_packet"] = project["_nr_packet"]+1
            # NEW
            packet_id = packet["id"]
            value_found = [item for item in packet_db if item[0] == packet_id]
            # print("value_of_key: ", value_found)
            if len(value_found) == 0:
                # insert packet in DB table
                packet_max_nr += 1
                # print("new packet (", packet_id, ") found: insert param in DB table with new number: ", packet_max_nr)
                db_execute(cur, """
                    INSERT INTO packetidentifier (idProject, nrPacket, idPacket) VALUES ({0}, {1}, {2})""".format(
                    project["id"], packet_max_nr, packet_id))
                db.commit()
                packet["_nr"] = int(packet_max_nr)
            elif len(value_found) == 1:
                # packet found in DB table
                # print("packet (", value_found[0][0], ") found in DB: set number from DB table: ", value_found[0][1])
                packet["_nr"] = int(value_found[0][1])
            else:
                # error
                print("error ", value_found)
            project["_nr_packet"] = project["_nr_packet"] + 1
        else:
            packet["_nr"] = 0  # FIXME: otherwise KeyError "_nr" occurs for derived packets (ONLY for TM packets)
        # print("Packet: ", packet["name"], ", ", packet["_nr"])

        as_list.append(packet)
        as_hash[packet["id"]] = packet
        for derived in packet["derivations"]["list"]:
            as_hash[derived["id"]] = derived

    res = {}
    res["list"] = as_list
    res["hash"] = as_hash
    return res


# -------------------------------------------------------------------------------
def get_param_sequence_length_(elements, i, info):
    element = elements[i]
    group = element["group"]
    repetition = element["repetition"]
    param = element["param"]

    if group != None and int(group) > 0:
        # Defines the next "group" fields to build a group
        if repetition != None and int(repetition) > 0:
            group_info = {}
            group_info["length"] = 0
            ii = 0
            while ii < group:
                ii = get_param_sequence_length_(elements, i + ii + 1, group_info)
            element_length = group_info["length"]
            i = i + group
        else:
            # Dynamic repetition, length unknown.
            element_length = None
    else:
        if repetition != None and int(repetition) > 0:
            element_length = (param["_length"] * repetition)
        else:
            element_length = param["_length"]

    if element_length is None:
        info["length"] = None

    if info["length"] != None:
        element_length = 0 if element_length is None else int(element_length)
        info["length"] = info["length"] + element_length

    return i


# -------------------------------------------------------------------------------
def get_param_sequence_length(elements):
    i = 0
    info = {}
    info["length"] = 0
    while i < len(elements):
        i = get_param_sequence_length_(elements, i, info) + 1
        if info["length"] is None:
            # print("BREAK!! i = ", i, elements[i-1]["param"]["name"])
            break
    return info["length"]


# -------------------------------------------------------------------------------
# Return the sequence of parameters in a packet or in a TM/TC header for a given
# standard.
# If argument 'packet' is set to None, then the sequence of parameters in either
# the TM header or TC header (depending on the value of argument _type) is returned.
# Otherwise the sequence of parameters in the packet is returned.
#
def get_param_sequence(db, standard, _type, packet):
    project = standard["project"]

    if packet is None:
        # TC/TM header sequence
        where = "ps.idStandard={0} and ps.type={1} and ps.idPacket IS NULL ".format(str(standard["id"]), str(_type))
    else:
        # Packet sequence
        where = "ps.idPacket={0} ".format(str(packet["id"]))

    cur = db.cursor()
    db_execute(cur, """
        SELECT ps.* FROM parametersequence ps
        WHERE {0}
        ORDER BY ps.`order`""".format(where))

    res = []
    offset = 0
    #print("---------------------------------------------------------")
    for row in cur.fetchall():
        param = standard["params"]["hash"][int(row[2])]
        elem = {}
        elem["id"] = row[0]
        elem["standard"] = standard
        elem["param"] = param
        elem["datatype"] = param["type"]
        elem["packet"] = packet
        elem["type"] = row[4]
        elem["role"] = row[5]
        elem["group"] = int(row[7]) if row[7] is not None else 0
        elem["repetition"] = int(row[8]) if row[8] is not None else 0
        elem["value"] = row[9]
        elem["desc"] = row[10]
        elem["_value"] = (
            elem["value"] if len(elem["value"]) > 0 else
            param["_value"])
        elem["_desc"] = (
            elem["desc"] if len(elem["desc"]) > 0 else
            param["_desc"])
        elem["_nr"] = project["_nr_elem"]
        project["_nr_elem"] = project["_nr_elem"] + 1
        elem["_offset"] = offset
        #print("elem name: ", param["name"], " | elem[\"param\"][\"_length\"]: ", elem["param"]["_length"], " | offset: ", offset)

        # TODO: group / repetition not yet supported.
        if offset != None and elem["param"]["_length"] != None:
            offset = offset + int(elem["param"]["_length"])
        else:
            offset = None

        param["_sequence"].append(elem)
        res.append(elem)

    return res


# -------------------------------------------------------------------------------
# Return a list object. Each element describes a Standard associated to the 
# argument application.
#
def get_app_standards(db, app):
    cur = db.cursor()
    db_execute(cur, """
        SELECT s.idApplication, s.idStandard, s.relation, s.setting FROM applicationstandard s
        WHERE s.idApplication={0}""".format(str(app["id"])))

    standards = []
    for row in cur.fetchall():
        standard = {}
        standard["standard"] = app["project"]["standards"]["hash"][int(row[1])]
        standard["relation"] = int(row[2])
        standards.append(standard)
    return standards


# -------------------------------------------------------------------------------
def get_app_components(db, app):
    cur = db.cursor()
    db_execute(cur, """
        SELECT s.idApplication, s.idComponent, s.setting, c.id, c.shortName, c.name FROM applicationcomponent s
        JOIN component c ON s.idComponent=c.id 
        WHERE s.idApplication={0}""".format(str(app["id"])))

    as_list = []
    as_hash = {}
    for row in cur.fetchall():
        component = {}
        component["app"] = app
        component["setting"] = json.loads(row[2]) if row[2] else None
        component["id"] = row[3]
        component["shortName"] = row[4]
        component["name"] = row[5]
        as_list.append(component)
        as_hash[component["shortName"]] = component
    res = {}
    res["list"] = as_list
    res["hash"] = as_hash
    return res


# -------------------------------------------------------------------------------
# Return a list of specification objects.
# Each specification object describes the specification of a command or report
# supported by the application (possibly with multiple communication standards).
# The specification objects are read from the ApplicationPacket table.
#
def get_app_specifications(db, app):
    cur = db.cursor()
    db_execute(cur, """
        SELECT ap.idApplication, ap.idStandard, ap.idPacket, ap.cmdUsrCheckEnable, ap.cmdUsrCheckReady, 
        ap.cmdUsrCheckRepeat, ap.cmdPrvCheckAcceptance, ap.cmdPrvCheckReady, ap.cmdUsrActionUpdate, ap.cmdPrvActionStart, 
        ap.cmdPrvActionProgress, ap.cmdPrvActionTermination, ap.cmdPrvActionAbort, ap.repPrvCheckEnable, ap.repPrvCheckReady, 
        ap.repPrvCheckRepeat, ap.repUsrCheckAcceptance, ap.repPrvActionUpdate, ap.repUsrActionUpdate, ap.setting, 
        astd.relation
        FROM applicationpacket ap
        JOIN packet p ON ap.idPacket=p.id
        JOIN applicationstandard astd ON ap.idApplication=astd.idApplication and ap.idStandard=astd.idStandard
        WHERE ap.idApplication={0}
        ORDER BY p.type, p.subtype, p.discriminant""".format(str(app["id"])))

    specs = []
    for row in cur.fetchall():
        spec = {}
        spec["app"] = app
        spec["standard"] = app["project"]["standards"]["hash"][int(row[1])]
        spec["packet"] = spec["standard"]["packets"]["hash"][row[2]]
        spec["relation"] = int(row[20]) if row[20] is not None else None
        spec["cmdUsrCheckEnable"] = row[3] if row[3] is not None else ""
        spec["cmdUsrCheckReady"] = row[4] if row[4] is not None else ""
        spec["cmdUsrCheckRepeat"] = row[5] if row[5] is not None else ""
        spec["cmdPrvCheckAcceptance"] = row[6] if row[6] is not None else ""
        spec["cmdPrvCheckReady"] = row[7] if row[7] is not None else ""
        spec["cmdUsrActionUpdate"] = row[8] if row[8] is not None else ""
        spec["cmdPrvActionStart"] = row[9] if row[9] is not None else ""
        spec["cmdPrvActionProgress"] = row[10] if row[10] is not None else ""
        spec["cmdPrvActionTermination"] = row[11] if row[11] is not None else ""
        spec["cmdPrvActionAbort"] = row[12] if row[12] is not None else ""
        spec["repPrvCheckEnable"] = row[13] if row[13] is not None else ""
        spec["repPrvCheckReady"] = row[14] if row[14] is not None else ""
        spec["repPrvCheckRepeat"] = row[15] if row[15] is not None else ""
        spec["repUsrCheckAcceptance"] = row[16] if row[16] is not None else ""
        spec["repPrvActionUpdate"] = row[17] if row[17] is not None else ""
        spec["repUsrActionUpdate"] = row[18] if row[18] is not None else ""
        specs.append(spec)
        spec["packet"]["spec"] = spec

    return specs


# ------------------------------------------------------------------------------
# Return a dictionary object describing the applications in the argument project.
# The application information is read from the Application table.
# The return object has two entries:
# - "list": a list of application objects
# - "hash": a dictionary of application objects (the application id is the key)
# An application object is a dictionary with the following keys:
# - "id": the identifier of the application
# - "project": the object describing the project
# - "name": the name of the application
# - "desc": the description of the application
# - "addr": the address of the application (not used)
# - "setting": the application settngs (not used)
# - "standards": the communication standards used by the application 
# - "components": the components with the settings for the application's generators 
# - "specifications": the specification of the commands and reports supported 
#                     by the application 
#
def get_apps(db, project):
    cur = db.cursor()
    db_execute(cur, """
        SELECT a.* FROM application a
        WHERE a.idProject={0} 
        ORDER BY a.name""".format(str(project["id"])))

    as_list = []
    as_hash = {}
    for row in cur.fetchall():
        app = {}
        app["id"] = int(row[0])
        app["project"] = project
        app["name"] = row[2]
        app["desc"] = row[3]
        app["addr"] = row[4]
        app["setting"] = json.loads(row[5]) if row[5] is not None and len(row[5]) > 0 else None
        app["standards"] = get_app_standards(db, app)
        app["components"] = get_app_components(db, app)
        app["specifications"] = get_app_specifications(db, app)
        as_list.append(app)
        as_hash[app["id"]] = app
    res = {}
    res["list"] = as_list
    res["hash"] = as_hash
    return res


# ------------------------------------------------------------------------------
if __name__ == '__main__':
    if (len(sys.argv) == 2):

        project_id = sys.argv[1]
        il = get_data(project_id)
        if il != None:
            print("All good")
        else:
            print("Something went wrong.")

    else:
        print("Usage: python get_data.py {project_id}")

    sys.stdout.flush()
