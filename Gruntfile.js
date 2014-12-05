module.exports = function(grunt) {

    // Project configuration.
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        // Deploy via rsync
        deploy: {
            options: {
                src: "./",
                args: ["--verbose"],
                exclude: ['.git*', 'node_modules', '.sass-cache', 'Gruntfile.js', 'package.json', '.DS_Store', 'README.md', 'config.rb', '.jshintrc', 'sass', 'bower_components'],
                recursive: true,
                syncDestIgnoreExcl: true
            },
            production: {
                options: {
                    dest: "~/apps/hotline/public",
                    host: "serverpilot@104.131.181.203"
                }
            }
        }
    });

    // Require all tasks
    require('load-grunt-tasks')(grunt);

    grunt.renameTask('rsync', 'deploy');
};