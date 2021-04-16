<template>
  <div>
    <div :class="$style.overlay"></div>

    <div :class="$style.wrapper">
      <img :class="$style.logo" src="../assets/citsk_logo.png" />

      <div :class="$style.title">
        Заявки на получение электронно-цифровых подписей
      </div>

      <el-form ref="form" :model="formData" :rules="formRules">
        <el-form-item prop="login">
          <el-input
            v-model="formData.login"
            placeholder="логин"
            autofocus
            prefix-icon="el-icon-user"
          />
        </el-form-item>

        <el-form-item prop="password">
          <el-input
            type="password"
            v-model="formData.password"
            placeholder="пароль"
            prefix-icon="el-icon-lock"
            show-password
          />
        </el-form-item>

        <div :class="$style.btn_wrapper">
          <el-button
            native-type="submit"
            size="small"
            :loading="isLoading"
            @click="authorize"
            >войти</el-button
          >
        </div>
      </el-form>

      <div :class="$style.developer">
        <div>2020 - {{ new Date().getFullYear() }}</div>
        <div>ГКУ СК "Краевой центр информтехнологий"</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      isLoading: false,

      formData: {
        login: "",
        password: ""
      },

      formRules: {
        login: [
          {
            required: true,
            message: " "
          }
        ],

        password: [
          {
            required: true,
            message: " "
          }
        ]
      }
    };
  },

  created() {
    if (this.$isAuthorized()) this.$router.push("/home");
  },

  methods: {
    async authorize() {
      await this.$refs.form.validate();
      this.isLoading = true;

      try {
        await this.$auth.login(this.formData);
        this.$router.push("/home");
      } catch (e) {
        if (e.config.response.status == 401)
          return this.$onError(
            "Не удалось авторизоваться под указанными учетными данными",
            4000
          );

        if (e.config.response.status == 403)
          return this.$onError("Доступ запрещен");

        this.$onError();
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>

<style module>
.overlay {
  position: absolute;
  width: 100%;
  height: 100%;
  background-color: #000401c2;
}
.wrapper {
  color: white;
  background: url("../assets/background.png");
  background-repeat: no-repeat;
  background-position: center;
  background-size: cover;
  display: flex;
  justify-content: center;
  height: 100vh;
  align-items: center;
  flex-direction: column;
}
.title {
  text-align: center;
  border-bottom: 1px white solid;
  padding-bottom: 1rem;
  text-transform: uppercase;
  font-size: 18px;
  padding: 1rem 0;
}
.title,
.developer {
  width: 350px;
}
.title,
.developer,
form {
  z-index: 999;
}
form {
  padding: 1rem;
}
.btn_wrapper {
  display: flex;
  justify-content: center;
}
.btn_wrapper button {
  width: 100%;
}
.developer {
  border-top: 1px white solid;
  padding-top: 1rem;
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  font-size: 14px;
}
.overlay_logo {
  background-color: #525252c2;
}
.overlay_logo,
.logo {
  position: absolute;
  width: 50%;
  top: 15%;
  left: 25%;
}
</style>
