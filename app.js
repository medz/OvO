const express = require('express');
const app = express();

app.all('/graphql', (request, response) => {
  response.send('demo');
});

const server = app.listen(3000, function () {
  const { address: host, port } = server.address();

  console.log('App listening at http://%s:%s', host, port);
});
