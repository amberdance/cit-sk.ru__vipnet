<template>
  <div :class="$style.wrapper" v-loading="isLoading">
    <div class="a-center">{{ title }}</div>
    <el-divider />
    <el-form
      inline
      label-width="200px"
      label-position="left"
      size="small"
      ref="form"
      :model="formData"
      :rules="rules"
    >
      <div class="form_item">
        <el-form-item label="Количество человек:" required>
          <el-input-number
            size="small"
            v-model="formData.personCount"
            :min="1"
            :max="99"
            style="width:217px; !important"
          ></el-input-number>
        </el-form-item>
      </div>

      <div class="form_item">
        <el-form-item label="Организация:" prop="referenceId">
          <el-select
            v-model="formData.referenceId"
            filterable
            clearable
            remote
            size="small"
            placeholder="инн, наименование"
            allow-create
            :remote-method="remoteSearch"
            :loading="isLoading"
            @change="onReferenceChange"
          >
            <el-option
              v-for="item in options.references"
              :key="item.id"
              :value="item.id"
              :label="item.label"
            >
              <span style="float: left">{{ item.label }}</span>
              <span :class="$style.taxId">{{ item.taxId }}</span>
            </el-option>
          </el-select>
        </el-form-item>
      </div>

      <div class="form_item">
        <el-form-item label="Тип ЭП:" prop="signatureTypeId">
          <el-select v-model="formData.signatureTypeId" size="small">
            <el-option
              v-for="item in signatureTypes"
              :key="item.id"
              :value="item.id"
              :label="item.label"
            ></el-option>
          </el-select>
        </el-form-item>
      </div>

      <div class="form_item">
        <el-form-item label="Дата записи:" prop="date1">
          <el-date-picker
            placeholder="выберите дату"
            v-model="formData.date1"
            type="date"
            size="small"
            :picker-options="pickerOptions"
            value-format="yyyy-MM-dd"
          >
          </el-date-picker>
        </el-form-item>
      </div>

      <div class="form_item">
        <el-form-item label="Время записи:" prop="date2">
          <el-time-select
            placeholder="выберите время"
            size="small"
            v-model="formData.date2"
            :picker-options="timePickerOptions"
          >
          </el-time-select>
        </el-form-item>
      </div>

      <div class="form_item">
        <div style="margin-bottom: 0.5rem">Комментарий к заявке:</div>
        <el-input v-model="formData.note" :rows="3" type="textarea"></el-input>
      </div>

      <div :class="$style.note_wrapper" v-if="referenceNote.length">
        <div :class="$style.note_label">Заметки организации:</div>
        <div
          v-for="item in referenceNote"
          :key="item.id"
          style="border-bottom: 1px #dcdfe6 solid"
        >
          <i class="el-icon-date"> </i> {{ item.created }}: {{ item.note }}
        </div>
      </div>
    </el-form>

    <div class="a-center">
      <el-button-group>
        <el-button size="mini" type="info" @click="hide">reset</el-button>
        <el-button size="mini" type="primary" @click="onSubmit">{{
          isUpdateDialog ? "update" : "create"
        }}</el-button>
      </el-button-group>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      isLoading: false,
      isUpdateDialog: false,
      label: null,
      applicationId: null,
      referenceUpdateId: null,
      referenceNote: [],

      formData: {
        signatureTypeId: 1,
        personCount: 1,
        referenceId: null,
        receptionDate: null,
        note: null,
        date1: null,
        date2: null
      },

      options: {
        signatureTypes: [],
        references: []
      },

      timePickerOptions: {
        start: "09:00",
        step: "00:30",
        end: "18:00"
      },

      pickerOptions: {
        disabledDate(date) {
          const dateNow = new Date();
          dateNow.getDay();

          return (
            date.getDay() == 0 ||
            date.getDay() == 6 ||
            (date.getDate() < dateNow.getDate() &&
              dateNow.getMonth() == date.getMonth()) ||
            date.getFullYear() < dateNow.getFullYear()
          );
        }
      },

      rules: {
        signatureTypeId: [{ required: false }],
        referenceId: [{ required: false }],
        note: [{ required: false }],

        date1: [
          {
            required: true,
            message: "выберите дату",
            trigger: "change"
          }
        ],

        date2: [
          {
            required: true,
            message: "выберите время",
            trigger: "change"
          }
        ]
      }
    };
  },

  computed: {
    applicationRefsId() {
      return this.$store.getters["applist/getList"]("items").map(
        ({ referenceId }) => referenceId
      );
    },

    signatureTypes() {
      return this.$store.getters["applist/getList"]("signatures");
    },

    title() {
      return this.isUpdateDialog ? this.label : "Application creation:";
    }
  },

  methods: {
    async onSubmit() {
      await this.$refs.form.validate();

      try {
        await this.timePickerValidate();
      } catch (e) {
        return this.$onWarning(
          "Отдохните, перекусите, обеденное время настало"
        );
      }

      this.isLoading = true;

      try {
        this.formData.receptionDate = `${this.formData.date1} ${this.formData.date2}`;
        this.isUpdateDialog ? await this.update() : await this.add();
        this.$refs.form.clearValidate();
        this.$refs.form.resetFields();
        this.isUpdateDialog ? this.hide() : this.purge();
        this.$onSuccess();
      } catch (e) {
        if (e.code == 102)
          return this.$onWarning(
            `На дату ${this.formData.receptionDate} уже назначена запись`
          );
      } finally {
        this.isLoading = false;
      }
    },

    async remoteSearch(keyword) {
      if (keyword !== "") {
        this.isLoading = true;

        try {
          this.options.references = await this.$HTTPGet({
            route: "/reference/search-item",
            payload: { keyword }
          });
        } catch (e) {
          return;
        } finally {
          this.isLoading = false;
        }
      } else {
        this.options.references = [];
      }
    },

    async add() {
      await this.$store.dispatch("applist/loadData", {
        route: "add",
        payload: {
          receptionDate: this.formData.receptionDate,
          signatureTypeId: this.formData.signatureTypeId,
          personCount: this.formData.personCount,
          referenceId: this.formData.referenceId,
          note: this.formData.note
        }
      });
    },

    async update() {
      const selectedReference = this.options.references.filter(
        item => item.id == this.formData.referenceId
      )[0];

      await this.$store.dispatch("applist/update", {
        payload: {
          id: this.applicationId,
          receptionDate: this.formData.receptionDate,
          signatureTypeId: this.formData.signatureTypeId,
          personCount: this.formData.personCount,
          referenceId: this.formData.referenceId,
          note: this.formData.note,
          label: selectedReference.label,
          taxId: selectedReference.taxId,
          signatureType: this.signatureTypes.filter(
            item => item.id == this.formData.signatureTypeId
          )[0].label
        }
      });
    },

    fillUpdateFields(data) {
      const {
        id,
        label,
        taxId,
        signatureTypeId,
        referenceId,
        receptionDate,
        personCount,
        note
      } = data;

      this.applicationId = id;
      this.referenceUpdateId = referenceId;
      this.formData.signatureTypeId = signatureTypeId;
      this.formData.referenceId = referenceId;
      this.formData.personCount = personCount;
      this.formData.receptionDate = receptionDate;
      this.formData.note = note;
      this.options.references = [{ id: referenceId, label, taxId }];
      this.label = label;

      const formattedDate = this.getFormattedDate(receptionDate);
      this.formData.date1 = formattedDate.date;
      this.formData.date2 = formattedDate.time;
      this.isUpdateDialog = true;
    },

    getFormattedDate(inputDate) {
      const date = new Date(inputDate);
      const minutes = date.getMinutes();
      const month = date.getMonth() + 1;

      return {
        date: `${date.getFullYear()}-${
          month < 10 ? "0" + month : month
        }-${date.getDate()}`,
        time: `${date.getHours()}:${minutes < 10 ? "0" + minutes : minutes}`
      };
    },

    timePickerValidate() {
      if (this.formData.date2.indexOf("13") > -1) return Promise.reject(); //lunch time
    },

    onReferenceChange(itemId) {
      if (!Number(itemId) || !itemId) {
        this.referenceNote = [];
        return;
      }

      this.referenceNote = this.options.references.filter(
        ({ id }) => id == itemId
      )[0].notes;
    },

    purge() {
      this.formData.personCount = 1;
      this.formData.signatureTypeId = 1;
      this.isUpdateDialog = false;
      this.formData.note = null;
      this.formData.date1 = null;
      this.formData.date2 = null;
      this.options.references = [];
      this.referenceNote = [];
    },

    hide() {
      this.$refs.form.clearValidate();
      this.$refs.form.resetFields();
      this.purge();
    }
  }
};
</script>
<style module>
.taxId {
  float: right;
  color: #8492a6;
  font-size: 13px;
  margin-left: 1rem;
}
.note_wrapper {
  display: flex;
  justify-content: center;
  flex-direction: column;
  margin-top: 1rem;
  padding-top: 1rem;
  border-top: 1px #dadada solid;
}
.note_wrapper i {
  margin-right: 5px;
}
.note_wrapper div {
  margin-bottom: 0.5rem;
  padding-bottom: 0.5rem;
}
.note_label {
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 1rem;
}
</style>
