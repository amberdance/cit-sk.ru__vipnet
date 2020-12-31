import commonMutations from "../commonMutations";
import commonActions from "../commonActions";
import { dispatch } from "../../api";

export default {
  namespaced: true,

  state: {
    items: []
  },

  getters: {
    list: state => Object.values(state.items)
  },

  mutations: {
    ...commonMutations
  },

  actions: {
    ...commonActions,

    async loadData({ commit }, { route, key = "items", payload }) {
      if (route === "get-list") commit("clear", key);

      route = `/reference/${route}`;

      const responseData = await dispatch.HTTPPost({ route, payload });

      if (Array.isArray(responseData)) {
        responseData.forEach(item => {
          commit("set", { key, props: item });
        });

        return;
      }

      commit("set", { key, props: responseData });
    }
  }
};
