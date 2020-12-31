export default {
  data() {
    return {
      currentPage: 1,
      pageSizes: [50, 100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1500],
      pageSize: 100
    };
  },

  methods: {
    paginate(rows) {
      const from = this.currentPage * this.pageSize - this.pageSize;
      const to = this.currentPage * this.pageSize;

      return rows.slice(from, to);
    },

    handleCurrentChange(selectedPage) {
      this.currentPage = selectedPage;
    },

    handleSizeChange(size) {
      this.pageSize = size;
    }
  }
};
