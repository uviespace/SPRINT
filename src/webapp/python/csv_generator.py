#!/usr/bin/python

import sys
from generator import *
from fig_ref_conv import fig_ref_conv 

class CsvGenerator(Generator):
    f = None
    col_num = 0
    setting = {}

    def __init__(self, path, setting):
        Generator.__init__(self, path)
        self.setting = setting        

    def name(self):
        return "CSV"    

    def replaceForLatex(self, s):
        replacements = [
            ['\n', '\\newline '],
            ['^', "\\textasciicircum"],
            ['~', "\\textasciitilde"],
            ['_', "\\_"],
            ['&', "\\&"],
            ["\#defaultImplementation","Default implementation"]
        ]
        #if isinstance(s, basestring):
        if isinstance(s, ("".__class__, u"".__class__)):  # for Python2 and Python3
            for old, new in replacements:
                s = s.replace(old, new)
        return s

    def enc(self, s):
        s = fig_ref_conv(s, "csv")
        return s

    def begin(self, base_name, caption, caption_tbl, column_names):
        if self.popCol != None:
            column_names.pop(self.popCol)        
        self.open(u"{0}{1}.csv".format(base_name, caption))
        self.col_num = len(column_names)
        self.write(column_names)

    def end(self):
        self.close()

    def write(self, data):
        if self.popCol != None:
            data.pop(self.popCol)
        
        if len(data) == self.col_num: 
            convData = [self.replaceForLatex(d) for d in data]           
            self.writeln(u"{0}{1}{2}".format(
                self.setting["Delimiter"],
                "{0}{1}{2}".format(
                    self.setting["Delimiter"],
                    self.setting["Separator"],
                    self.setting["Delimiter"]
                ).join(self.conv(convData)),
                self.setting["Delimiter"]))
               
                
                
                
