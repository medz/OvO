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
            <ListItem
              primaryText="2017 开发计划"
            />
            <ListItem
              primaryText="开始"
              initiallyOpen={true}
              primaryTogglesNestedList={true}
              nestedItems={[
                <ListItem
                  key="introduction"
                  primaryText="介绍"
                  containerElement={<NavLink
                    exact
                    to="/introduction"
                  />}
                />
              ]}
            />
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
