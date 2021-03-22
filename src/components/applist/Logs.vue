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

    <HistoryDetails ref="historyDetails" />
    <DeleteDetails ref="deleteDetails" />
  </div>
</template>
<script>
import Pagination from "@/mixins/Pagination";
import TableManage from "@/mixins/TableManage";
import HistoryDetails from "@/dialogs/application/HistoryDetails";
import DeleteDetails from "@/dialogs/application/DeleteDetails";

export default {
  mixins: [Pagination, TableManage],

  components: {
    HistoryDetails,
    DeleteDetails
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

    applist() {
      return this.$store.getters["applist/getList"]("items");
    },

    displayedLogs() {
      return this.paginate(this.logs);
    }
  },

  async created() {
    this.$isLoading();
    this.$setHeaderTitle("События");
    this.$isLoading();

    await this.loadLogs();
    await this.loadApplist();

    this.$isLoading(false);
  },

  methods: {
    async loadLogs() {
      try {
        await this.$store.dispatch("applist/loadData", {
          route: "get-logs",
          key: "logs"
        });
      } catch (e) {
        return;
      }
    },

    async loadApplist() {
      try {
        await this.$store.dispatch("applist/loadData", {
          route: "get-list",
          payload: { isActive: false }
        });
      } catch (e) {
        return;
      }
    },

    openDetailsDialog(row) {
      if (row.event == "обновление")
        this.$refs.historyDetails.show(row.applistId);

      if (row.event == "удаление")
        this.$refs.deleteDetails.show(
          this.applist.filter(item => item.id == row.applistId)
        );
    },

    rowClassName({ row }) {
      return row.event == "обновление" || row.event == "удаление"
        ? "pointer"
        : null;
    }
  }
};
</script>
