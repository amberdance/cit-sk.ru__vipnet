import Vue from "vue";
import VueRouter from "vue-router";

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
    path: "/logout",
    component: () => import("@/views/Auth"),
  },

  {
    path: "/home",
    redirect: localStorage.getItem("activeTab") || "/applist",
    component: () => import("@/views/Home"),
  },

  {
    path: "/applist",
    component: () => import("@/components/entity/Applist"),
  },

  {
    path: "/organizations",
    component: () => import("@/components/entity/Organizations"),
  },
];

const router = new VueRouter({
  routes,
});

export default router;
