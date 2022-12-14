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

    dateSortMethod(a, b, prop) {
      const aa = new Date(this.parseDate(a[prop]));
      const bb = new Date(this.parseDate(b[prop]));

      return aa > bb ? -1 : aa == bb ? 0 : 1;
    },

    parseDate(date) {
      return date
        .split(/[ -.//]/)
        .reverse()
        .join(" ");
    },
  },
};
