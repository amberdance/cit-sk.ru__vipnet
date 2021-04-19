<template>
  <div class="d-flex">
    <div class="content_wrapper">
      <el-table
        height="83vh"
        class="table_wrapper"
        ref="dataTable"
        border
        :data="dataTable"
        :default-sort="{ prop: 'id', order: 'descending' }"
        :header-cell-style="headerCellStyle"
        @selection-change="handleSelectionChange"
        @header-click="openDatePicker"
      >
        <el-table-column align="center" type="selection" width="65" prop="id" />

        <el-table-column
          v-if="$isAdmin()"
          align="center"
          width="100"
          prop="id"
          sortable
          label="ID"
        />

        <el-table-column prop="label" label="организация" width="250" sortable>
          <template #default="{ row }">
            {{ row.label || "-" }}
          </template>
        </el-table-column>

        <el-table-column prop="taxId" label="инн" align="center" width="150">
          <template #default="{ row }">
            {{ row.taxId || "-" }}
          </template>
        </el-table-column>

        <el-table-column
          prop="receptionDate"
          width="190"
          align="center"
          sortable
        >
          <template #header>
            время записи
            <el-date-picker
              ref="datePicker"
              style="width: 0"
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
          sortable
        />

        <el-table-column
          prop="personCount"
          label="кол-во человек"
          width="120"
          align="center"
          sortable
        />

        <el-table-column prop="note" label="комментарий">
          <template #default="{ row }">
            {{ row.note || "-" }}
          </template>
        </el-table-column>

        <el-table-column align="center" width="100">
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
        :page-size="pageSize"
        :total="applist.length"
        :current-page="currentPage"
        @size-change="handleSizeChange"
        @current-change="handleCurrentChange"
      ></el-pagination>

      <Dialog ref="dialog" />
    </div>

    <div class="right_aside">
      <ApplistFilter
        slot="rightPanel"
        :filter-params="filterParams"
        :selected-rows-count="selection.length"
        @onRowsRemove="remove"
        @onCreateApplication="$refs.dialog.show()"
      />
    </div>
  </div>
</template>

<script>
import ApplistFilter from "@/components/applist/ApplistFilter";
import TableManage from "@/mixins/TableManage";
import Pagination from "@/mixins/Pagination";
import Manage from "@/dialogs/application/Manage";

export default {
  components: { Dialog: Manage, ApplistFilter },

  mixins: [TableManage, Pagination],

  data() {
    return {
      entity: "applist",
      filterDate: null,
      pageSize: 50,

      filterParams: {
        id: null,
        label: null,
        taxId: null,
        signatureTypeId: []
      },

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

    dataTable() {
      return this.paginate(
        this.applist
          .filter(
            ({ id }) =>
              !this.filterParams.id || String(id).includes(this.filterParams.id)
          )

          .filter(
            ({ label }) =>
              !label ||
              !this.filterParams.label ||
              label
                .toLowerCase()
                .includes(this.filterParams.label.toLowerCase())
          )

          .filter(
            ({ taxId }) =>
              !taxId ||
              !this.filterParams.taxId ||
              String(taxId).includes(this.filterParams.taxId.toLowerCase())
          )

          .filter(
            ({ signatureTypeId }) =>
              !this.filterParams.signatureTypeId.length ||
              this.filterParams.signatureTypeId.includes(
                Number(signatureTypeId)
              )
          )

          .sort((a, b) => b.id - a.id)
      );
    }
  },

  async created() {
    this.$isLoading();
    this.$setHeaderTitle("Заявки");

    try {
      await this.loadApplist();
      // await this.loadRefs();
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

    isFridayToday() {
      return Boolean(new Date().getDay() == 5);
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
