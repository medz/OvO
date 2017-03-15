import React, { Component } from 'react';
import AppBar from 'material-ui/AppBar';
import IconButton from 'material-ui/IconButton';
import Drawer from 'material-ui/Drawer';
import NavigationClose from 'material-ui/svg-icons/navigation/close';
import CommunicationForum from 'material-ui/svg-icons/communication/forum';
import { List, ListItem } from 'material-ui/List';
import Subheader from 'material-ui/Subheader';
import GitHub from '../icons/GitHub';
import Divider from 'material-ui/Divider';
import { NavLink } from 'react-router-dom';

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

class NavComponent extends Component {
  render() {

    const { open, handleClose } = this.props;

    return (
      <Drawer
        open={open}
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
          onLeftIconButtonTouchTap={handleClose}
          zDepth={0}
        />
        <div>
          <List>
            {navs.map(nav => {
              let { name, md, item = [], opne = false } = nav;
              let isNested = !!item.length;
              if (isNested) {
                return (
                  <ListItem
                    key={name}
                    primaryText={name}
                    initiallyOpen={!!open}
                    primaryTogglesNestedList={true}
                    nestedItems={item.map(({ name, md }) => (
                      <ListItem
                        key={md}
                        primaryText={name}
                        containerElement={
                          <NavLink exact to={md} />
                        }
                      />
                    ))}
                  />
                );
              }

              return (
                <ListItem
                  key={md}
                  primaryText={name}
                  containerElement={
                    <NavLink exact to={md} />
                  }
                />
              );
            })}
          </List>
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

export default NavComponent;
