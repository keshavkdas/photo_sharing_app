const path = require('path');

module.exports = {
    entry: './src/index.js',  // Entry point of your application
    output: {
        filename: 'bundle.js',  // Output bundle file name
        path: path.resolve(__dirname, 'dist')  // Output directory
    },
    // Add loaders, plugins, and other configurations as needed
};
