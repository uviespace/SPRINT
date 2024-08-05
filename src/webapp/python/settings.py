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
        settings["db_host"] = "127.0.0.1"
        settings["db_user"] = "user"
        settings["db_passwd"] = "pass"
        settings["db_name"] = "testdb"
        
    return settings
