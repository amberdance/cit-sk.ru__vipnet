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
      label-width="8.125rem"
      label-position="left"
      size="medium"
      ref="form"
      :model="formData"
      :rules="rules"
    >
      <div class="form_item">
        <el-form-item label="Наименование:" prop="label" required>
          <el-input v-model="formData.label"></el-input>
        </el-form-item>
      </div>

      <div class="form_item">
        <el-form-item label="ИНН:" prop="taxId" required>
          <el-input v-model="formData.taxId" type="number"></el-input>
        </el-form-item>
      </div>

      <div class="form_item">
        <el-form-item label="ОГРН:" prop="governmentId" required>
          <el-input v-model="formData.governmentId" type="number"></el-input>
        </el-form-item>
      </div>

      <div class="form_item">
        <el-form-item label="Город:" prop="city" required>
          <el-select
            placeholder=""
            v-model="formData.city"
            filterable
            allow-create
            clearable
            remote
            :remote-method="remoteSearchCity"
            :loading="isLoading"
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
      </div>

      <div class="form_item">
        <el-form-item label="Район:" prop="district">
          <el-select
            placeholder=""
            v-model="formData.district"
            filterable
            allow-create
            clearable
            remote
            :remote-method="remoteSearchDistrict"
            :loading="isLoading"
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

      <div class="form_item">
        <el-form-item label="Примечание:" prop="note">
          <el-input
            v-model="formData.note"
            rows="3"
            cols="23"
            type="textarea"
          ></el-input>
        </el-form-item>
      </div>
    </el-form>

    <template #footer>
      <el-button-group>
        <el-button size="mini" type="danger" @click="hide">отмена</el-button>
        <el-button size="mini" type="primary" @click="onSubmit">{{
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
      referenceId: null,

      formData: {
        label: null,
        taxId: null,
        governmentId: null,
        city: null,
        district: null,
        note: null
      },

      options: {
        cities: [],
        districts: []
      },

      rules: {
        label: [
          {
            required: true,
            message: "выберите наименование"
          }
        ],

        taxId: [
          {
            required: true,
            message: "выберите ИНН"
          }
        ],

        governmentId: [
          {
            required: true,
            message: "выберите ОГРН"
          }
        ],

        city: [
          {
            required: true,
            message: "выберите город"
          }
        ]
      }
    };
  },

  computed: {
    title() {
      return this.isUpdateDialog ? this.label : "Добавление организации";
    },

    cities() {
      return this.$store.getters["reference/list"].map(({ city }) => city);
    },

    districts() {
      return this.$store.getters["reference/list"].map(
        ({ district }) => district
      );
    }
  },

  methods: {
    async show(data) {
      this.$isLoading();

      if (data) {
        this.isUpdateDialog = true;
        this.label = data.label;
        this.fillUpdateFields(data);
      }

      this.isShowed = true;
      this.$isLoading(false);
    },

    async onSubmit() {
      await this.$refs.form.validate();

      try {
        this.$isLoading();
        this.isUpdateDialog ? await this.update() : await this.add();
        this.$refs.form.clearValidate();
        this.$refs.form.resetFields();
        this.hide();
        this.$onSuccess();
      } catch (e) {
        return;
      } finally {
        this.$isLoading(false);
      }
    },

    async add() {
      await this.$store.dispatch("reference/loadData", {
        route: "add",
        payload: this.formData
      });
    },

    async update() {
      await this.$store.dispatch("reference/update", {
        route: "/reference/update",
        payload: {
          id: this.referenceId,
          ...this.formData
        }
      });
    },

    fillUpdateFields(data) {
      const { id, label, taxId, governmentId, city, district } = data;

      this.label = label;
      this.referenceId = id;
      this.formData.label = label;
      this.formData.taxId = taxId;
      this.formData.governmentId = governmentId;
      this.formData.city = city;
      this.formData.district = district;
    },

    remoteSearchCity(query) {
      this.remoteSearch(query, "cities");
    },

    remoteSearchDistrict(query) {
      this.remoteSearch(query, "districts");
    },

    remoteSearch(query, key) {
      if (query !== "") {
        this.isLoading = true;

        setTimeout(() => {
          this.isLoading = false;

          let tmp = this[key].filter(item => {
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
      this.isShowed = false;
      this.isUpdateDialog = false;
      this.$refs.form.clearValidate();
      this.$refs.form.resetFields();
    }
  }
};
</script>
<style module>
.taxId {
  float: right;
  color: #8492a6;
  font-size: 0.8125rem;
  margin-left: 1rem;
}
</style>
