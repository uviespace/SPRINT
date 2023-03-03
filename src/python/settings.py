#!C:\Python310\python.exe
##!C:\Users\chris\Anaconda2\python.exe
##!/usr/bin/python

import sys
import os

def get_settings():
    cwd = os.getcwd()

    settings = {}
    if cwd.startswith("/homepages/"):
        # We are on the server
        settings["tmp_path"] = ""
        settings["db_host"] = ""
        settings["db_user"] = ""
        settings["db_passwd"] = ""
        settings["db_name"] = ""
    else:
        # We are on the developer machine
        settings["tmp_path"] = "/tmp/local"
        settings["db_host"] = "localhost"
        settings["db_user"] = "dbuser"
        settings["db_passwd"] = "dbpwd"
        settings["db_name"] = "dbnam"
    return settings