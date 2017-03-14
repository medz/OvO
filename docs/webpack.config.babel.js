/*
|--------------------------------------------------------
| 文档 webpack 配置文件
|--------------------------------------------------------
|
| 配置文件使用 ES6 语法配置，这样能保证整个文档项目的语法统一性
| 修改配置文件请使用 ES6 语法对 webpack 进行配置。
|
*/

import webpack from 'webpack';
import path from 'path';
import ExtractTextPlugin from 'extract-text-webpack-plugin';
import HtmlWebpackPlugin from 'html-webpack-plugin';


/*
|--------------------------------------------------------
| 获取 NODE 环境变量模式
|--------------------------------------------------------
|
| 获取变量的用处用于判断当前运行环境是否属于正式编译使用。
|
*/
const NODE_ENV = process.env.NODE_ENV || 'development';

/*
|--------------------------------------------------------
| 获取是否是正式环境
|--------------------------------------------------------
|
| 定义该常量的用处便于程序中多处的判断，用于快捷判断条件。
|
*/
const isProd = NODE_ENV === 'production';

/*
|--------------------------------------------------------
| webpack 配置
|--------------------------------------------------------
|
| webpack 配置的根，储存所有的 webpack 配置信息。
| 
*/
const webpackConfig = {

/*
|---------------------------------------------------------
| 开发工具
|---------------------------------------------------------
|
| 判断是不是正式环境，非正式环境，加载 source-map
|
*/
devtool: isProd ? false : 'source-map',

/*
|---------------------------------------------------------
| 配置入口
|---------------------------------------------------------
|
| 入口配置，多个入口增加更多配置项。这里配置需要编译的资源入口。
|
*/
entry: {
  app: path.resolve(__dirname, 'src/main.js')
},

/*
|---------------------------------------------------------
| 输出配置
|---------------------------------------------------------
|
| 输出配置用于配制输出的文件路径和 js 文件的地方
|
*/
output: {
  path: path.join(__dirname, 'dist'),
  filename: '[name].js',
  publicPath: './dist'
},

resolve: {
  extensions: ['.js', '.jsx', '.json'],
  modules: [
    path.resolve(__dirname, 'src'),
    path.resolve(__dirname, 'node_modules')
  ]
},

module: {
  rules: [
    {
      test: /\.js$/,
      include: [path.resolve(__dirname, 'src')],
      loader: 'babel-loader'
    },
    {
      test: /\.css$/,
      use: ExtractTextPlugin.extract({
        fallback: "style-loader",
        use: 'css-loader'
      })
    }
  ]
},

/*
|---------------------------------------------------------
| 插件配置
|---------------------------------------------------------
|
| 定义在编译环境中所使用的插件
|
*/
plugins: [
  // Base plugin.
  ...[
    // Defined build env.
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: JSON.stringify(NODE_ENV)
      }
    }),
    new ExtractTextPlugin({
      filename: '[name].css'
    }),
    new HtmlWebpackPlugin({
      title: 'phpwind Fans',
      filename: path.join(__dirname, 'index.html'),
      template: path.resolve(__dirname, 'src/index.html'),
      favicon: path.resolve(__dirname, '../favicon.ico'),
    }),
  ],
  // Prod plugin.
  ...(isProd ? [
    new webpack.optimize.UglifyJsPlugin({
      compress: {
      warnings: false
      },
      sourceMap: false
    }),
  // Dev plugin.
  ] : [
    new webpack.NoEmitOnErrorsPlugin(),
  ])
],

/* ------------------------------------------------------ */
};

export default webpackConfig;
