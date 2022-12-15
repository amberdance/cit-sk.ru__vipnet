export const matchPasswordsValidator = (
  password,
  comparePassword,
  callback
) => {
  if (!password) return callback(new Error("Повторите введенный пароль"));
  if (password !== comparePassword)
    return callback(new Error("Введенные пароли не совпадают"));

  return callback();
};

export const emailValidator = (email) =>
  /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/u.test(
    email
  );

export const passwordStrengthValidator = (password) =>
  /^(?=(.*[a-z]){2,})(?=(.*[A-Z]){1,})(?=(.*[0-9]){1,}).{6,}$/g.test(password);

export const incomeCallCodeValidator = (code) => /^\d{4}/g.test(code);

export const birthdatValidator = (birthday) =>
  /^(?:0[1-9]|[12]\d|3[01])([/.-])(?:0[1-9]|1[012])\1(?:19|20)\d\d$/g.test(
    birthday
  );

export const сyrillicValidator = (value) => /^[а-яё]+$/giu.test(value);

export const addressValidator = (address) =>
  /^[а-яё\d.,: \-\\/\\]+$/giu.test(address);
