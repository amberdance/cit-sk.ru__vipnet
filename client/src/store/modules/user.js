import mutations from "../mutations";
import { httpServiceProvider } from "@/providers/httpServiceProvider";

export default {
  namespaced: true,

  state: {
    me: {},
    users: [],
    roles: [],
    permissions: [],
    pagination: {},
  },

  getters: {
    me: (state) => Object.values(state.me)[0],
    index: (state) => (key) => Object.values(state[key]),
    pagination: (state) => state.pagination,
  },

  mutations: {
    ...mutations,
  },

  actions: {
    async index({ commit }, { route, state, params }) {
      commit("clear", state || "users");

      const data = await httpServiceProvider.get(route, params);

      commit("massSet", { key: state || "users", data });
    },

    async create({ commit }, params) {
      const data = await httpServiceProvider.post("/users", params);

      commit("set", { key: "users", data });
    },

    async update({ commit }, { id, params }) {
      const data = await httpServiceProvider.patch(`/users/${id}`, params);

      if ("password" in params) delete params.password;

      commit("update", {
        key: "users",
        data,
      });
    },

    async delete({ commit }, id) {
      await httpServiceProvider.delete(`/users/${id}`);

      commit("delete", { key: "users", id });
    },

    async massDelete({ commit }, ids) {
      await httpServiceProvider.post("/users/remove", { ids });

      commit("delete", { key: "users", id: ids });
    },

    async me({ commit }) {
      commit("clear", "me");

      const data = await httpServiceProvider.get("/auth/me");

      commit("set", { key: "me", data });
    },

    setMe({ commit }, data) {
      commit("update", { key: "me", data });
    },

    deleteMe({ commit }) {
      commit("clear", "me");
    },
  },
};
