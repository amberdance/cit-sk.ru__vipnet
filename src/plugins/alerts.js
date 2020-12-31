import Vue from "vue";
import { Notification } from "element-ui";

const alertsHandler = {
  successPhrases: [
    "Неплохо !",
    "Мойте руки",
    "Успех",
    "Отлично",
    "Было просто",
    "Здорово",
    "Триумфальный успех",
    "Судьбоносный успех",
    "Эпохальный успех",
    "Твои труды не напрасны"
  ],

  errorPhrases: [
    "Произошла ошибка",
    "Произошло что-то непредвиденное",
    "Что - то пошло не так",
    "Все пошло не так, как задумано",
    "Фатальная ошибка",
    "Роковая ошибка",
    "Эпохальная ошибка",
    "Триумфальная ошибка",
    "Судьбоносная ошибка",
    "Непростительная ошибка",
    "Неуместная ошибка",
    "Ошибка в ошибке"
  ],

  randomAlertPhrase(text, type, min, max) {
    if (!text) {
      const rand = min - 0.5 + Math.random() * (max + 1 - min);
      const index = Math.round(rand);

      return type === "success"
        ? this.successPhrases[index]
        : this.errorPhrases[index];
    }

    return text;
  },

  onSuccess(text = null, duration = 1500) {
    const message = this.randomAlertPhrase(
      text,
      "success",
      0,
      this.successPhrases.length - 1
    );

    return new Notification({
      type: "success",
      position: "bottom-right",
      message,
      duration
    });
  }
};

export const onWarning = (message = null, duration = 3000) => {
  return new Notification({
    type: "warning",
    position: "bottom-right",
    message,
    duration
  });
};

export const onError = (text = null, duration = 2000) => {
  const message = alertsHandler.randomAlertPhrase(
    text,
    "error",
    0,
    alertsHandler.errorPhrases.length - 1
  );

  return new Notification({
    type: "error",
    position: "bottom-right",
    message,
    duration
  });
};

const alerts = () => {
  Vue.prototype.$onSuccess = (message, duration) =>
    alertsHandler.onSuccess(message, duration);

  Vue.prototype.$onError = (message, duration) => onError(message, duration);

  Vue.prototype.$onWarning = (message, duration) =>
    onWarning(message, duration);
};

Vue.use(alerts);
