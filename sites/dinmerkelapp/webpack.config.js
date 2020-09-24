const path = require('path');
const webpack = require('webpack');

const MiniCssExtractPlugin = require('mini-css-extract-plugin');


module.exports = {
    module: {
        rules: [
            {
                test: /\.s[ac]ss$/i,
                use: [
                    // fallback to style-loader in development
                    {
                        loader: MiniCssExtractPlugin.loader,
                    },
                    'css-loader',
                    'sass-loader',
                ],
            },
            {
                test: /\.woff(2)?(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                loader: "url-loader?limit=10000&mimetype=application/font-woff",
                options: {
                    name: 'webfonts/[name].[ext]',
                },
            },
            {
                test: /\.(ttf|eot|svg)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                loader: "file-loader",
                options: {
                    name: 'webfonts/[name].[ext]',
                },
            },
            {
                test: /\.(jpg|png|gif)(\?v=[0-9]\.[0-9]\.[0-9])?$/,
                loader: "file-loader",
                options: {
                    name: '/css/img/[name].[ext]',
                },
            }
        ],
    },
    entry: './assets/app.js',
    output: {
        filename: 'js/bundle.js',
        path: path.resolve(__dirname, 'webroot')
    },
    plugins: [
        new webpack.ProvidePlugin({
            $: 'jquery',
            jQuery: 'jquery'
        }),
        new MiniCssExtractPlugin({
            // Options similar to the same options in webpackOptions.output
            // both options are optional
            filename: 'css/[name].css',
            chunkFilename: 'css/[id].css',
        }),
    ],
};