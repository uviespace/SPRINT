#!/usr/bin/python

import sys
import get_data
import traceback
from gen_cfw import get_ref_spec

from csv_generator import *
from tex_generator import *

def gen_spec(path, comp):
    settings = comp["setting"]
    app = comp["app"]

    if settings == None:
        return

    g = TexGenerator(path, None)
    g.open(u"{0}{1}.tex".format(app["name"], "Spec"))
    
    for spec in app["specifications"]:
        packet = spec["packet"]
        if 'derivations' in packet:
            kind = packet["kind"]
            disc_param = packet['_param_derived']
        else:
            kind = packet['parent']['kind']            
            disc_param = packet['parent']['_param_derived']
            
        if spec["relation"] is not None:
            if int(spec["relation"]) > 0:  # Service provider
                if kind == "TM":
                    namePrefix = "OutCmp"
                else:
                    namePrefix = "InCmd"
            else:
                if kind == "TM":
                    namePrefix = "InRep"
                else:
                    namePrefix = "OutCmp"
        
            disc = "None" if disc_param == None else disc_param['_desc']

            compName = packet["domain"] + packet["name"] + "("+str(packet["type"])+","+str(packet["subtype"])+")"
            g.begin(app["name"], namePrefix + packet["domain"] + packet["name"] + "Spec", "Specification of " + packet["domain"] + packet["name"] + " Component", ["Name", compName], False)

            # Check for 'default implementation" tag in out-going reports and,
            # if present, replace it with appropriate text
            baseSpec = get_ref_spec("repPrvCheckEnable", spec)
            if ('#defaultImplementation' in baseSpec["repPrvCheckEnable"]):
                repPrvCheckEnable = 'Default implementation (report is always enabled)'
            else:
                repPrvCheckEnable = baseSpec["repPrvCheckEnable"]

            baseSpec = get_ref_spec("repPrvCheckReady", spec)
            if ('#defaultImplementation' in baseSpec["repPrvCheckReady"]):
                repPrvCheckReady = 'Default implementation (report is always ready)'
            else:
                repPrvCheckReady = baseSpec["repPrvCheckReady"]

            baseSpec = get_ref_spec("repPrvCheckRepeat", spec)
            if ('#defaultImplementation' in baseSpec["repPrvCheckRepeat"]):
                repPrvCheckRepeat = 'Default implementation (report is not repeated)'
            else:
                repPrvCheckRepeat = baseSpec["repPrvCheckRepeat"]

            baseSpec = get_ref_spec("repPrvActionUpdate", spec)
            if ('#defaultImplementation' in baseSpec["repPrvActionUpdate"]):
                repPrvActionUpdate = 'Default implementation (do nothing)'
            else:
                repPrvActionUpdate = baseSpec["repPrvActionUpdate"]

            # Check for 'default implementation" tag in incoming commands and,
            # if present, replace it with appropriate text
            baseSpec = get_ref_spec("cmdPrvCheckReady", spec)
            if ('#defaultImplementation' in baseSpec["cmdPrvCheckReady"]):
                cmdPrvCheckReady = 'Default implementation (command is always ready)'
            else:
                cmdPrvCheckReady = baseSpec["cmdPrvCheckReady"]

            baseSpec = get_ref_spec("cmdPrvActionStart", spec)
            if ('#defaultImplementation' in baseSpec["cmdPrvActionStart"]):
                cmdPrvActionStart = "Default implementation (set action outcome to 'success')"
            else:
                cmdPrvActionStart = baseSpec["cmdPrvActionStart"]

            baseSpec = get_ref_spec("cmdPrvActionProgress", spec)
            if ('#defaultImplementation' in baseSpec["cmdPrvActionProgress"]):
                cmdPrvActionProgress = "Default implementation (set action outcome to 'completed')"
            else:
                cmdPrvActionProgress = baseSpec["cmdPrvActionProgress"]

            baseSpec = get_ref_spec("cmdPrvActionTermination", spec)
            if ('#defaultImplementation' in baseSpec["cmdPrvActionTermination"]):
                cmdPrvActionTermination = "Default implementation (set action outcome to 'success')"
            else:
                cmdPrvActionTermination = baseSpec["cmdPrvActionTermination"]

            baseSpec = get_ref_spec("cmdPrvActionAbort", spec)
            if ('#defaultImplementation' in baseSpec["cmdPrvActionAbort"]):
                cmdPrvActionAbort = "Default implementation (set action outcome to 'success')"
            else:
                cmdPrvActionAbort = baseSpec["cmdPrvActionAbort"]

            if spec["relation"] is not None:
                if int(spec["relation"]) > 0:  # Service provider
                    if kind == "TM":
                        g.write(["Description",packet["desc"]])
                        g.write(["Parameters",packet["descParam"]])
                        g.write(["Discriminant",disc])
                        g.write(["Destination",packet["descDest"]])
                        g.write(["Enable Check", repPrvCheckEnable])
                        g.write(["Ready Check", repPrvCheckReady])
                        g.write(["Repeat Check", repPrvCheckRepeat])
                        g.write(["Update Action", repPrvActionUpdate])
                    else:
                        g.write(["Description",packet["desc"]])
                        g.write(["Parameters",packet["descParam"]])
                        g.write(["Discriminant",disc])
                        g.write(["Ready Check", cmdPrvCheckReady])
                        g.write(["Start Action", cmdPrvActionStart])
                        g.write(["Progress Action", cmdPrvActionProgress])
                        g.write(["Termination Action", cmdPrvActionTermination])
                        g.write(["Abort Action", cmdPrvActionAbort])
                else:
                    if kind == "TM":
                        g.write(["Description",packet["desc"]])
                        g.write(["Parameters",packet["descParam"]])
                        g.write(["Discriminant",disc])
                        g.write(["Acceptance Check", spec["repUsrCheckAcceptance"]])
                        g.write(["Update Action", spec["repUsrActionUpdate"]])
                    else:
                        g.write(["Description",packet["desc"]])
                        g.write(["Parameters",packet["descParam"]])
                        g.write(["Discriminant",disc])
                        g.write(["Destination",packet["descDest"]])
                        g.write(["Enable Check", spec["cmdUsrCheckEnable"]])
                        g.write(["Ready Check", spec["cmdUsrCheckReady"]])
                        g.write(["Repeat Check", spec["cmdUsrCheckRepeat"]])
                        g.write(["Update Action", spec["cmdUsrActionUpdate"]])

            g.end(False)
    g.close()

if __name__ == '__main__':

    if (len(sys.argv) == 3):

        project_id = sys.argv[1]
        app_id = sys.argv[2]
        try:
            il = get_data.get_data(project_id)            
            app = il["apps"]["hash"][int(app_id)]
            gen_spec("./spec", app["components"]["hash"]["spec"])
            print("Done")
        except Exception as e:
            print("Something went wrong...")
            print(traceback.format_exc())

    else:
        print("Usage: python gen_spec.py {project_id} {application_id}")    
