<template>
  <div>
    <div class="btn_group__wrapper">
      <el-button size="mini" type="primary" @click="purgeTableData"
        >очистить таблицу</el-button
      >
    </div>

    <el-table
      :data="displayedSessions"
      height="79vh"
      class="table_wrapper"
      ref="dataTable"
      border
      :default-sort="{ prop: 'created', order: 'descending' }"
    >
      <el-table-column
        prop="created"
        label="дата входа"
        width="200"
        align="center"
        sortable
      />

      <el-table-column prop="login" label="логин" sortable />
      <el-table-column prop="ip" label="ip-адрес" />
    </el-table>

    <el-pagination
      class="pagination_wrapper"
      background
      layout="prev, pager, next, jumper, sizes, total"
      :page-sizes="pageSizes"
      :page-size="pageSize"
      :total="sessions.length"
      :current-page="currentPage"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
    ></el-pagination>
  </div>
</template>
<script>
import Pagination from "@/mixins/PaginationMixin";
import TableManage from "@/mixins/TableMixin";

export default {
  mixins: [Pagination, TableManage],

  data() {
    return {
      entity: "common",
      route: "clear-sessions",
      key: "sessions",
    };
  },

  computed: {
    sessions() {
      return this.$store.getters["common/getList"]("sessions");
    },

    displayedSessions() {
      return this.paginate(this.sessions);
    },
  },

  async created() {


    try {
      
      await this.$store.dispatch("common/loadData", {
        route: "/common/get-sessions",
        key: "sessions",
      });
    } catch (e) {
      return;
    } finally {
      this.$isLoading(false);
    }
  },
};
</script>
