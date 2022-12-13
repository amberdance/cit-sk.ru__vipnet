<template>
  <div :class="$style.header_wrapper">
    <slot name="navMenu" v-if="$slots.navMenu" />

    <div :class="$style.header_icon" :title="iconTitle">
      <i v-if="isAuthorized" class="el-icon-switch-button" @click="logout"></i>
      <i v-else class="el-icon-unlock"></i>
    </div>
  </div>
</template>
<script>
export default {
  computed: {
    isAuthorized() {
      return this.$user.isAuthorized();
    },

    iconTitle() {
      if (this.isAuthorized && this.$route.path !== "/auth") return "выход";

      if (!this.isAuthorized && this.$route.path !== "/auth")
        return "авторизоваться";

      if (this.$route.path == "/auth") return "Главная страница";

      return "";
    },
  },

  methods: {
    logout() {
      this.$auth.logout();
      this.$router.push("/auth");
    },
  },
};
</script>
<style module>
.header_wrapper {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  height: 100%;
  padding-right: 1rem;
}
.header_title {
  font-size: 18px;
  margin-right: 0.5rem;
}
.header_icon i {
  font-size: 20px;
  cursor: pointer;
  margin: 0 5px;
}
</style>
