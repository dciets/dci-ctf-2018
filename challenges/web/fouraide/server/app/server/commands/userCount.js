module.exports = async function(req, res) {
    const count = await req.db.collection('users').count();

    res.ok({ count });
};
