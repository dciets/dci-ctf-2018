module.exports = function(res, req) {
    res.session.user = null;

    req.ok();
};
