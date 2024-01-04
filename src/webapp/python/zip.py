import sys
import os
import zipfile

def zip_dir(path, name):
    f = zipfile.ZipFile(name, 'w', zipfile.ZIP_DEFLATED)    

    for root, dirs, files in os.walk(path):
        for file in files:
            s = os.path.join(root, file)
            if (name != s):
                f.write(s, s[len(path)+1:])

    f.close()

def unzip_file(path, name):
    with zipfile.ZipFile(name, "r") as z:
        z.extractall(path)