const express = require('express');
const { http } = require('../config');
const server = require('./server');

// Create application.
const app = express();
app.server = server(app, http);

module.exports = global.fans = app;
