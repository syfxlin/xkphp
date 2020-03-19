const path = require("path");
const fs = require("fs");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

module.exports = {
  webpack: function(config, env) {
    if (process.argv[process.argv.length - 1] !== "mix") {
      return config;
    }
    config.output.filename = "js/[name].js";
    config.output.chunkFilename = "js/[name].js";
    // config.plugins.MiniCssExtractPlugin.options.filename = "css/[name].css";
    // config.plugins.MiniCssExtractPlugin.options.chunkFilename =
    //   "css/[name].chunk.css";
    for (let i = 0; i < config.plugins.length; i++) {
      if (config.plugins[i].constructor.name === "MiniCssExtractPlugin") {
        config.plugins[i] = new MiniCssExtractPlugin({
          filename: "css/[name].css",
          chunkFilename: "css/[name].css"
        });
      }
    }
    for (let i = 0; i < config.module.rules[2].oneOf.length; i++) {
      if (
        config.module.rules[2].oneOf[i].options &&
        config.module.rules[2].oneOf[i].options.name
      ) {
        config.module.rules[2].oneOf[
          i
        ].options.name = config.module.rules[2].oneOf[i].options.name.replace(
          /(static\/|\.\[hash:8])/g,
          ""
        );
      }
    }
    config.optimization.splitChunks = {
      chunks: "all",
      name: true,
      automaticNameDelimiter: "-"
    };
    config.plugins.push({
      apply: compiler => {
        compiler.hooks.afterEmit.tap("AfterEmitPlugin", compilation => {
          fs.unlinkSync(path.resolve(__dirname, "public/index.html"));
        });
      }
    });
    return config;
  },
  paths: function(paths, env) {
    paths.appIndexJs = path.resolve(__dirname, "resources/js/index.js");
    paths.appSrc = path.resolve(__dirname, "resources/js");
    paths.appPublic = path.resolve(__dirname, "resources/public");
    paths.appHtml = path.resolve(__dirname, "resources/public/index.html");
    if (process.argv[process.argv.length - 1] !== "mix") {
      return paths;
    }
    paths.appBuild = path.resolve(__dirname, "public");
    return paths;
  }
};
