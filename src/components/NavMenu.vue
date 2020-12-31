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
      <template v-if="subMenu.length">
        <el-submenu
          v-for="item in subMenu"
          :key="item.index"
          :index="item.index"
        >
          <template #title>
            <i class="el-icon-menu"></i>
            <span>{{ item.title }}</span>
          </template>

          <el-menu-item-group>
            <el-menu-item
              v-for="children in item.childrens"
              :key="children.index"
              :index="children.route"
              @click="saveActiveTab"
              >{{ children.label }}</el-menu-item
            >
          </el-menu-item-group>
        </el-submenu>
      </template>

      <el-menu-item
        v-for="item in navMenu"
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
export default {
  data() {
    return {
      rootMenuItems: [
        {
          title: "Заявки",
          isAdmin: false,
          index: "1",
          route: "/applist",
          icon: "el-icon-s-custom"
        },

        {
          title: "Организации",
          isAdmin: false,
          index: "2",
          route: "/refs",
          icon: "el-icon-s-custom"
        },

        {
          title: "События",
          isAdmin: true,
          index: "3",
          route: "/logs",
          icon: "el-icon-date"
        },

        {
          title: "Удаленные заявки",
          isAdmin: true,
          index: "4",
          route: "/trash",
          icon: "el-icon-delete"
        },

        {
          title: "Сессии",
          isAdmin: true,
          index: "5",
          route: "/sessions",
          icon: "el-icon-connection"
        }
      ],

      subMenuItems: []
    };
  },

  computed: {
    isAdmin() {
      return this.$isAdmin();
    },

    navMenu() {
      return this.isAdmin
        ? this.rootMenuItems
        : this.rootMenuItems.filter(item => !item.isAdmin);
    },

    subMenu() {
      return this.isAdmin
        ? this.subMenuItems
        : this.subMenuItems.filter(item => !item.isAdmin);
    }
  },

  methods: {
    saveActiveTab({ index }) {
      localStorage.setItem("activeTab", index);
    },

    getSavedTab(defaultTab = "/applist") {
      return localStorage.getItem("activeTab") || defaultTab;
    }
  }
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
