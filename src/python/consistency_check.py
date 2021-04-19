#!/usr/bin/python

import sys
from get_data import get_data
from conformance_check import conformance_check
from common_check import *

def is_int(s):
    try: 
        int(s)
        return True
    except ValueError:
        return False

# For all parameters, the parameter's size must be left undefined or set to the
# type's size, if the size of a parameter type is defined.
def check_cc001(standard, res):
    for param in standard["params"]["list"]:
        if param["type"] != None:
            if param["type"]["size"] != None:
                my_assert(res, 
                    "CC-001",
                    "[{0}] Parameter '{1}' with size {2} must have size {3} or left empty.".format(standard["name"], param["name"], param["size"], param["type"]["size"]), 
                    param["size"] == None or param["size"] == param["type"]["size"])

# For all parameters added to a sequence with a group value N greater than
# zero and order number M, there are N or more parameters defined in this sequence
# with an order number greater than M.
def check_cc002_list(standard, res, elem_list, container_name):
    i = 0
    for elem in elem_list:
        n = elem["group"]
        m = len(elem_list)-1-i
        if n != None and n > 0:
            my_assert(res,
                "CC-002",
                "[{0}] Parameter '{1}' in '{4}' specifies group size of N={2}, M={3} parameters follow in this sequence. M >= N.".format(standard["name"], elem["param"]["name"], n, m, container_name),
                m >= n)
        i = i+1

def check_cc002(standard, res):
    check_cc002_list(standard, res, standard["headers"]["TC"], "TC Header")
    check_cc002_list(standard, res, standard["headers"]["TM"], "TM Header")
    for packet in standard["packets"]["list"]:
        if len(packet["derivations"]["list"]) > 0:
            for derived in packet["derivations"]["list"]:
                check_cc002_list(standard, res, packet["body"] + derived["body"], "{0} ({1})".format(packet["name"], derived["disc"]))
        else:
            check_cc002_list(standard, res, packet["body"], packet["name"])

def check_cc003_list(standard, res, elem_list, container_name):
    for elem in elem_list:
        if elem["group"] != None and elem["group"] > 0 and elem["repetition"] != None and elem["repetition"] > 1:
            my_assert(res, "CC-003", 
                "[{0}] Parameter '{1}' in '{5}' with group={2}, repetition={3} and size={4} must have size zero.".format(standard["name"], elem["param"]["name"], elem["group"], elem["repetition"], elem["param"]["_size"], container_name),
                elem["param"]["size"] == 0)

def check_cc003(standard, res):
    check_cc003_list(standard, res, standard["headers"]["TC"], "TC Header")
    check_cc003_list(standard, res, standard["headers"]["TM"], "TM Header")
    for packet in standard["packets"]["list"]:
        if len(packet["derivations"]["list"]) > 0:
            for derived in packet["derivations"]["list"]:
                check_cc003_list(standard, res, packet["body"] + derived["body"], "{0} ({1})".format(packet["name"], derived["disc"]))
        else:
            check_cc003_list(standard, res, packet["body"], packet["name"])                

def check_cc004_cc005(id, standard, res, role_id, role_desc):
    for header in ["TC", "TM"]:
        n_type = 0
        params = standard["headers"][header]
        for param in params:
            if param["role"] == role_id:
                n_type = n_type+1
        my_assert(res, id,
            "[{0}] Exactly one parameter in the {1} header definition must be given the role {2}.".format(standard["name"], header, role_desc),
            n_type == 1)

# Exactly one parameter in the TC and TM header definition must be given
# the role Type.
def check_cc004(standard, res):
    check_cc004_cc005("CC-004", standard, res, 1, "Type")

# Exactly one parameter in the TC and TM header definition must be given
# the role Subtype.
def check_cc005(standard, res):
    check_cc004_cc005("CC-005", standard, res, 2, "Subtype")

# For all packets which have derived packets defined, a parameter in the body
# must be given the role Discriminant.
def check_cc006(standard, res):
    for packet in standard["packets"]["list"]:
        if len(packet["derivations"]["list"]) > 0:
            n_discriminant = 0
            for param in packet["body"]:
                if param["role"] == 3:
                    n_discriminant = n_discriminant+1
            my_assert(res, "CC-006",
                "[{0}] Body of packet '{1}' must have exactly one parameter with role 'Discriminant'.".format(standard["name"], packet["name"]),
                n_discriminant == 1)
            
# Every parameter must have a type
def check_cc007(standard, res):
    for param in standard["params"]["list"]:
        my_assert(res, "CC-007",
            "[{0}] Parameter '{1}' must have a type.".format(standard["name"], param["name"]),
            param["type"] != None)

# Every discriminant field value must be a) an integer or b) its type must be enumerated and 
# contain the field value.
def check_cc008(standard, res):
    for packet in standard["packets"]["list"]:
        if len(packet["derivations"]["list"]) > 0:
            for derived in packet["derivations"]["list"]:
                my_assert(res, "CC-008",
                    """[{0}] Discriminant '{1}' value must must be a) an integer or 
                    b) its type must be enumerated and contain the field value.
                    """.format(standard["name"], derived["name"]),
                    derived["_disc"] != None)   

# Generate warning for unused types
def check_cc009(standard, res):
    for type_id, type_ in standard["types"].items():
        if type_id >= 1000: # Type IDs below 1000 are pre-defined types, e.g. the C types
            my_assert(res, "CC-009",
                """[{0}] Type '{1}/{2}' should be used.""".format(standard["name"], type_["domain"], type_["name"]), len(type_["params"]) > 0, 1)

def check_cc010(standard, res):
    for param in standard["params"]["list"]:
        if param["id"] >= 1000: # Parameter IDs below 1000 are for pre-defined parameters, e.g. Spare n-bit
            if param["kind"] == 1 or param["kind"] == 2: # kind 1: parameter can be part of a TC/TM header; kind 2: parameter can be part of body definition
                is_used = (len(param["_sequence"]) > 0)
                my_assert(res, "CC-010",
                    """[{0}] Parameter '{1}/{2}' should be used.""".format(standard["name"], param["domain"], param["name"]), is_used, 1)

# Generate warning for every enumerated type who's default value is not in the enumerated list.
def check_cc011(standard, res):
    for type_id, type_ in standard["types"].items():
        if type_id >= 1000: # Don't perform check for pre-defined types.
            if type_["value"] != None and len(type_["value"]) > 0 and len(type_["enums"]) > 0:
                valid = False
                for enum in type_["enums"]:
                    if type_["value"] == enum["Value"]:
                        valid = True
                my_assert(res, "CC-011",
                    """[{0}] Type's '{1}/{2}' default value {3} should be included in enumerated list.""".format(standard["name"], type_["domain"], type_["name"], type_["value"]), valid, 1)

# Generate warning for every parameter who's default value is not in the enumerated list of its type.
def check_cc012(standard, res):
    for param in standard["params"]["list"]:
        if param["id"] >= 1000:    
            type_ = param["type"]
            if type_ != None:
                if type_["id"] >= 1000: # Don't perform check for pre-defined types.
                    if param["value"] != None and len(param["value"]) > 0 and len(type_["enums"]) > 0:
                        valid = False
                        for enum in type_["enums"]:
                            if type_["value"] == enum["Value"]:
                                valid = True
                        my_assert(res, "CC-012",
                            """[{0}] Parameter's '{1}/{2}' default value {5} should be included in enumerated list of type '{3}/{4}'.""".format(standard["name"], type_["domain"], type_["name"], param["domain"], param["name"], param["value"]), valid, 1)

# The multiplicity must either be a positive integer or state the name of a constant who's value is a positive integer.
def check_cc013(standard, res):
    for param in standard["params"]["list"]:
        if param["id"] >= 1000:
            valid = (param["_multi"] != None)
            my_assert(res, "CC-013",
                """[{0}] Parameter '{1}/{2}' must have a multiplicity of either a positive integer or state the name of a constant who's value is a positive integer.""".format(standard["name"], param["domain"], param["name"]), valid)

def check_app(app, res):
    for standard_i in app["standards"]:
        standard = standard_i["standard"]
        check_cc001(standard, res)
        check_cc002(standard, res)
        check_cc004(standard, res)
        check_cc005(standard, res)
        check_cc006(standard, res)
        check_cc007(standard, res)
        check_cc008(standard, res)
        check_cc009(standard, res)
        check_cc010(standard, res)
        check_cc011(standard, res)
        check_cc012(standard, res)
        check_cc013(standard, res)

        for parent in standard["conforms"]:
            conformance_check(standard, parent, res)

def check(project):
    res = init_res()

    for app in project["apps"]["list"]:
        check_app(app, res)

    return res

if __name__ == '__main__':
    if (len(sys.argv) == 2):

        project_id = sys.argv[1]
        project = get_data(project_id)

        if project != None:
            res = check(project)
            print_res(res)
        else:
            print("Error: project not found.")

    else:
        print("Usage: python consistency_check.py {project_id}")

    sys.stdout.flush()