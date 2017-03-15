import React, { Component, PropTypes } from 'react';
import axios from 'axios';
import Paper from 'material-ui/Paper';

class ReaderComponent extends Component {

  static propTypes = {
    location: PropTypes.object.isRequired,
  };

  state = {
    markdowns: {},
  };

  getPathname(props = false) {
    const { location: { pathname = '/introduction' } } = props || this.props;

    return pathname;
  }

  componentWillReceiveProps(props, load = false) {
    const oldPathname = this.getPathname();
    const pathname = this.getPathname(props);
    if ((oldPathname !== pathname && !this.state.markdowns.hasOwnProperty(pathname)) || load === true) {
      this.handleRequestMarkdown(props);
    }
  }

  componentDidMount() {
    this.componentWillReceiveProps(this.props, true);
  }

  handleRequestMarkdown(props = false) {
    const pathname = this.getPathname(props);
    axios.get(`./assets${pathname}.md`)
    .then(({ data }) => {
      this.setState({
        ...this.state,
        markdowns: {
          ...this.state.markdowns,
          [pathname]: data
        }
      });
    })
    .catch(() => {});
  }

  render() {
    const pathname = this.getPathname();
    const markdown = this.state.markdowns[pathname] || '';

    return (
      <Paper
        zDepth={0}
        style={{
          padding: 15
        }}
      >
        {markdown}
      </Paper>
    );
  }
}

export default ReaderComponent;
