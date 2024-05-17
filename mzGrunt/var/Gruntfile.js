/* global require:false */

/* !
 * Grunt For Z-Blog 0.1.1 (http://www.wdssmq.com)
 * Copyright 2013-2015 沉冰浮水
 */

module.exports = function(grunt) {
  require("load-grunt-parent-tasks")(grunt, {
    config: "package.json",
    pattern: "grunt-*",
    scope: "dependencies",
    module: "grunt-collection",
  });

  grunt.initConfig({
    pkg: grunt.file.readJSON("package.json"),
    path: "<%= pkg.path %>/<%= pkg.id %>",
    banner:
      "/*!\n" +
      " * <%= pkg.id%> <%= pkg.version %> (<%= pkg.homepage %>)\n" +
      " * Copyright <%= pkg.pubdate %> [-] <%=  grunt.template.today(\"yyyy-mm-dd\") %> <%= pkg.author %>\n" +
      " * <%= pkg.description %>\n" +
      " */\n",

    // Task configuration.
    clean: {
      dist: ["dist"],
    },

    // 编译 less
    less: {
      options: {
        ieCompat: true,
        strictMath: true,
        sourceMap: true,
        outputSourceFiles: true,
      },
      core: {
        options: {
          sourceMapURL: "<%= pkg.id %>.css.map",
          sourceMapFilename: "dist/<%= pkg.id %>.css.map",
        },
        src: "<%= pkg.less %>",
        dest: "<%= pkg.style %>",
      },
    },

    // 编译 sass
    "dart-sass": {
      target: {
        // options: {
        //   sourceMap: true,
        //   outFile: "<%= pkg.style %>",
        // },
        files: {
          // "<%= pkg.style %>": "<%= pkg.sass %>",
          "dist/for-purge.css": "<%= pkg.sass %>",
        },
      },
    },

    // 去除无用 CSS
    purgecss: {
      my_target: {
        options: {
          content: ['<%= path %>/template/**/*.php']
        },
        files: {
          // 'dist/purg.css': "<%= pkg.style %>",
          "<%= pkg.style %>": "dist/for-purge.css",
        }
      }
    },

    // 压缩
    cssmin: {
      options: {
        sourceMap: false,
        // sourceMapInlineSources: true,
        // level: {
        //   1: {
        //     specialComments: "all"
        //   }
        // }
      },
      core: {
        files: {
          "<%= pkg.styleMin %>": "<%= pkg.style %>",
        },
      },
    },

    // 代码完善
    autoprefixer: {
      options: {
        browsers: [
          "Android >= 4",
          "Chrome >= 20",
          "Firefox >= 24", // Firefox 24 is the latest ESR
          "Explorer >= 9",
          "iOS >= 6",
          "Opera >= 16",
          "Safari >= 6",
        ],
      },
      core: {
        options: {
          map: false,
        },
        src: "<%= pkg.style %>",
      },
    },

    // 属性排序
    csscomb: {
      options: {
        config: "<%= pkg.pathCFG %>/.csscomb.json",
      },
      dist: {
        files: {
          "<%= pkg.style %>": "<%= pkg.style %>",
        },
      },
    },

    // 语法检查
    csslint: {
      options: {
        csslintrc: "<%= pkg.pathCFG%>/.csslintrc",
      },
      dist: ["<%= pkg.style %>"],
    },

    // 拼接JS
    concat: {
      options: {
        // banner: '<%= banner %>',
        stripBanners: false,
        separator: ";",
      },
      dist: {
        src: ["js/**/*.js"],
        dest: "<%= pkg.script %>",
      },
    },

    // JS压缩
    uglify: {
      options: {
        banner: "<%= banner %>",
        ASCIIOnly: true,
      },
      dist: {
        files: {
          "<%= pkg.scriptMin %>": ["<%= pkg.script %>"],
        },
      },
    },

    // JS语法检测
    jshint: {
      options: {
        jshintrc: "<%= pkg.pathCFG%>/.jshintrc",
      },
      src: {
        src: "js/*.js",
      },
    },

    // CSS Banner
    usebanner: {
      dist: {
        options: {
          position: "top",
          banner: "<%= banner %>",
        },
        files: {
          src: ["dist/*.css"],
        },
      },
    },

    replace: {
      cssMap: {
        src: "<%= pkg.styleMin%>.map",
        dest: "<%= pkg.styleMin%>.map",
        replacements: [
          {
            from: "dist\\\\",
            to: "",
          },
        ],
      },
    },

    copy: {
      css: {
        expand: true,
        flatten: true,
        cwd: "dist/",
        src: ["*.css", "*.min.css.map"],
        dest: "<%= path %>/style/",
      },
      js: {
        expand: true,
        flatten: true,
        cwd: "dist/",
        src: "*.js",
        dest: "<%= path %>/script/",
      },
    },

    watch: {
      // 自动编译
      css: {
        files: ["less/**/*.less", "sass/**/*.sass", '<%= path %>/template/purge-keep/*.php'],
        tasks: ["dist-css"],
      },
      js: {
        files: ["js/**/*.js"],
        tasks: ["dist-js"],
      },
    },
    // 自动刷新网页
    browserSync: {
      dev: {
        bsFiles: {
          src: [
            "<%= path %>/**/*.css",
            "<%= path %>/**/*.js",
            "<%= path %>/**/*.html",
            "<%= path %>/**/*.php",
            "<%= path %>/**/*.asp",
            "<%= path %>/**/*.json",
          ],
        },
        options: {
          watchTask: true,
          proxy: "<%= pkg.host %>",
        },
      },
    },
  });

  // require("load-grunt-tasks")(grunt, { scope: "devDependencies" });

  grunt.loadNpmTasks("grunt-collection");

  // Test task.
  grunt.registerTask("test-css", ["csslint"]);
  grunt.registerTask("test-js", ["jshint"]);
  grunt.registerTask("test", ["test-css", "test-js"]);

  // CSS distribution task.
  let pkg = grunt.file.readJSON("package.json");
  let cssType = "less";
  // console.log(pkg.sass);
  if (grunt.file.exists(pkg.sass)) {
    cssType = "dart-sass";
  }
  grunt.registerTask("dist-css", [
    cssType,
    "purgecss",
    // "autoprefixer",
    // "csscomb",
    "cssmin",
    "usebanner",
    "replace",
    "copy:css",
  ]);

  // JS distribution task.
  grunt.registerTask("dist-js", ["concat", "uglify", "copy:js"]);

  // Default task.
  grunt.registerTask("dist", ["clean", "dist-css", "dist-js", "browserSync", "watch"]);
};
