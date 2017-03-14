import React, { Component } from 'react';
import { HashRouter, BrowserRouter } from 'react-router-dom';

import Main from './components/Main';

const App = () => (
  <BrowserRouter>
    <Main />
  </BrowserRouter>
);

export default App;
