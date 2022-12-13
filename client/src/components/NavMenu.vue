<template>
  <div :class="$style.menu_wrapper">
    <el-menu
      ref="navMenu"
      router
      mode="horizontal"
      background-color="#2bb590"
      menu-trigger="click"
      unique-opened
      text-color="#ffffff"
      :default-active="getSavedTab()"
    >
      <el-menu-item
        v-for="item in NAV_MENU"
        :key="item.index"
        :index="item.route"
        @click="saveActiveTab"
      >
        <i :class="item.icon || 'el-icon-menu'"></i>
        <template #title>{{ item.title }}</template>
      </el-menu-item>
    </el-menu>
  </div>
</template>
<script>
import { NAV_MENU } from "@/values";

export default {
  data() {
    return {
      NAV_MENU: NAV_MENU,
    };
  },

  methods: {
    saveActiveTab({ index }) {
      localStorage.setItem("activeTab", index);
    },

    getSavedTab(defaultTab = "/applist") {
      return localStorage.getItem("activeTab") || defaultTab;
    },
  },
};
</script>
<style module>
.menu_wrapper {
  margin-right: auto;
}

.menu_wrapper li,
.menu_wrapper i {
  color: #ffffff !important;
}
</style>
