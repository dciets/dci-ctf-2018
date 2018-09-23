function Request(params, db, session) {
    this.params = params;
    this.db = db;
    this.session = session;
    this.user = null;
}

Request.prototype.isLoggedIn = async function() {
    this.user = await this.db.collection('users').findOne({ _id: this.session.user });

    return !!this.user;
};

Request.prototype.ensureLoggedIn = async function() {
    var loggedIn = await this.isLoggedIn();
    if(!loggedIn) {
        throw 'User is not logged in';
    }
};

module.exports = Request;
