import React, { Component, PropTypes } from 'react';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import { Route } from 'react-router-dom';
import AppBar from 'material-ui/AppBar';
import IconButton from 'material-ui/IconButton';
import GitHub from '../icons/GitHub';
import Index from './Index';
import AppNavDrawer from './AppNavDrawer';
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

  static contextTypes = {
    router: PropTypes.object.isRequired,
  };

  state = {
    open: false
  };

  handleToggle = () => {
    this.setState({
      ...this.state,
      open: !this.state.open
    });
  };

  handleChangeList = (event, value) => {
    const { router: { history: { push } } } = this.context;
    push(value);
  };

  handleRequestHome = () => {
    if (this.getPathname() !== '/') {
      const { router: { history: { push } } } = this.context;
      push('/');
    }
    this.handleToggle();
  };

  getPathname() {
    const { router: { route: { location: { pathname } } } } = this.context;
    return pathname;
  }

  componentDidMount() {
    if (this.getPathname() !== '/') {
      this.setState({
        ...this.state,
        open: true
      });
    }
  }

  render() {
    const pathname = this.getPathname();

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
            onLeftIconButtonTouchTap={this.handleToggle}
            zDepth={0}
          />
          <AppNavDrawer
            open={this.state.open}
            handleClose={this.handleToggle}
            value={pathname}
            onChangeList={this.handleChangeList}
            handleRequestHome={this.handleRequestHome}
          />
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
