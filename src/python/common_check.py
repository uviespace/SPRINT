#!/usr/bin/python

import sys

def init_res():
    res = {}
    res["passed"] = []
    res["warning"] = []
    res["error"] = []
    return res    

# severity: 0 - info (not used at the moment)
# severity: 1 - warning
# severity: 2 - error
def my_assert(res, id, text, result, severity=2):
    test = {}
    test["id"] = id
    test["desc"] = text
    test["result"] = result

    if result:
        res["passed"].append(test)
    else:
        if severity == 1:
            res["warning"].append(test)
        else:
            res["error"].append(test)

def print_res(res):
    def print_res_(res):
        print("<ul>")
        if len(res) > 0:
            for test in res:
                print(u"<li> ({1}) {0}</li>".format(test["desc"], test["id"]))
        else:
            print(u"<li>None</li>")            
        print("</ul>")

    print("Errors:")
    print_res_(res["error"])
    print("Warnings:")
    print_res_(res["warning"])
    print("Passed:")
    print_res_(res["passed"])
    print("Check done: {0} errors; {1} warnings; {2} passed; {3} total.".format(len(res["error"]), len(res["warning"]), len(res["passed"]), len(res["passed"])+len(res["warning"])+len(res["error"])))
            