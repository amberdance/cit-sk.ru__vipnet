<template>
  <el-dialog
    width="40%"
    :visible.sync="isShowed"
    :title="`Заявка # ${id}`"
    @close="hide"
  >
    <el-row type="flex" :gutter="40">
      <el-col :span="12" :class="$style.wrapper">
        <div>Было:</div>
        <div
          v-for="(item, index) in history.oldData"
          :key="index"
          :class="$style.form_item"
        >
          <i class="el-icon-right" />
          <div :class="$style.form_label">{{ item.property }}:</div>
          <div :class="$style.form_value">{{ item.value }}</div>
        </div>
      </el-col>

      <el-col :span="12" :class="$style.wrapper">
        <div>Стало:</div>
        <div
          v-for="item in history.newData"
          :key="item.property"
          :class="$style.form_item"
        >
          <div :class="$style.form_label">{{ item.property }}:</div>
          <div :class="$style.form_value">{{ item.value }}</div>
        </div>
      </el-col>
    </el-row>
  </el-dialog>
</template>

<script>
export default {
  data() {
    return {
      isShowed: false,
      id: null,
      history: [],
    };
  },

  methods: {
    async show(id) {
      try {
        this.$isLoading();

        await this.loadHistory(id);

        this.id = id;
        this.isShowed = true;
      } catch (e) {
        return;
      } finally {
        this.$isLoading(false);
      }
    },

    async loadHistory(id) {
      this.history = await this.$HTTPGet({
        route: "/applist/get-history",
        payload: { id },
      });
    },

    hide() {
      this.isShowed = false;
    },
  },
};
</script>
<style module>
.form_value,
.form_label {
  margin: 0.5rem 0;
}
.form_item {
  border-bottom: 1px gray solid;
}
.form_label {
  margin-bottom: 0.5rem;
  font-size: 15px;
  color: black;
}
.form_item i {
  position: absolute;
  left: 48%;
}
</style>
