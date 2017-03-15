import React, { Component, PropTypes } from 'react';
import AppBar from 'material-ui/AppBar';
import IconButton from 'material-ui/IconButton';
import Drawer from 'material-ui/Drawer';
import NavigationClose from 'material-ui/svg-icons/navigation/close';
import CommunicationForum from 'material-ui/svg-icons/communication/forum';
import { List, ListItem, makeSelectable } from 'material-ui/List';
import Subheader from 'material-ui/Subheader';
import GitHub from '../icons/GitHub';
import Divider from 'material-ui/Divider';
import { NavLink } from 'react-router-dom';

const SelectableList = makeSelectable(List);

const navs = [
  {
    name: '2017 开发计划',
    md: '/2017-dev'
  },
  {
    name: '开始',
    open: true,
    item: [
      {
        name: '介绍',
        md: '/introduction'
      }
    ]
  }
];

class AppNavDrawerComponent extends Component {

  static propTypes = {
    open: PropTypes.bool.isRequired,
    handleClose: PropTypes.func.isRequired,
    onChangeList: PropTypes.func.isRequired,
    handleRequestHome: PropTypes.func.isRequired,
    value: PropTypes.string.isRequired,
    docked: PropTypes.bool.isRequired
  };

  getAppNavItems(navs) {
    return navs.map(({ name, md, item = [] }) => {
      let isNested = !!item.length;
      let params = {key: md, value: md, primaryText: name};

      if (isNested) {
        params = {...params, key: name, primaryTogglesNestedList: true, nestedItems: this.getAppNavItems(item)};
      }

      return (<ListItem {...params} />);
    });
  }

  render() {
    const { open, handleClose, value, onChangeList, handleRequestHome, docked } = this.props;
    const AppNavItems = this.getAppNavItems(navs);

    return (
      <Drawer
        open={open}
        docked={docked}
        width={256}
        onRequestChange={handleClose}
      >
        <AppBar
          title="phpwind Fans"
          iconElementLeft={
            <IconButton>
              <NavigationClose />
            </IconButton>
          }
          onLeftIconButtonTouchTap={handleClose}
          onTitleTouchTap={handleRequestHome}
          zDepth={0}
          titleStyle={{
            cursor: 'pointer'
          }}
        />
        <div>
          <SelectableList value={value} onChange={onChangeList} >
            {AppNavItems}
          </SelectableList>
          <Divider />
          <List>
            <Subheader>更多</Subheader>
            <ListItem
              containerElement="a"
              primaryText="GitHub"
              href="https://github.com/medz/phpwind/fork"
              leftIcon={<GitHub />}
            />
            <ListItem
              containerElement="a"
              primaryText="New issue"
              href="https://github.com/medz/phpwind/issues/new"
              leftIcon={<CommunicationForum />}
            />
          </List>
        </div>
      </Drawer>
    );
  }
}

export default AppNavDrawerComponent;
