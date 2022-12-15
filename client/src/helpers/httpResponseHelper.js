import { onError } from "@/plugins/alert";
import { has } from "lodash";

const errorCollection = {
  HTTP: {
    500: (e) => {
      return Promise.reject({
        code: e.response.status,
        message: e.response.data.message || e.response.data.error.message,
        errors: e.errors,
      });
    },

    422: (e) => {
      return Promise.reject({
        code: e.response.status,
        message: e.response.data.message || e.response.data.error.message,
        errors: e.errors,
      });
    },

    413: (error) => {
      onError("Превышен объем загружаемых данных");
      return Promise.reject(
        `${error.response.status} ${error.response.statusText}`
      );
    },

    405: (error) => {
      onError(
        `Метод ${error.config.method.toUpperCase()} запрещен для данного маршрута`
      );

      return Promise.reject(
        `${error.response.status} ${error.response.statusText}`
      );
    },

    404: (error) => {
      onError(`Маршрут ${error.response.config.url} не найден`);
      return Promise.reject({
        error: `${error.response.status} ${error.response.statusText}`,
        code: 404,
      });
    },

    403: (error) => {
      onError("Доступ запрещен");

      return Promise.reject({
        code: error.response.status,
        message:
          error.response.data.message ?? error.response.data.error.message,
      });
    },

    400: (error) => {
      onError("Параметры Http запроса указаны некорректно");
      return Promise.reject(
        `${error.response.status} ${error.response.statusText}`
      );
    },

    401: (error) => Promise.reject(error),
  },

  custom: {
    0: (e) => {
      onError(process.env.NODE_ENV == "development" ? e.error : "");
      if (process.env.NODE_ENV == "development") console.error(e.error);

      return Promise.reject("Bad response from server");
    },

    1062: (e) =>
      Promise.reject({
        code: e.response.status,
        message: e.response.data.message || e.response.data.error.message,
      }),
  },
};

export const responseManage = (response) => {
  if ("error" in response) return errorManage(response);
  else if (response.data == "") return Promise.resolve(response);
  else if ("data" in response.data) return Promise.resolve(response);
  else if ("error" in response.data) return errorManage(response.data);

  return Promise.resolve(response);
};

export const errorManage = (error) => {
  if ("response" in error) {
    if (error.response.status in errorCollection.HTTP)
      return errorCollection.HTTP[error.response.status](error);

    return has(errorCollection.custom, error.response.data.code)
      ? errorCollection.custom[error.code](error)
      : errorCollection.custom[0](error);
  }

  if ("code" in error) {
    if (
      error.code.toLowerCase().includes("err") ||
      error.message.toLowerCase().includes("err")
    )
      return Promise.reject(error);
  }

  return Promise.reject("error" in error ? error.error : error);
};
