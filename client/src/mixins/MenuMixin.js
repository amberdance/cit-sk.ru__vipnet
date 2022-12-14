import { MENU } from "@/values";

export default {
  data() {
    return {
      menu: [],
    };
  },

  methods: {
    createMenu() {
      let rootIndex = 1;
      let subIndex = 1;

      MENU.forEach((parent) => {
        if (!this.$user.hasRole(parent.roles)) return;

        if (_.has(parent, "childrens")) {
          parent.childrens = parent.childrens.filter((children) => {
            children.index = `${rootIndex}-${subIndex}`;
            subIndex++;

            if (_.has(children, "roles") && this.$user.hasRole(children.roles))
              return true;

            if (!_.has(children, "roles") && this.$user.hasRole(parent.roles))
              return true;
          });
        }

        this.menu.push({ ...parent, index: String(rootIndex) });

        subIndex = 1;
        rootIndex++;
      });
    },
  },
};
