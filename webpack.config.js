const path = require('path');
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
		stats: {
			all: false,
			colors: true,
			performance: true,
			assets: true,
			warnings: true,
			cachedModules: true,
			errors: true,
			errorDetails: true,
		},
		entry: {
			edit: './src/edit.js',
			home: './src/home.js',
			graph: './src/graph.js',
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
		performance: {
			hints: false,
		},
	}
};
