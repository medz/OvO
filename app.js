const app = require('./src/bootstrap/app');

app.all('/graphql', (request, response) => {
  response.send('demo2');
});
