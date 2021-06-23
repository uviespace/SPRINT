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
    f = open(u"{0}/{1}.dat".format(path, name), "w")
    return f

def writeln(f, data):
    f.write(u"{0}\n".format(u"\t".join(data)).encode('utf8'))

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

def gen_pcf(app, path):
    f = new_file(path, "pcf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]

            dpid_offset = 345544320 - 0   # -1 because dpid starts with 0+1

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

                                writeln(f, [
                                    get_pcf_name(param),                                   # PCF_NAME
                                    outp(param["name"], 24, True),                         # PCF_DESCR
                                    outp((dpid_offset + int(param["_dpid"])) if "_dpid" in param else '', 10),  # PCF_PID
                                    outp(param["unit"], 4),                                # PCF_UNIT
                                    outp(ptc, 2),                                          # PCF_PTC
                                    outp(pfc, 5),                                          # PCF_PFC
                                    outp(width, 6),                                        # PCF_WIDTH
                                    '',                                                    # PCF_VALID
                                    '',                                                    # PCF_RELATED
                                    'S' if hasTextualCalibration else 'N',                 # PCF_CATEG
                                    'R',                                                   # PCF_NATUR
                                    get_txf_name(param["type"]) if hasTextualCalibration else '',  # PCF_CURTX
                                    'F',                                                   # PCF_INTER
                                    'N',                                                   # PCF_USCON
                                    pcf_decim(param["_size"]),                             # PCF_DECIM
                                    '',                                                    # PCF_PARVAL
                                    '',                                                    # PCF_SUBSYS
                                    '',                                                    # PCF_VALPAR
                                    '',                                                    # PCF_SPTYPE
                                    'Y',
                                    '',
                                    '',
                                    'B'
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
                                    writeln(f, [
                                        get_pcf_name(param),                                   # PCF_NAME
                                        outp(param["name"], 24, True),                         # PCF_DESCR
                                        outp((dpid_offset + int(param["_dpid"])) if "_dpid" in param else '', 10),  # PCF_PID
                                        outp(param["unit"], 4),                                # PCF_UNIT
                                        outp(ptc, 2),                                          # PCF_PTC
                                        outp(pfc, 5),                                          # PCF_PFC
                                        outp(width, 6),                                        # PCF_WIDTH
                                        '',                                                    # PCF_VALID
                                        '',                                                    # PCF_RELATED
                                        'S' if hasTextualCalibration else 'N',                 # PCF_CATEG
                                        'R',                                                   # PCF_NATUR
                                        get_txf_name(param["type"]) if hasTextualCalibration else '',  # PCF_CURTX
                                        'F',                                                   # PCF_INTER
                                        'N',                                                   # PCF_USCON
                                        pcf_decim(param["_size"]),                             # PCF_DECIM
                                        '',                                                    # PCF_PARVAL
                                        '',                                                    # PCF_SUBSYS
                                        '',                                                    # PCF_VALPAR
                                        '',                                                    # PCF_SPTYPE
                                        'Y',
                                        '',
                                        '',
                                        'B'
                                    ])
                                    written = 1
                                    break
                            if written == 1:
                                break

                        if written == 1:
                            break
    close_file(f)

def gen_ccf(app, path):
    f = new_file(path, "ccf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for packet in standard["packets"]["TC"]["list"]:
                npars = 0
                for param_i in packet["body"]:
                    npars = npars + 1
                # !!! naming convention !!!
                ccf_descr = "SASW "+outp(packet["name"], 19, True)

                '''
                if packet["derivations"]["list"]:
                    print("Derived pakets found! ", packet["name"])

                    for derived in packet["derivations"]["list"]:

                        print("Disc.: ", derived["disc"])

                        npars_derived = npars

                        for param_i in derived["body"]:
                            print("Param: ", param_i["param"]["name"])
                            npars_derived = npars_derived + 1

                        writeln(f, [
                            get_ccf_name_derived(packet),  # CCF_NAME
                            ccf_descr,  # CCF_DESCR; was: outp(packet["name"], 24, True),
                            outp(packet["shortDesc"], 64),  # CCF_DESCR2
                            '',  # CCF_CTYPE
                            'N',  # CCF_CRITICAL
                            outp(standard["name"], 8, True),  # CCF_PKTID
                            outp(packet["type"], 3),  # CCF_TYPE
                            outp(packet["subtype"], 3),  # CCF_STYPE
                            outp(packet["process"]["address"], 5),  # CCF_APID
                            outp(npars_derived, 3),  # CCF_NPARS: Number of elements
                            'A',
                            'Y',
                            'N',
                            'C',
                            '',  # CCF_SUBSYS
                            'N',
                            '',
                            '',
                            '',
                            '9',
                            ''
                        ])

                else:
                '''
                writeln(f, [
                    get_ccf_name(packet),                   # CCF_NAME
                    ccf_descr,                              # CCF_DESCR; was: outp(packet["name"], 24, True),
                    outp(packet["shortDesc"], 64),          # CCF_DESCR2
                    '',                                     # CCF_CTYPE
                    'N',                                    # CCF_CRITICAL
                    outp(standard["name"], 8, True),        # CCF_PKTID
                    outp(packet["type"], 3),                # CCF_TYPE
                    outp(packet["subtype"], 3),             # CCF_STYPE
                    outp(packet["process"]["address"], 5),  # CCF_APID
                    outp(npars, 3),                         # CCF_NPARS: Number of elements
                    'A',
                    'Y',
                    'N',
                    'C',
                    '',                                     # CCF_SUBSYS
                    'N',
                    '',
                    '',
                    '',
                    '9',
                    ''
                ])
    close_file(f)

def gen_cdf(app, path):
    f = new_file(path, "cdf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for packet in standard["packets"]["TC"]["list"]:
                ccf_name = get_ccf_name(packet)
                for param_i in packet["body"]:
                    param = param_i["param"]
                    # Fixed value only for group / repetition parameters

                    if param_i["group"] > 0 and param_i["repetition"] > 0:
                        value = outp(param_i["repetition"], 17)
                    elif param_i["role"] == 8:
                        value = "0"
                    elif param_i["value"] != '':
                        value = param_i["value"]
                    else:
                        value = None

                    #print(">>>>>>>>>>>>>> ", param["name"], value)

                    #value = outp(param_i["repetition"], 17) if param_i["group"] > 0 and param_i["repetition"] > 0 else None
                    size = outp(param["_size"], 4) if param["_size"] != None or param["_size"] == "" else '0'
                    eltype = \
                        'A' if param_i["role"] == 8 else \
                        'F' if value != None else \
                        'E'
                    writeln(f, [
                        ccf_name,                                 # CDF_CNAME
                        eltype,                                   # CDF_ELTYPE:  'A' for Spares
                        'SPARE' if param_i["role"] == 8 else '',  # CDF_DESCR  # TODO: or CPC_DESCR
                        size,                                     # CDF_ELLEN
                        outp(param_i["_offset"], 4),              # CDF_BIT
                        outp(param_i["group"], 2),                # CDF_GRPSIZE
                        '' if param_i["role"] == 8 else get_cpc_name(param),  # CDF_PNAME:  '' for Spare
                        'E' if param_i["value"] != '' and not param_i["value"].isdigit() else 'R',   # CDF_INTER  # NOTE: if text/numeric calibrated (CPC_CATEG = T/C) then 'E'
                        value if value != None else '',  # CDF_VALUE
                        ''
                    ])
    close_file(f)

def gen_cpc(app, path):
    f = new_file(path, "cpc")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for param in standard["packets"]["TC"]["params"].values():
                if "Spare" in param["name"]:  # !!!! TODO !!!!!!!!! better solution needed
                    #print(">>>>>>>>>>>>>>", param["name"], param["role"])
                    continue  # if spare no entry
                ptc, pfc, width, repetition = get_ptc_pfc(param)
                # 'C' not supported (raw to engineering value conversion)
                # Role = 6 (Parameter ID), Role = 7 (Command ID)
                categ = \
                    'T' if param["type"] != None and len(param["type"]["enums"]) > 0 else \
                    'A' if param["role"] == 7 else \
                    'P' if param["role"] == 6 else \
                    'N'
                # numerische calibration 'C'
                dispfmt = \
                    'A' if categ == 'T' else \
                    'U' if ptc == 3 else \
                    'I' if ptc == 4 else \
                    'R' if ptc == 5 else \
                    'T' if ptc == 9 else \
                    'D' if ptc == 10 else \
                    'R'  # default value
                # check for parameter limits
                limit = param["_limits"]["hash"]
                if len(limit) >= 1:
                    for id in limit:
                        par_limit_id = get_prf_name(id)
                        break  # only once
                else:
                    par_limit_id = ''
                '''
                if len(limit) > 1:  # TODO: DEBUG!!!
                    for x in limit:
                        print ("id: ", x)
                        for y in limit[x]:
                            print ("key: ", y, ', value: ', limit[x][y])
                '''
                writeln(f, [
                    get_cpc_name(param),           # CPC_PNAME
                    outp(param["name"], 24),       # CPC_DESCR
                    outp(ptc, 2),                  # CPC_PTC
                    outp(pfc, 5),                  # CPC_PFC
                    dispfmt,                       # CPC_DISPFMT: Flag controlling the input and display format of the engineering values for calibrated parameters and for time parameters.
                    'D',                           # CPC_RADIX: always Decimal
                    outp(param["unit"], 4),        # CPC_UNIT
                    categ,                         # CPC_CATEG
                    par_limit_id,                  # CPC_PRFREF - parameter limits (unsupported)
                    '',                            # CPC_CCAREF - numeric calibration curve set (unsupported)
                    get_paf_name(param["type"]) if categ == 'T' else '',          # CPC_PAFREF
                    'E' if categ == 'T' else 'R',  # CPC_INTER
                    outp(param["_value"], 17) if param["multi"] is None else '',  # CPC_DEFVAL: TODO: Not supported: default for arrays. empty for discriminants (e.g. EvtId, Sid, MemoryId, ParamSetId
                    'Y',
                    '0'
                ])
    close_file(f)

def gen_cvp(app, path):
    f = new_file(path, "cvp")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for packet in standard["packets"]["TC"]["list"]:
                for cvsid in [0, 1, 2]:
                    writeln(f, [
                        get_ccf_name(packet),  # CVP_TASK
                        'C',                   # CVP_TYPE
                        outp(cvsid, 1)         # CVP_CVSID
                    ])
    close_file(f)

def gen_cvs(app, path):
    f = new_file(path, "cvs")
    for cvsid in [0, 1, 2]:
        cvstype = \
            'A' if cvsid == 0 else \
            'C' if cvsid == 1 else \
            'S' if cvsid == 2 else \
            'A'
        cvsinterval = \
            '20' if cvsid == 0 else \
            '50' if cvsid == 1 else \
            '20' if cvsid == 2 else \
            '20'
        writeln(f, [
            outp(cvsid, 1),        # CVS_ID
            outp(cvstype, 1),      # CVS_TYPE
            'R',                   # CVS_SOURCE
            '0',                   # CVS_START
            outp(cvsinterval, 3),  # CVS_INTERVAL
            '',                    # CVS_SPID
            ''                     # CVS_UNCERTAINTY
        ])
    close_file(f)

def gen_prf(app, path):
    f = new_file(path, "prf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for param in standard["params"]["list"]:
                limit_cnt = len(param["_limits"]["hash"])
                if (limit_cnt != 0):
                    #print("--> limit_cnt: ", limit_cnt)
                    #print("gen_prf: packet type: ", check_packet_type(app, param))
                    # check if parameter is in TC packet
                    if check_packet_type(app, param) == "TC":
                        limit = param["_limits"]["hash"]
                        for id in limit:
                            #print ("id: ", id)
                            lvalue = ''
                            hvalue = ''
                            par_limit_id = get_prf_name(id)
                            for y in limit[id]:
                                #print ("key: ", y, ', value: ', limit[id][y])

                                if y == "setting" and limit[id][y] != '':
                                    setting = simplejson.loads(str(limit[id][y]))

                                if y == "type":
                                    type_val = limit[id][y]
                                    if type_val >= 10:
                                        prf_inter = 'E'
                                        type_val -= 10
                                    else:
                                        prf_inter = 'R'
                                    prf_dspfmt = \
                                        'A' if type_val == 0 else \
                                        'I' if type_val == 1 else \
                                        'U' if 2 <= type_val <= 4 else \
                                        'R' if type_val == 5 else \
                                        'T' if type_val == 6 else \
                                        'D' if type_val == 7 else \
                                        'U'  # default value
                                    prf_radix = \
                                        'D' if type_val == 2 else \
                                        'H' if type_val == 3 else \
                                        'O' if type_val == 4 else \
                                        'D'  # default value
                            break  # only once
                        writeln(f, [
                            par_limit_id,                  # parameter range set identification name.
                            str(setting["prf"]["descr"]),  # textual description of the parameter range set
                            prf_inter,                     # raw representation 'R' or engineering representation 'E'
                            prf_dspfmt,                    # representation type of the values specified for this range set (PRV table)
                            prf_radix,                     # radix used for the range values specified in the corresponding records (PRV table)
                            str(limit_cnt),                # number of records defined in the PRV table for this range set
                            ''                             # OPTIONAL: engineering unit mnemonic for consistency checking
                        ])
                #elif (): # TODO: also if enumerated values are given
                #    break;
    close_file(f)

def gen_prv(app, path):
    f = new_file(path, "prv")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for param in standard["params"]["list"]:
                limit_cnt = len(param["_limits"]["hash"])
                if (limit_cnt != 0):
                    #print("--> limit_cnt: ", limit_cnt)
                    #print("gen_prv: packet type: ", check_packet_type(app, param))
                    # check if parameter is in TC packet
                    if check_packet_type(app, param) == "TC":
                        limit = param["_limits"]["hash"]
                        limit_set_cnt = 0
                        for id in limit:
                            #print ("id: ", id)
                            if limit_set_cnt == 0:
                                limit_set_id = id
                                limit_set_cnt = 1
                            lvalue = ''
                            hvalue = ''
                            for y in limit[id]:
                                #print ("key: ", y, ', value: ', limit[id][y])
                                if (y == "lvalue"):
                                    lvalue = limit[id][y]
                                if (y == "hvalue"):
                                    hvalue = limit[id][y]
                            writeln(f, [
                                get_prf_name(limit_set_id),
                                str(lvalue),
                                str(hvalue)
                            ])
                #elif (): # TODO: also if enumerated values are given
                #    break;
    close_file(f)

def gen_paf(app, path):
    f = new_file(path, "paf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for type_ in standard["types"].values():
                num_enums = len(type_["enums"])
                if num_enums > 0 and type_["__mib_used_tc"]:
                    rawfmt = 'U'
                    for enum in type_["enums"]:
                        if enum["Value"] < 0:
                            rawfmt = 'I'
                            break

                    writeln(f, [
                        get_paf_name(type_),
                        outp(type_["name"], 24),
                        rawfmt,
                        outp(num_enums, 3)
                    ])
    close_file(f)

def gen_pas(app, path):
    f = new_file(path, "pas")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for type_ in standard["types"].values():
                if type_["__mib_used_tc"]:
                    for enum in type_["enums"]:
                        writeln(f, [
                            get_paf_name(type_),
                            outp(enum["Name"], 16),   # TODO: limit 16 characters
                            outp(enum["_dec"], 17)
                        ])
    close_file(f)

def gen_txf(app, path):
    f = new_file(path, "txf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for type_ in standard["types"].values():
                hasTextualCalibration = (len(type_["enums"]) > 0)
                if hasTextualCalibration and type_["__mib_used_tm"]:
                    writeln(f, [
                        get_txf_name(type_),
                        outp(type_["name"], 32),
                        'U',
                        outp(len(type_["enums"]), 3)
                    ])
    close_file(f)

def gen_txp(app, path):
    f = new_file(path, "txp")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for type_ in standard["types"].values():
                hasTextualCalibration = (len(type_["enums"]) > 0)
                if hasTextualCalibration and type_["__mib_used_tm"]:
                    txf_numbr = get_txf_name(type_)
                    for enum in type_["enums"]:
                        value = outp(enum["_dec"], 14)
                        writeln(f, [
                            txf_numbr,
                            value,
                            value,
                            outp(enum["Name"], 14)  # TODO: limit 14 characters
                        ])
    close_file(f)    

def gen_tcp(app, path):
    f = new_file(path, "tcp")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            writeln(f, [
                get_tcp_name(standard),
                outp("TC header of " + standard["name"], 24)
            ])
    close_file(f)

def gen_pcpc(app, path):
    f = new_file(path, "pcpc")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            offset = 0
            for param_i in standard["headers"]["TC"]:
                if param_i["role"] in [1, 2, 4, 5] or param_i["_value"] == None:
                    if offset >= 48:
                        prefix = "DF"
                    else:
                        prefix = "P"
                    pcpc_name = prefix + get_pcpc_name(standard, param_i)
                    writeln(f, [
                        pcpc_name,
                        outp(param_i["param"]["name"], 24),
                        'U'
                    ])
                pcdf_len = param_i["param"]["_size"]
                offset = offset + pcdf_len
    close_file(f)    

def gen_pcdf(app, path):
    f = new_file(path, "pcdf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            offset = 0
            for param_i in standard["headers"]["TC"]:
                if offset >= 48:
                    prefix = "DF"
                else:
                    prefix = "P"
                pcpc_name = prefix + get_pcpc_name(standard, param_i)
                pcdf_type, pcdf_pname, pcdf_value = \
                    ('T', pcpc_name, '0') if param_i["role"] == 1 else \
                    ('S', pcpc_name, '0') if param_i["role"] == 2 else \
                    ('A', pcpc_name, '0') if param_i["role"] == 4 else \
                    ('K', pcpc_name, '0') if param_i["role"] == 5 else \
                    ('P', pcpc_name, '0') if param_i["_value"] == None else \
                    ('F', '', param_i["_value"])
                pcdf_len = param_i["param"]["_size"]
                pcdf_bit = offset
                writeln(f, [
                    outp(standard["name"], 8, True),
                    outp(param_i["param"]["name"], 24),
                    pcdf_type,
                    outp(pcdf_len, 4),
                    outp(pcdf_bit, 4),
                    pcdf_pname,
                    pcdf_value,
                    'D'
                ])
                offset = offset + pcdf_len
    
    close_file(f)    

def gen_ocf(app, path):
    f = new_file(path, "ocf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for param in standard["params"]["list"]:
                limit_cnt = len(param["_limits"]["hash"])
                if limit_cnt != 0:
                    # check if parameter is in TM packet
                    if check_packet_type(app, param) == "TM":
                        limit = param["_limits"]["hash"]
                        limit_set_cnt = 0
                        for id in limit:
                            if limit_set_cnt == 0:
                                limit_set_cnt = 1
                            setting = {}
                            for y in limit[id]:
                                if y == "setting" and limit[id][y] != '':
                                    setting = simplejson.loads(str(limit[id][y]))
                        if 'ocf' in setting:
                            writeln(f, [
                                get_pcf_name(param),  # name of the parameter (= PCF_NAME)
                                '1' if 'nbchck' not in setting["ocf"] else str(setting["ocf"]["nbchck"]),  # number of consecutive valid parameter samples violating the check
                                '1' if 'nbool' not in setting["ocf"] else str(setting["ocf"]["nbool"]),  # number of checks associated to this parameter in the OCP table
                                '' if 'inter' not in setting["ocf"] else str(setting["ocf"]["inter"]),   # flag identifying the interpretation of the limit values
                                '' if 'codin' not in setting["ocf"] else str(setting["ocf"]["codin"])    # flag identifying the interpretation of the limit values
                            ])
    close_file(f)

def gen_ocp(app, path):
    f = new_file(path, "ocp")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for param in standard["params"]["list"]:
                limit_cnt = len(param["_limits"]["hash"])
                if limit_cnt != 0:
                    #print("--> limit_cnt: ", limit_cnt)
                    #print("gen_ocp: packet type: ", check_packet_type(app, param))
                    # check if parameter is in TM packet
                    if check_packet_type(app, param) == "TM":
                        limit = param["_limits"]["hash"]
                        limit_set_cnt = 0
                        for id in limit:
                            if limit_set_cnt == 0:
                                limit_set_cnt = 1
                            lvalue = ''
                            hvalue = ''
                            setting = {}
                            for y in limit[id]:
                                if y == "lvalue":
                                    lvalue = limit[id][y]
                                if y == "hvalue":
                                    hvalue = limit[id][y]
                                if y == "setting" and limit[id][y] != '':
                                    setting = simplejson.loads(str(limit[id][y]))
                            if 'ocp' in setting:
                                writeln(f, [
                                    get_pcf_name(param),  # name of the parameter (= OCF_NAME)
                                    '1' if 'pos' not in setting["ocp"] else str(setting["ocp"]["pos"]),   # used to define the order in which the checks are to be applied
                                    'S' if 'type' not in setting["ocp"] else str(setting["ocp"]["type"]), # flag identifying the type of monitoring check
                                    str(lvalue),  # value to be expressed in a format compatible with the OCF_CODIN
                                    str(hvalue),  # high limit value to be expressed in a format compatible with OCF_CODIN
                                    '' if 'rlchk' not in setting["ocp"] else str(setting["ocp"]["rlchk"]),   # name of the parameter to be used to determine the applicability of this monitoring check
                                    '' if 'valpar' not in setting["ocp"] else str(setting["ocp"]["valpar"])  # raw value of the applicability parameter (OCP_RLCHK)
                                ])
    close_file(f)

def gen_pid_line(f, tm, derived=None):
    length = None
    if derived != None:
        pid_pi1_val = derived["_disc"]
        pid_spid = derived["__mib_spid"]
        pid_descr = derived["name"]
        if tm["_header_length"] is not None:
            length = tm["_header_length"] + derived["_length"]
            length2 = tm["_length"]
            #print("derived: length = ", length2, " name = ", pid_descr)
        else:
            length = None
    else:
        pid_pi1_val = '0'
        pid_spid = tm["__mib_spid"]
        pid_descr = tm["name"]
        length = tm["_header_length"]
        length2 = tm["_length"]
        #print("base: length = ", length2, " name = ", pid_descr)

    #if length2 is None:
    #    print("None: pid_spid = ", pid_spid)
    pid_tpsd = pid_spid if length2 is None else '-1'

    # !!! namin convention !!!
    pid_descr = "SASW "+pid_descr

    writeln(f, [
        outp(tm["type"], 3),                # PID_TYPE
        outp(tm["subtype"], 3),             # PID_STYPE
        outp(tm["process"]["address"], 5),  # PID_APID
        outp(pid_pi1_val, 10),              # PID_PI1_VAL
        '0',                                # PID_PI2_VAL
        pid_spid,                           # PID_SPID
        pid_descr,                          # PID_DESCR
        '0',                                # PID_UNIT
        pid_tpsd,                           # PID_TPSD
        outp(tm["standard"]["headers"]["TM_length"]/8, 2),  # PID_DFHSIZE
        'Y',     # PID_TIME
        '',      # PID_INTER
        'Y',     # PID_VALID
        '1',     # PID_CHECK
        'N',     # PID_EVENT
        ''       # PID_EVID
    ])         

def gen_pid(app, path):
    f = new_file(path, "pid")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for tm in standard["packets"]["TM"]["list"]:
                if len(tm["derivations"]["list"]) > 0:
                    for derived in tm["derivations"]["list"]:
                        gen_pid_line(f, tm, derived)
                else:
                    gen_pid_line(f, tm)
    close_file(f)

def gen_vpd_param(f, param_i, attr):
    param = param_i["param"]
    writeln(f, [
        outp(attr["spid"], 10), # VPD_TPSD
        outp(attr["pos"], 4), # VPD_POS
        get_pcf_name(param), # VPD_NAME
        outp(param_i["group"], 3),
        outp(param_i["repetition"], 3),
        'N',
        'Y' if param["role"] == 6 else 'N',
        outp(param["name"], 16, True),
        '0' if param_i["group"] != None and param_i["group"] > 0 else '1',
        'L',
        'N',
        '0',
        'H',
        '0'
    ])
    attr["pos"] = attr["pos"]+1

def gen_vpd_params(f, spid, base, derived):
    attr = {}
    attr["spid"] = spid
    attr["pos"] = 0
    for param_i in base:
        offset = gen_vpd_param(f, param_i, attr)
    for param_i in derived:
        offset = gen_vpd_param(f, param_i, attr)

def gen_vpd(app, path):
    f = new_file(path, "vpd")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for tm in standard["packets"]["TM"]["list"]:
                if len(tm["derivations"]["list"]) > 0:
                    for derived in tm["derivations"]["list"]:
                        if tm["_length"] == None or derived["_length"] == None:
                            gen_vpd_params(f, derived["__mib_spid"], tm["body"], derived["body"])
                else:
                    if tm["_length"] == None:
                        gen_vpd_params(f, tm["__mib_spid"], tm["body"], [])
    close_file(f)

def gen_plf_param(f, param_i, spid, offset):
    param = param_i["param"]
    offby = offset / 8
    offbi = offset % 8
    nbocc = param_i["repetition"] if param_i["repetition"] != None and param_i["repetition"] > 0 else 1

    writeln(f, [
        get_pcf_name(param),
        spid,
        outp(offby, 5),
        outp(offbi, 1),
        outp(nbocc, 4),
        '0', # LGOCC
        '0',
        '1'
    ])
    return offset + param["_size"]    

def gen_plf_params(f, base, derived, spid, offset):
    for param_i in base:
        offset = gen_plf_param(f, param_i, spid, offset)
    for param_i in derived:
        offset = gen_plf_param(f, param_i, spid, offset)

def gen_plf(app, path):
    f = new_file(path, "plf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for tm in standard["packets"]["TM"]["list"]:
                offset = standard["headers"]["TM_length"]
                if len(tm["derivations"]["list"]) > 0:
                    for derived in tm["derivations"]["list"]:
                        if tm["_length"] != None and derived["_length"] != None:
                            gen_plf_params(f, tm["body"], derived["body"], derived["__mib_spid"], offset)
                else:
                    if tm["_length"] != None:
                        gen_plf_params(f, tm["body"], [], tm["__mib_spid"], offset)

    close_file(f)    

def getSpidPrefix(tm_type):
    switcher = {
        1: "ACK",  # Request Verification
        3: "HK_",  # Housekeeping
        5: "EVT",  # Event Reporting
        6: "MEM",  # Memory Management
        9: "TIM",  # Time Management
        13: "LDT",  # Large Data Transfer
        17: "TST",  # Comm. Test
        20: "PAR",  # Parameter Management
        191: "FDC",  # FDIR Check
        193: "AMM",  # ASW Mode Management
        194: "ALC",  # Algorithm Control
        197: "BRP",  # Boot Report
        198: "PRC",  # Procedure Control
        210: "DPM",  # DPU Management
        211: "PUP",  # Parameter Update
        212: "DOP",  # Data Operation
        213: "SMA"  # SW Maintenance
    }
    return "KSY_"+switcher.get(tm_type, "___")

def gen_tpcf_line(f, tm, derived=None):
    if derived is None:
        spid = tm["__mib_spid"]
        nbits = tm["_header_length"]
    else:
        spid = derived["__mib_spid"]
        nbits = tm["_header_length"] + derived["_length"]

    # !!! naming convention !!!
    # get prefix from lookup table
    spid_prefix = getSpidPrefix(tm["type"])
    spid_name = spid_prefix+spid
    #print ""
    #print "SPID: "+spid
    #print "TPCF_NAME: "+spid_name

    writeln(f, [
        spid,
        spid_name,  # TPCF_NAME: prefix added to SPID e.g. KSY_EVTspid, KSY_HK_spid, ...
        outp((tm["standard"]["headers"]["TM_length"] + nbits + 16)/8, 8) if nbits != None else '0'  # checksum length: 2 Bytes = 16 bits
    ])

def gen_tpcf(app, path):
    f = new_file(path, "tpcf")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for tm in standard["packets"]["TM"]["list"]:
                if len(tm["derivations"]["list"]) > 0:
                    for derived in tm["derivations"]["list"]:
                        gen_tpcf_line(f, tm, derived)
                else:
                    gen_tpcf_line(f, tm)
    close_file(f)    

def gen_pic(app, path):
    f = new_file(path, "pic")
    for relation in app["standards"]:
        if relation["relation"] == 1:
            standard = relation["standard"]
            for tm in standard["packets"]["TM"]["list"]:
                pi1_off = -1
                pi1_wid = 0
                for param_i in tm["body"]:
                    if param_i["role"] == 3:
                        pi1_off = tm["standard"]["headers"]["TM_length"]/8 + param_i["_offset"]/8  # including TM header length
                        pi1_wid = param_i["param"]["_size"]

                writeln(f, [
                    outp(tm["type"], 3),     # PIC_TYPE
                    outp(tm["subtype"], 3),  # PIC_STYPE
                    outp(pi1_off, 5),        # PIC_WI1_OFF
                    outp(pi1_wid, 3),        # PIC_PI1_WID
                    '-1',                    # PIC_PI2_OFF
                    '0',                     # PIC_PI2_WID
                    outp(tm["process"]["address"], 5)  # PIC_APID
                ])
    
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


def gen_mib(path, comp):
    global settings

    settings = comp["setting"]

    if settings is None:
        return

    app = comp["app"]
    prepare(app)
    # General
    gen_vdf(app, path) # Database Version Definition file
    # Monitoring
    gen_pcf(app, path) # parameter characteristics file
    empty_file(path, "cur") # calibration definitions conditional selection
    empty_file(path, "caf") # calibration curve file, defining the numerical calibration curves
    empty_file(path, "cap") # calibration curve definition file, defining all the raw/engineering value couples for each numerical calibration curves
    gen_txf(app, path) # text strings calibration curve file, defining the textual calibration curves
    gen_txp(app, path) # text strings calibration curve definition file, defining all the raw/string value couples for each textual calibration curve
    empty_file(path, "mcf") # polynomial calibration curve definitions, defining the coefficients of the polynomial function used for calibration
    empty_file(path, "lgf") # logarithmic calibration curve definitions, defining the coefficients of the logarithmic function used for calibration.
    #empty_file(path, "ocf") # OutOfLimits checks file, defining the characteristics of all the checks applied to a specified monitoring parameter.
    #empty_file(path, "ocp") # OutOfLimits definition file, defining the allowed (ranges of) values for monitoring parameters.
    gen_ocf(app, path)  # OutOfLimits checks file, defining the characteristics of all the checks applied to a specified monitoring parameter.
    gen_ocp(app, path)  # OutOfLimits definition file, defining the allowed (ranges of) values for monitoring parameters.
    gen_pid(app, path) # packet identification file, containing the definition of TM packets and their correspondence with the packet identification fields (e.g. APID/type/subtype).
    gen_pic(app, path) # packet identification criteria file, containing the definition and position of the additional identification fields for each packet type/subtype combination.
    gen_tpcf(app, path) # telemetry packets characteristics file, defining the attributes of the SCOS-2000 Telemetry Packets.
    gen_plf(app, path) # parameter location file, defining the location of the parameters in the fixed TM packets
    gen_vpd(app, path) # variable packet definition file, detailing the contents of variable TM packets.
    empty_file(path, "grp") # To be delivered empty: parameters and packets groups characteristics file, containing the definition of monitoringparameters and packets groups characteristics file, containing the definition of monitoring parameters and telemetry packets groups.
    empty_file(path, "grpa") # To be delivered empty: parameters groups file, defining the groups of parameters.
    empty_file(path, "grpk") # To be delivered empty: packets groups file, defining the groups of packets.
    # Displays
    empty_file(path, "dpf") # alphanumeric display proforma file, containing the list of TM alphanumeric displays (AND)
    empty_file(path, "dpc") # alphanumeric display proforma definition file, containing the list of parameters to be displayed in each AND.
    empty_file(path, "gpf") # graphic display proforma file, containing the list of TM graphic displays
    empty_file(path, "gpc") # graphic display proforma definition file, containing the list of parameters to be displayed in each GRD.        
    empty_file(path, "spf") # To be delivered empty: scrolling display proforma file, containing the list of TM scrolling displays (SCD).
    empty_file(path, "spc") # To be delivered empty: scrolling display proforma definition file, containing the list of parameters to be displayed in each SCD.
    empty_file(path, "ppf") # To be delivered empty: This table will contain the list and the format specification of telemetry printout proforma.
    empty_file(path, "ppc") # To be delivered empty: This table contains the list of parameters to be printed in each printout proforma.
    # Commanding
    gen_tcp(app, path) # packet header file which defines TC packet headers
    gen_pcpc(app, path) # packet header parameter characteristics file which defines the TC packet header parameters.
    gen_pcdf(app, path) # packet headers definition file which defines the structure of each packet header
    gen_ccf(app, path) # command characteristics file which defines the commands.
    empty_file(path, "dst") # command routing table which defines the destination of the commands.
    gen_cpc(app, path) # command parameter characteristics which defines the editable command parameters.
    gen_cdf(app, path) # command details file which defines the structure of the command application data field.
    empty_file(path, "ptv") # command pre-transmission validation file which defines the monitoring parameter and value pairs to satisfy validation
    empty_file(path, "csf") # To be delivered empty: command sequence file which defines the command sequences.
    empty_file(path, "css") # To be delivered empty: command sequence set which defines the elements (commands or sequences) used in a command sequence.
    empty_file(path, "sdf") # To be delivered empty: sequence details file which defines the values for the editable parameters of all elements contained in a command sequence.
    empty_file(path, "csp") # To be delivered empty: command sequence parameter file which defines the command sequence (formal) parameter.
    gen_cvs(app, path) # verification stages file which defines the verification stage details.
    empty_file(path, "cve") # verification expression file which defines the monitoring parameter and value pairs to satisfy verification.
    gen_cvp(app, path) # command/sequence verification profiles file which defines the mapping of verification stages with commands/sequences.
    empty_file(path, "pst") # To be delivered empty: command/sequence parameter set file which defines the parameter sets characteristics.
    empty_file(path, "psv") # To be delivered empty: command/sequence parameter value set file which defines the parameter value sets characteristics.
    empty_file(path, "cps") # To be delivered empty: command/sequence parameter set file which defines the parameters contained in a parameter set.
    empty_file(path, "pvs") # To be delivered empty: command/sequence parameter value set file which defines the parameter forming a parameter value set.
    empty_file(path, "psm") # To be delivered empty: parameter sets mapping file which defines the mapping between parameter sets and tasks (i.e. commands or sequences).
    empty_file(path, "cca") # parameter calibration curve file which defines the numerical (de-)calibration (for command or sequence parameters).
    empty_file(path, "ccs") # calibration curve set file which defines the numerical (de-)calibration values.
    gen_paf(app, path)  # parameter alias file which defines the text (de-)calibration (for command or sequence parameters).
    gen_pas(app, path)  # parameter alias set which defines the text (de-)calibration values.
    gen_prf(app, path)  # parameter alias file which defines the text (de-)calibration (for command or sequence parameters).
    gen_prv(app, path)  # parameter range value file which defines the parameter allowed value ranges.

if __name__ == '__main__':

    if (len(sys.argv) == 3):

        project_id = sys.argv[1]
        app_id = sys.argv[2]
        try:
            il = get_data.get_data(project_id)            
            app = il["apps"]["hash"][int(app_id)]
            gen_mib("./mib", app["components"]["hash"]["mib"])
            print("Done")
        except Exception as e:
            print("Something went wrong...")
            print(traceback.format_exc())

    else:
        print("Usage: python gen_mib.py {project_id} {application_id}")    
