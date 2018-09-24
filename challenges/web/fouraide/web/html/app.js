var ws = new WebSocket(`wss://${location.host}/ws`);

function queue() {
    this.elements = [];
}

queue.prototype.push = function(value) {
    this.elements.push(value);
    this.notify();
};

queue.prototype.notify = function() {
    if(this.callback) {
        this.callback(this.elements.shift());
    }
};

queue.prototype.waitOne = function() {
    return new Promise((resolve) => {
        if(this.elements.length) {
            resolve(this.elements.shift());
        } else {
            this.callback = resolve;
        }
    });
};

var msgQueue = new queue();

ws.onmessage = function(msg) {
    msg = JSON.parse(msg.data);

    console.log(["recv", msg]);

    msgQueue.push(msg);
};

function request(method, args) {
    ws.send(JSON.stringify({ method, args }));

    return msgQueue.waitOne();
}

function App() {
    this.ready = ko.observable(false);
    this.page = ko.observable("home");
    this.loggedIn = ko.observable(false);

    this.username = ko.observable("");
    this.password = ko.observable("");
    this.errorMessage = ko.observable("");
    this.avatar = ko.observable("");
    this.css = ko.observable("");
    this.userCount = ko.observable(3783);

    this.cssProperties = ko.observableArray([
        { selector: ko.observable("body"), property: ko.observable("color"), value: ko.observable("black") }
    ]);

    window.addEventListener('hashchange', () => {
        const hash = location.hash.slice(1);

        if(!hash) {
            hash = 'home';
        }

        this.username("");
        this.password("");
        this.errorMessage("");

        this.page(hash);
    }, false);

    this.handleAddProperty = () => {
        this.cssProperties.push({ selector: ko.observable(""), property: ko.observable(""), value: ko.observable("") });
    };

    this.handleRemoveProperty = (value) => {
        return () => {
            this.cssProperties.remove(value);
        };
    };

    this.handleSave = async () => {
        const properties = this.cssProperties();
        const json = {};

        for(var i = 0; i < properties.length; i++) {
            json[properties[i].selector()] = json[properties[i].selector()] || {};
            json[properties[i].selector()][properties[i].property()] = properties[i].value();
        }

        await request("getCSS", json);
        await request("setProfile", {});

        this.updateCss();
    };

    this.handleLogin = async () => {
        this.errorMessage("");

        const { error, user } = await request("login", { username: this.username(), password: this.password() });

        if(error) {
            this.errorMessage("Connexion refusée");

            localStorage.token = "";
            localStorage.userId = "";
        } else {
            localStorage.token = btoa(JSON.stringify([this.username(), this.password()]));
            localStorage.userId = user;

            this.loggedIn(true);

            location.hash = "#home";

            await this.updateCss();
        }
    };

    this.handleSignup = async() => {
        this.errorMessage("");

        const { error } = await request("signup", { username: this.username(), password: this.password() });

        if(error) {
            this.errorMessage("Échec de l'inscription");
        } else {
            this.errorMessage("Inscription succès.");
        }
    };

    this.handleLogout = () => {
        localStorage.clear();

        location.href = "/";
    };

    this.updateCss = async () => {
        const { css } = await request("getProfile", { id: localStorage.userId });

        this.css(css);
    };

    this.page.subscribe(async (value) => {
        if(value == 'profile') {
            const id = localStorage.userId || "";
            var sum = 0;

            for(var i = 0; i < id.length; i++) {
                sum += id.charCodeAt(i);
            }

            sum = sum % 6;

            const { data } = await request("getAvatar", { id: sum });

            this.avatar("data:image/jpg;base64," + data);

            await this.updateCss();
        }
    });

    msgQueue.waitOne().then(async (msg) => {
        this.ready(msg.ready);

        if(localStorage.token) {
            const values = JSON.parse(atob(localStorage.token));

            this.username(values[0]);
            this.password(values[1]);

            await this.handleLogin();
        }

        const { count } = await request("userCount", {});
        this.userCount(count);
    });
}

var app = new App();

ko.applyBindings(app);
