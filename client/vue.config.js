process.env.VUE_APP_VERSION = require("./package.json").version;
process.env.VUE_APP_TITLE = "Заявки на получение ЭЦП";
process.env.VUE_APP_API_URL =
  process.env.NODE_ENV == "development"
    ? "http://crp.ru/api"
    : "http://cryptoapps.citsk.ru/api";

module.exports = {
  productionSourceMap: false,
  lintOnSave: process.env.NODE_ENV !== "production",

  chainWebpack: (config) =>
    config.plugin("html").tap((args) => {
      args[0].title = process.env.VUE_APP_TITLE;
      return args;
    }),

  configureWebpack: (config) => {
    if (process.env.NODE_ENV === "production") {
      config.output.filename =
        "js/[name].[contenthash:8].min.js" +
        "?v=" +
        process.env.VUE_APP_VERSION;
      config.output.chunkFilename =
        "js/[name].[contenthash:8].min.js" +
        "?v=" +
        process.env.VUE_APP_VERSION;
    } else {
      config.output.filename = "js/[name].js";
      config.output.chunkFilename = "js/[name].js";
    }
  },
};
