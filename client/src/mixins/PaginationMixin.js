export default {
  data() {
    return {
      pagination: {
        total: 0,
        currentPage: 1,
        pageSizes: [50, 100, 200, 300, 400, 500],
      },
    };
  },

  methods: {
    handleSizeChange(size) {
      this.filter.perPage = size;
    },

    handleClickPage(page) {
      this.pagination.currentPage = page;
      this.filter.page = page;
    },
  },
};
