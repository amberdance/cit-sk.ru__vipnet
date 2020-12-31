<template>
  <div>
    <div class="btn_group__wrapper">
      <el-button size="mini" type="primary" @click="$refs.dialog.show()"
        >создать заявку</el-button
      >
      <transition name="el-fade-in">
        <el-button
          v-show="selection.length"
          size="mini"
          type="danger"
          @click="remove"
          >удалить({{ selection.length }})</el-button
        >
      </transition>
    </div>

    <el-table
      height="78vh"
      class="table_wrapper"
      ref="dataTable"
      border
      :data="dataTable"
      :default-sort="{ prop: 'receptionDate', order: 'descending' }"
      :header-cell-style="headerCellStyle"
      @selection-change="handleSelectionChange"
      @header-click="openDatePicker"
    >
      <el-table-column align="center" type="selection" width="65" prop="id" />

      <el-table-column
        v-if="$isAdmin()"
        align="center"
        width="160"
        prop="id"
        label="номер заявки"
      />

      <el-table-column prop="label" label="организация" sortable />
      <el-table-column prop="receptionDate" width="160" align="center">
        <template #header>
          время записи

          <el-date-picker
            ref="datePicker"
            style="width:0"
            align="left"
            placeholder="выберите дату"
            v-model="search"
            type="date"
            value-format="yyyy-MM-dd"
            prefix-icon=" "
            :class="$style.datePicker"
            :picker-options="pickerOptions"
          >
          </el-date-picker>
        </template>
      </el-table-column>

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

      <el-table-column align="center" width="200">
        <template #header>
          <el-input
            v-model="search"
            size="small"
            placeholder="наименование, инн"
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
      :total="applist.length"
      :current-page="currentPage"
      @size-change="handleSizeChange"
      @current-change="handleCurrentChange"
    ></el-pagination>

    <Dialog ref="dialog" />
  </div>
</template>
<script>
import TableManage from "@/mixins/TableManage";
import Pagination from "@/mixins/Pagination";
import Manage from "@/dialogs/application/Manage";

export default {
  components: { Dialog: Manage },

  mixins: [TableManage, Pagination],

  data() {
    return {
      entity: "applist",
      filterDate: null,
      search: "",
      pageSize: 20,

      pickerOptions: {
        shortcuts: [
          {
            text: "сегодня",
            onClick(picker) {
              picker.$emit("pick", new Date());
            }
          }
        ]
      }
    };
  },

  computed: {
    applist() {
      return this.$store.getters["applist/getList"]("items");
    },

    displayedApplist() {
      return this.paginate(this.applist);
    },

    dataTable() {
      return this.displayedApplist.filter(item => {
        return (
          !this.search ||
          item.label.toLowerCase().includes(this.search.toLowerCase()) ||
          item.receptionDate.includes(this.search.toLowerCase()) ||
          String(item.taxId).includes(this.search.toLowerCase())
        );
      });
    }
  },

  async created() {
    try {
      this.$isLoading();
      this.$setHeaderTitle("Заявки");

      await this.loadRefs();
      await this.loadApplist();
    } catch (e) {
      return;
    } finally {
      this.$isLoading(false);
    }
  },

  methods: {
    async loadApplist() {
      await this.$store.dispatch(`${this.entity}/loadData`, {
        route: "get-list"
      });
    },

    async loadRefs() {
      if (this.$store.getters["reference/list"].length) return;

      await this.$store.dispatch("reference/loadData", {
        route: "get-list",
        payload: { full: false }
      });
    },

    headerCellStyle(row) {
      if (row.column.property == "receptionDate") return "cursor:pointer;";
    },

    openDatePicker({ property }) {
      if (property == "receptionDate") this.$refs.datePicker.focus();
    }
  }
};
</script>
<style module>
.datePicker input {
  padding: 0 !important;
  width: 0;
  border: none;
}
</style>
