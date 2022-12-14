<template>
  <MainLayout>
    <el-table
      height="85vh"
      ref="table"
      border
      v-loading="isLoading"
      :data="applist"
      :default-sort="{ prop: 'id', order: 'descending' }"
      @selection-change="handleSelectionChange"
    >
      <el-table-column align="center" type="selection" width="65" prop="id" />

      <el-table-column align="center" width="100" prop="id" label="Id" sortable>
        <template #default="{ row }">
          <span
            class="column_clickable"
            @click="$refs.ApplicationDialog.show(row)"
            >{{ row.id }}</span
          >
        </template>
      </el-table-column>

      <el-table-column
        label="Организация"
        prop="organization.label"
        width="350"
        sortable
      />

      <el-table-column
        prop="organization.taxId"
        label="ИНН"
        align="center"
        width="150"
        :formatter="defaultFormatter"
      />

      <el-table-column
        prop="receptionDate"
        width="200"
        align="center"
        label="Время записи"
        sortable
        :sort-method="(a, b) => dateSortMethod(a, b, 'createdAt')"
      />

      <el-table-column
        prop="signature.label"
        label="Тип записи"
        width="200"
        align="center"
        sortable
      />

      <el-table-column
        prop="personCount"
        label="Человек"
        width="100"
        align="center"
        sortable
      />

      <el-table-column
        prop="note"
        label="Комментарий"
        min-width="350"
        :formatter="defaultFormatter"
      />
    </el-table>

    <div class="pagination_wrapper" v-if="pagination.total">
      <el-pagination
        class="pagination"
        background
        :total="pagination.total"
        :current-page="pagination.currentPage"
        :page-size="filter.perPage"
        :page-sizes="pagination.pageSizes"
        layout="sizes, prev, pager, next, jumper, total"
        @current-change="handleClickPage"
        @size-change="handleSizeChange"
      ></el-pagination>
    </div>

    <FilterLayout slot="filterPanel">
      <template>
        <el-date-picker
          v-model="filter.receptionDate"
          :picker-options="pickerOptions"
          size="small"
          class="form-item"
          value-format="yyyy-MM-dd HH:mm:ss"
          format="dd.MM.yyyy"
          type="daterange"
          start-placeholder="Время"
          end-placeholder="записи"
          clearable
        ></el-date-picker>

        <el-input
          size="small"
          placeholder="Поиск"
          v-model="filter.search"
          clearable
        ></el-input>
      </template>

      <template #buttons>
        <el-divider />
        <el-button
          size="mini"
          type="primary"
          @click="$refs.ApplicationDialog.show()"
          >Создать заявку</el-button
        >
      </template>
    </FilterLayout>

    <ControlBar :visible="Boolean(selectedRows.length)">
      <transition name="el-fade-in">
        <el-button
          v-show="selectedRows.length"
          size="mini"
          type="danger"
          @click="deleteApplication"
          >Удалить({{ selectedRows.length }})</el-button
        >
      </transition>
    </ControlBar>

    <ApplicationDialog ref="ApplicationDialog" />
  </MainLayout>
</template>

<script>
import TableMixin from "@/mixins/TableMixin";
import DatepickerMixin from "@/mixins/DatepickerMixin";
import PaginationMixin from "@/mixins/PaginationMixin";
import MainLayout from "@/components/layouts/MainLayout";
import FilterLayout from "@/components/layouts/FilterLayout";
import ControlBar from "@/components/common/ControlBar";
import ApplicationDialog from "./ApplicationDialog";
import { removeEmptyFields } from "@/helpers/commonHelper";

export default {
  components: { MainLayout, FilterLayout, ApplicationDialog, ControlBar },
  mixins: [TableMixin, DatepickerMixin, PaginationMixin],

  data() {
    return {
      isLoading: false,

      filter: {
        search: "",
        receptionDate: [],
        page: 1,
        perPage: 100,
      },

      pagination: {
        total: 0,
        currentPage: 1,
        pageSizes: [50, 100, 200, 300, 400, 500],
      },
    };
  },

  computed: {
    applist() {
      return this.$store.getters["storage/index"]("applist");
    },

    applistPagination() {
      return this.$store.getters["storage/pagination"];
    },
  },

  watch: {
    filter: {
      handler() {
        this.debouncedWatch();
      },

      deep: true,
    },
  },

  async created() {
    try {
      this.debouncedWatch = _.debounce(() => this.getApplist(), 250);

      await this.getApplist();

      if (_.isEmpty(this.$store.getters["storage/index"]("signatures")))
        await this.getSignatures();

      if (!_.isEmpty(this.applistPagination))
        this.pagination.total = this.applistPagination.total;
    } catch (e) {
      console.error(e);
    }
  },

  beforeUnmount() {
    this.debouncedWatch.cancel();
  },

  methods: {
    async getApplist() {
      try {
        this.isLoading = true;
        await this.$store.dispatch("storage/index", {
          entity: "applist",
          params: removeEmptyFields(this.filter),
        });
      } catch (e) {
        this.$onError("Не удалось загрузить список заявок");
        return Promise.reject(e);
      } finally {
        this.isLoading = false;
      }
    },

    async getSignatures() {
      try {
        await this.$store.dispatch("storage/index", {
          entity: "signatures",
        });
        return Promise.resolve();
      } catch (e) {
        this.$onError("Не удалось загрузить список типов ЭЦП");
        return Promise.reject(e);
      }
    },

    async getOrganizations() {
      try {
        await this.$store.dispatch("organization/index");
        return Promise.resolve();
      } catch (e) {
        this.$onError("Не удалось загрузить список организаций");
        return Promise.reject(e);
      }
    },

    async deleteApplication() {
      try {
        await this.$confirm("Удалить выбранные заявки?");
      } catch (e) {
        return;
      }

      try {
        this.isLoading = true;
        this.collectIds();

        if (this.selectedRows.length == 1)
          await this.$store.dispatch("storage/delete", {
            entity: "applist",
            id: this.selectedRows[0],
          });
        else
          await this.$store.dispatch("storage/massDelete", {
            entity: "applist",
            ids: this.selectedRows,
          });
      } catch (e) {
        this.$onError();
        console.error(e);
      } finally {
        this.isLoading = false;
      }
    },
  },
};
</script>

<style module>
.datePicker input {
  padding: 0 !important;
  width: 0;
  border: none;
}
.warning {
  display: flex;
  align-items: center;
  color: #a23737;
  font-weight: bold;
}
.warning i {
  margin-right: 4px;
}
</style>
