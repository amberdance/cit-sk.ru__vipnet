<template>
  <el-container>
    <el-header>
      <Header />
    </el-header>

    <div :class="$style.wrapper">
      <el-form :model="formData" :rules="rules" ref="form" :class="$style.form">
        <el-form-item label="Логин" prop="login"
          ><el-input
            v-model="formData.login"
            size="small"
            prefix-icon="el-icon-user"
          ></el-input
        ></el-form-item>

        <el-form-item label="Пароль" prop="password"
          ><el-input
            v-model="formData.password"
            size="small"
            type="password"
            prefix-icon="el-icon-lock"
            show-password
          ></el-input
        ></el-form-item>

        <div>
          <el-button
            size="small"
            style="width: 100%"
            :loading="isLoading"
            @click="authorize"
            >войти</el-button
          >
        </div>
      </el-form>
    </div>
  </el-container>
</template>
<script>
import Header from "@/components/Header";

export default {
  components: { Header },

  data() {
    return {
      isLoading: false,

      formData: {
        login: null,
        password: null
      },

      rules: {
        login: {
          required: true,
          trigger: "blur",
          message: "введите логин"
        },

        password: {
          required: true,
          trigger: "blur",
          message: "введите пароль"
        }
      }
    };
  },

  created() {
    this.$setHeaderTitle("Авторизация");
  },

  methods: {
    async authorize() {
      await this.$refs.form.validate();
      this.isLoading = true;

      try {
        await this.$auth.login(this.formData);
        this.$router.push("/home");
      } catch (e) {
        if (e === "Bad request") return this.$onError();

        if (e.config.response.status === 401) {
          return this.$onError("Все - таки что - то неверно");
        }

        if (e.config.response.status === 403) {
          return this.$onError("Доступ запрещен");
        }

        return this.$onError();
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>
<style module>
.form {
  padding: 2rem;
  background-color: #f9f9f9ba;
}
.wrapper {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 94vh;
  background-color: #eaeaea;
}
</style>
