const lighthouse = require('lighthouse');
const chromeLauncher = require('lighthouse/chrome-launcher');
const CDP = require('chrome-remote-interface');

const USERNAME = "Zark Muckerberg"
const PASSWORD = "A23Jsdjjj21399kasdmwh123"

const flags = {
    chromeFlags: ["--headless", "--no-sandbox", "--disable-web-security"]
};

StageEnum = {
    FLAGBOOK_LOGIN     : 0,
    FLAGBOOK_LOGGED_IN : 1,
    FLAGBOOK_GRABDATA  : 2
};

stage = StageEnum.FLAGBOOK_LOGIN;
last_update = 0;

function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

chromeLauncher.launch(flags).then(chrome => {
    flags.port = chrome.port;
    CDP({port: chrome.port}).then(client => {
        // extract domains 
        const {Network, Page, Runtime} = client;

        // setup handlers 
        Network.requestWillBeSent((params) => {
            //console.log(params.request.url);
        });

        Page.frameStoppedLoading(async () => {
            //console.log("finished.")
            //console.log(stage)
            if (stage === StageEnum.FLAGBOOK_LOGIN)
            {
                //console.log("LOGIN")
                stage = StageEnum.FLAGBOOK_GRABDATA;
                const js = "document.getElementsByName('username')[0].value = '" + USERNAME  + "';"
                         + "document.getElementsByName('password')[0].value = '" + PASSWORD  + "';"
                         + "document.getElementsByName('login')[0].click();";
                Runtime.evaluate({expression: js});
            }
            else if (stage === StageEnum.FLAGBOOK_LOGGEDIN)
            {
                //console.log("LOGGEDIN")
                stage = StageEnum.FLAGBOOK_GRABDATA;
                Page.navigate({url: 'http://flagbook/grab-data'});
            }
            else if (stage === StageEnum.FLAGBOOK_GRABDATA)
            {
                //console.log("GRABDATA")
                const js = "document.querySelector('html').innerHTML";
                const result = await Runtime.evaluate({expression: js});
                sleep(2000).then(() => {
                    Page.navigate({url: 'http://flagbook/grab-data'});
                });
            }
        });

        Page.frameStartedLoading(() => {
            //console.log("started loading")
            last_update = new Date().getTime();
            sleep(5000).then(() => {
                if (new Date().getTime() - last_update > 4000) {
                    client.close().then(() => {
                        chrome.kill().then(() => {
                            //console.log("exiting...")
                            process.exit(1)
                        });
                    });
                }
            });
        });

        // enable events then start! 
        Promise.all([Network.enable(), Page.enable(), Runtime.enable()]).then(() => {
            stage = StageEnum.FLAGBOOK_LOGIN;
            //console.log("start.")
            Page.navigate({url: 'http://flagbook/'});
        });
    });
});