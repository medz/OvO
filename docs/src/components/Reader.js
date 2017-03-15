import React, { Component, PropTypes } from 'react';
import axios from 'axios';
import Paper from 'material-ui/Paper';
import marked from 'marked';
import { highlight } from 'highlight.js';

import '../styles/mui-github-markdown.css';
import 'highlight.js/styles/default.css';

const langAlias = lang => {
  const alias = {
    json5: 'json',
  };

  return alias[lang] || lang;
}

marked.setOptions({
  highlight: (code, lang) => highlight(langAlias(lang), code).value
});

class ReaderComponent extends Component {

  static propTypes = {
    location: PropTypes.object.isRequired,
  };

  state = {
    markdowns: {},
    loadding: false
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

    if (this.state.loadding === false) {
      return (
        <Paper
          zDepth={0}
        >
          <div
            className="markdown-body"
            dangerouslySetInnerHTML={{__html: marked(markdown)}}
            style={{
              marginTop: 20,
              marginBottom: 20,
              padding: '0 10px'
            }}
          />
        </Paper>
      );
    }

    return null;
  }
}

export default ReaderComponent;
