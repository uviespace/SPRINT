#!/usr/bin/python

import sys
import traceback

import get_data
from csv_generator import *
from tex_generator import *

#-------------------------------------------------------------------------------------
def save_str(s):
    if s != None:
        return str(s)
    return ""

#-------------------------------------------------------------------------------------
def check_service_type(service_type):
    switcherSmile = {
        # Standard Services
        1: True,
        3: True,
        5: True,
        6: True,
        9: True,
        11: False,
        13: True,
        17: True,
        # Private (instrument specific) Services
        20: True,
        191: True,
        192: False,
        193: True,
        194: True,
        195: False,
        197: True,
        198: True,
        210: True,
        211: True,
        212: True,
        213: True
    }
    return switcherSmile.get(service_type, False)

#-------------------------------------------------------------------------------------
def check_service_subtype(service_type, service_subtype):
    switcherSmile = {
        "1,5": False,    # SuccPrgrRep: Successful Progress of Execution Verification Report
        "1,6": False,    # FailedPrgrRep: Failed Progress of Execution Verification Report
        "11,6": False,   # DelTbaFilterCmd: delete the time-based scheduled activities identified by a filter
        "11,7": False,   # TimeShiftTbaRidCmd: time-shift scheduled activities identified by request identifier
        "11,8": False,   # TimeShiftTbaFilterCmd: time-shift the scheduled activities identified by a filter
        "11,9": False,   # RepTimeReportDetRidCmd: detail-report time-based scheduled activities identified by request identifier
        "11,10": False,  # TimeSchedDetRep: time-based schedule detail report
        "11,11": False,  # RepTimeSchedDetFilterCmd: detail-report the time-based scheduled activities identified by a filter
        "11,12": False,  # RepTimeSchedSumRidCmd: summary-report time-based scheduled activities identified by request identifier
        "11,13": False,  # TimeSchedSumRep: time-based schedule summary report
        "11,14": False,  # RepTimeSchedSumFilterCmd: summary-report the time-based scheduled activities identified by a filter
        "11,15": False,  # TimeShiftTbaCmd: time-shift all scheduled activities
        "11,16": False,  # RepTbaDetCmd: detail-report all time-based scheduled activities
        "11,17": False,  # RepTbaSumCmd: summary-report all time-based scheduled activities
        "11,18": False,  # RepSubSchedCmd: report the status of each time-based sub-schedule
        "11,19": False,  # SubSchedRep: time-based sub-schedule status report
        "17,3": False,   # ConnectCmd: Perform On-Board Connection Test
        "17,4": False    # ConnectRep: On-Board Connection Test Report
    }
    return switcherSmile.get(str(service_type)+","+str(service_subtype), True)

#-------------------------------------------------------------------------------------
def outp_services(standard, g):
    g.begin(standard["name"], "Services", "Services", ["Type", "Name", "Description"])
    for service in standard["services"]["list"]:
        if check_service_type(int(service["type"])): # sort out used services
            g.write([save_str(service["type"]), service["name"], service["desc"]])
    g.end()

#-------------------------------------------------------------------------------------
def outp_service_overview(standard, g):
    g.begin(standard["name"], "Service Overview", "Service Overview", ["Type", "Name", "Description", "APID"])
    for service in standard["services"]["list"]:
        if check_service_type(int(service["type"])): # sort out unused services by service type
            g.write([save_str(service["type"]) + " - " + service["name"]])
            for packet in service["packets"]:
                if check_service_subtype(int(packet["type"]), int(packet["subtype"])): # sort out unused services by service type and subtype
                    g.write([
                        "{0}({1},{2})".format(packet["kind"], packet["type"], packet["subtype"]),
                        packet["name"], packet["shortDesc"],packet["process"]["address"]])
    g.end()

#-------------------------------------------------------------------------------------
def outp_service_desc(service, g):
    if check_service_type(int(service["type"])): # sort out unused services by service type
        g.begin(service["name"], "Description"+service["name"], service["name"]+" Commands and Reports", ["Kind", "Type", "Subtype", "Name", "Short Description", "Description", "Parameters", "Destination"])
        for packet in service["packets"]:
            if check_service_subtype(int(packet["type"]), int(packet["subtype"])): # sort out unused services by service type and subtype
                g.write([packet["kind"], packet["type"], packet["subtype"], packet["name"], packet["shortDesc"], packet["desc"], packet["descParam"], packet["descDest"]])
        g.end()

#-------------------------------------------------------------------------------------
def outp_type_def(type_, g):
    if type_["size"] != None and (type_["nativeType"].strip() !=""):
        tname = type_["name"]
        ntname = type_["nativeType"]
        tdesc = type_["desc"]
        g.write(["\\textbf{"+tname+"}", tdesc, ntname])
        if len(type_["enums"]) > 0:        
             for enum in type_["enums"]:
                 g.write(["\\hspace{0.5cm}"+enum["Name"], enum["desc"], enum["_dec"]])

#-------------------------------------------------------------------------------------
# Generate the file defining the data types defined in all the standards
# attached to the argument application.
# NB: General-purpose data types not attached to any standard are not covered. 
def outp_type_list(app, g):
    g.begin(app["name"], "Types", "Types", ["Name", "Description", "Value"])
    for standard_relation in app["standards"]:        
        standard = standard_relation["standard"]
        for type_ in standard["types"].values():
            if type_["ownerStandardId"] == standard["id"]:
                outp_type_def(type_, g)       
    g.end()

#-------------------------------------------------------------------------------------
def outp_elem(element, info, g, rep_desc, size_desc, depth):
    param = element["param"]

    if info["length"] is not None:
        offset_byte = save_str(int(int(info["length"]) / 8))
        offset_bit = save_str(int(info["length"]) % 8)
    else:
        offset_byte = "-"
        offset_bit = "-"

    if param["_length"] != None:
        if param["_length"] >= 0:
            if info["length"] != None:
                info["length"] = info["length"] + param["_length"]
            if param["_multi"] != None and param["_multi"] != 1:
                size = "{1}*{0}".format(save_str(param["_size"]), save_str(param["_multi"]))
            else:
                size = save_str(param["_size"])
            size = size + size_desc
        else:
            info["length"] = None
            size = "variable"
    else:
        info["length"] = None
        size = "undefined"        

    if param["domain"].lower() != "predefined" or param["name"].lower() != "dummy":
        g.write([
            ("- " * depth) + (" " if depth > 0 else "") + param["name"] + rep_desc,
            offset_byte,
            offset_bit,
            size,
            element["_value"],
            element["_desc"]])
        depth = depth + 1
    return depth

#-------------------------------------------------------------------------------------
def outp_cont_line(depth, g):
    g.write([("- " * depth) + (" " if depth > 0 else "") + "...", "", "", "", "", ""])    

#-------------------------------------------------------------------------------------
def process_elem(elements, i, info, g, rep_desc, size_desc, depth):
    element = elements[i]
    group = int(element["group"]) if element["group"] is not None else None
    repetition = int(element["repetition"]) if element["repetition"] is not None else None

    if group != None and group > 0:
        depth = outp_elem(element, info, g, rep_desc, size_desc, depth)
        if repetition != None and repetition > 0:
            # Fixed repetition
            for m in range(0, repetition):
                ii = 0
                while ii < group:
                    ii = process_elem(elements, i+ii+1, info, g, rep_desc + "[{0}]".format(m), size_desc, depth)-i
        else:
            # Dynamic repetition. Length only known for first iteration.
            ii = 0
            while ii < group:
                ii = process_elem(elements, i+ii+1, info, g, rep_desc + "[1]", size_desc, depth)-i
            info["length"] = None
            outp_cont_line(depth, g)
            ii = 0
            while ii < group:
                ii = process_elem(elements, i+ii+1, info, g, rep_desc + "[{0}]".format(element["param"]["name"]), size_desc, depth)-i
        i = i + group
    else:
        if repetition != None and repetition > 0:
            for m in range(0, repetition):
                outp_elem(element, info, g, rep_desc, size_desc, depth)
        else:
            outp_elem(element, info, g, rep_desc, size_desc, depth)
        
    return i

#-------------------------------------------------------------------------------------
def outp_sequence(name, caption, caption_tbl, elements, g):
    if len(elements) > 0:
        outp_sequence_table(name, caption, caption_tbl, elements, g)

#-------------------------------------------------------------------------------------
def outp_sequence_table(name, caption, caption_tbl, elements, g):
    info = {}
    info["length"] = 0
    g.begin(name, caption, caption_tbl, ["Name", "Byte", "Bit", "Size", "Value", "Description"])

    i = 0
    while i < len(elements):
        i = process_elem(elements, i, info, g, "", "", 0)+1

    if info["length"] != None:
        s = "Total bits: {0}\nTotal bytes: {1}\nTotal words: {2}".format(
            save_str(int(info["length"])),
            save_str(int(info["length"])/8.0),
            save_str(int(info["length"])/16.0))
        g.write(["", "", "" ,"", "", s])
    g.end()

#-------------------------------------------------------------------------------------
def outp_type(name, t, g):
    if t["standard"] is None or len(t["enums"]) == 0:
        return

    # Filter out all columns added for easier processing purposes. They all start with an underline.
    keys = []
    for key in t["enums"][0].keys():
        if key[0] != "_":
            keys.append(key)
        
    g.begin(name, t["name"], t["name"], keys)
    for enum in t["enums"]:
        values = []
        for key in keys:
            values.append(enum[key])

        g.write(values)
    g.end()

#-------------------------------------------------------------------------------------
def gen_packet_name(packet):
    return "{0} {1} {2}".format(packet["type"], packet["subtype"], packet["name"])

#-------------------------------------------------------------------------------------
def get_discriminant_param(params):
    for param in params:
        if param["role"] == 3:
            return param
    return None

#-------------------------------------------------------------------------------------
def outp_service(service, g):
    g.setPopCol(4)
    name = service["standard"]["name"]
    for packet in service["packets"]:
        if check_service_type(int(packet["type"])) and check_service_subtype(int(packet["type"]), int(packet["subtype"])):  # sort out unused services by service type and subtype
            body_params = packet["body"]
            if len(packet["derivations"]["list"]) > 0:
                n = 1
                discriminant_param = get_discriminant_param(body_params)
                for derived in packet["derivations"]["list"]:
                    #if derived["disc"] is not None:
                    #    discriminant_param["_value"] = derived["disc"]
                    base_name = "{0}{1}s{2}d{3}".format(name, packet["type"], packet["subtype"], n)
                    params = body_params + derived["body"]
                    caption = "{0}{1}".format(packet["name"], n)
                    caption_tbl = "{0} ({1})".format(packet["name"], derived["disc"])
                    outp_sequence(base_name, caption, caption_tbl, params, g)
                    n = n + 1
            else:
                base_name = u"{0}{1}s{2}".format(name, packet["type"], packet["subtype"])
                outp_sequence(base_name, packet["name"], packet["name"], body_params, g)
    g.setPopCol(None)

#-------------------------------------------------------------------------------------
def outp_packet_details_print_stmt(tex, caption, nelements):
    if nelements > 0:
        tex.writeln(u"\\print{0}{{|l|l|l|l|p{{14cm}}}}".format(tex.texName(caption)))                
    else:
        tex.writeln("")
        tex.writeln("This packet does not have any parameters.")

#-------------------------------------------------------------------------------------
def outp_packet_details(app, tex):
    tex.open(u"{0} {1}.tex".format(app["name"], "PacketDetails"))
    for standard_relation in app["standards"]:
        standard = standard_relation["standard"]
        for packet in standard["packets"]["list"]:
            if len(packet["derivations"]["list"]) > 0:
                n = 1
                for derived in packet["derivations"]["list"]:
                    tex.writeln(u"\\pagebreak")
                    caption = "{0}{1}".format(packet["name"], n)
                    tex.writeln("\\subsection{{{2}({3},{4}) {0} ({1})}}".format(tex.enc(packet["name"]), tex.enc(derived["disc"]), packet["kind"], packet["type"], packet["subtype"]))
                    #tex.writeln(tex.enc(packet["_desc"]))
                    #tex.writeln("")
                    tex.writeln(tex.enc(derived["desc"]))
                    outp_packet_details_print_stmt(tex, caption, len(packet["body"])+len(derived["body"]))
                    tex.writeln("")       
                    n = n + 1             
            else:
                tex.writeln(u"\\pagebreak")
                tex.writeln("\\subsection{{{1}({2},{3}) {0}}}".format(tex.enc(packet["name"]), packet["kind"], packet["type"], packet["subtype"]))
                tex.writeln(tex.enc(packet["desc"]))
                outp_packet_details_print_stmt(tex, packet["name"], len(packet["body"]))
                tex.writeln("")
    tex.close()

#-------------------------------------------------------------------------------------
def outp_datapool(std_name, name, list, g):
    g.begin(std_name, name, name, ["DPID", "Name", "Description", "Default", "Type", "Size"])
    for item in list:
        isArray = (item["multi"] is not None and item["multi"] != 1)
        multi = (1 if not isArray else item["_multi"])

        g.write([
            hex(item["_dpid"]),
            #"{0}/{1}".format(item["domain"], item["name"]) if len(item["domain"]) > 0 else item["name"],
            item["name"],
            item["desc"] if item['desc'] != None and len(item["desc"]) > 0 else item["shortDesc"],
            item["_value"],
            (item["type"]["name"] if item["type"] != None else "") + ("" if not isArray else ("[" + save_str(item["multi"]) + "]")),
            item["_size"] * multi if (item["_size"] != None and multi != None) else ""
        ])
    g.end()

#-------------------------------------------------------------------------------------
def outp_app(app, g):
    app_name = app["name"]
    for standard_relation in app["standards"]:
        standard = standard_relation["standard"]
        std_name = standard["name"]
        g.setPopCol(None)
        outp_sequence(std_name, "TC header", "TC header", standard["headers"]["TC"], g)
        outp_sequence(std_name, "TM header", "TM header", standard["headers"]["TM"], g)
        for s in standard["services"]["list"]:
            outp_service_desc(s,g)
        for t in standard["types"].values():
            outp_type(std_name, t, g)
        outp_services(standard, g)
        outp_service_overview(standard, g)
        for service in standard["services"]["list"]:
            outp_service(service, g)
        outp_datapool(std_name, "Datapool Parameters", standard["datapool"]["params"], g)
        outp_datapool(std_name, "Datapool Variables", standard["datapool"]["vars"], g)

#-------------------------------------------------------------------------------------
def outp_gen_files(app, tex):
    fileNames = tex.fileNames[:]
    tex.open(u"{0}.tex".format(app["name"]))          
    tex.writeln("\\def \\SetPacketDetailsTableSpec#1 {\\def\\@tblSpecPacketDetails{#1}}")          
    tex.writeln("% Use following line to overwrite the table spec for the packet details.")
    tex.writeln("\\SetPacketDetailsTableSpec{|l|l|l|l|l|}")
    for fileName in fileNames:
        tex.writeln(u"\\input{{./GeneratedTables/{0}}}".format(fileName))
    tex.close()

#-------------------------------------------------------------------------------------
# Generate the file defining the data types defined in all the standards
# attached to the argument application.
# NB: General-purpose data types not attached to any standard are not covered. 
def outp_app_types(path, app):
    def print_type(f, type_):
        if type_["size"] != None and (type_["nativeType"].strip() !=""):
            tname = type_["name"]
            ntname = type_["nativeType"]
            writeln(f, "/** Definition of type \"{0}\" */".format(tname))
            writeln(f, "typedef {0} {1};".format(ntname, tname))
            if len(type_["enums"]) > 0:        
                writeln(f, "enum {")
                for i, enum in enumerate(type_["enums"]):
                    isLast = (i == len(type_["enums"])-1)
                    writeln(f, "{0} = {1}{2}".format(cname(enum["Name"]), enum["_dec"], '' if isLast else ','), 1)
                writeln(f, "};")
            writeln(f, "")

    f = list()
    gen_includes(f)
    for standard_relation in app["standards"]:
        standard = standard_relation["standard"]
        for type_ in standard["types"].values():
            if type_["ownerStandardId"] == standard["id"]:
                print_type(f, type_)       
    gen_file(f, path, gen_file_name_h("Types"), True, False, "Type definitions.")


#-------------------------------------------------------------------------------------
def gen_icd(path, comp):
    settings = comp["setting"]
    app = comp["app"]

    if settings is None:
        return

    if settings["CSV"]["Enabled"]:
        csv = CsvGenerator(path, settings["CSV"])
        outp_app(app, csv)
        outp_type_list(app, csv)
    
    if settings["LaTeX"]["Enabled"]:
        tex = TexGenerator(path, settings["LaTeX"])
        outp_app(app, tex)
        outp_gen_files(app, tex)
        outp_packet_details(app, tex)    
        outp_type_list(app, tex)

if __name__ == '__main__':

    if (len(sys.argv) == 3):

        project_id = sys.argv[1]
        app_id = sys.argv[2]
        try:
            il = get_data.get_data(project_id)            
            app = il["apps"]["hash"][int(app_id)]
            gen_icd("./icd", app["components"]["hash"]["icd"])
            print("Done")
        except Exception as e:
            print("Something went wrong...")
            print(traceback.format_exc())

    else:
        print("Usage: python gen_icd.py {project_id} {application_id}")
