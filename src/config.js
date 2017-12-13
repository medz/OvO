const yaml = require('js-yaml');
const fs = require('fs');

const path = __dirname+'/../.fans.yml';
let doc = yaml.load(fs.readFileSync(path));
doc.rootPath = __dirname+'/..';

module.exports = doc;
