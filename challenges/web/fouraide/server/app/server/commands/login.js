const crypto = require('crypto');
const { execFile } = require('child_process');

function hashPassword(salt, password) {
    const h = crypto.createHash('sha1');

    h.update(salt);
    h.update(process.env.SECRET);
    h.update(password);

    return h.digest('hex');
}

module.exports = async function(req, res) {
    const { username, password } = req.params;
    const user = await req.db.collection('users').findOne({username: username.toString()});
    var success = false;
    var flag = "";

    if(user !== null) {
        if(hashPassword(user.salt, password) === user.passwordHash) {
            req.session.user = user._id;
            success = true;
        }
    }

    if(success) {
        if(user.username === "roger.letourneau") {
            const child = execFile("./get_flag", function(_, data) {
                res.ok({
                    loggedIn: success,
                    user: req.session.user,
                    flag: data
                });
            });
        } else {
            res.ok({
                loggedIn: success,
                user: req.session.user
            });
        }
    } else {
        res.error();
    }
};
