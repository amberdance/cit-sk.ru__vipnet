import commonMutations from "../commonMutations";
import commonActions from "../commonActions";
import { dispatch } from "../../api";

export default {
  namespaced: true,

  state: {
    items: [],
    logs: [],
    trash: []
  },

  getters: {
    getList: state => key => Object.values(state[key])
  },

  mutations: {
    ...commonMutations
  },

  actions: {
    ...commonActions,

    async loadData({ commit }, { route, key = "items", payload }) {
      route = `/applist/${route}`;

      if (route != "/applist/add") commit("clear", key);

      const responseData = await dispatch.HTTPPost({ route, payload });

      if (Array.isArray(responseData)) {
        responseData.forEach(item => {
          commit("set", { key, props: item });
        });

        return;
      }

      commit("set", { key, props: responseData });
    },

    async update({ commit }, { payload }) {
      const {
        id,
        personCount,
        receptionDate,
        referenceId,
        signatureTypeId,
        note
      } = payload;

      const updateFields = {
        id,
        personCount,
        receptionDate,
        referenceId,
        signatureTypeId,
        note
      };

      await dispatch.HTTPPost({
        route: "/applist/update",
        payload: updateFields
      });

      commit("update", {
        key: "items",
        props: { id: payload.id, ...payload }
      });
    },

    async restore({ commit }, { payload }) {
      await dispatch.HTTPPost({
        route: "/applist/restore",
        payload
      });

      if (Array.isArray(payload.id)) {
        return payload.id.forEach(id => {
          commit("update", {
            key: "trash",
            props: { id, isActive: 1 }
          });

          commit("remove", { key: "trash", id });
        });
      }

      commit("update", {
        key: "trash",
        props: { id: payload.id, isActive: 1 }
      });

      commit("remove", { key: "trash", id: payload.id });
    }
  }
};
