import { httpServiceProvider } from "@/providers/httpServiceProvider";
import mutations from "@/store/mutations";

export default {
  namespaced: true,

  state: {
    organizations: [],
    pagination: {},
  },

  getters: {
    index: (state) => Object.values(state.organizations),
    pagination: (state) => state.pagination,
  },

  mutations,

  actions: {
    async index({ commit }, params) {
      commit("clear", "organizations");
      commit("clear", "pagination");

      const data = await httpServiceProvider.get("/organizations", params);

      commit("massSet", { key: "organizations", data });
    },

    async create({ commit }, params) {
      const data = await httpServiceProvider.post("/organizations", params);

      commit("set", {
        key: "organizations",
        data,
      });

      return data;
    },

    async update({ commit }, { id, params }) {
      const data = await httpServiceProvider.patch(
        `/organizations/${id}`,
        params
      );

      commit("update", {
        key: "organizations",
        data: data,
      });
    },

    async delete({ commit }, id) {
      await httpServiceProvider.delete(`/organizations/${id}`);

      commit("delete", { key: "organizations", id });
    },

    async massDelete({ commit }, ids) {
      await httpServiceProvider.post("/organizations/remove", { ids });

      commit("delete", { key: "organizations", id: ids });
    },
  },
};
