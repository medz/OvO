import React, { Component, cloneElement } from 'react';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import { Route, Link } from 'react-router-dom';

import AppBar from 'material-ui/AppBar';
import Drawer from 'material-ui/Drawer';
import IconButton from 'material-ui/IconButton';
import ActionCode from 'material-ui/svg-icons/action/code';
import NavigationClose from 'material-ui/svg-icons/navigation/close';

import Index from './Index';

class MainComponent extends Component {

  constructor(props) {
    super(props);
    this.state = {
      open: false
    };
  }

  handleToggle() {
    this.setState({
      ...this.state,
      open: !this.state.open
    });
  }

  render() {
    return (
      <MuiThemeProvider>
        <div>
          <AppBar
            title="phpwind Fans"
            iconElementRight={
              <IconButton
                href="https://github.com/medz/phpwind"
                tooltip="点击浏览 phpwind Fans 代码仓库"
                tooltipPosition="bottom-left"
              >
                <ActionCode />
              </IconButton>
            }
            onLeftIconButtonTouchTap={() => this.handleToggle()}
          />
          <Drawer
            open={this.state.open}
            docked={true}
            width={256}
          >
            <AppBar
              title="phpwind Fans"
              iconElementLeft={
                <IconButton>
                  <NavigationClose />
                </IconButton>
              }
              onLeftIconButtonTouchTap={() => this.handleToggle()}
            />
          </Drawer>
          <Route exact path="/" component={Index} />
          <div
            style={{
              transition: 'all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms',
              ...(this.state.open ? {
                paddingLeft: 256
              } : {})
            }}
          >
            aaa
          </div>
        </div>
      </MuiThemeProvider>
    );
  }
}

export default MainComponent;
