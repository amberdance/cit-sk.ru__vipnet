<template>
  <div class="d-flex">
    <div class="content_wrapper">
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
        <el-table-column align="center" type="selection" width="65" prop="id" />
        <el-table-column
          prop="label"
          label="организация"
          width="480"
          sortable
        />
        <el-table-column prop="city" label="город" width="200" sortable />
        <el-table-column prop="district" label="район" width="200" sortable />

        <el-table-column
          prop="taxId"
          label="инн"
          align="center"
          width="200"
          sortable
        />
        <el-table-column
          prop="governmentId"
          label="огрн"
          align="center"
          width="200"
          sortable
        />

        <el-table-column width="100" v-if="isAdmin">
          <template #default="{row}">
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
        :page-size="pageSize"
        :total="references.length"
        :current-page="currentPage"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      ></el-pagination>

      <ManageDialog ref="dialog" />
      <DetailsDialog ref="detailsDialog" />
    </div>

    <div class="right_aside">
      <ReferenceFilter
        slot="rightPanel"
        :filter-params="filterParams"
        :selected-rows-count="selection.length"
        @onRowsRemove="remove"
        @onCreateOrganization="$refs.dialog.show()"
      />
    </div>
  </div>
</template>
<script>
import ReferenceFilter from "@/components/reference/ReferenceFilter";
import Pagination from "@/mixins/Pagination";
import TableManage from "@/mixins/TableManage";
import Manage from "@/dialogs/reference/Manage";
import Details from "@/dialogs/reference/Details";

export default {
  components: { ManageDialog: Manage, DetailsDialog: Details, ReferenceFilter },

  mixins: [Pagination, TableManage],

  data() {
    return {
      entity: "reference",
      isDetailDialogShowed: false,

      filterParams: {
        label: null,
        taxId: null,
        governmentId: null,
        city: null,
        district: null,
        hasNotes: false
      }
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
              !this.filterParams.label ||
              label
                .toLowerCase()
                .includes(this.filterParams.label.toLowerCase())
          )

          .filter(
            ({ city }) =>
              !this.filterParams.city ||
              city
                .toLowerCase()
                .includes(this.filterParams.governmentId.toLowerCase())
          )

          .filter(
            ({ district }) =>
              !this.filterParams.district ||
              district
                .toLowerCase()
                .includes(this.filterParams.district.toLowerCase())
          )

          .filter(
            ({ taxId }) =>
              !this.filterParams.taxId ||
              String(taxId).includes(this.filterParams.taxId.toLowerCase())
          )

          .filter(
            ({ governmentId }) =>
              !this.filterParams.governmentId ||
              String(governmentId).includes(
                this.filterParams.governmentId.toLowerCase()
              )
          )

          .filter(
            ({ notes }) => !this.filterParams.hasNotes || notes.length >= 1
          )

          .sort((a, b) => b.id - a.id)
      );
    },

    isAdmin() {
      return this.$isAdmin();
    }
  },

  async created() {
    this.$setHeaderTitle("Организации");

    if (this.references.length) return;

    this.$isLoading();

    try {
      await this.$store.dispatch(`${this.entity}/loadData`, {
        route: "get-list",
        payload: { full: true }
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
    }
  }
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
