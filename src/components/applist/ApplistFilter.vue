<template>
  <AsideLayout slot="rightPanel">
    <template #title>
      <div class="a-center" style="text-transform:uppercase;">
        Фильтр заявок
      </div>
    </template>

    <template #inputGroup>
      <el-date-picker
        size="small"
        class="form-item"
        v-model="dateRange"
        value-format="yyyy-MM-dd HH:mm:ss"
        format="dd.MM.yyyy"
        type="daterange"
        :picker-options="pickerOptions"
        @change="changeFilter()"
      ></el-date-picker>

      <el-input
        class="form-item"
        size="small"
        placeholder="номер заявки"
        v-model="filterParams.id"
        clearable
      ></el-input>

      <el-input
        class="form-item"
        size="small"
        placeholder="организация"
        v-model="filterParams.label"
        clearable
      ></el-input>

      <el-input
        class="form-item"
        size="small"
        placeholder="ИНН"
        v-model="filterParams.taxId"
        clearable
      ></el-input>

      <el-select
        class="form-item"
        size="small"
        placeholder="тип записи"
        v-model="filterParams.signatureTypeId"
        filterable
        multiple
        clearable
      >
        <el-option
          v-for="item in signatureTypes"
          :key="item.id"
          :label="item.label"
          :value="item.id"
        />
      </el-select>
    </template>

    <template #buttonGroup>
      <el-button
        size="mini"
        type="primary"
        @click="$emit('onCreateApplication')"
        >cоздать заявку</el-button
      >

      <transition name="el-fade-in">
        <el-button
          v-show="selectedRowsCount"
          size="mini"
          type="danger"
          @click="$emit('onRowsRemove')"
          >удалить({{ selectedRowsCount }})</el-button
        >
      </transition>

      <transition name="el-fade-in-linear">
        <el-button
          v-show="isFilterModified"
          size="mini"
          @click="changeFilter(true)"
          >сброс</el-button
        >
      </transition>
    </template>
  </AsideLayout>
</template>

<script>
import AsideLayout from "@/components/AsideLayout";
import DatepickerOptions from "@/mixins/DatepickerOptions";

export default {
  mixins: [DatepickerOptions],

  components: {
    AsideLayout
  },

  props: {
    filterParams: {
      type: Object,
      required: true
    },

    selectedRowsCount: {
      type: Number,
      required: true
    }
  },

  data() {
    return {
      dateRange: [
        `${new Date().getFullYear()}-01-01 00:00:00`,
        `${new Date().getFullYear() + 1}-01-01 00:00:00`
      ],

      signatureTypes: []
    };
  },

  computed: {
    isFilterModified() {
      return (
        this.filterParams.id ||
        this.filterParams.label ||
        this.filterParams.taxId ||
        this.filterParams.signatureTypeId.length ||
        this.dateRange
      );
    }
  },

  async created() {
    if (this.signatureTypes.length) return;

    this.signatureTypes = await this.$HTTPGet({
      route: "/common/get-signatures"
    });
  },

  methods: {
    async changeFilter(reset = false) {
      this.$isLoading();

      try {
        if (reset) this.resetFilter();

        await this.$store.dispatch("applist/loadData", {
          route: "get-list",
          payload: { receptionDate: this.dateRange }
        });
      } catch (e) {
        return;
      } finally {
        this.$isLoading(false);
      }
    },

    resetFilter() {
      this.filterParams.id = null;
      this.filterParams.taxId = null;
      this.filterParams.label = null;
      this.filterParams.signatureTypeId = [];
      this.dateRange = null;
    }
  }
};
</script>
