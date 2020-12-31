import Vue from "vue";
import VueRouter from "vue-router";

Vue.use(VueRouter);

const routes = [
  { path: "/", redirect: "/home" },
  { path: "*", component: () => import("@/components/NotFound") },

  {
    path: "/auth",
    meta: { requiresAuth: false },
    component: () => import("@/views/Auth")
  },

  {
    path: "/home",
    redirect: localStorage.getItem("activeTab") || "/applist",
    component: () => import("@/views/Home"),

    children: [
      {
        path: "/applist",
        component: () => import("@/components/applist/Applist")
      },

      {
        path: "/refs",
        component: () => import("@/components/Reference")
      },

      {
        path: "/logs",
        meta: { isAdmin: true },
        component: () => import("@/components/applist/Logs")
      },

      {
        path: "/trash",
        meta: { isAdmin: true },
        component: () => import("@/components/applist/Trash")
      },

      {
        path: "/sessions",
        meta: { isAdmin: true },
        component: () => import("@/components/Sessions")
      },

      {
        path: "/service",
        meta: { isAdmin: true },
        component: () => import("@/components/Service")
      }
    ]
  }
];

const router = new VueRouter({
  routes
});

export default router;
