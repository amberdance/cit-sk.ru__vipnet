<template>
  <div
    v-if="$parent.$el"
    :class="['control_bar', visible ? 'active' : '']"
    :style="{ width: controlBarWidth }"
  >
    <slot />
  </div>
</template>

<script>
export default {
  props: {
    visible: {
      type: Boolean,
      required: true,
    },
  },

  data() {
    return {
      navigationMenuResizeObserver: null,
      filterMenuResizeObserver: null,
      navigationMenuWidth: 0,
      filterMenuWidth: 0,
    };
  },

  computed: {
    controlBarWidth() {
      return `calc(100% - ${
        this.navigationMenuWidth + this.filterMenuWidth + 17
      }px)`;
    },
  },

  mounted() {
    try {
      //TODO: Убрать исключение при logout (Uncaught TypeError: Cannot read properties of null (reading 'offsetWidth'))
      this.navigationMenuResizeObserver = new ResizeObserver(
        () =>
          (this.navigationMenuWidth =
            this.$parent.$parent.$parent.$parent.$el.previousElementSibling.offsetWidth)
      );

      this.navigationMenuResizeObserver.observe(
        this.$parent.$parent.$parent.$parent.$el.previousElementSibling
      );

      this.filterMenuResizeObserver = new ResizeObserver(
        () =>
          (this.filterMenuWidth =
            this.$parent.$el.nextElementSibling.offsetWidth)
      );

      this.filterMenuResizeObserver.observe(
        this.$parent.$el.nextElementSibling
      );
    } catch (e) {
      //
    }
  },

  beforeDestroy() {
    try {
      this.navigationMenuResizeObserver.unobserve(
        this.$parent.$parent.$parent.$parent.$parent.$el.firstChild
      );

      this.filterMenuResizeObserver.unobserve(
        this.$parent.$el.nextElementSibling
      );
    } catch (e) {
      //
    }
  },
};
</script>

<style scoped>
.control_bar {
  position: fixed;
  background-color: var(--color-bg_primary);
  bottom: 0;
  z-index: 11;
  display: flex;
  min-height: 50px;
  align-items: center;
  border-top: 2px #ffffff solid;
  padding: 0.7rem 1rem;
  transition: transform 100ms ease 0s;
  -webkit-transform: translateY(100%);
  -ms-transform: translateY(100%);
  transform: translateY(100%);
  -webkit-box-shadow: 1px -3px 9px 0px #00000024;
  box-shadow: 1px -3px 9px 0px #00000024;
}

.control_bar.active {
  transform: translateY(0px);
  -webkit-transform: translateY(0px);
  -ms-transform: translateY(0px);
}
</style>
