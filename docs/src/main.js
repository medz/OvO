/*
|---------------------------------------------------
| phpwind Fsns doc SPA entry.
|---------------------------------------------------
|
| 这里定义文档应用入口页面
|
*/

import injectTapEventPlugin from 'react-tap-event-plugin';
import React from 'react';
import { render } from 'react-dom';
import App from './App';
import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
  // Needed for onTouchTap
  // http://stackoverflow.com/a/34015469/988941
  injectTapEventPlugin();

  render(
    <App />,
    document.getElementById('app')
  )

});
