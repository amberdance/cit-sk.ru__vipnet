<template>
  <div>
    <div class="btn_group__wrapper" v-if="isAdmin">
      <el-button size="mini" type="primary" @click="$refs.dialog.show()"
        >добавить организацию</el-button
      >
      <transition name="el-fade-in">
        <el-button
          v-show="selection.length"
          size="mini"
          type="danger"
          @click="remove"
          >удалить ({{ selection.length }})</el-button
        >
      </transition>
    </div>

    <div
      v-if="isNotesExists"
      :class="$style.legend_wrapper"
      @click="isNoteFilter = !isNoteFilter"
    >
      <span :class="[$style.legend_square, 'highlighted-row']"></span
      ><span :class="$style.lengend_title"> - имеются заметки</span>
    </div>
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
      <el-table-column prop="label" label="организация" width="700" sortable />
      <el-table-column prop="city" label="город" sortable />
      <el-table-column prop="district" label="район" sortable />

      <el-table-column
        prop="taxId"
        label="инн"
        align="center"
        width="150"
        sortable
      />
      <el-table-column
        prop="governmentId"
        label="огрн"
        align="center"
        width="150"
        sortable
      />

      <el-table-column width="200" align="center" v-if="isAdmin">
        <template #header>
          <el-input
            v-model="search"
            size="small"
            placeholder="наименование"
            clearable
          />
        </template>

        <template #default="{row}">
          <el-button
            size="mini"
            type="secondary"
            @click="$refs.dialog.show(row)"
            >редактировать</el-button
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
      :total="refs.length"
      :current-page="currentPage"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
    ></el-pagination>

    <ManageDialog ref="dialog" />
    <DetailsDialog ref="detailsDialog" />
  </div>
</template>
<script>
import Pagination from "@/mixins/Pagination";
import TableManage from "@/mixins/TableManage";
import Manage from "@/dialogs/reference/Manage";
import Details from "@/dialogs/reference/Details";

export default {
  components: { ManageDialog: Manage, DetailsDialog: Details },

  mixins: [Pagination, TableManage],

  data() {
    return {
      entity: "reference",
      isDetailDialogShowed: false,
      isNoteFilter: false
    };
  },

  computed: {
    initialRefs() {
      return this.$store.getters["reference/list"];
    },

    tableData() {
      return this.paginate(this.refs);
    },

    refs() {
      return this.initialRefs
        .filter(
          ({ notes }) =>
            !this.isNoteFilter || (this.isNoteFilter && notes.length)
        )
        .filter(item => {
          return (
            !this.search ||
            item.label.toLowerCase().includes(this.search.toLowerCase()) ||
            item.taxId.toLowerCase().includes(this.search.toLowerCase()) ||
            item.governmentId.toLowerCase().includes(this.search.toLowerCase)
          );
        });
    },

    isAdmin() {
      return this.$isAdmin();
    },

    isNotesExists() {
      return this.refs.filter(({ notes }) => notes.length).length;
    }
  },

  async created() {
    this.$setHeaderTitle("Организации");

    if (this.refs.length) return;

    this.$isLoading();

    try {
      await this.$store.dispatch(`${this.entity}/loadData`, {
        route: "get-list",
        payload: {
          full: true
        }
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
