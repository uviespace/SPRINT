#!/usr/bin/python

import sys
import get_data
import consistency_check
import traceback

from common import *
from common_check import *

def build(app): 
    path_base = new_path()
    gen_path(path_base)
    for comp in app["components"]["list"]:
        cmd = "gen_" + comp["shortName"]
        path = "{0}/{1}".format(path_base, comp["shortName"])
        gen_path(path)
        try:
            #if (cmd!="gen_pck"):    # TODO: REMOVE !!!
            module = __import__(cmd)
            f = getattr(module, cmd)
            f(path, comp)
        except Exception as e:
            print(e)
            print("Module " + cmd + " could not be loaded.")
            raise

    return gen_zip(path_base)

if __name__ == '__main__':
    if (len(sys.argv) == 3):
        project_id = sys.argv[1]
        app_id = sys.argv[2]

        
        try:
            il = get_data.get_data(project_id)
            res = consistency_check.check(il)
            if len(res["error"]) == 0:
                app = il["apps"]["hash"][int(app_id)]
                path = build(app)
                print(path)
            else:
                print_res(res)
        except Exception as e:
            print("Something went wrong...")
            print(traceback.format_exc()) 
        
    else:
        print("Usage: python build_app.py {project_id} {application_id}")
