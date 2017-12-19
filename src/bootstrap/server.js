module.exports = function (app, { /*host, */listen, ssl, ssl_certificate, ssl_certificate_key }) {
  if (ssl) {
    const fs = require('fs');
    const https = require('https');
    const options = {
      key: fs.readFileSync(ssl_certificate_key),
      cert: fs.readFileSync(ssl_certificate),
    };

    return https.createServer(options, app).listen(listen);
  }

  const http = require('http');

  return http.createServer(app).listen(listen);
};
