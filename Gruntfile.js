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

    sed: {
      h2: {
        path: 'README.txt',
        pattern: /^##([^#]+)#*?\s*?\n/gm,
        replacement: '==$1==\n',
        recursive: true
      },
      del_sc: {
        path: 'README.txt',
        pattern: /^\[(youtube|vimeo)[^\]]+\]/gm,
        replacement: '',
        recursive: true
      },
      sc_start: {
        path: 'README.txt',
        pattern: /`\[\[/gm,
        replacement: '`[',
        recursive: true
      },
      sc_end: {
        path: 'README.txt',
        pattern: /\]\]`/gm,
        replacement: ']`',
        recursive: true
      }
    },

    concat: {
      options: {
        separator: '\n\n',
      },
      readme_md: {
        src: [
           'readme/description.md',
           'readme/description-intro.md',
           'readme/description-links.md',
           'readme/description-features.md',
           'readme/description-supported-providers.md',
           'readme/description-quick-intro.md',
           'readme/description-pro-addon-demo.md',
           'readme/description-hd-thumbnail.md',
           'readme/description-reviews.md',
           'readme/description-roadmap.md',
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
  grunt.registerTask('default', ['concat', 'sed']);
};
