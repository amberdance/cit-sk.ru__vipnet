<template>
  <div id="app">
    <HeaderLayout v-if="$user.isAuthorized()" />

    <el-container>
      <NavigationMenu v-if="$user.isAuthorized() && $user.isLoaded()" />
      <router-view />
    </el-container>
  </div>
</template>

<script>
import HeaderLayout from "@/components/layouts/HeaderLayout";
import NavigationMenu from "@/components/common/NavigationMenu";

export default {
  components: {
    NavigationMenu,
    HeaderLayout,
  },
};
</script>
