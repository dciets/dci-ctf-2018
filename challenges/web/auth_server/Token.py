import base64
import datetime
import json

import rsa

def generate_token(host, user, key):
    token_time = datetime.datetime.now()
    token_expire = token_time + datetime.timedelta(minutes = 5)

    token = {
        "user": user,
        "time": str(token_time),
        "expire": str(token_expire),
        "host": host
    }

    token = json.dumps(token).encode()

    signature = rsa.sign(token, key, "SHA-256")

    token = base64.b64encode(token)
    signature = base64.b64encode(signature)

    return b"%s|%s" % (token, signature)