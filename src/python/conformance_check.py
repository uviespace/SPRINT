#!/usr/bin/python

import sys
from get_data import get_data
import common_check
from common_check import *

# For all types defined in both the child and parent, the child's type size equals the
# parent's type size, if the parent's type size is specified.
def check_cc100(child, parent, res):
    for parent_type_id in parent["types"]:
        if parent_type_id >= 1000:
            parent_type = parent["types"][parent_type_id]
            for child_type_id in child["types"]:
                child_type = child["types"][child_type_id]
                if child_type["domain"] == parent_type["domain"] and child_type["name"] == parent_type["name"]:
                    my_assert(res, "CC-100",
                        u"""[{0}] Type '{2}/{3}' should have same size as in '{1}'.
                        """.format(child["name"], parent["name"], child_type["domain"], child_type["name"]), parent_type["size"] == None or child_type["size"] == parent_type["size"], 1)


# For all parameters defined in both the child and parent, the child's parameter type
# equals the parent's parameter type, if the parent's parameter type is specified.
def check_cc101(child, parent, res):
    for parent_param in parent["params"]["list"]:
        if parent_param["id"] >= 1000:
            for child_param in child["params"]["list"]:
                if child_param["domain"] == parent_param["domain"] and child_param["name"] == parent_param["name"]:
                    passed = parent_param["type"] == None or (child_param["type"] != None and child_param["type"]["domain"] == parent_param["type"]["domain"] and child_param["type"]["name"] == parent_param["type"]["name"])
                    my_assert(res, "CC-101",
                        u"""[{0}] Parameter '{2}/{3}' should have same type as in '{1}'.
                        """.format(child["name"], parent["name"], child_param["domain"], child_param["name"]), passed, 1)

# For all packets defined in both the child and parent, the child's packet parameter
# sequence correponds to the parent's parameter sequence.
def check_cc102(child, parent, res):
    for child_pckt in child["packets"]["list"]:
        for parent_pckt in parent["packets"]["list"]:
            if child_pckt["type"] == parent_pckt["type"] and child_pckt["subtype"] == parent_pckt["subtype"]:
                if len(child_pckt["body"]) == len(parent_pckt["body"]):
                    paramsOk = True
                    for i in range(0, len(child_pckt["body"])):
                        child_param = child_pckt["body"][i]
                        parent_param = parent_pckt["body"][i]
                        paramsOk = \
                            paramsOk and \
                            (child_param["param"]["domain"] == parent_param["param"]["domain"]) and \
                            (child_param["param"]["name"] == parent_param["param"]["name"]) and \
                            (child_param["_value"] == parent_param["_value"]) and \
                            (child_param["role"] == parent_param["role"]) and \
                            (child_param["group"] == parent_param["group"]) and \
                            (child_param["repetition"] == parent_param["repetition"])
                else:
                    paramsOk = False
                
                passed = \
                    paramsOk and \
                    child_pckt["kind"] == parent_pckt["kind"] and \
                    child_pckt["process"]["address"] == parent_pckt["process"]["address"]

                my_assert(res, "CC-102",
                    u"""[{0}] Packet '{2}/{3}' should have same definition as in '{1}'.
                    """.format(child["name"], parent["name"], child_pckt["domain"], child_pckt["name"]), passed, 1)

def conformance_check(child, parent, res):
    check_cc100(child, parent, res)
    check_cc101(child, parent, res)
    check_cc102(child, parent, res)

def check(project):
    res = init_res()

    for child in project["standards"]["list"]:
        for parent in child["conforms"]:
            conformance_check(child, parent, res)

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
        print("Usage: python conformance_check.py {project_id}")

    sys.stdout.flush()