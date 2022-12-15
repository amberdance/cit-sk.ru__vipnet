<template>
  <el-aside class="aside-left">
    <el-menu
      ref="menu"
      mode="vertical"
      class="menu_wrapper"
      router
      unique-opened
      menu-trigger="click"
      :collapse="isMenuCollapsed"
      :collapse-transition="hasCollapseTransition"
      :default-active="currentActiveMenu"
    >
      <fragment v-for="item in menu" :key="item.index">
        <el-submenu
          v-if="'childrens' in item"
          :key="item.index"
          :index="item.index"
          :popper-append-to-body="true"
        >
          <template #title>
            <img
              v-if="'img' in item && item.img"
              :src="require(`@/assets/icons/common/${item.img}`)"
            />
            <i v-else :class="item.icon || 'el-icon-menu'"></i>
            <span>{{ item.title }}</span>
          </template>

          <div
            class="menu-item_wrapper d-flex align-center"
            v-for="children in item.childrens"
            :key="children.index"
          >
            <el-menu-item :index="children.route" @click="onMenuItemClick">
              <template #title>
                <img
                  v-if="'img' in children && children.img"
                  :src="require(`@/assets/icons/common/${children.img}`)"
                />
                <i v-else :class="children.icon || 'el-icon-menu'"></i>
                <span>{{ children.title }}</span>
              </template>
            </el-menu-item>

            <el-tooltip
              effect="light"
              content="Открыть в новой вкладке"
              placement="right-end"
              :open-delay="300"
            >
              <span class="new-tab hidden" @click="openNewTab(children)">
                <i class="el-icon-copy-document"></i>
              </span>
            </el-tooltip>
          </div>
        </el-submenu>

        <el-menu-item
          v-else
          :key="item.index"
          :index="item.route"
          @click="onMenuItemClick"
        >
          <img
            v-if="'img' in item"
            :src="require(`@/assets/icons/common/${item.img}`)"
          />
          <i v-else :class="item.icon || 'el-icon-menu'"></i>
          <template #title
            ><span>{{ item.title }}</span></template
          >
        </el-menu-item>
      </fragment>

      <el-menu-item
        class="collapse"
        index="collapse"
        route="/collapse"
        @click="handleCollapseMenu"
      >
        <i
          :class="
            isMenuCollapsed ? 'el-icon-caret-right' : 'el-icon-caret-left'
          "
        ></i>
        <template #title>
          <span>{{ isMenuCollapsed ? "Развернуть" : "Свернуть" }}</span>
        </template>
      </el-menu-item>
    </el-menu>
  </el-aside>
</template>

<script>
import MenuMixin from "@/mixins/MenuMixin";

export default {
  mixins: [MenuMixin],

  data() {
    return {
      hasCollapseTransition: true,
    };
  },

  created() {
    this.createMenu();
  },

  computed: {
    isMenuCollapsed() {
      return this.$cookies.get("menuCollapsed");
    },

    currentActiveMenu() {
      return this.$cookies.get("currentMenu");
    },
  },

  methods: {
    openNewTab(data) {
      this.$store.dispatch("common/setBreadcrumb", data);
      this.$router.push(data.route);
      this.$cookies.set("currentMenu", data.route);
    },

    onMenuItemClick({ index }) {
      if (index == "collapse") return;

      this.$cookies.set("currentMenu", index);
    },

    handleCollapseMenu() {
      this.$refs.menu.activeIndex = this.$cookies.get("currentMenu");
      this.$cookies.set("menuCollapsed", !this.$cookies.get("menuCollapsed"));
    },

    createMenuItemImage(img) {
      return process.env.BASE_URL + "../../assets/icons/common/" + img;
    },
  },
};
</script>

<style scoped>
.aside-left {
  width: unset !important;
  z-index: 10;
  box-shadow: 4px 0 9px -2px #00000036;
  margin-top: 0 !important;
  border: none;
  height: 100vh;
}

.menu_wrapper {
  border-right: 0;
}

.menu_wrapper:not(.el-menu--collapse) {
  width: 220px;
}

::v-deep.el-submenu img,
::v-deep.el-menu-item img {
  height: 18px;
  margin-right: 7px;
  margin-left: 3px;
  opacity: 0.6;
}

::v-deep.el-menu-item.is-active i {
  font-weight: bold;
  color: black;
}

::v-deep.el-menu-item.is-active img {
  opacity: 1;
}

.menu-item_wrapper:hover .new-tab {
  visibility: visible;
  opacity: 1;
}

.new-tab {
  cursor: pointer;
  position: absolute;
  right: 10px;
  margin-left: auto;
  transition: opacity 0.3s ease-in;
}
</style>
