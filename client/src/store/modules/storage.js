import mutations from "../mutations";
import { httpServiceProvider } from "@/providers/httpServiceProvider";

export default {
  namespaced: true,

  state: {
    applist: [],
    signatures: [],
    pagination: {},
  },

  getters: {
    index: (state) => (key) => Object.values(state[key]),
    pagination: (state) => state.pagination,
  },

  mutations,

  actions: {
    async index({ commit }, { entity, params }) {
      commit("clear", entity);

      if (entity == "applist") commit("clear", "pagination");

      const data = await httpServiceProvider.get(`/${entity}`, params);

      commit("massSet", { key: entity, data });
    },

    async create({ commit }, { entity, params }) {
      const data = await httpServiceProvider.post(`/${entity}`, params);

      commit("set", { key: entity, data });
    },

    async update({ commit }, { entity, id, params }) {
      const data = await httpServiceProvider.patch(`/${entity}/${id}`, params);

      commit("update", {
        key: entity,
        data,
      });
    },

    async massUpdate({ commit }, { entity, params }) {
      const data = await httpServiceProvider.post(`/${entity}/update`, params);

      data.forEach((item) =>
        commit("update", {
          key: entity,
          data: item,
        })
      );
    },

    async delete({ commit }, { entity, id }) {
      await httpServiceProvider.delete(`/${entity}/${id}`);

      commit("delete", {
        key: entity,
        id,
      });
    },

    async massDelete({ commit }, { entity, ids }) {
      await httpServiceProvider.post(`/${entity}/remove`, { ids });

      commit("delete", {
        key: entity,
        id: ids,
      });
    },
  },
};
