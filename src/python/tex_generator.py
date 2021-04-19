#!/usr/bin/python

import sys
import latexcodec
from generator import *
from string import maketrans
from fig_ref_conv import fig_ref_conv 

class TexGenerator(Generator):

    def __init__(self, path, setting):
        Generator.__init__(self, path)
        self.col_num = 0        
        self.setting = setting
        self.tblTranslate = maketrans(b"1234567890", b"abcdefghij")

    def name(self):
        return "LaTeX"

    def enc(self, s):
        replacements = [
            ['\n', '\\newline '],
            ['^', "\\textasciicircum"],
            ['~', "\\textasciitilde"],
            ['_', "_\\-"],
            ["\#defaultImplementation","Default implementation"]
        ]

        s = fig_ref_conv(s, "latex")
        s = s.encode("latex")
        s = s.decode('utf-8')

        for old, new in replacements:
            s = s.replace(old, new)
        
        return s

    def texName(self, s):
        # Split string s into its component words and capitalize each word and re-from the string
        lst = [word[0].upper() + word[1:] for word in s.split()]
        s = " ".join(lst)

        return s.replace(" ", "").replace("_", "").replace("-", "").encode('utf8').translate(self.tblTranslate).decode('utf8')
        
    def begin(self, base_name, caption, caption_tbl, column_names, openFile=True):
        if self.popCol != None:
            column_names.pop(self.popCol)
        if openFile:
            self.open(u"{0}{1}.tex".format(base_name, caption))
        self.col_num = len(column_names)
        caption = self.texName(caption)
        self.writeln(
            u"\\def \\print{0}#1 {{\n".format(caption) + 
            "\\begin{pnptable}{#1}" + 
            "{" + self.enc(caption_tbl) + "}" + 
            "{tab:" + caption + "}" +
            "{" + " & ".join(column_names) + "}")

    def end(self, closeFile=True):
        self.writeln("\end{pnptable}}\n")
        if closeFile:
            self.close()

    def write(self, data):
        if self.popCol != None:
            data.pop(self.popCol)
        data = self.conv(data)
        if len(data) == self.col_num:
            self.writeln(" & ".join([self.enc(d) for d in data]) + " \\\\\\hline")
        else:
            # Only single columns supported
            self.writeln(
                "\multicolumn{" + str(self.col_num) + "}{|l|}{\\textbf{" + self.enc(data[0]) + "}} \\\\\\hline")
