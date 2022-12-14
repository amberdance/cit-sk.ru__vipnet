<template>
  <div>
    <el-dialog
      :title="title"
      :visible="isVisible"
      width="40%"
      :close-on-click-modal="false"
      @close="hide"
    >
      <el-form
        label-position="top"
        size="small"
        ref="form"
        class="p-3"
        :model="formData"
        :show-message="false"
      >
        <div class="row no-gutters">
          <div class="col-lg-6 col">
            <el-form-item
              label="Количество человек:"
              prop="personCount"
              required
            >
              <el-input-number
                size="small"
                v-model="formData.personCount"
                :min="1"
                :max="99"
                style="width:217px; !important"
              ></el-input-number>
            </el-form-item>

            <el-form-item label="Организация:" prop="organizationId" required>
              <el-select
                v-model="formData.organizationId"
                filterable
                clearable
                remote
                size="small"
                placeholder="инн, наименование"
                :remote-method="remoteSearch"
                @change="onOrganizationChange"
              >
                <el-option
                  v-for="item in options.organizations"
                  :key="item.id"
                  :value="item.id"
                  :label="item.label"
                >
                  <span style="float: left">{{ item.label }}</span>
                  <span class="taxId">{{ item.taxId }}</span>
                </el-option>
              </el-select>
            </el-form-item>

            <el-form-item label="Тип ЭП:" prop="signatureId" required>
              <el-select v-model="formData.signatureId" size="small">
                <el-option
                  v-for="item in signatures"
                  :key="item.id"
                  :value="item.id"
                  :label="item.label"
                ></el-option>
              </el-select>
            </el-form-item>
          </div>

          <div class="col-lg-6 col">
            <el-form-item label="Дата записи:" prop="date" required>
              <el-date-picker
                style="width: 100%"
                placeholder="выберите дату"
                v-model="formData.date"
                type="date"
                size="small"
                format="dd-MM-yyyy"
                value-format="yyyy-MM-dd"
                :picker-options="datePickerOptions"
              >
              </el-date-picker>
            </el-form-item>

            <el-form-item label="Время записи:" prop="time" required>
              <el-input
                v-model="formData.time"
                v-mask="'##:##'"
                placeholder="Время от руки"
              ></el-input>
            </el-form-item>
          </div>
        </div>

        <div class="row">
          <el-form-item label="Комментарий:" prop="note" style="width: 100%">
            <el-input
              v-model="formData.note"
              :rows="4"
              type="textarea"
            ></el-input>
          </el-form-item>
        </div>
      </el-form>

      <template #footer>
        <el-button-group>
          <el-button
            type="danger"
            size="mini"
            :loading="isLoading"
            @click="isVisible = false"
            >Отмена</el-button
          >
          <el-button
            size="mini"
            type="primary"
            :loading="isLoading"
            :disabled="!isApplicationModified"
            @click="submit"
            >{{ isUpdateDialog ? "Обновить" : "Создать" }}</el-button
          >
        </el-button-group>
      </template>
    </el-dialog>

    <OrganizationDialog
      ref="OrganizationDialog"
      @onOrganizationCreate="onOrganizationCreated"
    />
  </div>
</template>
<script>
import OrganizationDialog from "./OrganizationDialog";
import { deepDiffObject, removeEmptyFields } from "@/helpers/commonHelper";
import { mask } from "vue-the-mask";

export default {
  directives: { mask },
  components: {
    OrganizationDialog,
  },

  data() {
    return {
      isLoading: false,
      isUpdateDialog: false,
      isVisible: false,
      original: {},
      copied: {},

      formData: {
        id: null,
        receptionDate: null,
        organizationId: null,
        note: null,
        date: null,
        time: null,
        personCount: 1,
        signatureId: 1,
      },

      options: {
        organizations: [],
        defaultOption: { label: "Добавить огранизацию", id: -1 },
      },

      datePickerOptions: {
        firstDayOfWeek: 1,
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
        },
      },
    };
  },

  computed: {
    isApplicationModified() {
      if (!this.isUpdateDialog) return true;

      return !_.isEqual(this.copied, this.formData);
    },

    signatures() {
      return this.$store.getters["storage/index"]("signatures");
    },

    organizations() {
      return this.$store.getters["organization/index"];
    },

    title() {
      return this.isUpdateDialog
        ? "№" + this.formData.id + " " + this.original.organization.label
        : "Создание заявки на получение ЭЦП";
    },
  },

  methods: {
    show(data) {
      if (_.isEmpty(data)) {
        this.original = {};
        this.copied = {};
        this.isUpdateDialog = false;
      } else {
        const formattedDate = this.getFormattedDate(data.receptionDate);

        this.formData.id = data.id;
        this.formData.personCount = data.personCount;
        this.formData.signatureId = data.signatureId;
        this.formData.organizationId = data.organizationId;
        this.formData.receptionDate = data.receptionDate;
        this.formData.note = data.note;
        this.formData.date = formattedDate.date;
        this.formData.time = formattedDate.time;
        this.original = data;
        this.copied = _.cloneDeep(this.formData);
        this.isUpdateDialog = true;
      }

      this.isVisible = true;
    },

    async submit() {
      try {
        await this.$refs.form.validate();
      } catch (e) {
        return;
      }

      try {
        this.isLoading = true;
        this.formData.receptionDate = `${this.formData.date} ${this.formData.time}:00`;
        this.isUpdateDialog ? await this.update() : await this.create();
        this.hide();
        this.$onSuccess();
      } catch (e) {
        if (e.code == 422) {
          if (e.message.indexOf("already exists") !== -1)
            return this.$onWarning(
              "На данное время и дату уже назначена другая заявка"
            );

          return this.$onWarning(
            "Проверьте правильность заполнения всех полей"
          );
        }

        this.$onError();
        console.error(e);
      } finally {
        this.isLoading = false;
      }
    },

    async remoteSearch(search) {
      if (search == "") {
        this.options.organizations = [];
        return;
      }

      this.options.organizations = this.organizations.filter(
        (item) =>
          item.label.toLowerCase().indexOf(search.toLowerCase()) !== -1 ||
          item.taxId.indexOf(search) !== -1
      );

      this.options.organizations.unshift(this.options.defaultOption);
    },

    async create() {
      try {
        const params = removeEmptyFields(this.formData);
        delete params.date;
        delete params.time;

        await this.$store.dispatch("storage/create", {
          entity: "applist",
          params,
        });

        return Promise.resolve();
      } catch (e) {
        return Promise.reject(e);
      }
    },

    async update() {
      try {
        const params = deepDiffObject(this.copied, this.formData);
        delete params.date;
        delete params.time;

        await this.$store.dispatch("storage/update", {
          entity: "applist",
          id: this.formData.id,
          params,
        });

        return Promise.resolve();
      } catch (e) {
        return Promise.reject(e);
      }
    },

    onOrganizationCreated(organization) {
      this.formData.organizationId = organization.id;
      this.options.organizations = [organization];
    },

    onOrganizationChange(value) {
      if (value == -1) this.$refs.OrganizationDialog.show();
    },

    purge() {
      this.isUpdateDialog = false;
      this.formData.personCount = 1;
      this.formData.signatureId = 1;
      this.formData.receptionDate = null;
      this.formData.organizationId = null;
      this.formData.note = null;
      this.formData.date = null;
      this.formData.time = null;
      this.formData.id = null;
      this.original = {};
      this.copied = {};
      this.options.organizations = [];
    },

    hide() {
      this.isVisible = false;
      this.purge();
      this.$refs.form.clearValidate();
      this.$refs.form.resetFields();
    },

    getFormattedDate(inputDate) {
      const date = new Date(inputDate);
      const minutes = date.getMinutes();
      const day = date.getDate();
      const month = date.getMonth() + 1;
      const hours = date.getHours();

      return {
        date: `${date.getFullYear()}-${month < 10 ? "0" + month : month}-${
          day < 10 ? "0" + day : day
        }`,

        time: `${hours < 10 ? "0" + hours : hours}:${
          minutes < 10 ? "0" + minutes : minutes
        }`,
      };
    },
  },
};
</script>

<style scoped>
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
