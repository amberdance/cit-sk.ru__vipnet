export default {
  data() {
    return {
      selectedRows: [],
    };
  },

  methods: {
    clearSelection() {
      this.selectedRows = [];

      if (_.has(this.$refs, "table")) this.$refs.table.clearSelection();
    },

    handleSelectionChange(rows) {
      this.selectedRows = rows;
    },

    collectIds() {
      this.selectedRows = this.selectedRows
        .map(({ id }) => id)
        .filter((id) => id);
    },

    defaultFormatter(row, column, value) {
      return value || "-";
    },
  },
};
