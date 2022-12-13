<template>
  <el-dialog
    width="30%"
    :visible.sync="isShowed"
    :title="`Заявка # ${application.id}`"
    @close="hide"
  >
    <div :class="$style.wrapper">
      <div class="form_item">
        <span>Организация:</span> {{ application.label }}
      </div>
      <div class="form_item"><span>ИНН:</span> {{ application.taxId }}</div>
      <div class="form_item">
        <span>Время записи: </span>{{ application.receptionDate }}
      </div>
      <div class="form_item">
        <span>Количество человек: </span>
        {{ application.personCount }}
      </div>
      <div class="form_item">
        <span>Тип ЭП: </span>{{ application.signatureType }}
      </div>
    </div>
  </el-dialog>
</template>

<script>
export default {
  data() {
    return {
      isShowed: false,
      application: {},
    };
  },

  methods: {
    show(payload) {
      this.application = payload[0];
      this.isShowed = true;
    },

    hide() {
      this.isShowed = false;
    },
  },
};
</script>
<style module>
.wrapper div {
  margin: 1rem 0;
  border-bottom: 1px #dadada solid;
  padding-bottom: 0.5rem;
  width: 90%;
}
.wrapper span {
  font-weight: bold;
  margin-right: 1rem;
}
</style>
