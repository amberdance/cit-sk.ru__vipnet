import { API_BASE_URL } from "../api-config";

export const auth = {
  loginMethod: "POST",
  logoutMethod: "GET",
  loginUrl: `${API_BASE_URL}/auth/login`,
  logoutUrl: `${API_BASE_URL}/auth/logout`,
  storage: window.localStorage,

  set accessToken(value) {
    this.saveInStorage("jwt", value);
  },

  get accessToken() {
    return this.getFromStorage("jwt");
  },

  set userRole(value) {
    this.saveInStorage("role", value);
  },

  get userRole() {
    return this.getFromStorage("role");
  },

  async login(formData) {
    const response = await this.HTTPRequest(
      this.loginMethod,
      this.loginUrl,
      formData
    );

    if (!response.status) return Promise.reject("Bad request");

    const { jwt, role } = response.data;

    this.accessToken = jwt;
    this.userRole = role;
  },

  logout() {
    this.purge();
    this.HTTPRequest(this.logoutMethod, this.logoutUrl);
  },

  getRole() {
    return Number(this.userRole);
  },

  getAccessToken() {
    return String(this.accessToken);
  },

  purge() {
    this.accessToken = null;
    this.userRole = null;
    localStorage.removeItem("activeTab");
  },

  isAuthorized() {
    return Boolean(this.accessToken);
  },

  async HTTPRequest(method, url, props) {
    const params = {
      method: method,
      mode: "cors",
      credentials: "omit",
      headers: {
        "Content-Type": "application/json"
      }
    };

    if (this.isAuthorized()) {
      params.headers.Authorization = `Bearer ${this.accessToken}`;
    }

    if (props) params.body = JSON.stringify(props);

    const response = await fetch(url, params);

    if (response.status < 200 || response.status >= 300) {
      const error = new Error(`${response.status} ${response.statusText}`);
      error.config = { response };

      throw error;
    }

    if (url === this.loginUrl) return response.json();
  },

  saveInStorage(key, value) {
    value ? this.storage.setItem(key, value) : this.storage.removeItem(key);
  },

  getFromStorage(key) {
    return this.storage.getItem(key);
  }
};
