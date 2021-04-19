<template>
  <div :class="$style.form_wrapper">
    <template v-if="$slots.title">
      <slot name="title" />
      <el-divider />
    </template>

    <template v-if="$slots.inputGroup">
      <slot name="inputGroup" />
      <el-divider />
    </template>

    <template v-if="$slots.buttonGroup">
      <div :class="$style.btn_group">
        <slot name="buttonGroup" />
      </div>
    </template>

    <template v-if="$slots.summaryRow">
      <el-divider />
      <div class="form-label form-item a-center">
        <slot name="summaryRow" />
      </div>
      <el-divider />
    </template>
  </div>
</template>

<style module>
.form_wrapper {
  font-size: 14px;
  color: gray;
  display: flex;
  align-items: center;
  flex-direction: column;
  padding: 0.7rem;
  margin-top: 50%;
}
.form_wrapper div {
  width: 100% !important;
}
.btn_group {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 100%;
}
.btn_group button {
  margin: 0.2rem 0;
  margin-left: 0 !important;
  width: 100%;
  font-size: 13px;
}
.btn_group span:first-child {
  min-width: 155px;
}
.date_picker {
  width: 220px !important;
}
.date_picker input {
  background-color: #fbfbfb;
}
</style>
