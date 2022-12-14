<template>
  <el-dialog
    :title="title"
    :visible="isVisible"
    width="40%"
    :close-on-click-modal="false"
    @close="hide"
  >
    <el-form
      inline
      :model="formData"
      label-position="top"
      size="medium"
      ref="form"
      class="form_wrapper p-3"
    >
      <div class="row">
        <div class="col-lg-6 col">
          <el-form-item label="Наименование:" prop="label" required>
            <el-input
              v-model="formData.label"
              type="textarea"
              :rows="2"
            ></el-input>
          </el-form-item>

          <el-form-item label="ИНН:" prop="taxId" required>
            <el-input v-model="formData.taxId" type="number"></el-input>
          </el-form-item>
        </div>

        <div class="col">
          <el-form-item label="ОГРН:" prop="governmentId">
            <el-input v-model="formData.governmentId" type="number"></el-input>
          </el-form-item>

          <el-form-item label="Город:" prop="city">
            <el-select
              v-model="formData.city"
              placeholder=""
              filterable
              allow-create
              clearable
              remote
              :remote-method="remoteSearchCity"
              :loading="isSearching"
            >
              <el-option
                v-for="(item, index) in options.cities"
                :key="index"
                :value="item"
                :label="item"
              >
              </el-option>
            </el-select>
          </el-form-item>

          <el-form-item label="Район:" prop="district">
            <el-select
              placeholder=""
              v-model="formData.district"
              filterable
              allow-create
              clearable
              remote
              :remote-method="remoteSearchDistrict"
              :loading="isSearching"
            >
              <el-option
                v-for="(item, index) in options.districts"
                :key="index"
                :value="item"
                :label="item"
              >
              </el-option>
            </el-select>
          </el-form-item>
        </div>
      </div>

      <div class="row">
        <el-form-item label="Комментарий:" prop="note">
          <el-input v-model="formData.note" rows="4" type="textarea"></el-input>
        </el-form-item>
      </div>
    </el-form>

    <template #footer>
      <el-button-group>
        <el-button
          size="mini"
          type="danger"
          :loading="isLoading"
          @click="isVisible = false"
          >Отмена</el-button
        >
        <el-button
          size="mini"
          type="primary"
          :loading="isLoading"
          :disabled="!isFormModified"
          @click="submit"
          >{{ isUpdateDialog ? "Обновить" : "Создать" }}</el-button
        >
      </el-button-group>
    </template>
  </el-dialog>
</template>

<script>
import { deepDiffObject, removeEmptyFields } from "@/helpers/commonHelper";

export default {
  data() {
    return {
      isVisible: false,
      isLoading: false,
      isSearching: false,
      isUpdateDialog: false,

      original: {},
      formData: {},

      options: {
        cities: [],
        districts: [],
      },
    };
  },

  computed: {
    title() {
      return this.isUpdateDialog
        ? this.formData.label
        : "Добавление организации";
    },

    isFormModified() {
      if (!this.isUpdateDialog) return true;

      return !_.isEqual(this.original, this.formData);
    },

    cities() {
      return this.$store.getters["organization/index"].map(({ city }) => city);
    },

    districts() {
      return this.$store.getters["organization/index"].map(
        ({ district }) => district
      );
    },
  },

  methods: {
    show(data) {
      if (!_.isEmpty(data)) {
        this.isUpdateDialog = true;
        this.original = _.cloneDeep(data);
        this.formData = _.cloneDeep(data);
      } else {
        this.formData = {};
        this.original = {};
        this.isUpdateDialog = false;
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
        this.isUpdateDialog ? await this.update() : await this.create();

        this.$onSuccess();
        this.hide();
      } catch (e) {
        if (e.code == 422) {
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

    async create() {
      const result = await this.$store.dispatch(
        "organization/create",
        removeEmptyFields(this.formData)
      );
      this.$emit("onOrganizationCreate", result);
    },

    async update() {
      await this.$store.dispatch("organization/update", {
        id: this.formData.id,
        params: deepDiffObject(this.original, this.formData),
      });
    },

    remoteSearchCity(query) {
      this.remoteSearch(query, "cities");
    },

    remoteSearchDistrict(query) {
      this.remoteSearch(query, "districts");
    },

    remoteSearch(query, key) {
      if (query !== "") {
        this.isSearching = true;

        setTimeout(() => {
          this.isSearching = false;

          const tmp = this[key].filter((item) => {
            if (item == null) return;

            return item.toLowerCase().includes(query.toLowerCase());
          });

          this.options[key] = Object.freeze(Array.from(new Set(tmp)));
        }, 100);
      } else {
        this.options[key] = [];
      }
    },

    hide() {
      this.isVisible = false;
      this.isUpdateDialog = false;
      this.original = {};
      this.formData = {};
      this.$refs.form.clearValidate();
      this.$refs.form.resetFields();
    },
  },
};
</script>

<style scoped>
::v-deep .el-form-item,
::v-deep .el-select {
  width: 100% !important;
}
</style>
