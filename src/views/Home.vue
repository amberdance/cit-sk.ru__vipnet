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
        <router-view />
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
