import React, { Component } from 'react';

class ReaderComponent extends Component {
  render() {
    const { location: { pathname = '/introduction' } } = this.props;
    console.log(pathname);

    return null;
  }
}

export default ReaderComponent;
