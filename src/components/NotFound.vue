<template>
  <el-container>
    <el-header>
      <Header title="страница не найдена" />
    </el-header>
    <el-container>
      <el-main>
        <div :class="$style.wrapper">
          Этой страницы не существует, вернитесь
          <el-link type="primary" style="margin:0 5px" @click="pushToSavedTab">
            откуда пришли </el-link
          >, и начните с чистого листа
        </div>
      </el-main>
    </el-container>
  </el-container>
</template>
<script>
import Header from "@/components/Header";
export default {
  components: {
    Header
  },

  methods: {
    pushToSavedTab() {
      this.$router.push(localStorage.getItem("activeTab"));
    }
  }
};
</script>
<style module>
.wrapper {
  display: flex;
  justify-content: center;
  margin-top: 10%;
}
.wrapper,
.wrapper a {
  font-size: 18px;
}
</style>
