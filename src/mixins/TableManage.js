export default {
  data() {
    return {
      selection: [],
      search: null
    };
  },

  methods: {
    handleSelectionChange(rows) {
      this.selection = [];

      rows.forEach(({ id }) => this.selection.push({ id }));
    },

    async remove() {
      if (this.selection.length >= 5) {
        try {
          await this.$confirm(
            `Вы собираетесь удалить более ${this.selection.length} записей, возможно, Вы хотите пересмотреть свое решение`,
            "Удаляем записи?",
            {
              type: "warning",
              confirmButtonText: "да",
              cancelButtonText: "нет, мне нужно все хорошенько обдумать"
            }
          );
        } catch (e) {
          this.selection = [];
          this.$refs.dataTable.clearSelection();
          return;
        }
      }

      try {
        this.$isLoading();
        const id = this.selection.map(item => item.id);

        await this.$store.dispatch(`${this.entity}/remove`, {
          entity: this.entity,
          key: this.storageKey ?? "items",
          payload: {
            completelyRemove: this.isCompletelyRemove,
            id: id.length === 1 ? id[0] : id
          }
        });

        this.selection = [];
        this.$refs.dataTable.clearSelection();
        this.$onSuccess();
      } catch (e) {
        return;
      } finally {
        this.$isLoading(false);
      }
    },

    async purgeTableData() {
      await this.$confirm(
        "Удалить таблицу? В отличии от заявок эти данные удаляются навсегда",
        {
          type: "warning",
          confirmButtonText: "да",
          cancelButtonText: "нет"
        }
      );

      try {
        this.$isLoading();

        await this.$store.dispatch(`${this.entity}/purgeTable`, {
          entity: this.entity,
          route: this.route,
          key: this.key
        });

        this.$onSuccess();
      } catch (e) {
        return;
      } finally {
        this.$isLoading(false);
      }
    }
  }
};
