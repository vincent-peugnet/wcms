const path = require('path');
const PrettierPlugin = require('prettier-webpack-plugin');

module.exports = (env) => {
	return {
		// Environment dependent
		mode: env == 'dev' ? 'development' : 'production',
		devtool: env == 'dev' ? 'inline-source-map' : 'none',
		stats: env == 'dev' ? {} : { warnings: false },

		// Constant
		entry: {
			edit: './src/edit.js',
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
