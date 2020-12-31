"use strict";

import Vue from "vue";
import axios from "axios";
import router from "../router";
import { auth } from "@/plugins/auth";
import { responseManage, errorManage } from "@/utils/responseManage";
import { API_BASE_URL } from "../api-config";

axios.defaults.baseURL = API_BASE_URL;

axios.interceptors.request.use(
  request => {
    const accessToken = auth.getAccessToken();
    const userRole = auth.getRole();

    if (accessToken && (userRole === 1 || userRole === 2)) {
      request.headers.Authorization = `Bearer ${accessToken}`;
    }

    return request;
  },

  error => Promise.reject(error)
);

axios.interceptors.response.use(
  response => responseManage(response),

  error => {
    if (error.response && error.response.status === 401) {
      auth.purge();
      return router.push("/auth");
    }

    return errorManage(error);
  }
);

router.beforeEach((to, from, next) => {
  if (auth.isAuthorized() && to.fullPath == "/auth") return next("/home");
  if (to.fullPath == "/auth") return next();
  if ("isAdmin" in to.meta && auth.getRole() !== 1)
    return next(localStorage.getItem("activeTab") ?? "home");

  if (auth.isAuthorized()) return next();

  next("/auth");
});

Plugin.install = Vue => {
  Vue.prototype.$auth = auth;
  Vue.prototype.$isAdmin = () => auth.getRole() === 1;
  Vue.prototype.$isManager = () => auth.getRole() === 2;

  Vue.prototype.$HTTPPost = async ({ route, payload }) => {
    const { data } = await axios.post(route, payload);

    return data;
  };

  Vue.prototype.$HTTPGet = async ({ route, payload }) => {
    const { data } = await axios.get(route, { params: payload });

    return data;
  };
};

Vue.use(Plugin);

export default Plugin;
