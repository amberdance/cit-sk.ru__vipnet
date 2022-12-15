<template>
  <el-container>
    <el-main>
      <slot />

      <div
        v-if="$slots.controlBar"
        class="control_bar"
        :style="{ width: controlBarWidth }"
      >
        <slot name="controlBar" />
      </div>
    </el-main>

    <el-aside
      v-if="$slots.filterPanel"
      class="right_aside"
      :style="{ width: filterPanelWidth + 'px' }"
      @onFilterCollapse="handleFilterCollapse(true)"
    >
      <div v-show="!$cookies.get('filterCollapsed')" class="p-3">
        <slot name="filterPanel" />
      </div>

      <div
        v-show="$cookies.get('filterCollapsed')"
        class="collapse_btn"
        @click="handleFilterCollapse(false)"
      >
        <i class="el-icon-caret-left"></i>
      </div>
    </el-aside>
  </el-container>
</template>

<script>
export default {
  props: {
    filterWidth: {
      type: Number,
      required: false,
      default: 220,
    },
  },

  computed: {
    filterPanelWidth() {
      return this.$cookies.get("filterCollapsed") ? 50 : this.filterWidth;
    },
  },

  methods: {
    handleFilterCollapse(state) {
      this.$cookies.set("filterCollapsed", state);
    },
  },
};
</script>

<style scoped>
.right_aside {
  display: flex;
  justify-content: center;
  z-index: 10;
  height: 100vh;
  box-shadow: -4px 0 9px -2px #00000036;
  transition: width 0.2s linear;
}

.collapse_btn {
  display: flex;
  width: 100%;
  cursor: pointer;
  justify-content: center;
  align-items: center;
  color: #909399;
  transition: background-color 0.2s linear;
}

.collapse_btn:hover {
  color: #000000;
  background-color: var(--color-divider);
}
</style>
