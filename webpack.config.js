const path = require('path');

module.exports = (env) => {
	return {
		// Environment dependent
		mode: env == 'dev' ? 'development' : 'production',
		devtool: env == 'dev' ? 'inline-source-map' : 'none',

		// Constant
		entry: {
			edit: './assets/js/edit.js',
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
	}
};