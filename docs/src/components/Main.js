import React, { Component, cloneElement } from 'react';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import { Route } from 'react-router-dom';
import AppBar from 'material-ui/AppBar';
import IconButton from 'material-ui/IconButton';
import GitHub from '../icons/GitHub';
import Index from './Index';
import Nav from './Nav';
import Reader from './Reader';

/*

<div dangerouslySetInnerHTML={{
  __html: md.render(`
    # markdown-it rulezz!
  `)
}} />
 */

// import MarkdownIt from 'markdown-it';

// const md = MarkdownIt();

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

    const handleToggle = () => this.handleToggle();

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
                <GitHub color="#fff" />
              </IconButton>
            }
            onLeftIconButtonTouchTap={handleToggle}
            zDepth={0}
          />
          <Nav open={this.state.open} handleClose={handleToggle} />
          <Route exact path="/" component={Index} />
          <div
            style={{
              transition: 'all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms',
              ...(this.state.open ? {
                paddingLeft: 256
              } : {})
            }}
          >
            <Route path="/:path" component={Reader} />
          </div>
        </div>
      </MuiThemeProvider>
    );
  }
}

export default MainComponent;
