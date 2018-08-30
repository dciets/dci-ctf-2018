#!/usr/bin/env python
# -*- coding: utf-8 -*-

from flask import Flask, request
from secret import Secret
from cgi import escape

app = Flask(__name__)
app.debug = False

def int2hex(n):
    s=hex(abs(int(n)))[2:]
    return '0'+s if len(s)%2 else s

@app.route('/',methods=['GET'])
def print_flag():
    if not request.args.get('n'):
        return 'no "n" specified<br/><br/>Example: http://host:port/?n=123<br/><br/>Source code: /code'
    n=request.args.get('n')
    secret = Secret()
    try:
        n=int(n)
    except ValueError:
        return 'n should be an integer'
    try:
        s=int2hex(n).decode('hex')
        secret.flag = 'sorry ¯\_(ツ)_/¯'
    except:
        n%=2**3*10**8
        s=int2hex(n).decode('hex')
    if s!='yes':
        secret.flag = 'sorry ¯\_(ツ)_/¯'
    return secret.flag

@app.route('/code',methods=['GET'])
def show_source_code():
    return '<pre>' + escape(open('./app.py').read()) + '</pre>'

if __name__=='__main__':
    app.run(host='0.0.0.0', port=5001)
