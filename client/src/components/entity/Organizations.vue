<template>
  <el-container>
    <div style="width: calc(100% - var(--filter-width))">
      <el-table
        height="85vh"
        ref="table"
        border
        v-loading="isLoading"
        :data="organizations"
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
            <span
              class="column_clickable"
              @click="$refs.OrganizationDialog.show(row)"
              >{{ row.id }}</span
            >
          </template>
        </el-table-column>

        <el-table-column prop="label" label="Наименование" sortable />

        <el-table-column
          prop="taxId"
          label="ИНН"
          align="center"
          width="200"
          sortable
          :formatter="defaultFormatter"
        />

        <el-table-column
          prop="governmentId"
          label="ОГРН"
          align="center"
          width="200"
          sortable
          :formatter="defaultFormatter"
        />

        <el-table-column
          prop="city"
          label="Город"
          width="250"
          sortable
          :formatter="defaultFormatter"
        />

        <el-table-column
          prop="district"
          label="Район"
          width="250"
          sortable
          :formatter="defaultFormatter"
        />

        <el-table-column
          prop="note"
          label="Комментарий"
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
    </div>

    <FilterLayout>
      <template>
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
          @click="$refs.OrganizationDialog.show()"
          >Добавить организацию</el-button
        >

        <transition name="el-fade-in">
          <el-button
            v-show="selectedRows.length"
            size="mini"
            type="danger"
            @click="deleteOrganization"
            >Удалить({{ selectedRows.length }})</el-button
          >
        </transition>
      </template>
    </FilterLayout>
    <OrganizationDialog ref="OrganizationDialog" />
  </el-container>
</template>

<script>
import FilterLayout from "@/components/common/FilterLayout";
import OrganizationDialog from "./OrganizationDialog";
import PaginationMixin from "@/mixins/PaginationMixin";
import TableMixin from "@/mixins/TableMixin";
import { removeEmptyFields } from "@/helpers/commonHelper";

export default {
  components: {
    FilterLayout,
    OrganizationDialog,
  },

  mixins: [PaginationMixin, TableMixin],

  data() {
    return {
      isLoading: false,

      filter: {
        search: "",
        page: 1,
        perPage: 200,
      },

      pagination: {
        total: 0,
        currentPage: 1,
        pageSizes: [50, 100, 200, 300, 400, 500],
      },
    };
  },

  computed: {
    organizations() {
      return this.$store.getters["organization/index"];
    },

    organizationPagination() {
      return this.$store.getters["organization/pagination"];
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
      this.debouncedWatch = _.debounce(() => this.getOrganizations(), 250);

      if (!this.organizations.length) await this.getOrganizations();

      if (!_.isEmpty(this.organizationPagination))
        this.pagination.total = this.organizationPagination.total;
    } catch (e) {
      console.error(e);
    }
  },

  beforeUnmount() {
    this.debouncedWatch.cancel();
  },

  methods: {
    async getOrganizations() {
      try {
        this.isLoading = true;
        await this.$store.dispatch(
          "organization/index",
          removeEmptyFields(this.filter)
        );
        return Promise.resolve();
      } catch (e) {
        this.$onError("Не удалось загрузить список организаций");
        return Promise.reject(e);
      } finally {
        this.isLoading = false;
      }
    },

    async deleteOrganization() {
      try {
        await this.$confirm("Удалить выбранные организации ?");
      } catch (e) {
        return;
      }

      try {
        this.isLoading = true;
        this.collectIds();

        if (this.selectedRows.length == 1)
          await this.$store.dispatch(
            "organization/delete",
            this.selectedRows[0]
          );
        else
          await this.$store.dispatch(
            "organization/massDelete",
            this.selectedRows
          );
      } catch (e) {
        if (e.code == 1451)
          return this.$onError("На выбранные организации ссылаются заявки");
        this.$onError();
        console.error(e);
      } finally {
        this.isLoading = false;
      }
    },
  },
};
</script>
