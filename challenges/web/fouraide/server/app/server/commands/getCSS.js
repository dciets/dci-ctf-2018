const { recursive } = require('merge');

var defaults = {
    body: { "background-color": '#fff', color: "#000"},
    a: { color: "#00f" }
};

function escape(color) {
    return ("" + color).replace(/[^0-9a-z()\-,#]/gi, '');
}

function cssTemplate(params) {
    var output = "";

    for(var selector in params) {
        output += escape(selector) + " { ";

        for(var prop in params[selector]) {
            output += escape(prop) + ": " + escape(params[selector][prop]) + ";\n";
        }

        output += "}\n";
    }

    return output;
}


module.exports = async function(req, res) {
    const theme = recursive(defaults, req.params);
    const css = cssTemplate(theme);

    req.session.css = css;

    res.ok({ css });
};
