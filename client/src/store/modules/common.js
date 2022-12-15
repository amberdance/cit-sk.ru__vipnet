import mutations from "../mutations";

export default {
  namespaced: true,

  state: {
    isLoading: false,
  },

  getters: {
    isLoading: (state) => {
      return state.isLoading;
    },
  },

  mutations: {
    ...mutations,

    isLoading(state, status = true) {
      state.isLoading = status;
    },
  },
};
