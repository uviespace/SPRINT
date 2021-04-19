#!C:\Users\chris\Anaconda2\python.exe
##!/usr/bin/python

import sys
import os

def get_settings():
    cwd = os.getcwd()

    settings = {}
    if cwd.startswith("/homepages/"):
        # We are on the server
        settings["tmp_path"] = "/tmp"
        settings["db_host"] = "db_host"
        settings["db_user"] = "db_user"
        settings["db_passwd"] = "db_passwd"
        settings["db_name"] = "db_name"
    else:
        # We are on the developer machine
        settings["tmp_path"] = "/tmp"
        settings["db_host"] = "db_host"
        settings["db_user"] = "db_user"
        settings["db_passwd"] = "db_passwd"
        settings["db_name"] = "db_name"
    return settings