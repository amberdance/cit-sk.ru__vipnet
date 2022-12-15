<template>
  <div class="filter_wrapper">
    <div class="block_wrapper">
      <template v-if="$slots.title">
        <slot name="title"></slot>
      </template>

      <div v-else class="d-flex align-center">
        <img src="@/assets/icons/common/filter.svg" class="icon mini" />
        <div class="heading">Фильтр</div>
        <el-tooltip content="Свернуть">
          <i
            class="el-icon-caret-right"
            style="
              font-size: 25px;
              color: var(--color-font_primary);
              cursor: pointer;
            "
            @click="hidePanel"
          ></i>
        </el-tooltip>
      </div>
      <el-divider />
    </div>

    <div class="block_wrapper">
      <div>
        <slot />
      </div>

      <template v-if="$slots.buttons">
        <div class="btn_group">
          <slot name="buttons" />
        </div>
      </template>

      <template v-if="$slots.footer">
        <el-divider />
        <div class="form-label form-item a-center">
          <slot name="footer" />
        </div>
      </template>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    hidePanel() {
      !this.$parent.$slots.default
        ? this.$parent.$parent.$emit("onFilterCollapse")
        : this.$parent.$emit("onFilterCollapse");
    },
  },
};
</script>

<style scoped>
.filter_wrapper {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
}

.block_wrapper {
  display: flex;
  align-items: center;
  flex-direction: column;
}

.block_wrapper .el-switch {
  display: flex;
  justify-content: space-between;
}

.block_wrapper div {
  width: 100% !important;
}

.block_wrapper button,
.block_wrapper .el-select,
.block_wrapper .form-label,
.block_wrapper .el-input,
.block_wrapper .el-date-editor {
  margin-bottom: 0.5rem;
}

.btn_group {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  margin-top: 0.5rem;
}

.btn_group button {
  margin-left: 0 !important;
}

.btn_group button {
  width: 100%;
}
</style>
