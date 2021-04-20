export default {
  data() {
    return {
      pickerOptions: {
        shortcuts: [
          {
            text: "Текущий  месяц",
            onClick(picker) {
              const start = new Date(
                new Date().getFullYear(),
                new Date().getMonth(),
                1
              );

              const end = new Date(
                new Date().getFullYear(),
                new Date().getMonth() + 1,
                0
              );
              picker.$emit("pick", [start, end]);
            }
          },

          {
            text: "Текущий год",
            onClick(picker) {
              const start = new Date(new Date().getFullYear(), 0);
              const end = new Date(new Date().getFullYear(), 11, 31);

              picker.$emit("pick", [start, end]);
            }
          },

          {
            text: "Прошлый год",
            onClick(picker) {
              const start = new Date(new Date().getFullYear() - 1, 0);
              const end = new Date(new Date().getFullYear() - 1, 11, 31);

              picker.$emit("pick", [start, end]);
            }
          }
        ]
      }
    };
  }
};
