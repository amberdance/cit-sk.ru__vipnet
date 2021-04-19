<template>
  <AsideLayout slot="rightPanel">
    <template #title>
      <div class="a-center" style="text-transform:uppercase;">
        Фильтр организаций
      </div>
    </template>

    <template #inputGroup>
      <el-input
        class="form-item"
        size="small"
        placeholder="наименование"
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

      <el-input
        class="form-item"
        size="small"
        placeholder="ОГРН"
        v-model="filterParams.governmentId"
        clearable
      ></el-input>

      <el-input
        class="form-item"
        size="small"
        placeholder="город"
        v-model="filterParams.city"
        clearable
      ></el-input>

      <el-input
        class="form-item"
        size="small"
        placeholder="район"
        v-model="filterParams.district"
        clearable
      ></el-input>
    </template>

    <template #buttonGroup>
      <el-switch
        class="form-item"
        v-model="filterParams.hasNotes"
        active-text="Имеются заметки"
      ></el-switch>

      <el-divider />

      <el-button
        size="mini"
        type="primary"
        @click="$emit('onCreateOrganization')"
        >добавить организацию</el-button
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
        <el-button v-show="isFilterModified" size="mini" @click="reset"
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

  computed: {
    isFilterModified() {
      return (
        this.filterParams.label ||
        this.filterParams.taxId ||
        this.filterParams.governmentId ||
        this.filterParams.city ||
        this.filterParams.district ||
        this.filterParams.hasNotes
      );
    }
  },

  methods: {
    reset() {
      this.filterParams.label = null;
      this.filterParams.taxId = null;
      this.filterParams.governmentId = null;
      this.filterParams.city = null;
      this.filterParams.district = null;
      this.filterParams.hasNotes = false;
    }
  }
};
</script>
