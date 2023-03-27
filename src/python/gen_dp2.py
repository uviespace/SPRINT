#!/usr/bin/python

import sys
import datetime
import os
import traceback

import get_data
import fig_ref_conv

settings = {}
filenameCap = None

def cname(s):
    return s.replace(" ", "").replace("-", "").replace("/", "").replace("\\", "")

def writeln(f, s, indent_level=0):
    #f += "{0}{1}\n".format(" " * get_indent(indent_level), s.encode("utf8"))
    f += "{0}{1}\n".format(" " * get_indent(indent_level), s)

def write_separator(f):
    f += "/*{0}*/\n".format("-" * (settings["max_line_length"]-4))

def gen_file(f, path, s, isHeader, addTypeInclude=True, desc="Interface for accessing data pool items."):
    f_ = new_file(path, s, isHeader, addTypeInclude, desc)
    f_.write(fig_ref_conv.fig_ref_conv(''.join(f), "doxy"))
    close_file(f_)

def new_file(path, s, isHeader, addTypeInclude=True, desc="Interface for accessing data pool items."):
    def ensure_dir(file_path):
        directory = os.path.basename(file_path)
        if not os.path.exists(directory):
            os.makedirs(directory)

    global filenameCap

    ensure_dir(path)
    f = list()
    write_doxy(f, [
        "@file",
        "@ingroup gen_dp",
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
            writeln(f, "#include \"{0}\"".format(gen_file_name_h("Types")))
            writeln(f, "#include \"{0}\"".format(gen_file_name_h("Constants")))
        gen_includes(f)
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

def gen_funcs(f, dname, params, vars):
    def write_funcs(f, dname, params):
        for param in params:
            tname = param["type"]["name"] if param["type"] != None else "undefined"
            pname = cname(param["name"])
            domain = cname(param["domain"])
            pnameFirstCap = pname[:1].upper() + pname[1:]
            domainFirstCap = domain[:1].upper() + domain[1:]
            multi = param["multi"]

            if multi != None:
                # getter
                write_doxy(f, [
                    "Get the data pool array " + param["name"]  + " (" + param["_desc"] + ")",
                    "@return The data pool array " + param["name"]
                ])
                writeln(f, "static inline {0}* getDp{1}{2}Array() {3}".format(tname, domainFirstCap, pnameFirstCap, "{"))
                writeln(f, "return &dp{0}.{1}[0];".format(dname, pname), 1)
                writeln(f, "}")
                writeln(f, "")

                write_doxy(f, [
                    "Get the value of the i-th element in the data pool array " + param["name"] + " (" + param["_desc"] + ")",
                    "@param i Index variable",
                    "@return The i-th element in the data pool array " + param["name"]
                ])
                writeln(f, "static inline {0} getDp{1}{2}Item(int i) {3}".format(tname, domainFirstCap, pnameFirstCap, "{"))
                writeln(f, "return dp{0}.{1}[i];".format(dname, pname), 1)
                writeln(f, "}")
                writeln(f, "")

                # setter
                write_doxy(f, [
                    "Set the value of the i-th element in the data pool array " + param["name"] + " (" + param["_desc"] + ")",
                    "@param i Index variable",
                    "@param {0} The value to be stored into the i-th element of data pool array {0}.".format(pname)
                ])
                writeln(f, "static inline void setDp{3}{0}Item(int i, {1} {4}) {2}".format(pnameFirstCap, tname, "{", domainFirstCap, pname))
                writeln(f, "dp{0}.{1}[i] = {1};".format(dname, pname), 1)
                writeln(f, "}")
                writeln(f, "")

            else:
                # getter
                write_doxy(f, [
                    "Get the value of the data pool item " + param["name"] + " (" + param["_desc"] + ")",
                    "@return The value of data pool item " + param["name"]
                ])
                writeln(f, "static inline {0} getDp{3}{1}() {2}".format(tname, pnameFirstCap, "{", domainFirstCap))
                writeln(f, "return dp{0}.{1};".format(dname, pname), 1)
                writeln(f, "}")
                writeln(f, "")

                # setter
                write_doxy(f, [
                    "Set the value of the data pool item " + param["name"] + " (" + param["_desc"] + ")",
                    "@param {0} The value to be stored into the data pool item {0}.".format(pname)
                ])
                writeln(f, "static inline void setDp{3}{0}({1} {4}) {2}".format(pnameFirstCap, tname, "{", domainFirstCap, pname))
                writeln(f, "dp{0}.{1} = {1};".format(dname, pname), 1)
                writeln(f, "}")
                writeln(f, "")

    write_funcs(f, dname+"Params", params)
    write_funcs(f, dname+"Vars", vars)

def gen_struct(f, dname, params, vars):
    def write_struct(f, name, params):
        if len(params) > 0:
            writeln(f, "/** Type description */")
            writeln(f, "typedef struct {")
            for param in params:
                tname = param["type"]["name"] if param["type"] != None else "undefined"
                pname = cname(param["name"])
                multi = param["multi"]
                writeln(f, "/** {0} */".format(param["_desc"]), 1)
                if multi != None:
                    writeln(f, u"{0} {1}[{2}];".format(tname, pname, multi), 1)
                else:
                    writeln(f, u"{0} {1};".format(tname, pname), 1)
            writeln(f, "}} Dp{0}_t;".format(name))
            writeln(f, "")

    write_struct(f, dname + "Params", params)
    write_struct(f, dname + "Vars", vars)
    if len(params) > 0:
        writeln(f, "/** Extern declaration for structure holding data pool variables in service {0} */".format(dname))
        writeln(f, "extern Dp{0}_t dp{0};".format(dname + "Params"))
    if len(vars) > 0:
        writeln(f, "/** Extern declaration for structure holding data pool parameters in service {0} */".format(dname))
        writeln(f, "extern Dp{0}_t dp{0};".format(dname + "Vars"))
    writeln(f, "")

def gen_file_name(dname):
    return "{0}{1}".format(settings["prefix"], dname)

def gen_file_name_h(dname):
    return gen_file_name(dname) + ".h"

def gen_file_name_c(dname):
    return gen_file_name(dname) + ".c"

#-------------------------------------------------------------------------------
def gen_files(path, domain, params, vars):

    # Write initializer for structure holding the data items in a domain
    def write_init_val(f, params, type_):
        for param in params:
            pname = cname(param["name"])
            value = param["value"]
            if (param["multi"] != None) and not ("{" in value):
                value = "{"+value+"}"
            if param == params[-1]:
                writeln(f, "{0} /* {1} */ \\".format(value, pname), 1)
            else:
                writeln(f, "{0}, /* {1} */ \\".format(value, pname), 1)

    dname = cname(domain)
    h_name = gen_file_name_h("Dp" + dname)
    c_name = gen_file_name_c("Dp" + dname)

    # Domain header file
    f = list()
    gen_struct(f, dname, params, vars)
    gen_funcs(f, dname, params, vars)
    gen_file(f, path, h_name, True)

    # Domain source file
    f = list()
    # XXX next one is a dirty fix for where enums are used in initialisation
    #     I don't see a per-file include setting, so we will do this for all of them
    writeln(f, "#include \"CrFwDp.h\"".format(h_name))
    writeln(f, "#include \"{0}\"".format(h_name))
    writeln(f, "")
    if len(params) > 0:
        writeln(f, "Dp{0}_t dp{0} {1} = {2}".format(dname+"Params", settings["param_attr"],"{ \\"))
        multi_params = write_init_val(f, params, "Params")
        writeln(f,"};")
    writeln(f, "")
    if len(vars) > 0:
        writeln(f, "Dp{0}_t dp{0} {1} = {2}".format(dname+"Vars", settings["var_attr"],"{ \\"))
        multi_params = write_init_val(f, vars, "Vars")
        writeln(f,"};")
    writeln(f, "")

    gen_file(f, path, c_name, False)

#------------------------------------------------------------------------------
# Generate header of module implementing the data pool
def gen_datapool_h(path, domain_dict, params_list, vars_list):
    f = list()

    # Generate ID enumeration
    writeln(f, "enum datapool_id {")
    if len(params_list) > 0:
        writeln(f, "/* Parameters */", 1)
        # first entry, requires initialisation with lower number than any other id present;
        # we assume ids are in order order and all are integers...
        writeln(f, "DATAPOOL_ID_FIRST = {0},".format(params_list[0]["_dpid"] - 1), 1)
        #writeln(f, "DpIdParamsHighest = {0},".format(params_list[len(params_list)-1]["_dpid"] + 1), 1)
        for i, param in enumerate(params_list):
            pname = cname(param["name"])
            writeln(f, "DpId{0}{1}".format(pname, "," if i < len(params_list)-1 or (len(vars_list) > 0) else ""), 1)
    if len(vars_list) > 0:
        writeln(f, "/* Variables */", 1)
        for i, param in enumerate(vars_list):
            pname = cname(param["name"])
            writeln(f, "DpId{0},".format(pname, param["_dpid"]), 1)
        # final entry (mandatory)
        writeln(f, "DATAPOOL_ID_LAST", 1)
    writeln(f, "};")
    writeln(f, "")

    # Add struct / functions for all parameters with empty domain (if any)
    if '' in domain_dict:
        d = domain_dict['']
        if len(d["params"]) > 0 or len(d["vars"]) > 0:
            gen_struct(f, 'Datapool', d["params"], d["vars"])
            gen_funcs(f, 'Datapool', d["params"], d["vars"])

    gen_file(f, path, gen_file_name_h("Dp"), True)

#------------------------------------------------------------------------------
# Generate body of module implementing the data pool
def gen_datapool_c(path, domain_dict, params_list, vars_list):
    def outp_element(f, list, kind):
        for i, param in enumerate(list):
            dname = cname(param["domain"])
            if len(dname) == 0:
                dname = 'Datapool'
            pname = cname(param["name"])
            nname = param["type"]["name"] if param["type"] != None else "undefined"
            tname = param["type"]["nativeType"] if param["type"] != None else "undefined"

            if len(tname) == 0:
                tname = nname;


            # type mapping
            if tname == "uint8_t" or tname == "int8_t" or tname == "unsigned char" or tname == "signed char":
                tname = "DP_TYPE_INT8"
            if tname == "uint16_t" or tname == "int16_t" or tname == "unsigned short" or tname == "signed short":
                tname = "DP_TYPE_INT16"
            if tname == "uint32_t" or tname == "int32_t" or tname == "unsigned int" or tname == "signed int":
                tname = "DP_TYPE_INT32"
            if tname == "float":
                tname = "DP_TYPE_FLOAT"
            if tname == "double":
                tname = "DP_TYPE_DOUBLE"

            length = "sizeof(dp{0}{1}.{2})".format(dname, kind, pname)
            if param["multi"] != None:
                element_length = "sizeof(dp{0}{1}.{2}[0])".format(dname, kind, pname)
                nelems = param["multi"]
            else:
                element_length = length
                nelems = "1"

            # could do (minimum) size verification here, the size type table contains
            # bit widths of the more abstract types used in the PUS packets

                #tname = param["type"]["name"] if param["type"] != None else "undefined"
                #pname = cname(param["name"])
                #multi = param["multi"]
            #writeln(f, "{{(void*)&dp{0}{3}.{1}, {4}, {5}, {6}}}{2}".format(dname, pname, "," if ((i < len(list)-1) or (kind == "Params")) else "", kind, length, nelems, element_length), 1)
            writeln(f, "{{(void *) &dp{0}{1}.{2}, {3}, {4}}}{5}".format(dname, kind, pname, tname, nelems, "," if ((i < len(list)-1) or (kind == "Params")) else ""), 1)
            #for i in range(1,param["_multi"]):
            #    writeln(f, "{{(void*)(&dp{0}{3}.{1}+{7}), {6}, {5}, {6}}}{2}".format(dname, pname, "," if ((i < len(list)-1) or (kind == "Params")) else "", kind, length, nelems, element_length,i), 1)



    f = list()
    writeln(f, "#include \"{0}\"".format(gen_file_name_h("Dp")))
    writeln(f, "#include <stddef.h>");
    writeln(f, "#include <datapool.h>");


    writeln(f, "")
    # Generate one #include file for each domain
    for domain in domain_dict:
        dname = cname(domain)
        if len(dname) > 0:
            h_name = gen_file_name_h("Dp" + dname)
            writeln(f, "#include \"{0}\"".format(h_name))
    writeln(f, "")

    # Add meta information (address / size) of data pool entries
    write_doxy(f, ["""
        Array of @ref datapool_entry to hold the meta information of all
        data pool entries."""])
    writeln(f, "struct datapool_entry datapool [] = {")
    outp_element(f, params_list, "Params")
    outp_element(f, vars_list, "Vars")
    writeln(f, "};")
    writeln(f, "")

    # Add struct instantiation
    if '' in domain_dict:
        d = domain_dict['']
        if len(d["params"]) > 0:
            writeln(f, "Dp{0}Params_t dp{0}{1};".format('Datapool', settings["param_attr"]))
        if len(d["vars"]) > 0:
            writeln(f, "Dp{0}Vars_t dp{0}{1};".format('Datapool', settings["var_attr"]))
        writeln(f, "")

    # Add implementation for get/set by id functions
#    writeln(f, "static DpMetaInfoEntry_t* getMetaInfo(CrPsParId_t id) {")
#    writeln(f, "DpMetaInfoEntry_t* p;", 1)
#    writeln(f, "assert(id >= DpIdParamsLowest && id <= DpIdVarsHighest);", 1)
#    writeln(f, "p = &dpMetaInfo[id-DpIdParamsLowest];", 1)
#    writeln(f, "return p;", 1)
#    writeln(f, "}")
#    writeln(f, "")
#
#    writeln(f, "size_t getDpValue(CrPsParId_t id, void* dest) {")
#    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
#    writeln(f, "entry = getMetaInfo(id);", 1)
#    writeln(f, "(void)memcpy(dest, entry->addr, entry->length);", 1)
#    writeln(f, "return entry->length;", 1)
#    writeln(f, "}")
#    writeln(f, "")
#
#    writeln(f, "size_t getDpValueElem(CrPsParId_t id, void* dest) {")
#    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
#    writeln(f, "entry = getMetaInfo(id);", 1)
#    writeln(f, "(void)memcpy(dest, entry->addr, entry->elementLength);", 1)
#    writeln(f, "return entry->elementLength;", 1)
#    writeln(f, "}")
#    writeln(f, "")
#
#    writeln(f, "size_t getDpValueEx(CrPsParId_t id, void* dest, size_t* pElementLength, unsigned int* pNElements) {")
#    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
#    writeln(f, "entry = getMetaInfo(id);", 1)
#    writeln(f, "(void)memcpy(dest, entry->addr, entry->length);", 1)
#    writeln(f, "*pElementLength = entry->elementLength;", 1)
#    writeln(f, "*pNElements = entry->nElements;", 1)
#    writeln(f, "return entry->length;", 1)
#    writeln(f, "}")
#    writeln(f, "")
#
#    writeln(f, "int setDpValue(CrPsParId_t id, const void* src) {")
#    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
#    writeln(f, "entry = getMetaInfo(id);", 1)
#    writeln(f, "(void)memcpy(entry->addr, src, entry->length);", 1)
#    writeln(f, "return entry->length;", 1)
#    writeln(f, "}")
#    writeln(f, "")
#
#    writeln(f, "int setDpValueElem(CrPsParId_t id, const void* src) {")
#    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
#    writeln(f, "entry = getMetaInfo(id);", 1)
#    writeln(f, "(void)memcpy(entry->addr, src, entry->elementLength);", 1)
#    writeln(f, "return entry->elementLength;", 1)
#    writeln(f, "}")
#    writeln(f, "")
#
#    writeln(f, "int setDpValueEx(CrPsParId_t id, const void* src, void** dest, size_t* pElementLength, unsigned int* pNElements) {")
#    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
#    writeln(f, "entry = getMetaInfo(id);", 1)
#    writeln(f, "(void)memcpy(entry->addr, src, entry->length);", 1)
#    writeln(f, "*dest = entry->addr;", 1)
#    writeln(f, "*pElementLength = entry->elementLength;", 1)
#    writeln(f, "*pNElements = entry->nElements;", 1)
#    writeln(f, "return entry->length;", 1)
#    writeln(f, "}")
#    writeln(f, "")
#
#    writeln(f, "size_t getDpSize(CrPsParId_t id) {")
#    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
#    writeln(f, "entry = getMetaInfo(id);", 1)
#    writeln(f, "return entry->length;", 1)
#    writeln(f, "}")
#    writeln(f, "")
#
#    writeln(f, "size_t getDpParamSize(CrPsParId_t id) {")
#    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
#    writeln(f, "entry = getMetaInfo(id);", 1)
#    writeln(f, "return entry->length;", 1)
#    writeln(f, "}")
#    writeln(f, "")
#
#    writeln(f, "size_t getDpVarSize(CrPsParId_t id) {")
#    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
#    writeln(f, "entry = getMetaInfo(id);", 1)
#    writeln(f, "return entry->length;", 1)
#    writeln(f, "}")

    gen_file(f, path, gen_file_name_c("Dp"), False)

def get_indent(level):
    return settings["indent"] * level

#------------------------------------------------------------------------------
# Generate #include's specified by user in the generator settings
def gen_includes(f):
    for include in settings["includes"]:
        writeln(f, "#include \"{0}\"".format(include))
    writeln(f, "")

#------------------------------------------------------------------------------
# Generate the header file holding the data-types definitions
def gen_app_types(path, app):
    def print_type(f, type_):
        if type_["size"] != None and (type_["nativeType"].strip() !=""):
            tname = type_["name"]
            ntname = type_["nativeType"]
            writeln(f, "/* Definition of type \"{0}\". */".format(tname))
            writeln(f, "typedef {0} {1};".format(ntname, tname))
# XXX: this doesn't work, dunno what the intent was...
#            if len(type_["enums"]) > 0:
#                writeln(f, "enum {")
#                for i, enum in enumerate(type_["enums"]):
#                    isLast = (i == len(type_["enums"])-1)
#                    writeln(f, "{0} = {1}{2}".format(cname(enum["Name"]), enum["_dec"], '' if isLast else ','), 1)
#                writeln(f, "};")
#            writeln(f, "")

    f = list()
    writeln(f, "#include <stddef.h>");
    writeln(f, "#include <stdint.h>");
    writeln(f, "")
    for standard_relation in app["standards"]:
        standard = standard_relation["standard"]
        for type_ in standard["types"].values():
            if type_["ownerStandardId"] == standard["id"]:
                print_type(f, type_)
    gen_file(f, path, gen_file_name_h("Types"), True, False)

#------------------------------------------------------------------------------
# Generate the header file holding the constants defined in the editor
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
    gen_file(f, path, gen_file_name_h("Constants"), True, False, "Constant definitions.")

#------------------------------------------------------------------------------
def gen_dp2(path, comp):
    def touch_param_i(param_i):
        param_i["__dp_parent"] = None
        param_i["__dp_subparams"] = []
        param_i["__dp_isLeaf"] = True
        param_i["__dp_isPartOfGroup"] = False

    def add_elem(d, param, type_):
        if param["domain"] not in d:
            d[param["domain"]] = {}
            d[param["domain"]]["params"] = []
            d[param["domain"]]["vars"] = []
        d[param["domain"]][type_].append(param)

    global settings

    domain_dict = {}
    params_list = []
    vars_list = []
    app = comp["app"]
    settings = comp["setting"]

    if settings == None:
        return

    for standard_relation in app["standards"]:
        standard = standard_relation["standard"]
        for param_i in standard["headers"]["TC"]:
            touch_param_i(param_i)
        for param_i in standard["headers"]["TM"]:
            touch_param_i(param_i)

    gen_app_types(path, app)
    gen_app_constants(path, app)

    for standard_relation in app["standards"]:
        standard = standard_relation["standard"]
        for param in standard["datapool"]["params"]:
            if param["ownerStandardId"] == standard["id"]:
                add_elem(domain_dict, param, "params")
                params_list.append(param)
        for var in standard["datapool"]["vars"]:
            if var["ownerStandardId"] == standard["id"]:  # was  'if param["ownerStandardId"] == standard["id"]:'  =>  UnboundLocalError: local variable 'param' referenced before assignment
                add_elem(domain_dict, var, "vars")
                vars_list.append(var)

    if len(params_list) > 0 or len(vars_list) > 0:  # was and !
        gen_datapool_h(path, domain_dict, params_list, vars_list)
        gen_datapool_c(path, domain_dict, params_list, vars_list)

        for domain in domain_dict:
            d = domain_dict[domain]
            if len(d["params"]) > 0 or len(d["vars"]) > 0:
                gen_files(path, domain, d["params"], d["vars"])

if __name__ == '__main__':

    if (len(sys.argv) == 3):

        project_id = sys.argv[1]
        app_id = sys.argv[2]
        try:
            il = get_data.get_data(project_id)
            app = il["apps"]["hash"][int(app_id)]
            gen_dp2("./dp2", app["components"]["hash"]["dp2"])
            print("Done")
        except Exception as e:
            print("Something went wrong...")
            print(traceback.format_exc())

    else:
        print("Usage: python gen_dp2.py {project_id} {app_id}")
