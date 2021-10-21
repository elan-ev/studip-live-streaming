const webpack = require('webpack')
const {merge} = require('webpack-merge')
const common = require('./webpack.common.js')
const TerserPlugin = require("terser-webpack-plugin");
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');

module.exports = merge(common, {
    mode: 'production',
    output: {
        publicPath: undefined
    },
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                parallel: true,
            }),
            new CssMinimizerPlugin({
                minimizerOptions: {
                    discardComments: {
                        removeAll: true
                    },
                    safe: true
                }
            })
        ]
    }
})
