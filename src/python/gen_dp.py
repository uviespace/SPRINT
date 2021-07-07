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
    f += "{0}{1}\n".format(" " * get_indent(indent_level), s.encode("utf8"))

def write_separator(f):
    f += "/*{0}*/\n".format("-" * (settings["max_line_length"]-4))

def gen_file(f, path, s, isHeader, addTypeInclude=True, desc="Interface for accessing data pool items."):
    f_ = new_file(path, s, isHeader, addTypeInclude, desc)
    f_.write(fig_ref_conv.fig_ref_conv(''.join(f), "doxy"))
    close_file(f_)

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
            if (param["multi"] > 0) and not ("{" in value):
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
    writeln(f, "enum {")
    if len(params_list) > 0:
        writeln(f, "/* Parameters */", 1)
        writeln(f, "DpIdParamsLowest = {0},".format(params_list[0]["_dpid"]), 1)
        writeln(f, "DpIdParamsHighest = {0},".format(params_list[len(params_list)-1]["_dpid"]), 1)
        for i, param in enumerate(params_list):
            pname = cname(param["name"])
            writeln(f, "DpId{0} = {1}{2}".format(pname, param["_dpid"], "," if i < len(params_list)-1 or (len(vars_list) > 0) else ""), 1)
    if len(vars_list) > 0:
        writeln(f, "/* Variables */", 1)
        writeln(f, "DpIdVarsLowest = {0},".format(vars_list[0]["_dpid"]), 1)
        writeln(f, "DpIdVarsHighest = {0},".format(vars_list[len(vars_list)-1]["_dpid"]), 1)
        for i, param in enumerate(vars_list):
            pname = cname(param["name"])
            writeln(f, "DpId{0} = {1}{2}".format(pname, param["_dpid"], "," if i < len(vars_list)-1 else ""), 1)
    writeln(f, "};")
    writeln(f, "")

    # Add struct / functions for all parameters with empty domain (if any)
    if '' in domain_dict:
        d = domain_dict['']
        if len(d["params"]) > 0 or len(d["vars"]) > 0:
            gen_struct(f, 'Datapool', d["params"], d["vars"])
            gen_funcs(f, 'Datapool', d["params"], d["vars"])

    # Generate getter / setter by id
    write_doxy(f, [
        "Get the value of a data pool item by identifier.",
        "If the identifier points to a data item with multiplicity greater than 1 (an array), then",
        "all the elements in the array-like data pool item are copied to the destination.",
        "In all other cases, the function retrieves the value of a single element.",
        " ",
        "If the data pool item identifier is invalid, an assertion violation is raised.",
        "@param id The data pool item identifier",
        "@param dest The address of the target variable where the value gets copied to.",
        "@return Number of bytes copied."
    ])    
    writeln(f, "extern size_t getDpValue(CrPsParId_t id, void* dest);")

    write_doxy(f, [
        "Get the value of a data pool item by identifier.",
        "This function has the same behaviour as function #getDpValue in all cases but one:",
        "if the identifier points to a data item with multiplicity greater than 1 (an array),",
        "it returns the value of the first element of the array (function #getDpValue instead",
        "returns the value of all elements in the array).",
        " ",
        "If the data pool item identifier is invalid, an assertion violation is raised.",
        "@param id The data pool item identifier",
        "@param dest The address of the target variable where the value gets copied to.",
        "@return Number of bytes copied."
    ])    
    writeln(f, "extern size_t getDpValueElem(CrPsParId_t id, void* dest);")

    write_doxy(f, [
        "Get the value of a data pool item plus meta information by identifier.",
        "If the identifier points to a data item with multiplicity greater than 1 (an array), then",
        "all the elements in the array-like data pool item are copied to the destination.",
        "In all other cases, the function retrieves the value of a single element.",
        " ",
        "If the data pool item identifier is invalid, an assertion violation is raised.",
        "@param id The data pool item identifier",
        "@param dest The address of the target variable where the value gets copied to.",
        "@param pElementLength Pointer to where the element size is copied to.",
        "@param pNElements Pointer to where the number of elements is copied to."        
        "@return Number of bytes copied."
    ])  
    writeln(f, "extern size_t getDpValueEx(CrPsParId_t id, void* dest, size_t* pElementLength, unsigned int* pNElements);")    

    write_doxy(f, [
        "Set the value of a data pool item by identifier",
        "If the identifier points to a data item with multiplicity greater than 1 (an array), then",
        "all the elements in the array-like data pool item are set.",
        "In all other cases, the function sets the value of a single element.",
        " ",
        "If the data pool item identifier is invalid, an assertion violation is raised.",
        "@param id The data pool item identifier",
        "@param src The address of the source variable where the value gets copied from.",
        "@return Number of bytes copied."
    ])    
    writeln(f, "extern int setDpValue(CrPsParId_t id, const void* src);")

    write_doxy(f, [
        "Set the value of a data pool item by identifier.",
        "This function has the same behaviour as function #setDpValue in all cases but one:",
        "if the identifier points to a data item with multiplicity greater than 1 (an array),",
        "it sets the value of the first element of the array only (function #setDpValue instead",
        "sets the value of all elements in the array).",
        " ",
        "If the data pool item identifier is invalid, an assertion violation is raised.",
        "@param id The data pool item identifier",
        "@param dest The address of the target variable where the value gets copied to.",
        "@return Number of bytes copied."
    ])    
    writeln(f, "extern int setDpValueElem(CrPsParId_t id, const void* dest);")

    write_doxy(f, [
        "Set the value of a data pool item by identifier and get meta information",
        "If the identifier points to a data item with multiplicity greater than 1 (an array), then",
        "all the elements in the array-like data pool item are set.",
        "In all other cases, the function sets the value of a single element.",
        " ",
        "If the data pool item identifier is invalid, an assertion violation is raised.",
        "@param id The data pool item identifier",
        "@param src The address of the source variable where the value gets copied from.",
        "@param dest Pointer to pointer to where the element address is copied to."
        "@param pElementLength Pointer to where the element size is copied to.",
        "@param pNElements Pointer to where the number of elements is copied to."                
        "@return Number of bytes copied."
    ])   
    writeln(f, "extern int setDpValueEx(CrPsParId_t id, const void* src, void** dest, size_t* pElementLength, unsigned int* pNElements);")    

    write_doxy(f, [
        "Get the size of a data pool item by identifier.",
        "If the identifier points to a data item with multiplicity greater than 1 (an array), then",
        "the size of the array is returned.",
        "In all other cases, the function returns the size of a single element.",
        " ",
        "If the data pool item identifier is invalid, an assertion violation is raised.",
        "@param id The data pool item identifier",
        "@return The size of the data pool parameter."
    ])
    writeln(f, "extern size_t getDpSize(CrPsParId_t id);")

    write_doxy(f, [
        "Get the size of a data pool parameter by identifier.",
        "This function is deprecated: it has the same behaviour as #getDpSize.",
        "@param id The data pool parameter identifier",
        "@return The size of the data pool parameter. 0 if id is invalid."
    ])
    writeln(f, "extern size_t getDpParamSize(CrPsParId_t id);")       

    write_doxy(f, [
        "Get the size of a data pool variable by identifier.",
        "This function is deprecated: it has the same behaviour as #getDpSize.",
        "@param id The data pool variable identifier",
        "@return The size of the data pool variable. 0 if id is invalid."
    ])
    writeln(f, "extern size_t getDpVarSize(CrPsParId_t id);")       

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

            length = "sizeof(dp{0}{1}.{2})".format(dname, kind, pname)
            if param["multi"] != None:                
                element_length = "sizeof(dp{0}{1}.{2}[0])".format(dname, kind, pname)
                nelems = param["multi"]
            else:
                element_length = length
                nelems = "1"
            
            writeln(f, "{{(void*)&dp{0}{3}.{1}, {4}, {5}, {6}}}{2}".format(dname, pname, "," if ((i < len(list)-1) or (kind == "Params")) else "", kind, length, nelems, element_length), 1)
            for i in range(1,param["_multi"]):
                writeln(f, "{{(void*)(&dp{0}{3}.{1}+{7}), {6}, {5}, {6}}}{2}".format(dname, pname, "," if ((i < len(list)-1) or (kind == "Params")) else "", kind, length, nelems, element_length,i), 1)
            
        
        
    f = list()
    writeln(f, "#include \"{0}\"".format(gen_file_name_h("Dp")))
    writeln(f, "#include <assert.h>");
    writeln(f, "#include <string.h>");
    writeln(f, "#include <stdlib.h>");
    
    writeln(f, "")
    # Generate one #include file for each domain
    for domain in domain_dict:
        dname = cname(domain)        
        if len(dname) > 0:
            h_name = gen_file_name_h("Dp" + dname)
            writeln(f, "#include \"{0}\"".format(h_name))
    writeln(f, "")

    # Add meta information (address / size) of data pool entries
    write_doxy(f, ["Structure to hold the location and size information of a data pool entry."])
    writeln(f, "typedef struct _DpMetaInfoEntry_t {")
    write_doxy(f, ["The address of the data pool entry."], 1)
    writeln(f, "void* addr;", 1)
    write_doxy(f, ["For entries representing the first element of an array, this is the total size in bytes",
                   "of the array. In all other cases, this is the size in byte of the entry element."], 1)
    writeln(f, "size_t length;", 1)
    write_doxy(f, ["The number of array elements."],1)
    writeln(f, "unsigned int nElements;", 1)
    write_doxy(f, ["The length of a single array element."], 1)
    writeln(f, "size_t elementLength;", 1)
    writeln(f, "} DpMetaInfoEntry_t;")
    writeln(f, "")
    write_doxy(f, ["""
        Array of @ref _DpMetaInfoEntry_t to hold the meta information of all 
        data pool entries."""])
    writeln(f, "static DpMetaInfoEntry_t dpMetaInfo[] = {")
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
    writeln(f, "static DpMetaInfoEntry_t* getMetaInfo(CrPsParId_t id) {")
    writeln(f, "DpMetaInfoEntry_t* p;", 1)
    writeln(f, "assert(id >= DpIdParamsLowest && id <= DpIdVarsHighest);", 1)
    writeln(f, "p = &dpMetaInfo[id-DpIdParamsLowest];", 1)
    writeln(f, "return p;", 1)
    writeln(f, "}")    
    writeln(f, "")    

    writeln(f, "size_t getDpValue(CrPsParId_t id, void* dest) {")
    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
    writeln(f, "entry = getMetaInfo(id);", 1)
    writeln(f, "(void)memcpy(dest, entry->addr, entry->length);", 1)
    writeln(f, "return entry->length;", 1)
    writeln(f, "}")
    writeln(f, "")

    writeln(f, "size_t getDpValueElem(CrPsParId_t id, void* dest) {")
    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
    writeln(f, "entry = getMetaInfo(id);", 1)
    writeln(f, "(void)memcpy(dest, entry->addr, entry->elementLength);", 1)
    writeln(f, "return entry->elementLength;", 1)
    writeln(f, "}")
    writeln(f, "")

    writeln(f, "size_t getDpValueEx(CrPsParId_t id, void* dest, size_t* pElementLength, unsigned int* pNElements) {")
    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
    writeln(f, "entry = getMetaInfo(id);", 1)
    writeln(f, "(void)memcpy(dest, entry->addr, entry->length);", 1)
    writeln(f, "*pElementLength = entry->elementLength;", 1)
    writeln(f, "*pNElements = entry->nElements;", 1)
    writeln(f, "return entry->length;", 1)
    writeln(f, "}")
    writeln(f, "")    

    writeln(f, "int setDpValue(CrPsParId_t id, const void* src) {")
    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
    writeln(f, "entry = getMetaInfo(id);", 1)
    writeln(f, "(void)memcpy(entry->addr, src, entry->length);", 1)
    writeln(f, "return entry->length;", 1)
    writeln(f, "}")
    writeln(f, "")    

    writeln(f, "int setDpValueElem(CrPsParId_t id, const void* src) {")
    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
    writeln(f, "entry = getMetaInfo(id);", 1)
    writeln(f, "(void)memcpy(entry->addr, src, entry->elementLength);", 1)
    writeln(f, "return entry->elementLength;", 1)
    writeln(f, "}")
    writeln(f, "")    

    writeln(f, "int setDpValueEx(CrPsParId_t id, const void* src, void** dest, size_t* pElementLength, unsigned int* pNElements) {")
    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
    writeln(f, "entry = getMetaInfo(id);", 1)
    writeln(f, "(void)memcpy(entry->addr, src, entry->length);", 1)
    writeln(f, "*dest = entry->addr;", 1)
    writeln(f, "*pElementLength = entry->elementLength;", 1)
    writeln(f, "*pNElements = entry->nElements;", 1)    
    writeln(f, "return entry->length;", 1)
    writeln(f, "}")
    writeln(f, "")        
    
    writeln(f, "size_t getDpSize(CrPsParId_t id) {")    
    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
    writeln(f, "entry = getMetaInfo(id);", 1)
    writeln(f, "return entry->length;", 1)
    writeln(f, "}")
    writeln(f, "")

    writeln(f, "size_t getDpParamSize(CrPsParId_t id) {")    
    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
    writeln(f, "entry = getMetaInfo(id);", 1)
    writeln(f, "return entry->length;", 1)
    writeln(f, "}")
    writeln(f, "")

    writeln(f, "size_t getDpVarSize(CrPsParId_t id) {")    
    writeln(f, "DpMetaInfoEntry_t* entry;", 1)
    writeln(f, "entry = getMetaInfo(id);", 1)
    writeln(f, "return entry->length;", 1)
    writeln(f, "}")

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
def gen_dp(path, comp):
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
            if param["ownerStandardId"] == standard["id"]:
                add_elem(domain_dict, var, "vars")
                vars_list.append(var)

    if len(params_list) > 0 and len(vars_list) > 0:
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
            gen_dp("./dp", app["components"]["hash"]["dp"])
            print("Done")
        except Exception as e:
            print("Something went wrong...")
            print(traceback.format_exc())

    else:
        print("Usage: python gen_dp.py {project_id} {app_id}")
