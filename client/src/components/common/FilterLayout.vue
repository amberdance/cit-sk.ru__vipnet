<template>
  <el-aside class="right_aside">
    <div class="p-3">
      <div class="filter_wrapper">
        <div class="block_wrapper">
          <div class="d-flex align-center">
            <div class="heading">Фильтр</div>
            <img src="@/assets/icons/common/filter.svg" class="icon mini" />
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
    </div>
  </el-aside>
</template>

<style scoped>
.right_aside {
  display: flex;
  justify-content: center;
  z-index: 10;
  height: 100vh;
  box-shadow: -4px 0 9px -2px #00000036;
  transition: width 0.2s linear;
  width: var(--filter-width) !important;
}

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

::v-deep .btn_group button {
  margin-left: 0 !important;
  margin-top: 0.5rem;
  width: 100% !important;
}

::v-deep .el-date-editor {
  width: 200px;
}
</style>
