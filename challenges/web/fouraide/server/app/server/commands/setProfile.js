const { ObjectID } = require('mongodb');

module.exports = async function(req, res) {
    req.ensureLoggedIn();

    req.db.collection('profiles').update(
        { _id: ObjectID(req.session.user) },
        { $set: { css: req.session.css } },
        { upsert: true });

    res.ok();
};
