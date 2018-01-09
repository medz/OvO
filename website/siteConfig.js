/**
 * Copyright (c) 2017-present, Facebook, Inc.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */

/* List of projects/orgs using your project for the users page */
const users = [
  {
    caption: 'phpwind',
    image: 'http://img1.phpwind.net/attachout/background/4439b6b579ac496.png',
    infoLink: 'https://www.phpwind.net',
    pinned: true,
  },
];

const siteConfig = {
  title: 'Fans 3' /* title for your website */,
  tagline: 'Using Node.js created a Forum.',
  url: 'https://medz.github.io/phpwind' /* your website url */,
  baseUrl: 'https://medz.github.io/phpwind/' /* base url for your project */,
  projectName: 'phpwind',
  headerLinks: [
    {search: true},
    {doc: 'getting-started-installation', label: 'Docs'},
    {doc: 'api-overview', label: 'API'},
    {page: 'help', label: 'Help'},
    {blog: false, label: 'Blog'},
    {href: 'https://github.com/medz/phpwind', label: 'GitHub'},
  ],
  users,
  /* path to images for header/footer */
  headerIcon: 'img/fans.svg',
  footerIcon: 'img/fans.svg',
  favicon: 'favicon.ico',
  /* colors for website */
  colors: {
    primaryColor: '#1E88E5',
    secondaryColor: '#64B5F6',
  },
  // This copyright info is used in /core/Footer.js and blog rss/atom feeds.
  copyright:
    'Copyright © ' +
    new Date().getFullYear() +
    ' Seven Du.',
  organizationName: 'medz', // or set an env variable ORGANIZATION_NAME
  // projectName: 'test-site', // or set an env variable PROJECT_NAME
  highlight: {
    // Highlight.js theme to use for syntax highlighting in code blocks
    theme: 'default',
  },
  scripts: ['https://buttons.github.io/buttons.js'],
  // You may provide arbitrary config keys to be used as needed by your template.
  repoUrl: 'https://github.com/medz/phpwind',
  algolia: {
    apiKey: "60b8864eee85540be6172d559fecd604",
    indexName: "fans-docs"
  },
};

module.exports = siteConfig;
