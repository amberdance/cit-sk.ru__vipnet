<template>
  <el-container>
    <Preloader v-show="isLoading" />

    <el-header>
      <Header>
        <template #navMenu>
          <NavMenu />
        </template>
      </Header>
    </el-header>

    <el-container>
      <el-main>
        <slot>
          <router-view />
        </slot>
      </el-main>
    </el-container>
  </el-container>
</template>
<script>
import Preloader from "@/components/Preloader";
import Header from "@/components/Header";
import NavMenu from "@/components/NavMenu";

export default {
  components: { Preloader, Header, NavMenu },

  computed: {
    isLoading() {
      return this.$store.getters["common/isLoading"];
    }
  }
};
</script>

<style module>
.right_aside {
  display: flex;
  justify-content: center;
  z-index: 1;
  height: 100vh;
  box-shadow: 1px -4px 10px 0px #0e000024;
}
</style>
