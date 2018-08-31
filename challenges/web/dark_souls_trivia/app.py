#!/usr/bin/env python3
from flask import Flask
app = Flask(__name__)

@app.route("/", methods=["GET"])
def index():
    import pickle
    from flask import render_template

    user = get_user_data()
    return render_template("index.html", prizes = user.prizes)

@app.route("/submit", methods=["POST"])
def submit():
    import base64
    from datetime import datetime
    import pickle
    from flask import render_template, request, make_response
    from ItemList import ItemList

    user = get_user_data()
    item = request.form["item"]

    if user.has_submitted(item):
        return render_template("duplicate.html")

    result = ItemList.check_item(item)

    if result is None:
        template = render_template("bad.html")
    else:
        prize = result[0]
        count = result[1]
        user.add_prize(item, prize, count)
        template = render_template("good.html", prize = prize, count = count)

    response = make_response(template)
    response.set_cookie("data", value=base64.b64encode(pickle.dumps(user)))
    return response

def get_user_data():
    import base64
    from flask import request
    import pickle
    from UserData import UserData

    if "data" not in request.cookies:
        return UserData()
    else:
        user = pickle.loads(base64.b64decode(request.cookies["data"]))
        return user