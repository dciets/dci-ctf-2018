const fs = require('fs');
const glob = require('glob');

function getFile(pattern) {
    return new Promise((resolve) => {
        glob(pattern, (_, files) => {
            resolve(files[0]);
        });
    });
}

module.exports = async (req, res) => {
    var { id } = req.params;

    var file = await getFile(`avatars/${id}*`);

    if(!file) {
        file = await getFile(`avatars/0*`);
    }

    fs.readFile(file, (_, data) => {
        res.ok({ data: Buffer.from(data).toString("base64") });
    });
};
