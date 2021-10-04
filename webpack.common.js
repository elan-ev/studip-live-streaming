const path = require('path');
const webpack = require('webpack');
const assetsPath = path.resolve(__dirname, "assets");
const srcPath = path.resolve(__dirname, "src");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
    entry: {
        "livestream": [srcPath + '/javascripts/livestream.js', srcPath + '/scss/livestream.scss'],
        "videoplayer": [srcPath + '/javascripts/videoplayer.js', srcPath + '/scss/videoplayer.scss']
    },
    output: {
        filename: 'javascripts/[name].js',
        path: assetsPath,
    },
    module: {
        rules: [
            {
                test: /\.js$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        presets: ['@babel/preset-env']
                    }
                }
            },
            {
                test: /\.scss$/,
                use: [
                    {
                        loader: MiniCssExtractPlugin.loader
                    },
                    {
                        loader: "css-loader",
                        options: {
                            importLoaders: 2
                        }
                    },
                    {
                        loader: 'sass-loader'
                    }
                ]
            }
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
        }),
    ]
};
