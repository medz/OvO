import React, { Component } from 'react';

class IndexComponent extends Component {
  render() {
    return (
      <div
        style={{
          backgroundColor: "#00bcd4",
          textAlign:'center',
          paddingBottom: 15
        }}
      >
        <h1 style={{
          fontWeight: 300,
          color: '#fff'
        }}
        >
          phpwind Fans
        </h1>
        <h2
          style={{
            color: 'rgba(255, 255, 255, 0.870588)',
            fontWeight: 300,
            fontSize: 20,
            lineHeight: '28px',
            paddingTop: 19,
            marginBottom: 13,
            letterSpacing: 0
          }}
        >
          使用 PHP 和 MySQL 开发的高性能社区系统
        </h2>
      </div>
    );
  }
}

export default IndexComponent;
