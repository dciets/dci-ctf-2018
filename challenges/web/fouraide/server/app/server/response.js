function Response(args) {
    this.args = args;
}

Response.prototype.ok = function(msg) {
    msg = msg || {};
    msg.error = false;
    console.log(JSON.stringify(msg));
};

Response.prototype.error = function(msg) {
    msg = msg || {};
    msg.error = true;
    console.log(JSON.stringify(msg));
};

module.exports = Response;
