#!/usr/bin/python

import sys

class Generator:
    def __init__(self, path):
        self.f = None
        self.fileNames = []
        self.path = path
        self.popCol = None

    def open(self, name):
        s = self.validName(name)
        self.f = open(u"{0}/{1}".format(self.path, s), "w")        
        self.fileNames.append(s)        

    def close(self):
        self.f.close()

    def writeln(self, s):
        #self.f.write(str(s.encode('utf8')) + "\n")
        self.f.write(str(s) + "\n")

    def conv(self, data):
        #return [u"" if d is None else unicode(d) for d in data]
        return [u"" if d is None else str(d) for d in data]  # unicode of Python2 is equivalent to str in Python3

    def validName(self, name):
        return name.replace(" ", "").replace("/", "").replace("_", "")

    def setPopCol(self, popCol):
        self.popCol = popCol