import Vue from "vue";
import App from "@/App";
import router from "@/router";
import store from "@/store";
import VueCookies from "vue-cookies-reactive";
import { Fragment } from "vue-frag";

import "@/providers/axiosServiceProvider";
import "@/plugins/element";
import "@/plugins/alert";
import "@/plugins/prototype";
import "@/styles/style.css";
import "@/styles/grid.css";

Vue.config.productionTip = false;

Vue.use(VueCookies);
Vue.component("Fragment", Fragment);

new Vue({
  router,
  store,
  render: (h) => h(App),
}).$mount("#app");
