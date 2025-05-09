const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CssMinimizerPlugin = require("css-minimizer-webpack-plugin");
const TerserPlugin = require("terser-webpack-plugin");

module.exports = {
    entry: {
        main: ['./resources/js/main.js', './resources/css/main.css'],
        geolocation: ['./resources/js/geolocation.js'],
        netteForms: ['./resources/js/netteforms.js'],
        maskitoPhone: ['./resources/js/maskitoPhone.js'],
        phoneMask: ['./resources/js/phoneMask.js'],
        admin: ['./resources/js/admin.js', './resources/css/admin.css'],
    },
    mode: (process.env.NODE_ENV === 'production') ? 'production' : 'development',
    resolve: {
        extensions: ['.*', '.js', '.jsx']
    },
    output: {
        path: path.join(__dirname, 'www', 'assets'),
        filename: 'js/[name].js',
        clean: true, // folder from path cleaning
    },
    devServer: {
        static: path.join(__dirname, 'www/'),
        devMiddleware: {
            publicPath: '/assets/'
        },
        server: {
            type: "https",
            options: {},
        },
        port: 3000,
        headers: {
            "Access-Control-Allow-Origin": "*",
            "Access-Control-Allow-Methods": "GET, POST, PUT, DELETE, PATCH, OPTIONS",
            "Access-Control-Allow-Headers": "X-Requested-With, content-type, Authorization"
        },
        allowedHosts: ['acnette.local'], // Список допустимых хостов
        hot: true,
        client: {
            overlay: {
                errors: true,
                warnings: false,
            },
            progress: true
        },
    },
    module: {
        rules: [
            /*
            {
                test: /\.(html)$/,
                use: ['html-loader']
            },
            */
            {
                test: /.s?css$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                    // 'sass-loader' // for sass, compile SCSS to CSS
                ],
            },
            {
                test: /\.(png|svg|jpg|jpeg|gif|ico|webp)$/i,
                type: 'asset',
                generator: {
                    filename: 'images/[hash][ext][query]'
                },
                parser: { dataUrlCondition: { maxSize: 8 * 1024 } }, // Limit to 8KB
            },
            {
                test: /\.(woff|woff2|eot|ttf|otf)$/i,
                type: 'asset/resource',
                generator: {
                    filename: 'fonts/[hash][ext][query]'
                },
            },
        ],
    },
    optimization: {
        minimize: true,
        minimizer: [
            new TerserPlugin({
                parallel: true,
                terserOptions: {
                    compress: {
                        drop_console: true,
                    },
                    mangle: true,
                },
            }),
            new CssMinimizerPlugin({
                parallel: true,
                /* 
                /// for cssnano ///
                minimizerOptions: {
                    preset: ['default', {
                        discardComments: {
                            removeAll: true,
                        },
                    }]
                },
                /// end for cssnano ///
                */
                minify: CssMinimizerPlugin.cleanCssMinify,
            }),
        ],
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: 'css/[name].css',
        }),
    ],
    stats: {
        errorDetails: true,
    },
};