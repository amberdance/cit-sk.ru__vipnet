import { dispatch } from "../api";

export default {
  async update({ commit }, { route, key = "items", payload }) {
    const responseData = await dispatch.HTTPPost({
      route,
      payload
    });

    commit("update", {
      key,
      props: { id: responseData.id, ...responseData }
    });
  },

  async remove({ commit }, { entity, key = "items", payload }) {
    await dispatch.HTTPPost({ route: `/${entity}/remove`, payload });

    if (Array.isArray(payload.id)) {
      return payload.id.forEach(id => {
        commit("remove", { key, id });
      });
    }

    commit("remove", { key, id: payload.id });
  },

  async purgeTable({ commit }, { entity, route, key }) {
    await dispatch.HTTPGet({
      route: `/${entity}/${route}`
    });

    commit("purge", key);
  }
};
