import Vue from "vue";
import { authServiceProvider } from "@/providers/authServiceProvider";
import { rolesAndPermissionsServiceProvider } from "@/providers/rolesAndPermissionsServiceProvider";
import { httpServiceProvider } from "@/providers/httpServiceProvider";

Plugin.install = (Vue) => {
  Vue.prototype.$isLoading = function (state = true) {
    this.$store.commit("common/isLoading", state);
  };

  Vue.prototype.$user = rolesAndPermissionsServiceProvider;
  Vue.prototype.$user.isAuthorized = () => authServiceProvider.isAuthorized();
  Vue.prototype.$login = (payload) => authServiceProvider.login(payload);
  Vue.prototype.$logout = () => authServiceProvider.logout();
  Vue.prototype.$http = httpServiceProvider;
};

Vue.use(Plugin);
