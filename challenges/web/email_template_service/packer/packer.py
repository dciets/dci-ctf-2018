#!/usr/bin/env python3
import glob
import sqlite3

connection = sqlite3.connect("../templates.db")
connection.execute("DROP TABLE IF EXISTS templates")
connection.execute("DROP TABLE IF EXISTS secret")
connection.execute("CREATE TABLE secret (flag TEXT)")
connection.execute("CREATE TABLE templates (page TEXT, html TEXT, type TEXT)")

connection.execute("INSERT INTO secret VALUES ('I think I lost the flag somewhere on the file system...')")

base_directory = "templates/"
extension = ".html"
templates = glob.glob(base_directory + "*" + extension) + glob.glob(base_directory + "**/*" + extension)

def template_name(base, path):
    path = path[len(base) : path.rfind(".html")]
    return path

def glob_type(connection, base, type_name):
    directory = "%s/%s/" % (base, type_name)
    templates = glob.glob("%s*%s" % (directory, extension))

    for template in templates:
        with open(template, "r") as f:
            html = f.read()

        page = template_name(directory, template)
        print((page, html, type_name))
        connection.execute("INSERT INTO templates VALUES (?, ?, ?)", (page, html, type_name))

glob_type(connection, "templates", "content")
glob_type(connection, "templates", "form")
glob_type(connection, "templates", "email")
connection.commit()