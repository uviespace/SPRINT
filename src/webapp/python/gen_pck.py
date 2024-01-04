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
        directory = os.path.dirname(file_path)
        if not os.path.exists(directory):
            os.makedirs(directory)

    global filenameCap

    ensure_dir(path)
    f = list()
    write_doxy(f, [ 
        "@file",
        "@ingroup gen_pck",
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

def gen_file_name(dname):
    return "{0}{1}".format(settings["prefix"], dname)

def gen_file_name_h(dname):
    return gen_file_name(dname) + ".h"

def gen_file_name_c(dname):
    return gen_file_name(dname) + ".c"

def get_indent(level):
    return settings["indent"] * level

__dp_packet_struct_param_unaligned_bits = 0

def gen_packet_struct_param(f, param_i, level=1):
    global __dp_packet_struct_param_unaligned_bits
    param = param_i["param"]
    #print("param size of ", param["name"], param["_size"])
    if param["_size"] != None:
        if (param_i["_offset"] % 8) == 0 and (param["_size"] % 8) == 0:
            writeln(f, "/** {0} */".format(param_i["_desc"]), level)
            pname = cname(param_i["param"]["name"])
            tname = param_i["param"]["type"]["name"]
            if param_i["param"]["_multi"] != None and param_i["param"]["_multi"] > 1:
                writeln(f, "{0} {1}[{2}];".format(tname, pname, int(param_i["param"]["_multi"])), level)
            else:
                writeln(f, "{0} {1};".format(tname, pname), level)
            writeln(f, "", level)
            if "__dp_subparams" in param_i and len(param_i["__dp_subparams"]) > 0:
                writeln(f, "struct __attribute__((packed)) {", level)
                for subparam_i in param_i["__dp_subparams"]:
                    if not gen_packet_struct_param(f, subparam_i, level+1):
                        return False
                writeln(f, "}} {0}_[{1}];".format(pname, int(param_i["repetition"]) if param_i["repetition"] != None else '1'), level)
        else:
            __dp_packet_struct_param_unaligned_bits = __dp_packet_struct_param_unaligned_bits + param["_size"]
            if (__dp_packet_struct_param_unaligned_bits % 8) == 0:
                writeln(f, "/** Spacer block */", 1)
                writeln(f, "uint8_t block_{1}[{0}];".format(int(__dp_packet_struct_param_unaligned_bits/8), param_i["_offset"]), level)
                writeln(f, "")
                __dp_packet_struct_param_unaligned_bits = 0
        return True
    else:
        return False
        
def gen_packet_struct(f, packet_name, kind, param_list):
    global __dp_packet_struct_param_unaligned_bits
    __dp_packet_struct_param_unaligned_bits = 0
    name = cname(packet_name)
    #print("generate struct ...")
    writeln(f, "/** Structure for {0} */".format(packet_name))
    writeln(f, "typedef struct __attribute__((packed)) _{0}_t {{".format(name))
    if kind == "TC" or kind == "TM":
        writeln(f, "/** Packet header */", 1)
        writeln(f, "{0} Header;".format("TcHeader_t" if kind == "TC" else "TmHeader_t"), 1)
        writeln(f, "")
    for param_i in param_list:
        if not gen_packet_struct_param(f, param_i):
            #print("FALSE!")
            return False
    writeln(f, "}} {0}_t {1}; /*!< Buffer for {2} */".format(name, settings["struct_attr"], packet_name))
    writeln(f, "")
    return True

def gen_packet_functions_param(f, packet_domain, packet_name, param_i):
    def gen_function_params(parents):
        params = ""
        for parent in parents:
            params = ", {0} {1}".format(parent["param"]["type"]["name"], cname(parent["param"]["name"])) + params
        return params

    def gen_group_ref(parents, firstZero = False):
        ref = ""
        for parent in parents:
            pname = cname(parent["param"]["name"])
            if firstZero:
                firstZero = False
                indexValue = "0"
            else:
                indexValue = pname
            ref = "{0}_[{1}].".format(pname, indexValue) + ref
        return ref

    def gen_grou_ref2(parents):
        ref = ""
        index_var_name = ord("i")
        for parent in parents:
            pname = cname(parent["param"]["name"])
            ref = "{0}_[{1}].".format
            

    def gen_doxy_param(parents):
        comment = ""
        for parent in parents:
            comment = "\n@param {0} {1}".format(cname(parent["param"]["name"]), parent["param"]["_desc"]) + comment
        return comment

    def wrap_endian(size, s):
        #print(">>> ", settings["endian"]["swap"], size)
        if settings["endian"]["swap"]:
            if size % 16 == 0:
                s = "{0}{1}({2})".format(settings["endian"]["fnc"], str(size), s)
                #print(s)
        return s

    if param_i["param"]["_size"] == None:
        return

    parents = []
    parent = param_i["__dp_parent"]
    while parent != None:
        parents.append(parent)
        parent = parent["__dp_parent"]

    reference = gen_group_ref(parents)
    referenceZero = gen_group_ref(parents, True)
    param_i["__dp_reference"] = reference

    pck_name = cname(packet_name)
    pck_domain = cname(packet_domain)
    pname = cname(param_i["param"]["name"])
    psize = param_i["param"]["_size"]
    if (param_i["_offset"] % 8) == 0 and (psize % 8) == 0:
    
        # handle parameters of unitary multiplicty
        if param_i["param"]["_multi"] != None and param_i["param"]["_multi"] == 1:   
        
            # If a parameter is a in a group of size 1 (i.e. if it is an "only child"), 
            # then we provide getter/setter function to access it as an array
            if param_i["__dp_isLeaf"] and param_i["__dp_isPartOfGroup"] and param_i["__dp_isOnlyChild"]:   
                doxy_param = gen_doxy_param(parents[1:])
                params = gen_function_params(parents[1:])
                write_doxy(f, [
                    "Get pointer to \"{0}\" array from \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@return Pointer to the start of the {0} array.".format(pname)
                ])
                writeln(f, "static inline {0}* get{1}{2}{3}Array(void* p{4}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "return &t->{1}{0};".format(pname, referenceZero), 1)
                writeln(f, "}")
                writeln(f, "")
                write_doxy(f, [
                    "Get \"{0}\" array from \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@param dest Pointer to memory location where array data are copied to."
                ])
                writeln(f, "static inline void read{1}{2}{3}Array(void* p{4}, void* dest) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "memcpy(dest, &t->{1}{0}, t->{2}{3}*sizeof(t->{1}{0}));".format(pname, referenceZero, param_i["__dp_parent"]["__dp_reference"], param_i["__dp_parent"]["param"]["name"]), 1)
                writeln(f, "}")
                writeln(f, "")
                write_doxy(f, [
                    "Set \"{0}\" array in \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@param src Pointer to memory location from where array data are copied."
                ])
                writeln(f, "static inline void write{1}{2}{3}Array(void* p{4}, const void* src) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "memcpy(&t->{1}{0}, src, t->{2}{3}*sizeof(t->{1}{0}));".format(pname, referenceZero, param_i["__dp_parent"]["__dp_reference"], param_i["__dp_parent"]["param"]["name"]), 1)
                writeln(f, "}")
                writeln(f, "")
                
            # Handle all parameters other than those which define the size of a group     
            if param_i["__dp_isLeaf"]:
                doxy_param = gen_doxy_param(parents)    # The parent is the parameter defining the size of the group
                params = gen_function_params(parents)
                write_doxy(f, [
                    "Get \"{0}\" from \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@return Value of \"{0}\".".format(pname)
                ])
                writeln(f, "static inline {0} get{1}{2}{3}(void* p{4}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "return {0};".format(wrap_endian(psize, "t->{1}{0}".format(pname, reference))), 1)
                writeln(f, "}")
                writeln(f, "")
                write_doxy(f, [
                    "Set \"{0}\" in \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@param {0} Value of \"{0}\" to be set in packet.".format(pname)
                ])
                writeln(f, "static inline void set{1}{2}{3}(void* p{4}, {0} {3}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "t->{1}{0} = {2};".format(pname, reference, wrap_endian(psize, pname)), 1)
                writeln(f, "}")
                writeln(f, "")
                
            # Handle parameters which define the size of a group and are not part of a group (e.g. N1 parameter in (3,1) command)
            if not param_i["__dp_isLeaf"] and not param_i["__dp_isPartOfGroup"]:
                doxy_param = ""
                params = ""
                write_doxy(f, [
                    "Get \"{0}\" from \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@return Value of \"{0}\".".format(pname)
                ])
                writeln(f, "static inline {0} get{1}{2}{3}(void* p{4}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "return {0};".format(wrap_endian(psize, "t->{1}{0}".format(pname, reference))), 1)
                writeln(f, "}")
                writeln(f, "")
                write_doxy(f, [
                    "Set \"{0}\" in \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@param {0} Value of \"{0}\" to be set in packet.".format(pname)
                ])
                writeln(f, "static inline void set{1}{2}{3}(void* p{4}, {0} {3}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "t->{1}{0} = {2};".format(pname, reference, wrap_endian(psize, pname)), 1)
                writeln(f, "}")
                writeln(f, "")       
                
            # Handle parameters which define the size of a group and are part of a group (e.g. N2 parameter in (3,1) command)
            if not param_i["__dp_isLeaf"] and param_i["__dp_isPartOfGroup"]:
                doxy_param = gen_doxy_param(parents)    # The parent is the parameter defining the size of the group
                params = gen_function_params(parents)
                write_doxy(f, [
                    "Get \"{0}\" from \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@return Value of \"{0}\".".format(pname)
                ])
                writeln(f, "static inline {0} get{1}{2}{3}(void* p{4}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "return {0};".format(wrap_endian(psize, "t->{1}{0}".format(pname, reference))), 1)
                writeln(f, "}")
                writeln(f, "")
                write_doxy(f, [
                    "Set \"{0}\" in \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@param {0} Value of \"{0}\" to be set in packet.".format(pname)
                ])
                writeln(f, "static inline void set{1}{2}{3}(void* p{4}, {0} {3}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "t->{1}{0} = {2};".format(pname, reference, wrap_endian(psize, pname)), 1)
                writeln(f, "}")
                writeln(f, "")       
                
        else:   # parameters of non-unitary multiplicity
            doxy_param = gen_doxy_param(parents)
            params = gen_function_params(parents)
            write_doxy(f, [
                "Get pointer to \"{0}\" array from \"{1}\" packet.".format(pname, packet_name),
                "@param p Pointer to the packet." + doxy_param,
                "@return Pointer to the start of the {0} array.".format(pname)
            ])
            writeln(f, "static inline {0}* get{1}{2}{3}Array(void* p{4}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
            writeln(f, "{0}_t* t;".format(pck_name), 1)
            writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
            writeln(f, "return &t->{1}{0}[0];".format(pname, reference), 1)
            writeln(f, "}")
            writeln(f, "")
            if param_i["param"]["_multi"] != None and param_i["param"]["_multi"] > 1:
                write_doxy(f, [
                    "Get \"{0}\" array from \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@param dest Pointer to memory location where array data are copied to."
                ])
                writeln(f, "static inline void get{0}{1}{2}(void* p{3}, void* dest) {{".format(pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "memcpy(dest, &t->{1}{0}[0], sizeof(t->{1}{0}));".format(pname, reference), 1)
                writeln(f, "}")
                writeln(f, "")        
                write_doxy(f, [
                    "Set \"{0}\" array in \"{1}\" packet.".format(pname, packet_name),
                    "@param p Pointer to the packet." + doxy_param,
                    "@param src Pointer to memory location from where array data are copied."
                ])
                writeln(f, "static inline void set{0}{1}{2}(void* p{3}, const void* src) {{".format(pck_domain, pck_name, pname, params))
                writeln(f, "{0}_t* t;".format(pck_name), 1)
                writeln(f, "t = ({0}_t*)p;".format(pck_name), 1)
                writeln(f, "memcpy(&t->{1}{0}[0], src, sizeof(t->{1}{0}));".format(pname, reference), 1)
                writeln(f, "}")
                writeln(f, "")                    
    else:
        if param_i["param"]["_multi"] != None and param_i["param"]["_multi"] == 1:
            params = gen_function_params(parents)            
            base_bit = param_i["_offset"] - (param_i["_offset"]%8)
            base_byte = int(base_bit/8)
            size = param_i["param"]["_size"]
            if size <= 9:
                type_size = 16
            elif size <= 25:
                type_size = 32
            elif size <= 57:
                type_size = 64
            elif size <= 121:
                type_size = 128
            else:
                # Not supported.
                return

            doxy_param = gen_doxy_param(parents)
            write_doxy(f, [
                "Get \"{0}\" from \"{1}\" packet.".format(pname, pck_name),
                "@param p Pointer to the packet." + doxy_param,
                "@return Value of \"{0}\".".format(pname)
            ])
            writeln(f, "static inline {0} get{1}{2}{3}(void* p{4}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
            writeln(f, "uint{0}_t t;".format(type_size), 1)
            writeln(f, "(void)memcpy(&t, &((uint8_t*)p{0})[{1}], sizeof(t));".format(params, base_byte), 1)
            if settings["endian"]["swap"]:
                writeln(f, "t = {0}{1}(t);".format(settings["endian"]["fnc"], type_size), 1)
            shift = type_size-(param_i["_offset"]%8+size)
            if shift > 0:
                writeln(f, "t >>= {0};".format(shift), 1)
            writeln(f, "t &= 0x{0:x};".format(2**size-1), 1)
            writeln(f, "return ({0})t;".format(param_i["param"]["type"]["name"]), 1)
            writeln(f, "}")
            writeln(f, "")

            write_doxy(f, [
                "Set \"{0}\" in \"{1}\" packet.".format(pname, packet_name),
                "@param p Pointer to the packet." + doxy_param,
                "@param {0} Value of \"{0}\" to be set in packet.".format(pname)
            ])
            writeln(f, "static inline void set{1}{2}{3}(void* p{4}, {0} {3}) {{".format(param_i["param"]["type"]["name"], pck_domain, pck_name, pname, params))
            writeln(f, "uint{0}_t s;".format(type_size), 1)
            writeln(f, "uint{0}_t t;".format(type_size), 1)
            writeln(f, "s = ((uint{2}_t){0} << {1});".format(pname, shift, type_size), 1)
            writeln(f, "s &= (uint{1}_t)0x{0:x}ull;".format((2**size-1)*(2**shift), type_size), 1)
            writeln(f, "(void)memcpy(&t, &((uint8_t*)p{0})[{1}], sizeof(t));".format(params, base_byte), 1)
            if settings["endian"]["swap"]:
                writeln(f, "t = {0}{1}(t);".format(settings["endian"]["fnc"], type_size), 1)
            shift = type_size-(param_i["_offset"]%8+size)
            writeln(f, "t &= (uint{1}_t)~0x{0:x}ull;".format((2**size-1)*(2**shift), type_size), 1)
            writeln(f, "t |= s;", 1)
            if settings["endian"]["swap"]:
                writeln(f, "t = {0}{1}(t);".format(settings["endian"]["fnc"], type_size), 1)
            writeln(f, "(void)memcpy(&((uint8_t*)p{0})[{1}], &t, sizeof(t));".format(params, base_byte), 1)
            writeln(f, "}")
            writeln(f, "")
        else:
            # Not yet supported.
            pass
        

def gen_packet_functions_params(f, packet_domain, packet_name, param_list):
    for param_i in param_list:
        #print("generate packet functions params ...")
        gen_packet_functions_param(f, packet_domain, packet_name, param_i)
        gen_packet_functions_params(f, packet_domain, packet_name, param_i["__dp_subparams"])

def gen_packet_functions(f, packet):        
    gen_packet_functions_params(f, packet["domain"], packet["name"], packet["body"])

def get_param_hierarchy_group(parent, hierarchy, body, i):
    param_i = body[i]
    param_i["__dp_subparams"] = []
    param_i["__dp_parent"] = parent
    param_i["__dp_isLeaf"] = True
    param_i["__dp_isPartOfGroup"] = (parent != None)
    param_i["__dp_isOnlyChild"] = False
    hierarchy.append(param_i)
    i = i + 1
    if param_i["group"] is not None and int(param_i["group"]) > 0:
        param_i["__dp_isLeaf"] = False
        n = i + int(param_i["group"])
        while i < n:
            i = get_param_hierarchy_group(param_i, param_i["__dp_subparams"], body, i)
    if (len(param_i["__dp_subparams"]) == 1):
        param_i["__dp_subparams"][0]["__dp_isOnlyChild"] = True
    return i

def get_param_hierarchy_packet(packet, hierarchy):
    i = 0
    while i < len(packet["body"]):
        #print(">>>> Body Name: ", packet["name"], packet["disc"])
        i = get_param_hierarchy_group(None, hierarchy, packet["body"], i)

def get_param_hierarchy(packet, derived = None):
    hierarchy = []
    get_param_hierarchy_packet(packet, hierarchy)
    if derived != None:
        get_param_hierarchy_packet(derived, hierarchy)
    return hierarchy

#-------------------------------------------------------------------------------------
def gen_getter_setter(path, service):    
    desc = "Interface for accessing fields in packets of service \"{0}\".".format(service["name"])

    packets = []            # List of all packets (both base and derived packets)
    discDescriptors = []    # List of descriptors of discriminants
    listOfPredefinedHkRepDef = []   # List of definitions of predefined (3,25) reports
    
    for packet in service["packets"]:
        packets.append({
             "name" : packet["name"],
             "domain" : packet["domain"],
             "header" : packet["header"],
             "body" : get_param_hierarchy(packet),
             "kind" : packet["kind"],
             "length" : (packet["_header_length"] + packet["_length"])/8 if packet["_length"] != None else packet["_header_length"]/8,
             "disc" : packet["disc"]
        })
        
        # Process the derived packets
        if len(packet["derivations"]["list"]) > 0:
            discDesc = {
                    "name" : packet["name"],
                    "domain" : packet["domain"],
                    "nOfDisc" : len(packet["derivations"]["list"])
                    }
            listOfDiscriminants = []
            for derived in packet["derivations"]["list"]:
                packets.append({
                    "name" : derived["name"],
                    "domain" : derived["domain"],
                    "header" : packet["header"],
                    "body" : get_param_hierarchy(packet, derived),
                    "kind" : packet["kind"],
                    "length" : (packet["_header_length"] + packet["_length"] + derived["_length"])/8 if derived["_length"] != None else (packet["_header_length"] + packet["_length"])/8,
                    "disc" : derived["disc"]
                })
                # Add to the lisf of discriminants, the discriminant in symbolic and numeric format
                listOfDiscriminants.append({"disc": derived["disc"], "_disc":derived["_disc"]})
            # Sort the list of discriminant using the discriminant's numeric value as key
            def GetDiscNumValue(s):
                return s["_disc"]
            listOfDiscriminants.sort(key=GetDiscNumValue)
            # Generate a string holding the discriminants as a comma-separated list 
            stringListOfDiscs = ""
            comma = ""
            for item in listOfDiscriminants:
                stringListOfDiscs = stringListOfDiscs + comma + str(item["disc"])
                comma = ","
            discDesc["listOfDiscs"] = stringListOfDiscs
            discDescriptors.append(discDesc)
        
        # Process the pre-defined housekeeping reports to create the list of data pool identifiers of their parameters
        if (packet["type"] == 3) and (packet["subtype"] == 25):
            for derived in packet["derivations"]["list"]:
                predefinedHkRepDef = ""
                comma = ""
                #print("Derived Body found")
                #for item in derived["body"]:
                    #try:  # TODO: check was added
                    #predefinedHkRepDef = predefinedHkRepDef + comma + str(item["param"]["_dpid"])
                    #comma = ","
                    #except KeyError:
                    #    print("KeyError: ", item["param"])
                    #print("Item found: ", item["param"])
                listOfPredefinedHkRepDef.append({"def": predefinedHkRepDef, "sid":derived["disc"], "nOfItems":len(derived["body"])})

    pck_structs = list()
    pck_functions = list()

    for packet in packets:
        this_struct = list()
        if gen_packet_struct(this_struct, packet["name"], packet["kind"], packet["body"]):
            pck_structs += this_struct
            gen_packet_functions(pck_functions, packet)

    f = list()
    writeln(f, "#include \"{0}\"".format(gen_file_name_h("Pckt")))
    
    # Generate constants holding packet lengths 
    writeln(f, "")
    writeln(f, "/* Constants defining:                                                               */")
    writeln(f, "/* - For statically defined packets: the full length                                 */")
    writeln(f, "/* - For packets with groups of dynamic size: length of header + length of CRC field */")
    writeln(f, "/* - For parent packets: length of the parent part of a packet                       */")
    writeln(f, "/* Constant name format is: LEN_<domain>_<basePacketName>_<discriminant>             */")
    writeln(f, " ")
    for packet in packets:
        defConstName = "LEN_"+packet["domain"]+"_"+packet["name"]
        pcktLength = packet["length"]+settings["crc_size"]
        writeln(f, " ")
        writeln(f, "/** Length constant for packet {0}{1} */".format(packet["domain"],packet["name"]))     
        writeln(f, "#define {0} {1}".format(defConstName.upper(), int(pcktLength)))
    writeln(f, "")
    
    # Define constants holding number of discriminants
    if len(discDescriptors) > 0:
        writeln(f, "/* Constants defining the number of derived packets in each base packet */")
        writeln(f, "/* and the list of discriminants of the derived packets                 */")
    for item in discDescriptors:
        defConstName = "N_OF_DER_PCKT_"+item["domain"]+"_"+item["name"]
        writeln(f, " ")
        writeln(f, "/** Number of derived packets in packet {0}{1} */".format(item["domain"],item["name"]))    
        writeln(f, "#define {0} {1}".format(defConstName.upper(), item["nOfDisc"]))    
        writeln(f, "/** List of discriminants for derived packets of packet {0}{1} */".format(item["domain"],item["name"]))    
        defConstName = "LIST_OF_DER_PCKT_"+item["domain"]+"_"+item["name"]
        writeln(f, "#define {0} {{ {1} }}".format(defConstName.upper(), item["listOfDiscs"]))           
    writeln(f, "")
    
    # For predefined (3,25) packets only: define constants holding the packet definitions
    if len(listOfPredefinedHkRepDef) > 0:
        writeln(f, "/* Constants defining the SIDs of pre-defined HK packets and    */")
        writeln(f, "/* the list of data item identifiers in each pre-defined packet */")
        writeln(f, " ")
        for item in listOfPredefinedHkRepDef:
            sidName = "HK_DEF_" + item["sid"] 
            nOfItemsName = "HK_NOFITEMS_" + item["sid"]
            writeln(f, " ")
            writeln(f, "/** Number of items in pre-defined SID {0} */".format(item["sid"]))    
            writeln(f, "#define {0} {{ {1} }}".format(sidName, item["def"]))
            writeln(f, "/** List of data item identifiers in pre-defined SID {0} */".format(item["sid"]))    
            writeln(f, "#define {0} {1}".format(nOfItemsName, str(item["nOfItems"])))
        writeln(f, "")
    
    f += pck_structs
    f += pck_functions

    gen_file(f, path, gen_file_name_h("Pckt" + cname(service["name"])), True, True, desc)    

#-------------------------------------------------------------------------------------
def gen_includes(f):
    for include in settings["includes"]:
        writeln(f, "#include \"{0}\"".format(include))
    writeln(f, "")    

#-------------------------------------------------------------------------------------
# Generate the header file defining the data types defined in all the standards
# attached to the argument application.
# NB: General-purpose data types not attached to any standard are not covered as
#     they are supposed to be defined by the user application.
# 
def gen_app_types(path, app):
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
# Generate file holding the constants representing the identifiers of the service types and sub-types
def gen_serv_id(path, app):
    def print_type_id(f, serv):
        sname = serv["name"]
        sdesc = serv["desc"]
        sid = str(serv["type"])
        prefix = settings["prefix"]
        write_doxy(f, ["Identifier for service: {0} ({1})".format(sname,sdesc)])
        writeln(f, "#define {0}_TYPE ({1})".format(sname.upper(), sid))

    def print_sub_type_id(f, pckt):
        sdomain = pckt["domain"]
        pname = pckt["name"]
        pdesc = pckt["desc"]
        pid = str(pckt["subtype"])
        prefix = settings["prefix"]
        write_doxy(f, ["Identifier for sub-type of packet: {0} in domain {2} ({1})".format(pname,pdesc,sdomain)])
        writeln(f, "#define {0}{1}_STYPE ({2})".format(sdomain.upper(),pname.upper(), pid))

    f = list()
    for standard_relation in app["standards"]:
        listOfServices = standard_relation["standard"]["services"]["list"]
        listOfPackets = standard_relation["standard"]["packets"]["list"]
        for serv in listOfServices:
            if serv["ownerStandardId"] == standard_relation["standard"]["id"]:
                print_type_id(f, serv)
        for pckt in listOfPackets:
            if pckt["ownerStandardId"] == standard_relation["standard"]["id"]:
                print_sub_type_id(f, pckt)
             
    gen_file(f, path, gen_file_name_h("ServTypeId"), True, False, "Definition of type and sub-type identifiers.")    

#-------------------------------------------------------------------------------------
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

#-------------------------------------------------------------------------------------
def gen_header(path, standard):
    f = list()
    gen_packet_struct(f, "TcHeader", "TC_HEADER", standard["headers"]["TC"])
    gen_packet_struct(f, "TmHeader", "TM_HEADER", standard["headers"]["TM"])
    gen_packet_functions_params(f, "", "TcHeader", standard["headers"]["TC"])
    gen_packet_functions_params(f, "", "TmHeader", standard["headers"]["TM"])
    gen_file(f, path, gen_file_name_h("Pckt"), True, True, "Packet header definitions.")

#-------------------------------------------------------------------------------
def gen_pck(path, comp):

    def touch_param_i(param_i):
        param_i["__dp_parent"] = None
        param_i["__dp_subparams"] = []
        param_i["__dp_isLeaf"] = True
        param_i["__dp_isPartOfGroup"] = False    

    global settings 

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
    gen_serv_id(path,app)

    for standard_relation in app["standards"]:
        standard = standard_relation["standard"]
        gen_header(path, standard)
        for service in standard["services"]["list"]:
            #print("Service: ", service["type"], service["name"])
            if service["ownerStandardId"] == standard["id"]:
                #print("generate setter/getter")
                gen_getter_setter(path, service)

#-------------------------------------------------------------------------------
if __name__ == '__main__':

    if (len(sys.argv) == 3):

        project_id = sys.argv[1]
        app_id = sys.argv[2]
        try:
            il = get_data.get_data(project_id)
            app = il["apps"]["hash"][int(app_id)]
            gen_pck("./pck", app["components"]["hash"]["pck"])
            print("Done")
        except Exception as e:
            print("Something went wrong...")
            print(traceback.format_exc())

    else:
        print("Usage: python gen_dp.py {project_id} {app_id}")
