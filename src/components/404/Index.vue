<template>
  <component :is="notFoundComponent" />
</template>

<script>
export default {
  data() {
    return {
      notFoundComponent: null,
      coef: 0.8
    };
  },

  created() {
    this.notFoundComponent =
      Number(Math.random().toFixed(2)) < this.coef
        ? () => import("./Blue")
        : () => import("./Red");
  }
};
</script>
