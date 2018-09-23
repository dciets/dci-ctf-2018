const { ObjectID } = require('mongodb');

module.exports = async function(req, res) {
    var theme = await req.db.collection('profiles').findOne({ _id: ObjectID(req.params.id) });

    if(theme) {
        res.ok({ css: theme.css });
    } else {
        res.ok({ css: "" });
    }
};
