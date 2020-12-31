import Vue from "vue";
import Vuex from "vuex";
import common from "./modules/common";
import applist from "./modules/applist";
import reference from "./modules/reference";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {},
  mutations: {},
  actions: {},
  modules: { common, applist, reference }
});
