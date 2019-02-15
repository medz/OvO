const env = require('./env');

module.exports = {
    title: `${env.APP_NAME}`,
    description: `The ${env.APP_NAME} app REST API docs.`,
    base: '/docs/',
    head: [
        ['link', { rel: 'shortcut icon', href: '/favicon.ico' }],
        ['link', { rel: 'icon', href: '/fans.svg' }],
        ['link', { rel: 'mask-icon', href: '/fans.svg' }],
    ],
    dest: `${__dirname}/../../public/docs`,
    themeConfig: {
        env: {
            APP_NAME: env.APP_NAME,
            APP_URL: env.APP_URL,
        },
        nav: [
            { text: 'Home', link: '/' },
            { text: 'Docs', link: '/api/' },
            { text: 'GitHub', link: 'https://github.com/medz/fans' },
        ],
        sidebar: {
            '/api/': [
                {
                    title: 'Kernel',
                    collapsable: false,
                    sidebarDepth: 1,
                    children: [
                        '',
                        'authorizations',
                        'comments',
                        'itc',
                        'jurisdictions',
                        'talks',
                        'upload',
                        'users',
                    ]
                },
                {
                    title: 'Forum',
                    collapsable: false,
                    sidebarDepth: 1,
                    children: [
                        'forum/nodes',
                        'forum/threads',
                    ]
                }
            ],
        }
    }
};
