#!/usr/bin/python

import sys
import datetime
import os
import traceback
import re

import get_data
import fig_ref_conv

settings = {}
filenameCap = None

def cname(s):
    return s.replace(" ", "").replace("-", "").replace("/", "").replace("\\", "")

def writeln(f, s, indent_level=0):
    f += "{0}{1}\n".format(" " * get_indent(indent_level), s.encode("utf8"))

def write_separator(f):
    f += "/*{0}*/\n".format("-" * (settings["max_line_length"]-4))

def gen_file(f, path, s, isHeader, addTypeInclude=True, desc="Interface for accessing data pool items."):
    f_ = new_file(path, s, isHeader, addTypeInclude, desc)
    f_.write(fig_ref_conv.fig_ref_conv(''.join(f), "doxy"))
    close_file(f_)

def gen_file_name(dname):
    return "{0}{1}".format(settings["prefix"], dname)

def gen_file_name_h(dname):
    return gen_file_name(dname) + ".h"

def gen_file_name_c(dname):
    return gen_file_name(dname) + ".c"    

def new_file(path, s, isHeader, addTypeInclude=True, desc="Interface for accessing data pool items."):
    def ensure_dir(file_path):
        directory = os.path.dirname(file_path)
        if not os.path.exists(directory):
            os.makedirs(directory)

    global filenameCap

    ensure_dir(path)
    f = list()
    write_doxy(f, [ 
        "@file",
        "@ingroup gen_cfw",
        "",
        desc,
        "",
        "@note This file was generated on {0}".format(datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")),
        "@author " + settings["author"],
        "@copyright " + settings["copyright"]
    ])
    if isHeader:
        filenameCap = s.upper().replace('.', '_')
        writeln(f, "#ifndef {0}_".format(filenameCap))
        writeln(f, "#define {0}_".format(filenameCap))
        if addTypeInclude:
            writeln(f, "")
            writeln(f, "#include \"{0}\"".format(gen_file_name_h("Constants")))
            writeln(f, "#include \"{0}\"".format(gen_file_name_h("Types")))
    else:
        filenameCap = None
    writeln(f, "")
    f_ = open("{0}/{1}".format(path, s), "w")
    f_.write(''.join(f))
    return f_

def close_file(f_):
    global filenameCap

    f = list()
    if filenameCap != None:
        writeln(f, "")
        write_separator(f)
        writeln(f, "#endif /* {0} */".format(filenameCap))

    f_.write(''.join(f))
    f_.close

def write_comment_text(f, text, indent_level = 0):
    for block in text.split('\n'):
        write_comment_text_block(f, block, indent_level)

def write_comment_text_block(f, text, indent_level = 0):
    line = ""
    lines = []
    max_line_length = settings["max_line_length"]-get_indent(indent_level)-4
    for word in text.split():
        if (len(line) + len(word)) < max_line_length:
            line = line + " " + word
        else:
            lines.append(line)
            line = " " + word
    if len(line) > 0:
        lines.append(line)
    if len(lines) > 0:
        for line in lines:
            writeln(f, " *" + line, indent_level)
    else:
        writeln(f, " *")

def write_doxy(f, params = [], indent_level = 0):
    writeln(f, "/**", indent_level)
    for param in params:
        write_comment_text(f, param, indent_level)
    writeln(f, " */", indent_level)

def get_indent(level):
    return settings["indent"] * level

#-------------------------------------------------------------------------------
# String 'key' holds the name of a check or action of a command or report
# ('repPrvCheckEnable', 'repPrvCheckReady', etc).
# Object 'spec' holds the specification of a command or report.
# The specification of the 'key' check/action is then equal to: spec[key]
#
# This function checks whether spec[key] has the form '#TM(x,y)' or 'TC(x,y)'.
# If this is so, then it looks for the specification object corresponding to
# the referenced report or command and returns it.
#
# If no match is found, the function checks whether spec[key] has the form 
# '#TM(x,y,d)' or 'TC(x,y,d)'.
# If this is so, then it looks for the specification object corresponding to
# the referenced report or command and returns it.
#
# If no match is found, it is concluded that the specification field is not
# referencing any other specification object and the input specification object
# 'spec' is returned.
#
def get_ref_spec(key, spec):
    spec_text = spec[key]
    pattern = "#T[MC]\(\s*\d+\s*,\s*\d+\s*\)"
    m = re.search(pattern, spec_text)
    if m != None:
         idx_comma = spec_text.find(",", m.start(), m.end())
         type_ = int(spec_text[m.start()+4:idx_comma])
         subtype = int(spec_text[idx_comma+1:m.end()-1])
         for spec_ in spec["app"]["specifications"]:
             if spec_ != spec:
                  packet = spec_["packet"]
                  if "parent" not in packet and \
                      packet["type"] == type_ and \
                      packet["subtype"] == subtype:

                      return spec_

    pattern = "#T[MC]\(\s*\d+\s*,\s*\d+\s*,\s*\w+\s*\)"
    m = re.search(pattern, spec_text)
    if m != None:
         idx_comma_1 = spec_text.find(",", m.start(), m.end())
         idx_comma_2 = spec_text.find(",", idx_comma_1+1, m.end())
         type_ = int(spec_text[m.start()+4:idx_comma_1].strip())
         subtype = int(spec_text[idx_comma_1+1:idx_comma_2].strip())
         discriminant = spec_text[idx_comma_2+1:m.end()-1].strip()
         for spec_ in spec["app"]["specifications"]:
            if spec_ != spec:
                packet = spec_["packet"]
                if "parent" in packet and \
                    packet["parent"]["type"] == type_ and \
                    packet["parent"]["subtype"] == subtype and \
                    packet["disc"] == disc:
                 
                    return spec_
                    
    return spec

#-------------------------------------------------------------------------------
# String spec_text holds one specification field of a command or report.
# This function returns TRUE if the an implementation for the specification
# field is needed and FALSE otherwise.
# A specification is not needed in the following cases:
# - The specification field contains '#defaultImplementation'
# - The specification field holds a reference to another specification field
#   (i.e. it is of a form like: '#TM(x,y)'
#
def is_impl_needed(spec_text):

    if "#defaultImplementation" in spec_text:
        return False
        
    pattern = "#T[MC]\(\s*\d+\s*,\s*\d+\s*\)"
    m = re.search(pattern, spec_text)
    if m != None:
        return False    

    pattern = "#T[MC]\(\s*\d+\s*,\s*\d+\s*,\s*\w+\s*\)"
    m = re.search(pattern, spec_text)
    if m != None:
        return False

    return True

#-------------------------------------------------------------------------------
def gen_spec(path, spec):
    def gen_stub(h, incCode, name, argName, retValue):
        if incCode:
            writeln(f, "{")
            writeln(f, "CRFW_UNUSED({0});".format(argName), 1)
            writeln(f, "DBG(\"{0}\");".format(name), 1)
            writeln(f, "return {0};".format(retValue), 1)
            writeln(f, "}")
        
    def gen_funcs(h, incCode, spec, full_name, pname, kind):
        term = ";" if not incCode else ""
        if spec["relation"] > 0: # Service provider
            if kind == "TC":
                if is_impl_needed(spec["cmdPrvCheckAcceptance"]):
                    write_doxy(h, [
                        "Validity check of {0}.".format(full_name),
                        spec["cmdPrvCheckAcceptance"],
                        "@param prDesc The descriptor of the validity check procedure.",
                        "@return The validity check result."
                    ])
                    writeln(h, "CrFwBool_t {0}{1}ValidityCheck(FwPrDesc_t prDesc){2}".format(settings["prefix"], pname, term))     
                    gen_stub(h, incCode, "{0}{1}ValidityCheck".format(settings["prefix"], pname), "prDesc", "1")       
                    writeln(h, "")
                if is_impl_needed(spec["cmdPrvCheckReady"]):
                    write_doxy(h, [
                        "Ready check of {0}.".format(full_name),
                        spec["cmdPrvCheckReady"],
                        "@param smDesc The state machine descriptor.",
                        "@return The ready check result."
                    ])
                    writeln(h, "CrFwBool_t {0}{1}ReadyCheck(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))            
                    gen_stub(h, incCode, "{0}{1}ReadyCheck".format(settings["prefix"], pname), "smDesc", "1")       
                    writeln(h, "")
                if is_impl_needed(spec["cmdPrvActionStart"]):
                    write_doxy(h, [
                        "Start action of {0}.".format(full_name),
                        spec["cmdPrvActionStart"],
                        "@param smDesc The state machine descriptor."
                    ])
                    writeln(h, "void {0}{1}StartAction(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))            
                    gen_stub(h, incCode, "{0}{1}StartAction".format(settings["prefix"], pname), "smDesc", "")       
                    writeln(h, "")
                if is_impl_needed(spec["cmdPrvActionProgress"]):
                    write_doxy(h, [
                        "Progress action of {0}.".format(full_name),
                        spec["cmdPrvActionProgress"],
                        "@param smDesc The state machine descriptor."
                    ])
                    writeln(h, "void {0}{1}ProgressAction(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))            
                    gen_stub(h, incCode, "{0}{1}ProgressAction".format(settings["prefix"], pname), "smDesc", "")       
                    writeln(h, "")
                if is_impl_needed(spec["cmdPrvActionTermination"]):
                    write_doxy(h, [
                        "Termination action of {0}.".format(full_name),
                        spec["cmdPrvActionTermination"],
                        "@param smDesc The state machine descriptor."
                    ])
                    writeln(h, "void {0}{1}TerminationAction(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))                
                    gen_stub(h, incCode, "{0}{1}TerminationAction".format(settings["prefix"], pname), "smDesc", "")       
                    writeln(h, "")
                if is_impl_needed(spec["cmdPrvActionAbort"]):
                    write_doxy(h, [
                        "Abort action of {0}.".format(full_name),
                        spec["cmdPrvActionAbort"],
                        "@param smDesc The state machine descriptor."
                    ])
                    writeln(h, "void {0}{1}AbortAction(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))                
                    gen_stub(h, incCode, "{0}{1}AbortAction".format(settings["prefix"], pname), "smDesc", "")       
            else:
                if is_impl_needed(spec["repPrvCheckEnable"]):
                    write_doxy(h, [
                        "Enable check of {0}.".format(full_name),
                        spec["repPrvCheckEnable"],
                        "@param smDesc The state machine descriptor.",
                        "@return The enable check result."
                    ])
                    writeln(h, "CrFwBool_t {0}{1}EnableCheck(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))
                    gen_stub(h, incCode, "{0}{1}EnableCheck".format(settings["prefix"], pname), "smDesc", "1")       
                    writeln(h, "")
                if is_impl_needed(spec["repPrvCheckReady"]):
                    write_doxy(h, [
                        "Ready check of {0}.".format(full_name),
                        spec["repPrvCheckReady"],
                        "@param smDesc The state machine descriptor.",
                        "@return The ready check result."
                    ])
                    writeln(h, "CrFwBool_t {0}{1}ReadyCheck(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))
                    gen_stub(h, incCode, "{0}{1}ReadyCheck".format(settings["prefix"], pname), "smDesc", "1")       
                    writeln(h, "")
                if is_impl_needed(spec["repPrvCheckRepeat"]):
                    write_doxy(h, [
                        "Repeat check of {0}.".format(full_name),
                        spec["repPrvCheckRepeat"],
                        "@param smDesc The state machine descriptor.",
                        "@return The repeat check result."
                    ])
                    writeln(h, "CrFwBool_t {0}{1}RepeatCheck(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))
                    gen_stub(h, incCode, "{0}{1}RepeatCheck".format(settings["prefix"], pname), "smDesc", "1")       
                    writeln(h, "")
                if is_impl_needed(spec["repPrvActionUpdate"]):
                    write_doxy(h, [
                        "Update action of {0}.".format(full_name),
                        spec["repPrvActionUpdate"],
                        "@param smDesc The state machine descriptor."
                    ])
                    writeln(h, "void {0}{1}UpdateAction(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))
                    gen_stub(h, incCode, "{0}{1}UpdateAction".format(settings["prefix"], pname), "smDesc", "")       
        else:
            if kind == "TC":
                if is_impl_needed(spec["cmdUsrCheckEnable"]):
                    write_doxy(h, [
                        "Enable check of {0}.".format(full_name),
                        spec["cmdUsrCheckEnable"],
                        "@param smDesc The state machine descriptor.",
                        "@return The enable check result."
                    ])
                    writeln(h, "CrFwBool_t {0}{1}EnableCheck(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))
                    gen_stub(h, incCode, "{0}{1}EnableCheck".format(settings["prefix"], pname), "smDesc", "1")       
                    writeln(h, "")
                if is_impl_needed(spec["cmdUsrCheckReady"]):
                    write_doxy(h, [
                        "Ready check of {0}.".format(full_name),
                        spec["cmdUsrCheckReady"],
                        "@param smDesc The state machine descriptor.",
                        "@return The ready check result."
                    ])
                    writeln(h, "CrFwBool_t {0}{1}ReadyCheck(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))
                    gen_stub(h, incCode, "{0}{1}ReadyCheck".format(settings["prefix"], pname), "smDesc", "1")       
                    writeln(h, "")
                if is_impl_needed(spec["cmdUsrCheckRepeat"]):
                    write_doxy(h, [
                        "Repeat check of {0}.".format(full_name),
                        spec["cmdUsrCheckRepeat"],
                        "@param smDesc The state machine descriptor.",
                        "@return The repeat check result."
                    ])
                    writeln(h, "CrFwBool_t {0}{1}RepeatCheck(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))            
                    gen_stub(h, incCode, "{0}{1}RepeatCheck".format(settings["prefix"], pname), "smDesc", "1")       
                    writeln(h, "")            
                if is_impl_needed(spec["cmdUsrActionUpdate"]):
                    write_doxy(h, [
                        "Update action of {0}.".format(full_name),
                        spec["cmdUsrActionUpdate"],
                        "@param smDesc The state machine descriptor."
                    ])
                    writeln(h, "void {0}{1}UpdateAction(FwSmDesc_t smDesc){2}".format(settings["prefix"], pname, term))
                    gen_stub(h, incCode, "{0}{1}UpdateAction".format(settings["prefix"], pname), "smDesc", "")       
            else: # TM
                if is_impl_needed(spec["repUsrCheckAcceptance"]):
                    write_doxy(h, [
                        "Acceptance check of {0}.".format(full_name),
                        spec["repUsrCheckAcceptance"],
                        "@param prDesc The descriptor of the Reset Procedure of the InReport.",
                        "@return The acceptance check result."
                    ])
                    writeln(h, "CrFwBool_t {0}{1}AcceptanceCheck(FwPrDesc_t prDesc){2}".format(settings["prefix"], pname, term))                    
                    gen_stub(h, incCode, "{0}{1}AcceptanceCheck".format(settings["prefix"], pname), "prDesc", "1")       
                    writeln(h, "")            
                if is_impl_needed(spec["repUsrActionUpdate"]):
                    write_doxy(h, [
                        "Update action of {0}.".format(full_name),
                        spec["repUsrActionUpdate"],
                        "@param prDesc The descriptor of the Execution Procedure of the InReport."
                    ])
                    writeln(h, "void {0}{1}UpdateAction(FwPrDesc_t prDesc){2}".format(settings["prefix"], pname, term))        
                    gen_stub(h, incCode, "{0}{1}UpdateAction".format(settings["prefix"], pname), "prDesc", "")       

    packet = spec["packet"]
    if 'derivations' in packet:
        p = packet
    else:
        p = packet["parent"]

    pdom = packet["domain"]
    pname = cname(packet["name"])
    if (packet["spec"]["relation"] > 0): 
        if (packet["kind"]=="TC"):      # TC in a service provider application
            cmpKind = "InCmd"
            cmpKindName = "incoming command"
        else:
            cmpKind = "OutCmp"          # TM report in a service provider appliation
            cmpKindName = "out-going report"
    else:
        if (packet["kind"]=="TC"):      # TC in a service user application
            cmpKind = "OutCmp"
            cmpKindName = "out-going commend"
        else:
            cmpKind = "InRep"          # TM report in a service user appliation
            cmpKindName = "incoming report"
    
    full_name = "{0}({1},{2}) {3}{4}".format(p["kind"], p["type"], p["subtype"], p["domain"], p["name"])
    desc = "Implementation of {0} as an {1}.".format(full_name,cmpKindName)
    
    f = list()
    writeln(f, "#include \"FwSmCore.h\"")
    writeln(f, "#include \"CrFwConstants.h\"")
    writeln(f, "")
    gen_funcs(f, False, spec, full_name, cmpKind+pdom+pname, p["kind"])
    gen_file(f, path+"/"+packet["domain"]+"/", gen_file_name_h(cmpKind+pdom+pname), True, True, desc)

    f = list()
    writeln(f, "#include \"{0}\"".format(gen_file_name_h(cmpKind+pdom+pname)))
    writeln(f, "")
    gen_funcs(f, True, spec, full_name, cmpKind+pdom+pname, p["kind"])
    gen_file(f, path+"/"+packet["domain"]+"/", gen_file_name_c(cmpKind+pdom+pname), False, False, desc)

#-------------------------------------------------------------------------------
def get_cr_check(spec, kind, spec_base, default, adaptationPoint, pname1):
    useDefault = True
    if spec != None:
        if kind == "TC":
            if spec["relation"] > 0: # service provider
                key = "cmdPrv" + spec_base
            else:
                key = "cmdUsr" + spec_base
        else:
            if spec["relation"] > 0: # service provider
                key = "repPrv" + spec_base
            else:
                key = "repUsr" + spec_base    
        refSpec = get_ref_spec(key, spec)
        pname2 = cname(refSpec["packet"]["name"])
        spec_text = refSpec[key]
            
        useDefault = ("#defaultImplementation" in spec_text)

    if useDefault:
        return default
    else:
        return "{0}{1}{2}".format(settings["prefix"], pname1+pname2, adaptationPoint)

#-------------------------------------------------------------------------------
def gen_cr_fw_out_factory_user_par(path, app):

    def print_entry(f, servType, servSubType, discriminant, cmdRepType, pcktLength, isEnabled, isReady, isRepeat, update, serialize, comma):
        writeln(f, "{{{0}, {1}, {2}, {3}, {4}, &{5}, &{6}, \\".format(servType, servSubType, discriminant, cmdRepType, pcktLength, isEnabled, isReady), 1)
        writeln(f, "&{0}, &{1}, &{2}}}{3} \\".format(isRepeat, update, serialize, comma), 2)

    f = list()
    
    # Generate list of include files which defines the functions used in definition of CR_FW_OUTCMP_INIT_KIND_DESC 
    for spec in app["__dp_spec_out_cmp"]:
        writeln(f, "#include \"{1}/{0}{2}.h\"".format(settings["prefix"],spec["packet"]["domain"],"OutCmp"+spec["packet"]["domain"]+spec["packet"]["name"]))  
    writeln(f, "")
    
    write_doxy(f, [
        "The maximum number of OutComponents which may be allocated at any one time."
    ])
    writeln(f, "#define CR_FW_OUTFACTORY_MAX_NOF_OUTCMP ({0})".format(settings["CrFwOutFactoryMaxNOfOutCmp"]))
    writeln(f, "")
    write_doxy(f, [
        "The total number of kinds of OutComponents supported by the application."
    ])
    writeln(f, "#define CR_FW_OUTCMP_NKINDS ({0})".format(len(app["__dp_spec_out_cmp"])))
    writeln(f, "")
    write_doxy(f, [
        "Definition of the OutComponent kinds supported by an application."
    ])
    writeln(f, "#define CR_FW_OUTCMP_INIT_KIND_DESC {\\")
    for i, spec in enumerate(app["__dp_spec_out_cmp"]):     
        last_pck = (i == len(app["__dp_spec_out_cmp"])-1)
        servType = spec["packet"]["type"]
        servSubType = spec["packet"]["subtype"]
        cmdRepType = "1" if spec["packet"]["kind"] == "TC" else "2"
        pcktLength = spec["packet"]["_header_length"] + (spec["packet"]["_length"] if spec["packet"]["_length"] != None else 0)
        pname1 = "OutCmp" + spec["packet"]["domain"]
        pname2 = cname(spec["packet"]["name"])
        discriminant = "0"
        isEnabled = get_cr_check(spec, spec["packet"]["kind"], "CheckEnable", "CrFwOutCmpDefEnableCheck", "EnableCheck", pname1)
        isReady = get_cr_check(spec, spec["packet"]["kind"], "CheckReady", "CrFwSmCheckAlwaysTrue", "ReadyCheck", pname1)
        isRepeat = get_cr_check(spec, spec["packet"]["kind"], "CheckRepeat", "CrFwSmCheckAlwaysFalse", "RepeatCheck", pname1)
        update = get_cr_check(spec, spec["packet"]["kind"], "ActionUpdate", "CrFwSmEmptyAction", "UpdateAction", pname1)
        serialize = "CrFwOutCmpDefSerialize" # todo
        discriminant = spec["packet"]["disc"]
        comma = "" if last_pck else ","
        print_entry(f, servType, servSubType, discriminant, cmdRepType, pcktLength/8, isEnabled, isReady, isRepeat, update, serialize, comma)
    writeln(f, "}")
    gen_file(f, path, "CrFwOutFactoryUserPar.h", True, False, "This file is part of the PUS Extension of the CORDET Framework.")

#-------------------------------------------------------------------------------
def gen_cr_fw_in_factory_user_par(path, app):
    def print_in_cmd_line(f, servType, servSubType, discriminant, isValid, isReady, startAction, progressAction, terminationAction, abortAction, comma):
        writeln(f, "{{{0}, {1}, {2}, &{3}, &{4}, &{5}, \\".format(servType, servSubType, discriminant, isValid, isReady, startAction), 1)
        writeln(f, "&{0}, &{1}, &{2}}}{3} \\".format(progressAction, terminationAction, abortAction, comma), 2)        

    def print_in_rep_line(f, servType, servSubType, discriminant, updateAction, isValid, comma):
        writeln(f, "{{{0}, {1}, {2}, &{3}, &{4}}}{5} \\".format(servType, servSubType, discriminant, updateAction, isValid, comma), 1)

    f = list()
    # Generate list of include files which defines the functions used in definition of CR_FW_INCMD_INIT_KIND_DESC 
    for spec in app["__dp_spec_in_cmd"]:
        writeln(f, "#include \"{1}/{0}{2}.h\"".format(settings["prefix"],spec["packet"]["domain"],"InCmd"+spec["packet"]["domain"]+spec["packet"]["name"]))  
    writeln(f, "")
    # Generate list of include files which defines the functions used in definition of CR_FW_INREP_INIT_KIND_DESC 
    for spec in app["__dp_spec_in_rep"]:
        writeln(f, "#include \"{1}/{0}{2}.h\"".format(settings["prefix"],spec["packet"]["domain"],"InRep"+spec["packet"]["domain"]+spec["packet"]["name"]))  
    writeln(f, "")
    # Add include files for default validity check functions
    writeln(f, "#include \"InCmd/CrFwInCmd.h\" ")
    writeln(f, "#include \"InRep/CrFwInRep.h\" ")
    
    write_doxy(f, [ 
        "The maximum number of InCommands which may be allocated at any one time."
    ])
    writeln(f, "#define CR_FW_INFACTORY_MAX_NOF_INCMD ({0})".format(settings["CrFwInFactoryMaxNOfInCmd"]))
    writeln(f, "")
    
    write_doxy(f, [
        "The maximum number of InReports which may be allocated at any one time."
    ])
    writeln(f, "#define CR_FW_INFACTORY_MAX_NOF_INREP ({0})".format(settings["CrFwInFactoryMaxNOfInRep"]))
    writeln(f, "")
    
    write_doxy(f, [
        "The total number of kinds of incoming commands supported by the application."
    ])
    writeln(f, "#define CR_FW_INCMD_NKINDS ({0})".format(len(app["__dp_spec_in_cmd"])))
    writeln(f, "")
    write_doxy(f, [
        "The total number of kinds of incoming reports supported by the application."
    ])
    writeln(f, "#define CR_FW_INREP_NKINDS ({0})".format(len(app["__dp_spec_in_rep"])))
    writeln(f, "")
    write_doxy(f, [
        "Definition of the incoming command kinds supported by an application. Each record contains",
        "the following fields:",
        "- The type of the InCommand",
        "- The sub-type of the InCommand",
        "- The discriminant of the InCommand (or zero if no discriminant is specified)",
        "- The function implementing the Validity Check for the InCommand",
        "- The function implementing the Ready Check for the InCommand",
        "- The function implementing the Start Action for the InCommand",
        "- The function implementing the Progress Action for the InCommand",
        "- The function implementing the Termination Action for the InCommand",
        "- The function implementing the Abort Action for the InCommand",
        "."
    ])
    writeln(f, "#define CR_FW_INCMD_INIT_KIND_DESC {\\")
    for i, spec in enumerate(app["__dp_spec_in_cmd"]):
        last_pck = (i == len(app["__dp_spec_in_cmd"])-1)
        pname1 = "InCmd" + spec["packet"]["domain"]
        pname2 = cname(spec["packet"]["name"])
        servType = spec["packet"]["type"]
        servSubType = spec["packet"]["subtype"]
        isValid = get_cr_check(spec, spec["packet"]["kind"], "CheckAcceptance", "CrFwInCmdDefValidityCheck", "ValidityCheck", pname1)
        isReady = get_cr_check(spec, spec["packet"]["kind"], "CheckReady", "CrFwSmCheckAlwaysTrue", "ReadyCheck", pname1)
        startAction = get_cr_check(spec, spec["packet"]["kind"], "ActionStart", "CrFwSmEmptyAction", "StartAction", pname1)
        progressAction = get_cr_check(spec, spec["packet"]["kind"], "ActionProgress", "CrFwSmEmptyAction", "ProgressAction", pname1)
        terminationAction = get_cr_check(spec, spec["packet"]["kind"], "ActionTermination", "CrFwSmSuccessAction", "TerminationAction", pname1)
        abortAction = get_cr_check(spec, spec["packet"]["kind"], "ActionAbort", "CrFwSmEmptyAction", "AbortAction", pname1)
        comma = "" if last_pck else ","
        if len(spec["packet"]["derivations"]["list"]) == 0:     # this is a base packet
            discriminant = "0"
        else:
            discriminant = spec["packet"]["disc"]
        print_in_cmd_line(f, servType, servSubType, discriminant, isValid, isReady, startAction, progressAction, terminationAction, abortAction, comma)

    writeln(f, "}")
    writeln(f, "")
    write_doxy(f, [
        "Definition of the incoming report kinds supported by an application. Each record contains",
        "the following fields:",
        "- The type of the InReport",
        "- The sub-type of the InReport",
        "- The discriminant of the InReport (or zero if no discriminant is specified)",
        "- The function implementing the Update Action for the InReport",
        "- The function implementing the Validity Check for the InReport",
        "."
    ])
    writeln(f, "#define CR_FW_INREP_INIT_KIND_DESC {\\")
    for i, spec in enumerate(app["__dp_spec_in_rep"]):
        last_pck = (i == len(app["__dp_spec_in_rep"])-1)
        pname1 = "InRep" + spec["packet"]["domain"]
        pname2 = cname(spec["packet"]["name"])
        servType = spec["packet"]["type"]
        servSubType = spec["packet"]["subtype"]
        updateAction = get_cr_check(spec, spec["packet"]["kind"], "ActionUpdate", "CrFwPrEmptyAction", "UpdateAction", pname1)        
        isValid = get_cr_check(spec, spec["packet"]["kind"], "CheckAcceptance", "CrFwInRepDefValidityCheck", "AcceptanceCheck", pname1)            
        comma = "" if last_pck else ","
        if len(spec["packet"]["derivations"]["list"]) == 0:     # this is a base packet
            discriminant = "0"
        else:
            discriminant = spec["packet"]["disc"]
        print_in_rep_line(f, servType, servSubType, discriminant, updateAction, isValid, comma)

    writeln(f, "}")    
    gen_file(f, path, "CrFwInFactoryUserPar.h", True, False, "This file is part of the PUS Extension of the CORDET Framework.")

#---------------------------------------------------------------------------------
def gen_cr_fw_out_registry_user_par(path, app):
    def create_list_of_outgoing_types_subtypes(spec_out_cmp):
        list_of_outgoing_types_subtypes = []
        for i,spec in enumerate(spec_out_cmp):
            if (i == 0):
                spec_prev = spec
                n = 0
            if (spec_prev["packet"]["type"] == spec["packet"]["type"]) and (spec_prev["packet"]["subtype"] == spec["packet"]["subtype"]):
                n = n + 1
            else:
                list_element = {}
                list_element["type"] = spec_prev["packet"]["type"]
                list_element["subtype"] = spec_prev["packet"]["subtype"]
                list_element["n_of_same_type_subtype"] = n
                if (spec_prev["packet"]["_param_derived"] != None):
                    min_disc = spec_prev["packet"]["_param_derived"]["datatype"]["enumsMin"]
                    max_disc = spec_prev["packet"]["_param_derived"]["datatype"]["enumsMax"]
                else:
                    min_disc = 0
                    max_disc = 0                
                list_element["max_disc"] = max_disc
                list_element["min_disc"] = min_disc
                list_of_outgoing_types_subtypes.append(list_element)
                spec_prev = spec
                n = 0
            if (i == len(spec_out_cmp)-1):
                list_element = {} 
                list_element["type"] = spec["packet"]["type"]
                list_element["subtype"] = spec["packet"]["subtype"]
                if (spec_prev["packet"]["_param_derived"] != None):
                    min_disc = spec_prev["packet"]["_param_derived"]["datatype"]["enumsMin"]
                    max_disc = spec_prev["packet"]["_param_derived"]["datatype"]["enumsMax"]
                else:
                    min_disc = 0
                    max_disc = 0                
                list_element["max_disc"] = max_disc
                list_element["min_disc"] = min_disc
                if (spec_prev["packet"]["type"] == spec["packet"]["type"]) and (spec_prev["packet"]["subtype"] == spec["packet"]["subtype"]):
                    list_element["n_of_same_type_subtype"] = n
                else:
                    list_element["n_of_same_type_subtype"] = 1
                list_of_outgoing_types_subtypes.append(list_element)
        return list_of_outgoing_types_subtypes       
        
    f = list()
    list_of_outgoing_types_subtypes = create_list_of_outgoing_types_subtypes(app["__dp_spec_out_cmp"])
    writeln(f, "#include \"{0}\"".format(gen_file_name_h("Constants")))    
    writeln(f, "#include \"{0}\"".format(gen_file_name_h("Types")))    
    writeln(f, "")    
    write_doxy(f, [
        "The total number of out-going service types/sub-types provided by the application."
    ])
    writeln(f, "#define CR_FW_OUTREGISTRY_NSERV ({0})".format(len(list_of_outgoing_types_subtypes)))
    writeln(f, "")
    write_doxy(f, [
        "The maximum number of out-going commands or reports which can be tracked by the OutRegistry."
    ])
    writeln(f, "#define CR_FW_OUTREGISTRY_N ({0})".format(settings["CrFwOutRegistryN"]))
    writeln(f, "")
    write_doxy(f, [
        "Definition of the range of out-going services supported by an application.",
        "An application supports a number of service types and, for each service type, it supports a number of sub-types.",
        "Each sub-type may support a range of discriminant values.",
        "Each line in this initializer defines one [type,sub-type] pair supported by the application.",
        "The fields in the initializer are as follows:",
        "- The service type",
        "- The service sub-type",
        "- The lower bound l of the range of discriminant values associated to the [type,sub-type] pair",
        "- The upper bound u of the range of discriminant values associated to the [type,sub-type] pair",
        "- Four values used internally by the software which must be set to zero",
        ".",
        "If a [type,sub-type] pair does not support a discriminant, then l and u are set to zero.",
        "The list of service descriptors satisfies the following constraints:",
        "- The number of lines must be the same as <code>#CR_FW_OUTREGISTRY_NSERV</code>.",
        "- The service types must be listed in increasing order.",
        "- The service sub-types within a service type must be listed in increasing order.",
        "- The set of service type and sub-types must be consistent with the service types and sub-types declared in the <code>#CR_FW_OUTCMP_INIT_KIND_DESC</code> initializer.",
        "- The lower bound l of a range of discriminant values must be smaller than or equal to the upper bound u.",
        ".",
        "Compliance with the last four constraints is checked by <code>::CrFwAuxOutRegistryConfigCheck</code>."
    ])
    writeln(f, "#define CR_FW_OUTREGISTRY_INIT_SERV_DESC { \\")
    type_prev = 0
    subtype_prev = 0
    n = 0
    for i,item in enumerate(list_of_outgoing_types_subtypes):
        last = (i == len(list_of_outgoing_types_subtypes)-1)
        comma = "," if not last else ""
        writeln(f, "{{{0}, {1}, {2}, {3}, 0, 0, 0, 0}}{4} \\".format(item["type"], item["subtype"], item["min_disc"], item["max_disc"], comma), 1)
    writeln(f, "}")
    gen_file(f, path, "CrFwOutRegistryUserPar.h", True, False, "This file is part of the PUS Extension of the CORDET Framework.")

#------------------------------------------------------------------------------
def gen_app_constants(path, app):
    def print_constant(f, constant_):
        cname = constant_["name"]
        write_doxy(f, [
            "Definition of constant \"{0}\".".format(cname),
            constant_["desc"]
        ])
        writeln(f, "#define {0} ({1})".format(constant_["name"], constant_["value"]))

    f = list()
    for standard_relation in app["standards"]:
        standard = standard_relation["standard"]
        for constant_ in standard["constants"].values():
            if constant_["ownerStandardId"] == standard["id"]:
                print_constant(f, constant_)       
    gen_file(f, path, gen_file_name_h("Constants"), True, False, "Definition of constants.")    

#------------------------------------------------------------------------------
def gen_cr_fw_files(path, app):
    gen_cr_fw_out_factory_user_par(path, app)
    gen_cr_fw_in_factory_user_par(path, app)
    gen_cr_fw_out_registry_user_par(path, app)
    gen_app_constants(path, app)    

#------------------------------------------------------------------------------
def gen_cfw(path, comp):

    global settings 

    app = comp["app"]
    settings = comp["setting"]

    if settings == None:
        return

    for spec in app["specifications"]:
        print("SPEC - ", spec["packet"]["type"], spec["packet"]["subtype"], spec["packet"]["kind"], spec["packet"]["name"]) # 76 spec for SMILE IASW
        gen_spec(path, spec)

    spec_in_cmd = []
    spec_in_rep = []
    spec_out_cmp = []
    for spec in app["specifications"]:
        if spec["relation"] > 0:                # Service provider
            if spec["packet"]["kind"] == "TC":  # incoming command
                spec_in_cmd.append(spec)
            else:                               # outgoing report
                spec_out_cmp.append(spec)
        else:                                   # Service user
            if spec["packet"]["kind"] == "TC":  # out-going command
                spec_out_cmp.append(spec)
            else:                               # incoming report
                spec_in_rep.append(spec)

    app["__dp_spec_in_cmd"] = spec_in_cmd
    app["__dp_spec_in_rep"] = spec_in_rep
    app["__dp_spec_out_cmp"] = spec_out_cmp
    
    gen_cr_fw_files(path + "/PusConfig/", app)

if __name__ == '__main__':

    if (len(sys.argv) == 3):
        project_id = sys.argv[1]
        app_id = sys.argv[2]
        try:
            il = get_data.get_data(project_id)
            print("il = ", il)
            app = il["apps"]["hash"][int(app_id)]
            gen_cfw("./cfw", app["components"]["hash"]["cfw"])
            print("Done")
        except Exception as e:
            print("Something went wrong...")
            print(traceback.format_exc())

    else:
        print("Usage: python gen_cfw.py {project_id} {app_id}")
