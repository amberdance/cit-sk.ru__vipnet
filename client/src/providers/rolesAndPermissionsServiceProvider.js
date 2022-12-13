import store from "@/store";
import { ROOT } from "@/values";

const me = () => store.getters["user/me"];

export const rolesAndPermissionsServiceProvider = {
  roles: () => me().roles,
  permissions: () => me().permissions,

  hasRole(role) {
    return this.errorSuppress(() => {
      if (me().roles.indexOf(ROOT) !== -1) return true;

      const roles = me().roles;

      return _.isArray(role)
        ? Boolean(role.filter((r) => roles.indexOf(r.trim()) !== -1).length)
        : roles.indexOf(role.trim()) !== -1;
    });
  },

  not(role) {
    return this.errorSuppress(() => {
      if (me().roles.indexOf(ROOT) !== -1) return false;

      const roles = me().roles;

      return _.isArray(role)
        ? Boolean(role.filter((r) => roles.indexOf(r.trim()) !== -1).length) ==
            false
        : roles.indexOf(role.trim()) == -1;
    });
  },

  can(permission) {
    return this.errorSuppress(() => {
      if (me().roles.indexOf(ROOT) !== -1) return true;
      const permissions = me().permissions;

      return _.isArray(permission)
        ? Boolean(
            permission.filter((p) => permissions.indexOf(p.trim()) !== -1)
              .length
          )
        : permissions.indexOf(permission.trim()) !== -1;
    });
  },

  cannot(permission) {
    return this.errorSuppress(() => {
      if (me().roles.indexOf(ROOT) !== -1) return false;

      const permissions = me().permissions;

      return _.isArray(permission)
        ? Boolean(
            !permission.filter((p) => permissions.indexOf(p.trim()) === -1)
              .length
          )
        : permissions.indexOf(permission.trim()) === -1;
    });
  },

  errorSuppress: (callback) => {
    try {
      return callback();
    } catch (e) {
      //
    }
  },
};
