<template>
  <div>
    <div class="btn_group__wrapper">
      <el-button size="mini" type="primary" @click="purgeTableData"
        >очистить таблицу</el-button
      >
    </div>

    <el-table
      :data="displayedLogs"
      height="79vh"
      class="table_wrapper"
      ref="dataTable"
      border
      :default-sort="{ prop: 'created', order: 'descending' }"
      :row-class-name="rowClassName"
      @row-click="openDetailsDialog"
    >
      <el-table-column
        prop="applistId"
        label="номер заявки"
        width="160"
        align="center"
        sortable
      />

      <el-table-column
        prop="created"
        label="дата"
        width="200"
        align="center"
        sortable
      />
      <el-table-column prop="event" label="событие" sortable />

      <el-table-column sortable>
        <template #header>Фио</template>
        <template #default="{row}">
          {{ row.surname }} {{ row.name }} {{ row.patronymic }}
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      class="pagination_wrapper"
      background
      layout="prev, pager, next, jumper, sizes, total"
      :page-sizes="pageSizes"
      :page-size="pageSize"
      :total="logs.length"
      :current-page="currentPage"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
    ></el-pagination>

    <Dialog ref="dialog" />
  </div>
</template>
<script>
import Pagination from "@/mixins/Pagination";
import TableManage from "@/mixins/TableManage";
import History from "@/dialogs/application/History";

export default {
  mixins: [Pagination, TableManage],

  components: {
    Dialog: History
  },

  data() {
    return {
      entity: "applist",
      route: "clear-logs",
      key: "logs"
    };
  },

  computed: {
    logs() {
      return this.$store.getters["applist/getList"]("logs");
    },

    displayedLogs() {
      return this.paginate(this.logs);
    }
  },

  async created() {
    this.$isLoading();
    this.$setHeaderTitle("События");

    try {
      await this.$store.dispatch("applist/loadData", {
        route: "get-logs",
        key: "logs"
      });
    } catch (e) {
      return;
    } finally {
      this.$isLoading(false);
    }
  },

  methods: {
    openDetailsDialog({ applistId }, column) {
      if (column.property) this.$refs.dialog.show(applistId);
    },

    rowClassName({ row }) {
      if (row.event == "обновление") return "pointer";
    }
  }
};
</script>
