<template>
  <el-dialog
    width="30%"
    :visible.sync="isShowed"
    :close-on-click-modal="false"
    :title="title"
    @close="hide"
  >
    <el-form
      inline
      label-width="200px"
      label-position="left"
      size="medium"
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
            :max="10"
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
            placeholder="инн, наименование"
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
        <el-form-item label="Тип ЭП:" required>
          <el-select v-model="formData.signatureTypeId">
            <el-option
              v-for="item in options.signatureTypes"
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
            v-model="formData.date2"
            :picker-options="timePickerOptions"
          >
          </el-time-select>
        </el-form-item>
      </div>

      <div :class="$style.note_wrapper" v-if="referenceNote.length">
        <div :class="$style.note_label">
          Заметки организации:
        </div>
        <div
          v-for="item in referenceNote"
          :key="item.id"
          style="border-bottom: 1px #dcdfe6 solid;"
        >
          <i class="el-icon-date"> </i> {{ item.created }}: {{ item.note }}
        </div>
      </div>
    </el-form>

    <template #footer>
      <el-button-group>
        <el-button size="small" type="danger" @click="hide">отмена</el-button>
        <el-button size="small" type="primary" @click="onSubmit">{{
          isUpdateDialog ? "обновить" : "создать"
        }}</el-button>
      </el-button-group>
    </template>
  </el-dialog>
</template>

<script>
export default {
  data() {
    return {
      isShowed: false,
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
        referenceId: [
          {
            required: true,
            message: "выберите организацию"
          }
        ],

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

    title() {
      return this.isUpdateDialog ? this.label : "Создание заявки";
    },

    signatureTypePayload() {
      return this.$route.path == "/applist/vipnet" ? "vipnet" : "signature";
    }
  },

  methods: {
    async show(payload) {
      if (!payload) this.purge();

      try {
        this.$isLoading();

        await this.loadSignatureTypes();

        if (payload) {
          this.isUpdateDialog = true;
          this.label = payload.label;
          this.fillUpdateFields(payload);
        }
        this.isShowed = true;
      } catch (e) {
        return;
      } finally {
        this.$isLoading(false);
      }
    },

    async loadSignatureTypes() {
      if (this.options.signatureTypes.length) return;

      this.options.signatureTypes = await this.$HTTPGet({
        route: "/common/get-signatures"
      });
    },

    async onSubmit() {
      await this.$refs.form.validate();

      try {
        await this.timePickerValidate();
      } catch (e) {
        return this.$onWarning(
          "Отдохните, перекусите, обеденное время все - таки"
        );
      }

      this.$isLoading();

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
        this.$isLoading(false);
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
          referenceId: this.formData.referenceId
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
          label: selectedReference.label,
          taxId: selectedReference.taxId,
          signatureType: this.options.signatureTypes.filter(
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
        personCount
      } = data;

      this.applicationId = id;
      this.referenceUpdateId = referenceId;
      this.formData.signatureTypeId = signatureTypeId;
      this.formData.referenceId = referenceId;
      this.formData.personCount = personCount;
      this.formData.receptionDate = receptionDate;
      this.options.references = [{ id: referenceId, label, taxId }];

      const formattedDate = this.getFormattedDate(receptionDate);
      this.formData.date1 = formattedDate.date;
      this.formData.date2 = formattedDate.time;
    },

    getFormattedDate(inputDate) {
      const date = new Date(inputDate);
      const minutes = date.getMinutes();

      return {
        date: `${date.getFullYear()}-${date.getMonth() + 1}-${date.getDate()}`,
        time: `${date.getHours()}:${minutes < 10 ? "0" + minutes : minutes}`
      };
    },

    timePickerValidate() {
      if (this.formData.date2.indexOf("13") > -1) return Promise.reject(); //lunch time
    },

    onReferenceChange(itemId) {
      this.referenceNote = this.options.references.filter(
        ({ id }) => id == itemId
      )[0].notes;
    },

    purge() {
      this.formData.personCount = 1;
      this.formData.signatureTypeId = 1;
      this.isUpdateDialog = false;
      this.options.references = [];
      this.referenceNote = [];
    },

    hide() {
      this.isShowed = false;
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
}
.note_wrapper i {
  margin-right: 5px;
  color: #cc4444;
}
.note_wrapper div {
  margin-bottom: 0.5rem;
  padding-bottom: 0.5rem;
}
.note_label {
  color: #cc4444;
  font-size: 16px;
  font-weight: bold;
  margin-bottom: 1rem;
}
</style>
