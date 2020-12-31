import Vue from "vue";

const globals = {
  isLoading() {
    Vue.prototype.$isLoading = function(state = true) {
      this.$store.commit("common/isLoading", state);
    };
  },

  isAuthorized() {
    Vue.prototype.$isAuthorized = function(state) {
      this.$store.commit("common/isAuthorized", state);
    };
  },

  headerTitle() {
    Vue.prototype.$setHeaderTitle = function(title) {
      this.$store.commit("common/headerTitle", title);
    };
  }
};

for (const key in globals) {
  Vue.use(globals[key]);
}
