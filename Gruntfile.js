/*!
 * Bootstrap's Gruntfile
 * http://getbootstrap.com
 * Copyright 2013-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 *
 */

module.exports = function (grunt) {
  'use strict';

  // Force use of Unix newlines
  grunt.util.linefeed = '\n';

  // Project configuration.
  grunt.initConfig({

    concat: {
      options: {
        separator: '\n\n',
      },
      readme_md: {
        src: [
           'readme/description.md',
           'readme/description-lead.html',
           'readme/description-features.html',
           'readme/description-features-pro.html',
           'readme/description-supported-providers.md',
           'readme/description-only-wp-org.md',
           'readme/description-reviews.html',
           'readme/installation.md',
           'readme/faq.md',
           'readme/screenshots.md',
           'todo.md',
           'CHANGELOG.md'
        ],
        dest: 'README.md'
      },
      readme_txt: {
        src: [
          'readme/top.txt',
          'README.md'
        ],
        dest: 'README.txt'
      }
    }
  });

  require('load-grunt-tasks')(grunt, { scope: 'devDependencies' });

  // Default task.
  grunt.registerTask('default', ['concat']);
};
