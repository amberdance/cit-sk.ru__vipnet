<template>
  <div>
    <OrganizationsFilter
      :filter-params="filter"
      :selected-rows-count="selection.length"
      @onRowsRemove="remove"
      @onCreateOrganization="$refs.dialog.show()"
    />

    <el-table
      height="79vh"
      class="table_wrapper"
      ref="tableData"
      border
      :data="tableData"
      :default-sort="{ prop: 'label', order: 'ascending' }"
      :row-class-name="rowClassName"
      @selection-change="handleSelectionChange"
      @row-click="openDetailsDialog"
    >
      <el-table-column align="center" type="selection" width="70" prop="id" />
      <el-table-column prop="label" label="организация" sortable />
      <el-table-column prop="city" label="город" width="250" sortable />
      <el-table-column prop="district" label="район" width="250" sortable />

      <el-table-column
        prop="taxId"
        label="инн"
        align="center"
        width="250"
        sortable
      />
      <el-table-column
        prop="governmentId"
        label="огрн"
        align="center"
        width="250"
        sortable
      />

      <el-table-column width="100" v-if="$user.hasRole(ADMIN)">
        <template #default="{ row }">
          <el-button
            size="mini"
            type="secondary"
            @click="$refs.dialog.show(row)"
            >изменить</el-button
          >
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      class="pagination_wrapper"
      background
      layout="prev, pager, next, jumper, sizes, total"
      :page-sizes="pageSizes"
      :page-size="50"
      :total="references.length"
      :current-page="currentPage"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
    ></el-pagination>

    <ManageDialog ref="dialog" />
    <DetailsDialog ref="detailsDialog" />
  </div>
</template>
<script>
import OrganizationsFilter from "@/components/organization/OrganizationsFilter";
import Pagination from "@/mixins/PaginationMixin";
import TableManage from "@/mixins/TableMixin";
import Manage from "@/components/dialogs/organization/Manage";
import Details from "@/components/dialogs/organization/Details";
import { ADMIN } from "@/values";

export default {
  components: {
    ManageDialog: Manage,
    DetailsDialog: Details,
    OrganizationsFilter,
  },

  mixins: [Pagination, TableManage],

  data() {
    return {
      ADMIN: ADMIN,
      entity: "reference",
      isDetailDialogShowed: false,

      filter: {
        label: null,
        taxId: null,
        governmentId: null,
        city: null,
        district: null,
        hasNotes: false,
      },
    };
  },

  computed: {
    references() {
      return this.$store.getters["reference/list"];
    },

    tableData() {
      return this.paginate(
        this.references
          .filter(
            ({ label }) =>
              !this.filter.label ||
              label
                .toLowerCase()
                .includes(this.filter.label.toLowerCase())
          )

          .filter(
            ({ city }) =>
              !this.filter.city ||
              city
                .toLowerCase()
                .includes(this.filter.governmentId.toLowerCase())
          )

          .filter(
            ({ district }) =>
              !this.filter.district ||
              district
                .toLowerCase()
                .includes(this.filter.district.toLowerCase())
          )

          .filter(
            ({ taxId }) =>
              !this.filter.taxId ||
              String(taxId).includes(this.filter.taxId.toLowerCase())
          )

          .filter(
            ({ governmentId }) =>
              !this.filter.governmentId ||
              String(governmentId).includes(
                this.filter.governmentId.toLowerCase()
              )
          )

          .filter(
            ({ notes }) => !this.filter.hasNotes || notes.length >= 1
          )

          .sort((a, b) => b.id - a.id)
      );
    },
  },

  async created() {
    if (this.references.length) return;

    this.$isLoading();

    try {
      await this.$store.dispatch(`${this.entity}/loadData`, {
        route: "get-list",
        payload: { full: true },
      });
    } catch (e) {
      return;
    } finally {
      this.$isLoading(false);
    }
  },

  methods: {
    openDetailsDialog(row, column) {
      if (!row.notes.length) return;
      if (column.property) this.$refs.detailsDialog.show(row);
    },

    rowClassName({ row }) {
      if (row.notes.length) return "highlighted-row";
    },
  },
};
</script>

<style module>
.legend_wrapper {
  display: flex;
  margin: 0.5rem 0;
}
.legend_square {
  width: 40px;
  height: 20px;
  margin-right: 1rem;
  margin-left: 3px;
  border: 1px #05abb354 solid;
}
.legend_title {
  color: black;
}
</style>
