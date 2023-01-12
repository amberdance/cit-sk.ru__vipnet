export const downloadFile = (blob, fileName = null) => {
  const link = document.createElement("a");

  link.href = window.URL.createObjectURL(
    new Blob([blob], { type: "octet/stream" })
  );

  link.setAttribute("download", fileName ?? blob.fileName);

  document.body.appendChild(link);
  link.click();
  link.remove();
  URL.revokeObjectURL(link.href);
};

export const declOfNum = (number, titles) => {
  const cases = [2, 0, 1, 1, 1, 2];

  return titles[
    number % 100 > 4 && number % 100 < 20
      ? 2
      : cases[number % 10 < 5 ? number % 10 : 5]
  ];
};

export const deepDiffObject = (base, object) => {
  if (!object) {
    throw new Error(`The object compared should be an object: ${object}`);
  }

  if (!base) return object;

  const result = _.transform(object, (result, value, key) => {
    if (!_.has(base, key)) result[key] = value;
    if (!_.isEqual(value, base[key])) {
      result[key] =
        _.isPlainObject(value) && _.isPlainObject(base[key])
          ? deepDiffObject(base[key], value)
          : value;
    }
  });

  _.forOwn(base, (value, key) => {
    if (!_.has(object, key)) result[key] = undefined;
  });

  return result;
};

export const camelize = (obj) =>
  _.transform(obj, (acc, value, key, target) => {
    const camelKey = _.isArray(target) ? key : _.camelCase(key);

    acc[camelKey] = _.isObject(value) ? camelize(value) : value;
  });

// Does not provide a nested objects
export const removeEmptyFields = (obj, filterBoolean = false) => {
  let result = _.omitBy(
    obj,
    (val) => _.isUndefined(val) || _.isNull(val) || val === ""
  );

  if (filterBoolean) result = _.omitBy(result, (val) => val === false);

  return result;
};
