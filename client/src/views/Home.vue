<template>
  <el-container>
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
import Header from "@/components/common/Header";
import NavMenu from "@/components/common/NavMenu";

export default {
  components: { Header, NavMenu },
};
</script>
