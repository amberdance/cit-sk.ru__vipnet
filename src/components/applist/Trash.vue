<template>
  <div>
    <div class="btn_group__wrapper">
      <transition name="el-fade-in">
        <el-button
          v-show="selection.length"
          size="small"
          type="danger"
          @click="remove"
          >удалить({{ selection.length }})</el-button
        >
      </transition>
    </div>

    <el-table
      :data="displayedTrash"
      height="79vh"
      class="table_wrapper"
      ref="dataTable"
      border
      @selection-change="handleSelectionChange"
      :default-sort="{ prop: 'id', order: 'descending' }"
    >
      <el-table-column align="center" type="selection" width="65" prop="id" />
      <el-table-column
        prop="id"
        label="номер заявки"
        width="160"
        align="center"
      />
      <el-table-column prop="label" label="организация" />
      <el-table-column
        prop="receptionDate"
        label="дата записи"
        width="160"
        align="center"
      />

      <el-table-column
        prop="signatureType"
        label="тип записи"
        width="200"
        align="center"
      />
      <el-table-column prop="taxId" label="инн" align="center" width="150" />
      <el-table-column
        prop="personCount"
        label="кол-во человек"
        width="150"
        align="center"
      />
    </el-table>

    <el-pagination
      class="pagination_wrapper"
      background
      layout="prev, pager, next, jumper, sizes, total"
      :page-sizes="pageSizes"
      :page-size="pageSize"
      :total="trash.length"
      :current-page="currentPage"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
    ></el-pagination>
  </div>
</template>
<script>
import Pagination from "@/mixins/Pagination";
import TableManage from "@/mixins/TableManage";

export default {
  mixins: [TableManage, Pagination],

  data() {
    return {
      entity: "applist",
      storageKey: "trash",
      isCompletelyRemove: true
    };
  },

  computed: {
    trash() {
      return this.$store.getters["applist/getList"]("trash");
    },

    displayedTrash() {
      return this.paginate(this.trash);
    }
  },

  async created() {
    this.$setHeaderTitle("Удаленные заявки");
    this.$isLoading();

    try {
      await this.$store.dispatch("applist/loadData", {
        key: "trash",
        route: "get-trash"
      });
    } catch (e) {
      return;
    } finally {
      this.$isLoading(false);
    }
  }
};
</script>
