<template>
  <el-header>
    <div class="header_wrapper">
      <div class="header_inner shadowed row">
        <div class="logo_wrapper col-2">
          <a href="https://cit-sk.ru" target="_blank">
            <img src="@/assets/citsk_logo.webp" class="icon medium" />
          </a>
        </div>

        <div class="col-9"></div>

        <div class="col">
          <el-dropdown
            trigger="click"
            class="user_wrapper"
            v-if="user"
            @command="handleDropdownCommand"
          >
            <div class="user_menu rounded">
              <i class="el-icon-user"></i>
              <span>{{ user.login }}</span>
              <i class="el-icon-arrow-down el-icon--right"></i>
              <el-dropdown-menu>
                <el-dropdown-item command="logout">
                  <div class="user_menu__item">
                    <img src="@/assets/icons/common/logout.svg" />
                    <span>Выход</span>
                  </div>
                </el-dropdown-item>
              </el-dropdown-menu>
            </div>
          </el-dropdown>
        </div>
      </div>
    </div>
  </el-header>
</template>

<script>
export default {
  computed: {
    user() {
      return this.$store.getters["user/me"];
    },
  },

  methods: {
    handleDropdownCommand(command) {
      if (_.has(this, command)) return this[command]();
    },

    logout() {
      this.$logout();
      this.$store.dispatch("user/forgetMe");
      this.$router.push("/logout");
    },
  },
};
</script>

<style scoped>
.header_wrapper {
  background: url("../../assets/background.webp");
}

.header_inner {
  padding: 0.5rem 1rem;
  border-bottom: 2px white solid;
  align-items: center;
  background-color: #182140d9;
}

.header_inner img,
.header_inner i {
  cursor: pointer;
}

.user_wrapper,
.logo_wrapper {
  max-width: 220px;
}
.user_wrapper {
  display: flex;
  align-items: center;
  margin-left: auto;
  justify-content: end;
  color: #ffffff;
}

.user_menu {
  cursor: pointer;
  padding: 0.5rem;
  transition: 0.3s background-color linear;
}

.user_menu i {
  margin-right: 0.5rem;
}

.user_menu:hover {
  background-color: #182140d9;
}

.user_menu__item {
  display: flex;
  align-items: center;
}

.user_menu__item img {
  height: 20px;
  margin-right: 0.5rem;
}
</style>
