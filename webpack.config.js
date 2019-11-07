const path = require('path');
const PrettierPlugin = require('prettier-webpack-plugin');

module.exports = (env) => {
	return {
		// Environment dependent
		mode: env == 'dev' ? 'development' : 'production',
		devtool: env == 'dev' ? 'cheap-eval-source-map' : 'source-map',
		stats: env == 'dev' ? {} : { warnings: false },

		// Constant
		entry: {
			edit: './src/edit.js',
			home: './src/home.js',
			sentry: './src/sentry.js',
		},
		output: {
			filename: 'assets/js/[name].bundle.js',
			path: path.resolve(__dirname),
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
			new PrettierPlugin({
				tabWidth: 4,
				trailingComma: 'es5',
				singleQuote: true,
			})
		],
	}
};
