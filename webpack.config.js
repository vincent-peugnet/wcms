const path = require('path');
const PrettierPlugin = require('prettier-webpack-plugin');
const TerserPlugin = require("terser-webpack-plugin");

module.exports = (env) => {
	return {
		// Environment dependent
		mode: env.dev ? 'development' : 'production',
		devtool: env.dev ?
			'eval-cheap-module-source-map' :
			env.dist ?
				'hidden-source-map' :
				'source-map',
		// Constant
		entry: {
			edit: './src/edit.js',
			home: './src/home.js',
			map: './src/map.js',
			media: './src/media.js',
			sentry: './src/sentry.js',
		},
		output: {
			filename: '[name].bundle.js',
			path: path.resolve(__dirname, 'assets', 'js'),
			compareBeforeEmit: false,
			libraryTarget: 'window'
		},
		module: {
			rules: [
				{
					test: /\.css$/,
					use: [
						'style-loader',
						'css-loader',
					],
				},
			],
		},
		plugins: [
			new PrettierPlugin(),
		],
		externals: {
			'@sentry/browser': 'Sentry',
		},
		optimization: {
			minimizer: [
				new TerserPlugin({
					extractComments: false,
				}),
			],
		},
	}
};
