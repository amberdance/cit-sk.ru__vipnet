<template>
  <div :class="$style.header_wrapper">
    <slot name="navMenu" v-if="$slots.navMenu" />

    <div :class="$style.header_title">
      {{ title }}
    </div>

    <el-divider direction="vertical" />

    <div :class="$style.header_icon" :title="iconTitle">
      <i v-if="isAuthorized" class="el-icon-switch-button" @click="logout"></i>
      <i v-else :class="icon"></i>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    icon: {
      type: String,
      required: false,
      default: "el-icon-unlock"
    }
  },

  computed: {
    title() {
      return this.$store.getters["common/headerTitle"];
    },

    isAuthorized() {
      return this.$auth.isAuthorized();
    },

    iconTitle() {
      if (this.isAuthorized && this.$route.path !== "/auth") return "выход";

      if (!this.isAuthorized && this.$route.path !== "/auth")
        return "авторизоваться";

      if (this.$route.path == "/auth") return "главная страница";

      return "";
    }
  },

  methods: {
    logout() {
      this.$auth.logout();
      this.$router.push("/auth");
    }
  }
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
