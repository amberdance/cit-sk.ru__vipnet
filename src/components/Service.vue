<template>
  <div></div>
</template>

<script>
export default {
  async created() {
    try {
      this.$isLoading();

      await this.$HTTPGet({
        route: "/service/load-references"
      });
    } catch (e) {
      console.log(e);
    } finally {
      this.$isLoading(false);
    }
  }
};
</script>
