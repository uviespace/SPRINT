#!/usr/bin/python
# coding: utf-8

import sys
import get_data
import math
import traceback
import simplejson

settings = {}

# Limitations
# - Parameter limits is not supported in the cordet-editor. So tables prf, prv are empty.
# - Numeric conversions, i.e. from raw to engineering values, is not supported. So, tables cca, ccs are empty.
# - Command ID is not supported
# - Fixed values for TC parameters (CDF_ELTYPE) not supported, but only defaults.
# - Defaults for arrays not supported. Format unclear.

def new_file(path, name):
    f = open(u"{0}/{1}.csv".format(path, name), "w")
    return f

def writeln(f, data):
    #f.write(u"{0}\n".format(u"\t".join(data)).encode('utf8'))
    f.write(u"{0}\n".format(u"".join(data)).encode('utf8'))

def close_file(f):
    f.close()    

def empty_file(path, name):
    f = new_file(path, name)
    close_file(f)

def get_name(initial, nr):
    return "RMU{0}{1:04d}".format(initial[0], nr)

def get_txf_name(type_):
    preamble = "{0}{1}".format(settings["general"]["preamble"], settings["txf"]["preamble"])
    f = "{{0}}{{1:0{0}d}}".format(8-len(preamble))  # NOTE: 8 according to naming convention, could be longer according to SCOS
    s = f.format(preamble, settings["txf"]["offset"] + type_["_nr"])
    return s

def get_paf_name(type_):
    preamble = "{0}{1}".format(settings["general"]["preamble"], settings["paf"]["preamble"])
    f = "{{0}}{{1:0{0}d}}".format(8-len(preamble))  # NOTE: 8 according to naming convention, could be longer according to SCOS
    s = f.format(preamble, settings["paf"]["offset"] + type_["_nr"])
    return s

def get_pid_name(packet):
    return outp(settings["pid"]["offset"] + packet["_nr"], 10)

def get_pcf_name(param):
    preamble = "{0}{1}".format(settings["general"]["preamble"], settings["pcf"]["preamble"])
    f = "{{0}}{{1:0{0}d}}".format(8-len(preamble))
    s = f.format(preamble, settings["pcf"]["offset"] + param["_nr"])
    return s

def get_pcpc_name(standard, param_i):
    preamble = "{0}{1}".format(settings["general"]["preamble"], settings["pcpc"]["preamble"])
    f = "{{0}}{{1:0{0}d}}".format(6-len(preamble))  # maximum length reduced from 8 to 6, because prefix can be P or DF
    s = f.format(preamble, settings["pcpc"]["offset"] + param_i["_nr"])
    return s

def get_ccf_name(packet):
    preamble = "{0}{1}".format(settings["general"]["preamble"], settings["ccf"]["preamble"])
    f = "{{0}}{{1:0{0}d}}".format(8-len(preamble))
    s = f.format(preamble, settings["ccf"]["offset"] + packet["_nr"])
    return s

def get_cpc_name(param):
    preamble = "{0}{1}".format(settings["general"]["preamble"], settings["cpc"]["preamble"])
    f = "{{0}}{{1:0{0}d}}".format(8-len(preamble))
    s = f.format(preamble, settings["cpc"]["offset"] + param["_nr"])
    return s

def get_prf_name(limit_id):
    preamble = "{0}{1}".format(settings["general"]["preamble"], settings["prf"]["preamble"])
    f = "{{0}}{{1:0{0}d}}{{2}}".format(8-len(preamble))  # was 10-len(preamble)
    s = f.format(preamble, settings["prf"]["offset"] + limit_id, "_L")
    return s

def get_tcp_name(standard):
    # As we only support having a single TC header, a fixed name can be given.
    #return 'NOM_TC'
    return outp(standard["name"], 8, True)

def outp(s, max_len, stripSpaces = False):
    if s == None:
        return ''

    s = unicode(s).replace('\t', '').replace('\n', '')
    if stripSpaces and " " in s:
        s = s.title().replace(' ', '')

    return s[:max_len]

def check_packet_type(app, param_ii):
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for packet in standard["packets"]["TC"]["list"]:
                for param_i in packet["body"]:
                    param = param_i["param"]
                    if (param is param_ii):
                        return "TC"
            for packet in standard["packets"]["TM"]["list"]:
                for param_i in packet["body"]:
                    param = param_i["param"]
                    if (param is param_ii):
                        return "TM"
    return "not found"

def gen_vdf(app, path):
    f = new_file(path, "vdf")
    writeln(f, [
        outp(app["name"], 8, True),
        outp(app["desc"], 32),
        '1',                                      # domain ID
        outp(settings["general"]["release"], 5), 
        outp(settings["general"]["issue"], 5)
    ])
    close_file(f)

def get_ptc_pfc(param):
    domain, name = (param["type"]["domain"], param["type"]["name"]) if param["type"] != None else ('', 'Deduced')
    size = param["_size"]
    multi = param["multi"] if param["multi"] != None else -1
    #setting = param["type"]["setting"]
    #enums = param["type"]["setting"]["Enumerations"]
    dtype_data = param["type"]["datatype"][0]

    ptc = 0
    pfc = 0
    # get ptc and pfc from JSON structure in column 'setting' of table 'type'
    #print("get_ptc_pfc_NEW: name = ", name)
    #print("get_ptc_pfc_NEW: setting = ", setting)
    #print("get_ptc_pfc_NEW: enums = ", enums)
    #print("get_ptc_pfc_NEW: datatype = ", dtype_data)
    if (dtype_data != None):
        if ('PUS' in dtype_data):
            #print("get_ptc_pfc_NEW: datatype = ", dtype_data["PUS"])
            ptc = dtype_data["PUS"]["ptc"]
            pfc = dtype_data["PUS"]["pfc"]
        #else:
            #print("get_ptc_pfc_NEW: No PUS found in datatype")
    else:
        #print("get_ptc_pfc_NEW: datatype = None")
        ptc, pfc, size, multi = get_ptc_pfc_GEN(param)
        #if (name == "generic"):
        #    ptc, pfc, size, multi = 11, 0, 0, multi
        #if (name == "uint8_t"):
        #    if (multi < 0):
        #        ptc, pfc, size, multi = 3, 4, 8, multi
        #    else:
        #        ptc, pfc, size, multi = 7, param["_length"]/8, param["_length"], -1
        if  (ptc!=0 or pfc!=0 or size!=0 or multi!=0):
            return get_ptc_pfc_GEN(param)
        if (size==None):
            size = 0

    #format_list = [name, ptc, pfc, size, multi]
    #print("{}: (ptc, pfc, width, repetition) = ({}, {}, {}, {})".format(*format_list))
    #print(" ")

    # char and (u)int8_t arrays can be mapped to SCOS-2000 types. For arrays of other types, a repetition group
    # must be introduced.
    # format: (ptc, pfc, width, repetition)
    return \
        (ptc, pfc, size, multi)

def get_ptc_pfc_GEN(param):
    domain, name = (param["type"]["domain"], param["type"]["name"]) if param["type"] != None else ('', 'Deduced')
    size = param["_size"]
    multi = param["multi"] if param["multi"] != None else -1

    # char and (u)int8_t arrays can be mapped to SCOS-2000 types. For arrays of other types, a repetition group
    # must be introduced.
    # format: (ptc, pfc, width, repetition)
    return \
        (1, 0, 1, multi) if domain == "General" and name == "bit" else \
        (3, 4, 8, multi) if domain == "C99" and name == "uint8_t" and multi < 0 else \
        (7, param["_length"]/8, param["_length"], -1) if domain == "C99" and name == "uint8_t" else \
        (3, 12, 16, multi) if domain == "C99" and name == "uint16_t" else \
        (3, 14, 32, multi) if domain == "C99" and name == "uint32_t" else \
        (3, 16, 64, multi) if domain == "C99" and name == "uint64_t" else \
        (4, 4, 8, multi) if domain == "C99" and name == "int8_t" and multi < 0 else \
        (7, param["_length"]/8, param["_length"], -1) if domain == "C99" and name == "int8_t" else \
        (4, 12, 16, multi) if domain == "C99" and name == "int16_t" else \
        (4, 14, 32, multi) if domain == "C99" and name == "int32_t" else \
        (4, 16, 64, multi) if domain == "C99" and name == "int64_t" else \
        (5, 1, 32, multi) if domain == "C99" and name == "float" else \
        (5, 2, 64, multi) if domain == "C99" and name == "double" else \
        (8, param["_length"]/8, param["_length"], -1) if domain == "C99" and name == "char" else \
        (3, size-4, size, multi) if domain == "SCOS-2000" and name == "Unsigned Integer" and size >= 4 and size <= 16 else \
        (3, 13, 24, multi) if domain == "SCOS-2000" and name == "Unsigned Integer" and size == 24 else \
        (3, 14, 32, multi) if domain == "SCOS-2000" and name == "Unsigned Integer" and size == 32 else \
        (2, size, size, multi) if domain == "SCOS-2000" and name == "Unsigned Integer" and size > 0 and size < 33 else \
        (4, size-4, size, multi) if domain == "SCOS-2000" and name == "Signed Integer" and size >= 4 and size <= 16 else \
        (4, 13, 24, multi) if domain == "SCOS-2000" and name == "Signed Integer" and size == 24 else \
        (4, 14, 32, multi) if domain == "SCOS-2000" and name == "Signed Integer" and size == 32 else \
        (7, param["_length"]/8, param["_length"], -1) if domain == "SCOS-2000" and name == "Octet string" else \
        (8, param["_length"]/8, param["_length"], -1) if domain == "SCOS-2000" and name == "ASCII string" else \
        (9, 1, 0, multi) if domain == "SCOS-2000" and name == u"Absolute time CDS w/o μs" else \
        (9, 2, 0, multi) if domain == "SCOS-2000" and name == u"Absolute time CDS with μs" else \
        (9, 3, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (1/0)" else \
        (9, 4, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (1/1)" else \
        (9, 5, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (1/2)" else \
        (9, 6, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (1/3)" else \
        (9, 7, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (2/0)" else \
        (9, 8, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (2/1)" else \
        (9, 9, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (2/2)" else \
        (9, 10, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (2/3)" else \
        (9, 11, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (3/0)" else \
        (9, 12, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (3/1)" else \
        (9, 13, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (3/2)" else \
        (9, 14, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (3/3)" else \
        (9, 15, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (4/0)" else \
        (9, 16, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (4/1)" else \
        (9, 17, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (4/2)" else \
        (9, 18, 0, multi) if domain == "SCOS-2000" and name == "Absolute time CUC (4/3)" else \
        (10, 3, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (1/0)" else \
        (10, 4, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (1/1)" else \
        (10, 5, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (1/2)" else \
        (10, 6, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (1/3)" else \
        (10, 7, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (2/0)" else \
        (10, 8, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (2/1)" else \
        (10, 9, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (2/2)" else \
        (10, 10, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (2/3)" else \
        (10, 11, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (3/0)" else \
        (10, 12, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (3/1)" else \
        (10, 13, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (3/2)" else \
        (10, 14, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (3/3)" else \
        (10, 15, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (4/0)" else \
        (10, 16, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (4/1)" else \
        (10, 17, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (4/2)" else \
        (10, 18, 0, multi) if domain == "SCOS-2000" and name == "Relative time CUC (4/3)" else \
        (11, 0, 0, multi) if domain == "" and name == "Deduced" and size == None else \
        (11, size, 0, multi) if domain == "" and name == "Deduced" and size != None else \
        (11, 0, 0, multi) if domain == "General" and name == "generic" else \
        (11, size, 0, multi) if domain == "General" and name == "deduced" and size != None else \
        (0, 0, 0, 0)

"""(9, 17, 0, multi) if domain == "CrFwUserConstants" and name == "CrFwTimeStamp_t" else \
(3, 12, 16, multi) if domain == "CrFwUserConstants" and name == "CrFwDiscriminant_t" else \
(3, 4, 8, multi) if domain == "CrFwUserConstants" and name == "CrFwServSubType_t" else \
(3, 4, 8, multi) if domain == "CrFwUserConstants" and name == "CrFwServType_t" else \
(3, 4, 8, multi) if domain == "CrFwUserConstants" and name == "CrFwDestSrc_t" else \
(3, 12, 16, multi) if domain == "CrFwUserConstants" and name == "CrFwTypeId_t" else \
(3, 12, 16, multi) if domain == "CrFwUserConstants" and name == "CrFwCounterU2_t" else \
(3, 14, 24, multi) if domain == "CrFwUserConstants" and name == "CrFwCounterU4_t" else \
(2, 14, 14, multi) if domain == "subbyte" and name == "FourTeen_Bit_t" else \
(2, 11, 11, multi) if domain == "subbyte" and name == "Eleven_Bit_t" else \
(2, 4, 4, multi) if domain == "subbyte" and name == "Four_Bit_t" else \
(2, 3, 3, multi) if domain == "subbyte" and name == "Three_Bit_t" else \
(2, 2, 2, multi) if domain == "subbyte" and name == "Two_Bit_t" else \
(2, 1, 1, multi) if domain == "subbyte" and name == "One_Bit_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsApid_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsDestSrc_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsStepId_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsAux_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsEid_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsCollectInterval_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsParamId_t" else \
(3, 4, 8, multi) if domain == "CrPsUserConstants" and name == "CrPsStatus_t" else \
(3, 4, 8, multi) if domain == "CrPsUserConstants" and name == "CrPsSid_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsRepNum_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsFailReason_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsPart_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsTid_t" else \
(3, 4, 8, multi) if domain == "CrPsUserConstants" and name == "CrPsNFuncMon_t" else \
(3, 4, 8, multi) if domain == "CrPsUserConstants" and name == "CrPsNParMon_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsAddr_t" else \
(3, 4, 8, multi) if domain == "CrPsUserConstants" and name == "CrPsFlag_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsRepetition_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsPeriod_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsFailCode_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsFailData_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsSize_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsTimeOut_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsSeqCtrl_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsValueU4_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsParamValueU2_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsParamValueU4_t" else \
(3, 12, 16, multi) if domain == "CrPsUserConstants" and name == "CrPsNumberU2_t" else \
(3, 14, 32, multi) if domain == "CrPsUserConstants" and name == "CrPsNumberU4_t" else \
(7, 8, 64, multi) if domain == "CrPsUserConstants" and name == "CrPsErrLogInfo_t" else \
(3, 12, 16, multi) if domain == "PrivateUserConstants" and name == "Fid_t" else \
(3, 12, 16, multi) if domain == "PrivateUserConstants" and name == "HbData_t" else \
(3, 12, 16, multi) if domain == "Algo" and name == "CrFwAlgoId_t" else \
(3, 12, 16, multi) if domain == "Boot" and name == "CrFwDpuMemoryId_t" else \
(3, 0, 4, multi) if domain == "Boot" and name == "CrFwDpuMode_t" else \
(2, 2, 2, multi) if domain == "Boot" and name == "CrFwDpuSwActive_t" else \
(2, 1, 1, multi) if domain == "Boot" and name == "CrFwDpuUnit_t" else \
(2, 1, 1, multi) if domain == "Boot" and name == "CrFwDpuWatchdogStatus_t" else \
(3, 12, 16, multi) if domain == "Boot" and name == "CrFwErrCode_t" else \
(7, 0, 96, multi) if domain == "Boot" and name == "CrFwErrLogInfo_t" else \
(9, 18, 56, multi) if domain == "Boot" and name == "CrFwErrTimeStamp_t" else \
(3, 4, 8, multi) if domain == "Boot" and name == "CrFwResetType_t" else \
(3, 14, 32, multi) if domain == "Boot" and name == "CrFwStartAddress_t" else \
(3, 12, 16, multi) if domain == "Evt" and name == "CrFwEvtId_t" else \
(3, 12, 16, multi) if domain == "Fdir" and name == "CrFwFid_t" else \
(2, 1, 1, multi) if domain == "Gen" and name == "CrFwAckFlag_t" else \
(3, 4, 8, multi) if domain == "Gen" and name == "CrFwBool_t" else \
(3, 12, 16, multi) if domain == "Gen" and name == "CrFwCrc_t" else \
(3, 4, 8, multi) if domain == "Gen" and name == "CrFwDestSrc_t" else \
(3, 4, 8, multi) if domain == "Gen" and name == "CrFwDiscriminant_t" else \
(2, 11, 11, multi) if domain == "Gen" and name == "CrFwElevenBit_t" else \
(2, 4, 4, multi) if domain == "Gen" and name == "CrFwFourBit_t" else \
(2, 14, 14, multi) if domain == "Gen" and name == "CrFwFourteenBit_t" else \
(3, 12, 16, multi) if domain == "Gen" and name == "CrFwLength_t" else \
(2, 1, 1, multi) if domain == "Gen" and name == "CrFwOneBit_t" else \
(3, 4, 8, multi) if domain == "Gen" and name == "CrFwServSubType_t" else \
(3, 4, 8, multi) if domain == "Gen" and name == "CrFwServType_t" else \
(2, 16, 16, multi) if domain == "Gen" and name == "CrFwSixteenBit_t" else \
(2, 32, 32, multi) if domain == "Gen" and name == "CrFwThirtytwoBit_t" else \
(2, 3, 3, multi) if domain == "Gen" and name == "CrFwThreeBit_t" else \
(9, 18, 56, multi) if domain == "Gen" and name == "CrFwTimeStamp_t" else \
(3, 14, 32, multi) if domain == "Gen" and name == "CrFwTime_t" else \
(2, 2, 2, multi) if domain == "Gen" and name == "CrFwTwoBit_t" else \
(3, 12, 16, multi) if domain == "Hb" and name == "CrFwHbData_t" else \
(3, 12, 16, multi) if domain == "Hk" and name == "CrFwNParam_t" else \
(3, 14, 32, multi) if domain == "Hk" and name == "CrFwParamId_t" else \
(3, 12, 16, multi) if domain == "Hk" and name == "CrFwPeriod_t" else \
(3, 12, 16, multi) if domain == "Hk" and name == "CrFwSid_d" else \
(3, 12, 16, multi) if domain == "Ldt" and name == "CrFwDataPartLength_t" else \
(3, 4, 8, multi) if domain == "Ldt" and name == "CrFwLduId_t" else \
(3, 12, 16, multi) if domain == "Ldt" and name == "CrFwReasonCode_t" else \
(3, 12, 16, multi) if domain == "Ldt" and name == "CrFwSeqNmb_t" else \
(3, 14, 32, multi) if domain == "Mem" and name == "CrFwBlockLength_t" else \
(3, 12, 16, multi) if domain == "Mem" and name == "CrFwMemoryId_t" else \
(3, 14, 32, multi) if domain == "Mem" and name == "CrFwStartAddress_t" else \
(3, 12, 16, multi) if domain == "Param" and name == "CrFwArrayElemId_t" else \
(3, 12, 16, multi) if domain == "Param" and name == "CrFwNParams_t" else \
(3, 14, 32, multi) if domain == "Param" and name == "CrFwParamId_t" else \
(3, 4, 8, multi) if domain == "Param" and name == "CrFwParamType_t" else \
(3, 12, 16, multi) if domain == "Proc" and name == "CrFwProcId_t" else \
(9, 18, 56, multi) if domain == "Time" and name == "CrFwObtTime_t" else \
(3, 4, 8, multi) if domain == "Time" and name == "CrFwObtSync_t" else \
(3, 12, 16, multi) if domain == "Ver" and name == "CrFwFailCode_t" else \
(3, 12, 16, multi) if domain == "Ver" and name == "CrFwPcktId_t" else \
(3, 12, 16, multi) if domain == "Ver" and name == "CrFwPcktSeqCtrl_t" else \
(3, 12, 16, multi) if domain == "Ver" and name == "CrFwReceivedBytes_t" else \
(0, 0, 0, 0)"""

def pcf_decim(width):
    decim = ''
    if width != None and width > 0:
        decim = outp(int(math.ceil(math.log(math.pow(2, width), 10))), 3)
    return decim

#-----------------------------------------------------------------------------------------------------------------------


def cname(s):
    return s.replace(" ", "").replace("-", "").replace("/", "").replace("\\", "")


'''
def get_indent(level):
    #return settings["indent"] * level
    return 1 * level


def writeline(f, s, indent_level=0):
    f += "{0}{1}\n".format(" " * get_indent(indent_level), s.encode("utf8"))
'''

def getDatatype(ptc, pfc, width):
    dt = ""

    if (ptc == 3):
        if (pfc == 4):
            dt = "UINT8"
        elif (pfc == 12):
            dt = "UINT16"
        elif (pfc == 13):
            dt = "UINT24"
        elif (pfc == 14):
            dt = "UINT32"
        elif (pfc == 16):
            dt = "UINT64"
        else:
            dt = "UINT"
    elif (ptc == 4):
        if (pfc == 4):
            dt = "INT8"
        elif (pfc == 12):
            dt = "INT16"
        elif (pfc == 14):
            dt = "INT32"
        elif (pfc == 16):
            dt = "INT64"
        else:
            dt = "INT"
    elif (ptc == 5):
        if (pfc == 1):
            dt = "FLOAT"
        elif (pfc == 2):
            dt = "DOUBLE"
        else:
            dt = "FLOAT"+" ("+str(ptc)+"/"+str(pfc)+")"
    elif (ptc == 7):
        dt = "Octet string"+" ("+str(ptc)+"/"+str(pfc)+") "+str(width)
        dt = "UINT"+str(width/pfc)
    else:
        dt = "UNKNOWN"+" ("+str(ptc)+"/"+str(pfc)+")"

    return dt

def gen_dp_list(app, path):
    def add_elem(d, param, type_):
        if param["domain"] not in d:
            d[param["domain"]] = {}
            d[param["domain"]]["params"] = []
            d[param["domain"]]["vars"] = []
        d[param["domain"]][type_].append(param)

    dpid_offset = 345544320 - 0  # -1 because dpid starts with 0+1
    delimiter = "|"
    additionalInfo = True

    f = new_file(path, "dp")

    domain_dict = {}
    params_list = []
    vars_list = []

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

    if len(params_list) > 0:
        #writeln(f, "/* Parameters */")
        #writeln(f, "DpIdParamsLowest = {0},".format(params_list[0]["_dpid"]))
        #writeln(f, "DpIdParamsHighest = {0},".format(params_list[len(params_list)-1]["_dpid"]))
        for i, param in enumerate(params_list):
            pname = cname(param["name"])
            #writeln(f, "DpId{0} = {1}{2}".format(pname, param["_dpid"], "," if i < len(params_list)-1 or (len(vars_list) > 0) else ""))
            ptc, pfc, width, repetition = get_ptc_pfc(param)
            datatype = getDatatype(ptc, pfc, width)
            if param["size"] is not None:
                bitsize = str(param["size"])
            elif ptc == 7:
                bitsize = str(width/long(param["multi"]))
            else:
                bitsize = str(width)
            writeln(f, [
                outp(param["name"], 24, True) + delimiter,
                str(dpid_offset + int(param["_dpid"]))+delimiter if "_dpid" in param else delimiter,
                datatype + delimiter,
                #str(width) + delimiter,
                #str(repetition) + delimiter,
                bitsize + delimiter,
                '1'+delimiter if param["multi"] is None else str(param["multi"]) + delimiter,
                'PAR'+delimiter,
                param["value"]+delimiter if additionalInfo else delimiter,
                param["shortDesc"]+delimiter if additionalInfo else delimiter,
                param["domain"] if additionalInfo else ""
            ])
    if len(vars_list) > 0:
        #writeln(f, "/* Variables */")
        #writeln(f, "DpIdVarsLowest = {0},".format(vars_list[0]["_dpid"]))
        #writeln(f, "DpIdVarsHighest = {0},".format(vars_list[len(vars_list)-1]["_dpid"]))
        for i, param in enumerate(vars_list):
            pname = cname(param["name"])
            #writeln(f, "DpId{0} = {1}{2}".format(pname, param["_dpid"], "," if i < len(vars_list)-1 else ""))
            ptc, pfc, width, repetition = get_ptc_pfc(param)
            datatype = getDatatype(ptc, pfc, width)
            if param["size"] is not None:
                bitsize = str(param["size"])
            elif ptc == 7:
                bitsize = str(width/long(param["multi"]))
            else:
                bitsize = str(width)
            writeln(f, [
                outp(param["name"], 24, True) + delimiter,
                str(dpid_offset + int(param["_dpid"]))+delimiter if "_dpid" in param else delimiter,
                datatype + delimiter,
                #str(width) + delimiter,
                #str(repetition) + delimiter,
                bitsize + delimiter,
                '1'+delimiter if param["multi"] is None else str(param["multi"]) + delimiter,
                'VAR'+delimiter,
                param["value"]+delimiter if additionalInfo else delimiter,
                param["shortDesc"]+delimiter if additionalInfo else delimiter,
                param["domain"] if additionalInfo else ""
            ])

    close_file(f)

def gen_dp_pckt_list(app, path):
    f = new_file(path, "dp_pckt")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]

            dpid_offset = 345544320 - 0   # -1 because dpid starts with 0+1
            delimiter = ","
            additionalInfo = False

            #for tm in standard["packets"]["TM"]["list"]:
            #    for param_i in tm["body"]:
            #        print("gen_pcf: ", param_i["param"]["id"], param_i["param"]["name"])


            for param in standard["params"]["list"]:

                # check if parameter is in header
                found = 0
                for param_i in standard["headers"]["TM"]:  # TODO: check if param kind is also possible for this decision
                    #print(param_i["param"]["id"], param_i["param"]["name"])
                    if param_i["param"]["id"] == param["id"]:
                        #print("FOUND id: ", param["id"], " name: ", param["name"])
                        found = 1
                        break

                if found == 0:
                    # check if parameter is in packet
                    #print(param["id"])
                    for tm in standard["packets"]["TM"]["list"]:
                        written = 0
                        #print(tm["type"], tm["subtype"], tm["name"])

                        # check if parameter is in base packet
                        for param_i in tm["body"]:
                            #print("gen_pcf: ", param_i["param"]["name"])
                            if param_i["param"]["id"] == param["id"]:
                                #print(param["id"], param["name"])
                                ptc, pfc, width, repetition = get_ptc_pfc(param)
                                #ptc2,pfc2,width2,repetition2 = get_ptc_pfc_NEW(param)
                                hasTextualCalibration = (len(param["type"]["enums"]) > 0)

                                if "_dpid" in param:
                                    writeln(f, [
                                        outp(param["name"], 24, True)+delimiter,
                                        outp((dpid_offset + int(param["_dpid"])) if "_dpid" in param else '', 10)+delimiter,
                                        outp(width, 6)+delimiter,
                                        '1'+delimiter if param["multi"] is None else outp(param["multi"], 6)+delimiter,
                                        ''+delimiter,
                                        param["value"]+delimiter if additionalInfo else delimiter,
                                        param["shortDesc"]+delimiter if additionalInfo else delimiter,
                                        param["domain"] if additionalInfo else ""
                                    ])
                                written = 1
                                break

                        # check if parameter is in derived packet
                        for derived in tm["derivations"]["list"]:
                            for param_i in derived["body"]:
                                if param_i["param"]["id"] == param["id"]:
                                    #print(param["id"], param["name"])
                                    ptc, pfc, width, repetition = get_ptc_pfc(param)
                                    hasTextualCalibration = (len(param["type"]["enums"]) > 0)
                                    if "_dpid" in param:
                                        writeln(f, [
                                            outp(param["name"], 24, True)+delimiter,
                                            outp((dpid_offset + int(param["_dpid"])) if "_dpid" in param else '', 10)+delimiter,
                                            outp(width, 6)+delimiter,
                                            '1'+delimiter if param["multi"] is None else outp(param["multi"], 6)+delimiter,
                                            ''+delimiter,
                                            param["value"]+delimiter if additionalInfo else delimiter,
                                            param["shortDesc"]+delimiter if additionalInfo else delimiter,
                                            param["domain"] if additionalInfo else ""
                                        ])
                                    written = 1
                                    break
                            if written == 1:
                                break

                        if written == 1:
                            break
    close_file(f)



def prepare(app):
    # Generate SPID for all TM packets
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for tm in standard["packets"]["TM"]["list"]:
                if len(tm["derivations"]["list"]) > 0:
                    for derived in tm["derivations"]["list"]:
                        derived["__mib_spid"] = get_pid_name(derived)
                else:
                    tm["__mib_spid"] = get_pid_name(tm)

    # NEW: Copy param information into standard["packets"]["TC"]["params"] structure
    for packet in standard["packets"]["TC"]["list"]:
        #ccf_name = get_ccf_name(packet)
        for param_i in packet["body"]:
            param = param_i["param"]
            standard["packets"]["TC"]["params"][param["id"]] = param
            #print("param: "+str(param["id"]))

    # NEW: Copy param information into standard["packets"]["TM"]["params"] structure
    for packet in standard["packets"]["TM"]["list"]:
        # ccf_name = get_ccf_name(packet)
        for param_i in packet["body"]:
            param = param_i["param"]
            standard["packets"]["TM"]["params"][param["id"]] = param
            # print("param: "+str(param["id"]))

    # Mark all types for whether they are used for commands and/or reports
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for type_ in standard["types"].values():
                type_["__mib_used_tc"] = False
                type_["__mib_used_tm"] = False
            for param in standard["packets"]["TC"]["params"].values():
                type_ = param["type"]
                if type_ != None:
                    type_["__mib_used_tc"] = True
            for param in standard["packets"]["TM"]["params"].values():
                type_ = param["type"]
                if type_ != None:
                    type_["__mib_used_tm"] = True


def gen_dp_csv(path, comp):
    global settings

    settings = comp["setting"]
    #print(settings["pid"])

    if settings is None:
        return

    app = comp["app"]
    prepare(app)

    # General
    gen_dp_pckt_list(app, path)
    gen_dp_list(app, path)


if __name__ == '__main__':

    if (len(sys.argv) == 3):

        project_id = sys.argv[1]
        app_id = sys.argv[2]
        try:
            il = get_data.get_data(project_id)            
            app = il["apps"]["hash"][int(app_id)]
            gen_dp_csv("./dp_csv", app["components"]["hash"]["mib"])
            print("Done")
        except Exception as e:
            print("Something went wrong...")
            print(traceback.format_exc())

    else:
        print("Usage: python gen_dp_csv.py {project_id} {application_id}")
