const path = require('path');
const PrettierPlugin = require('prettier-webpack-plugin');

module.exports = (env) => {
	return {
		// Environment dependent
		mode: env == 'dev' ? 'development' : 'production',
		devtool: env == 'dev' ?
			'cheap-eval-source-map' :
			env == 'dist' ?
				'hidden-source-map' :
				'source-map',
		stats: env == 'dev' ? {} : { warnings: false },

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
	}
};
