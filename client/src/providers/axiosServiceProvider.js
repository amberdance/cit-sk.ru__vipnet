import axios from "axios";
import router from "@/router";
import store from "@/store";
import VueCookies from "vue-cookies-reactive";
import { authServiceProvider } from "@/providers/authServiceProvider";
import { rolesAndPermissionsServiceProvider } from "@/providers/rolesAndPermissionsServiceProvider";
import { responseManage, errorManage } from "@/helpers/httpResponseHelper";
import { Loading, MessageBox } from "element-ui";

axios.defaults.baseURL = process.env.VUE_APP_API_URL;

axios.interceptors.request.use(
  (request) => {
    const accessToken = VueCookies.get("access_token");

    if (accessToken) request.headers.Authorization = `Bearer ${accessToken}`;

    return request;
  },

  (error) => Promise.reject(error)
);

axios.interceptors.response.use(
  (response) => responseManage(response),
  (error) => (error.response.status == 401 ? logout() : errorManage(error))
);

router.beforeEach(async (to, from, next) => {
  if (authServiceProvider.isAuthorized()) {
    const version = VUE_APP_VERSION;

    if (version !== localStorage.getItem("version"))
      try {
        await MessageBox({
          title:
            "Версия приложения устарела, для корректной и стабильной работы перезагрузите страницу",
          customClass: "p-3",
          confirmButtonClass: "el-button--success el-button--mini",
          closeOnClickModal: false,
          closeOnPressEscape: false,
          closeOnHashChange: false,
        });
      } catch (e) {
        return;
      } finally {
        localStorage.setItem("version", version);
        resetCache();
      }

    if (to.path == "/auth") return next({ path: from.path });
    if (to.path == "/logout") return next();

    if (!store.getters["user/me"]) {
      try {
        Loading.service();
        await store.dispatch("user/me");
      } catch (e) {
        if ("code" in e && e.code == 401) logout();
        else console.error(e);
      } finally {
        Loading.service().close();
      }
    }

    if (
      _.has(to.meta, "roles") &&
      !rolesAndPermissionsServiceProvider.hasRole(to.meta.roles)
    )
      return next({ path: from.path });

    return next();
  }

  if (to.path == "/auth") return next();

  next("/auth");
});

const logout = () => {
  router.push("/auth");
  store.dispatch("user/deleteMe");
  authServiceProvider.purge();
};

const resetCache = () => {
  localStorage.removeItem("app_cache");
  VueCookies.remove("currentMenu");
  location.reload(true);
};
