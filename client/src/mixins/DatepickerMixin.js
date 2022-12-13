export default {
  data() {
    return {
      pickerOptions: {
        shortcuts: [
          {
            text: "Сегодня",
            onClick: (picker) => {
              const date = new Date();
              picker.$emit("pick", [date, date]);
            },
          },

          {
            text: "Текущий  месяц",
            onClick: (picker) =>
              picker.$emit("pick", [
                new Date(new Date().getFullYear(), new Date().getMonth(), 1),
                new Date(
                  new Date().getFullYear(),
                  new Date().getMonth() + 1,
                  0
                ),
              ]),
          },

          {
            text: "Текущий год",
            onClick: (picker) =>
              picker.$emit("pick", [
                new Date(new Date().getFullYear(), 0),
                new Date(new Date().getFullYear(), 11, 31),
              ]),
          },

          {
            text: "Прошлый год",
            onClick: (picker) =>
              picker.$emit("pick", [
                new Date(new Date().getFullYear() - 1, 0),
                new Date(new Date().getFullYear() - 1, 11, 31),
              ]),
          },
        ],
      },
    };
  },
};
