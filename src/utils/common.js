/* eslint-disable no-prototype-builtins */
export const dateHelper = {
  currentDate: null,
  year: null,
  month: null,
  day: null,
  hour: null,
  minutes: null,
  seconds: null,

  _initialize(customDate) {
    this.currentDate = customDate ? new Date(customDate) : new Date();
    this.year = this.currentDate.getFullYear();
    this.month = this.currentDate.getMonth() + 1;
    this.day = this.currentDate.getDate();
    this.hour = this.currentDate.getHours();
    this.minutes = this.currentDate.getMinutes();
    this.seconds = this.currentDate.getSeconds();
  },

  getDate(customDate, delimiter = "-") {
    this._initialize(customDate);

    return `${this.year}${delimiter}${
      this.month < 10 ? "0" + this.month : this.month
    }${delimiter}${this.day < 10 ? "0" + this.day : this.day}`;
  },

  getDateTime(customDate, delimiter = "-") {
    this._initialize(customDate);

    return `${this.year}${delimiter}${
      this.month < 10 ? "0" + this.month : this.month
    }${delimiter}${this.day < 10 ? "0" + this.day : this.day} ${this.hour}:${
      this.minutes < 10 ? "0" + this.minutes : this.minutes
    }:${this.seconds < 10 ? "0" + this.seconds : this.seconds}`;
  }
};

export const dataSet = (destinationObject, sourceObject) => {
  const result = destinationObject;

  for (const prop in sourceObject) {
    result[prop] = sourceObject[prop];
  }

  return result;
};

export const throttle = (func, ms) => {
  let isThrottled = false;
  let savedArgs;
  let savedThis;

  function wrapper() {
    if (isThrottled) {
      savedArgs = arguments;
      savedThis = this;
      return;
    }

    func.apply(this, arguments);

    isThrottled = true;

    setTimeout(function() {
      isThrottled = false;
      if (savedArgs) {
        wrapper.apply(savedThis, savedArgs);
        savedArgs = savedThis = null;
      }
    }, ms);
  }

  return wrapper;
};

export const getPayloadData = object => {
  const payload = {};

  for (const key in object) {
    if (object.hasOwnProperty(key)) {
      const prop = object[key];

      if (prop) {
        payload[key] = prop;
      }
    }
  }

  return payload;
};

export const isEmptyObject = object => {
  for (const key in object) {
    if (object.hasOwnProperty(key)) {
      return false;
    }
  }

  return true;
};

export const removeEmptyFields = object => {
  const result = {};

  Object.keys(object).forEach(key => {
    if (object[key] !== null && Object.keys(object[key]).length) {
      result[key] = object[key];
    }
  });

  return result;
};

export const getRandomInt = (min, max) => {
  min = Math.ceil(min);
  max = Math.floor(max);

  return Math.floor(Math.random() * (max - min + 1)) + min;
};

export const declOfNum = (number, titles) => {
  const cases = [2, 0, 1, 1, 1, 2];

  return titles[
    number % 100 > 4 && number % 100 < 20
      ? 2
      : cases[number % 10 < 5 ? number % 10 : 5]
  ];
};
