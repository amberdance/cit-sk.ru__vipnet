import Vue from "vue";
import VueRouter from "vue-router";
import { ADMIN } from "../values";

const originalPush = VueRouter.prototype.push;

VueRouter.prototype.push = function push(location) {
  return originalPush.call(this, location).catch((err) => err);
};

Vue.use(VueRouter);

const routes = [
  { path: "/", redirect: "/home" },
  { path: "*", redirect: "/home" },

  {
    path: "/auth",
    component: () => import("@/views/Auth"),
  },

  {
    path: "/home",
    redirect: localStorage.getItem("activeTab") || "/applist",
    component: () => import("@/views/Home"),

    children: [
      {
        path: "/applist",
        component: () => import("@/components/applist/Applist"),
      },

      {
        path: "/refs",
        component: () => import("@/components/organization/Organizations"),
      },

      {
        path: "/logs",
        meta: { roles: [ADMIN] },
        component: () => import("@/components/applist/Logs"),
      },

      {
        path: "/trash",
        meta: { roles: [ADMIN] },
        component: () => import("@/components/applist/Trash"),
      },
    ],
  },
];

const router = new VueRouter({
  routes,
});

export default router;
