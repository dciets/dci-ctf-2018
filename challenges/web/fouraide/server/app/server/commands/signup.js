const crypto = require('crypto');

function hashPassword(salt, password) {
    const h = crypto.createHash('sha1');

    h.update(salt.toString());
    h.update(process.env.SECRET.toString());
    h.update(password.toString());

    return h.digest('hex');
}

module.exports = async function(req, res) {
    const { username, password } = req.params;

    var user = await req.db.collection('users').findOne({username: username.toString()});
    var success = false;

    if(!user) {
        var salt = crypto.randomBytes(8).toString('hex');

        user = {
            username: username.toString(),
            salt: salt,
            passwordHash: hashPassword(salt, password)
        };

        await req.db.collection('users').insert(user);

        req.session.user = user._id;

        success = true;
    }

    if(success) {
        res.ok({user: req.session.user});
    } else {
        res.error();
    }
};
