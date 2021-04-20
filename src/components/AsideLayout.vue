<template>
  <div :class="$style.form_wrapper">
    <div style="display:flex;">
      <template v-if="$slots.title">
        <slot name="title" />
      </template>

      <template v-if="$slots.inputGroup">
        <slot name="inputGroup" />
      </template>
    </div>

    <template v-if="$slots.buttonGroup">
      <div :class="$style.btn_group">
        <slot name="buttonGroup" />
      </div>
    </template>

    <template v-if="$slots.summaryRow">
      <div class="form-label form-item a-center">
        <slot name="summaryRow" />
      </div>
    </template>
  </div>
</template>

<style module>
.form_wrapper {
  font-size: 14px;
  color: gray;
  display: flex;
  flex-direction: column;
  padding: 0.7rem;
}
.form_wrapper div {
  width: 100% !important;
  margin: 0 0.2rem;
}
.btn_group {
  display: flex;
  margin: 0.5rem 0 !important;
}
.btn_group button {
  margin: 0.2rem 0;
  margin-left: 0 !important;
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
