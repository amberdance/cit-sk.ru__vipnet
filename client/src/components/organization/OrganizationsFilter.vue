<template>
  <AsideLayout>
    <template #inputGroup>
      <el-input
        class="form-item"
        size="small"
        placeholder="наименование"
        v-model="filter.label"
        clearable
      ></el-input>

      <el-input
        class="form-item"
        size="small"
        placeholder="ИНН"
        v-model="filter.taxId"
        clearable
      ></el-input>

      <el-input
        class="form-item"
        size="small"
        placeholder="ОГРН"
        v-model="filter.governmentId"
        clearable
      ></el-input>

      <el-input
        class="form-item"
        size="small"
        placeholder="город"
        v-model="filter.city"
        clearable
      ></el-input>

      <el-input
        class="form-item"
        size="small"
        placeholder="район"
        v-model="filter.district"
        clearable
      ></el-input>
    </template>

    <template #buttonGroup>
      <el-button-group style="display: flex; align-items: center">
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

        <el-switch
          class="form-item"
          v-model="filter.hasNotes"
          active-text="Имеются заметки"
        ></el-switch>
      </el-button-group>
    </template>
  </AsideLayout>
</template>

<script>
import AsideLayout from "@/components/AsideLayout";
import DatepickerOptions from "@/mixins/DatepickerMixin";

export default {
  mixins: [DatepickerOptions],

  components: {
    AsideLayout,
  },

  props: {
    filter: {
      type: Object,
      required: true,
    },

    selectedRowsCount: {
      type: Number,
      required: true,
    },
  },

  computed: {
    isFilterModified() {
      return (
        this.filter.label ||
        this.filter.taxId ||
        this.filter.governmentId ||
        this.filter.city ||
        this.filter.district ||
        this.filter.hasNotes
      );
    },
  },

  methods: {
    reset() {
      this.filter.label = null;
      this.filter.taxId = null;
      this.filter.governmentId = null;
      this.filter.city = null;
      this.filter.district = null;
      this.filter.hasNotes = false;
    },
  },
};
</script>
