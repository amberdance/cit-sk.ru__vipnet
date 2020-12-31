import commonMutations from "../commonMutations";
import commonActions from "@/store/commonActions";
import { dispatch } from "../../api";

export default {
  namespaced: true,

  state: {
    isLoading: false,
    isAuthorized: false,
    isAdmin: false,
    headerTitle: "Заявки на получение электронных подписей",
    sessions: []
  },

  getters: {
    getList: state => key => Object.values(state[key]),

    isLoading: state => {
      return state.isLoading;
    },

    isAuthorized: state => {
      return state.isAuthorized;
    },

    isAdmin(state) {
      return state.isAdmin;
    },

    headerTitle(state) {
      return state.headerTitle;
    }
  },

  mutations: {
    ...commonMutations,

    isLoading(state, status = true) {
      state.isLoading = status;
    },

    isAuthorized(state, val = false) {
      state.isAuthorized = val;
      state.isAdmin = localStorage.getItem("role") == 1 || false;
    },

    headerTitle(state, title) {
      state.headerTitle = title;
    }
  },

  actions: {
    ...commonActions,

    async loadData({ commit }, { route, key, payload }) {
      commit("clear", key);

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
