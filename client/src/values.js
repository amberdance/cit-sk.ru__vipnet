export const ROOT = "root";
export const ADMIN = "admin";
export const USER = "user";

export const MENU = [
  {
    title: "Заявки",
    route: "/applist",
    icon: "el-icon-menu",
    roles: [ADMIN, USER],
  },

  {
    title: "Организации",
    route: "/organizations",
    icon: "el-icon-office-building",
    roles: [ADMIN, USER],
  },
];
