<template>
  <el-container>
    <div style="width: calc(100% - var(--filter-width))">
      <el-table
        height="85vh"
        ref="table"
        v-loading="isLoading"
        :data="applist"
        :default-sort="{ prop: 'id', order: 'descending' }"
        @selection-change="handleSelectionChange"
      >
        <el-table-column align="center" type="selection" width="65" prop="id" />

        <el-table-column
          align="center"
          width="100"
          prop="id"
          label="Id"
          sortable
        >
          <template #default="{ row }">
            <span class="column_clickable">{{ row.id }}</span>
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
        />

        <el-table-column
          prop="receptionDate"
          width="200"
          align="center"
          label="Время записи"
          sortable
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

        <el-table-column prop="note" label="Комментарий" min-width="350">
          <template #default="{ row }">
            {{ row.note || "-" }}
          </template>
        </el-table-column>
      </el-table>

      <el-pagination
        class="pagination_wrapper"
        background
        layout="prev, pager, next, jumper, sizes, total"
        :page-sizes="pageSizes"
        :page-size="pageSize"
        :total="applist.length"
        :current-page="currentPage"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      ></el-pagination>
    </div>

    <FilterLayout>
      <template>
        <el-date-picker
          v-model="filter.receptionDate"
          :picker-options="pickerOptions"
          size="small"
          class="form-item"
          value-format="yyyy-MM-dd HH:mm:ss"
          format="dd.MM.yyyy"
          type="daterange"
          start-placeholder="Начало"
          end-placeholder="Конец"
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
        <el-button size="mini">Сброс</el-button>
        <el-divider />
        <el-button size="mini" type="primary">Создать заявку</el-button>

        <transition name="el-fade-in">
          <el-button v-show="selectedRows.length" size="mini" type="danger"
            >Удалить({{ selectedRows.length }})</el-button
          >
        </transition>
      </template>
    </FilterLayout>
  </el-container>
</template>

<script>
import TableMixin from "@/mixins/TableMixin";
import DatepickerMixin from "@/mixins/DatepickerMixin";
import PaginationMixin from "@/mixins/PaginationMixin";
import FilterLayout from "@/components/FilterLayout";
import { ADMIN } from "@/values";

export default {
  components: { FilterLayout },
  mixins: [TableMixin, DatepickerMixin, PaginationMixin],

  data() {
    return {
      ADMIN: ADMIN,
      isLoading: false,

      filter: {
        search: "",
        receptionDate: [],
      },
    };
  },

  computed: {
    applist() {
      return this.$store.getters["storage/index"]("applist");
    },
  },

  async created() {
    try {
      this.isLoading = true;
      await this.getApplist();
      await this.getOrganizations();
    } catch (e) {
      console.error(e);
    } finally {
      this.isLoading = false;
    }
  },

  methods: {
    async getApplist() {
      try {
        await this.$store.dispatch("storage/index", {
          entity: "applist",
        });
      } catch (e) {
        this.$onError("Не удалось загрузить список заявок");
        return Promise.reject(e);
      }
    },

    async getOrganizations() {
      if (!_.isEmpty(this.$store.getters["reference/list"])) return;

      try {
        await this.$store.dispatch("organization/index");
        return Promise.resolve();
      } catch (e) {
        this.$onError("Не удалось загрузить список организаций");

        return Promise.reject(e);
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
