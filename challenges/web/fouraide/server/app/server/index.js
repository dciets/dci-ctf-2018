const MongoClient = require('mongodb').MongoClient;
const readline = require('readline');
const commands = require('./commands');
const Request = require('./request');
const Response = require('./response');

var db, session = {};
const url = 'mongodb://mongodb:27017/';
const rl = readline.createInterface({
    input: process.stdin,
    output: process.stdout,
    terminal: false
});

async function main() {
    console.log(JSON.stringify({ready: true}));

    rl.on('line', function(line) {
        try {
            var cmd = JSON.parse(line);

            if(commands.hasOwnProperty(cmd.method)) {
                var req = new Request(cmd.args, db, session);
                var res = new Response();

                commands[cmd.method](req, res);
            } else {
                throw 'command not found';
            }
        } catch(e) {
            Response.prototype.error();
        }
    });
}

MongoClient.connect(url, { useNewUrlParser: true }, function(err, client) {
    db = client.db("fouraide");

    main();
});
