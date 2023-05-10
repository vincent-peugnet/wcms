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
			edit: {
				import: './src/edit.js',
				dependOn: 'leaflet',
			},
			home: './src/home.js',
			graph: './src/graph.js',
			map: {
				import: './src/map.js',
				dependOn: 'leaflet',
			},
			pagemap: {
				import: './src/pagemap.js',
				dependOn: 'leaflet',
			},
			media: './src/media.js',
			sentry: './src/sentry.js',
			leaflet: 'leaflet',
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
				{
					test: /\.png$/,
					type: 'asset/resource',
					generator: {
						filename: 'images/[name]-[hash][ext]'
					}
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
