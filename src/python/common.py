#!/usr/bin/python

import sys
import string
import random
import zip
import os

from datetime import datetime
from settings import *

def id_generator(size=16, chars=string.ascii_uppercase + string.digits):
    return ''.join(random.choice(chars) for _ in range(size))

def new_path():    
    settings = get_settings()
    path = "{0}/{1}".format(settings["tmp_path"], id_generator())
    return path

def gen_path(path):    
    os.makedirs(path)
    os.chmod(path, 0o777)

def create_path():
    path = new_path()
    gen_path(path)
    return path

def gen_zip(path):
    zip_name = os.path.join(path, "{0}_CrData.zip".format(datetime.now().strftime("%Y%m%d-%H%M_")))
    zip.zip_dir(path, zip_name)
    return zip_name    