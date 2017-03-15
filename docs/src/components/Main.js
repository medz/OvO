import React, { Component, PropTypes } from 'react';
import MuiThemeProvider from 'material-ui/styles/MuiThemeProvider';
import lightBaseTheme from 'material-ui/styles/baseThemes/lightBaseTheme';
import getMuiTheme from 'material-ui/styles/getMuiTheme';
import withWidth, { SMALL } from 'material-ui/utils/withWidth';
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

  static propTypes = {
    width: PropTypes.number.isRequired,
  };

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
    if (this.props.width === SMALL) {
      this.handleToggle();
    }
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
    if (this.getPathname() !== '/' && this.props.width !== SMALL) {
      this.setState({
        ...this.state,
        open: true
      });
    }
  }

  render() {
    const pathname = this.getPathname();

    return (
      <MuiThemeProvider muiTheme={getMuiTheme(lightBaseTheme)}>
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
            value={pathname}
            docked={this.props.width !== SMALL}
            handleClose={this.handleToggle}
            onChangeList={this.handleChangeList}
            handleRequestHome={this.handleRequestHome}
          />
          <Route exact path="/" component={Index} />
          <div
            style={{
              transition: 'all 450ms cubic-bezier(0.23, 1, 0.32, 1) 0ms',
              ...(this.state.open && this.props.width !== SMALL ? {
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

export default withWidth()(MainComponent);
