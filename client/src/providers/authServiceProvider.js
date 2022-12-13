import VueCookies from "vue-cookies-reactive";

export const authServiceProvider = {
  loginUrl: `${process.env.VUE_APP_API_URL}/auth/login`,
  logoutUrl: `${process.env.VUE_APP_API_URL}/auth/logout`,

  async login(params) {
    try {
      const response = await this.http("POST", this.loginUrl, params);

      if ("error" in response) return Promise.reject(response);

      const { access_token, expires_in } = response.data;

      VueCookies.set("access_token", access_token, String(expires_in) + "s");
      VueCookies.set("token_expires_in", expires_in, String(expires_in) + "s");

      return Promise.resolve(response.data.user);
    } catch (e) {
      return Promise.reject(e);
    }
  },

  async http(method, url, props) {
    const params = {
      method,
      mode: "cors",
      credentials: "omit",
      headers: {
        "Content-Type": "application/json",
      },
    };

    if (this.isAuthorized()) {
      params.headers.Authorization = `Bearer ${VueCookies.get("access_token")}`;
    }

    if (props) params.body = JSON.stringify(props);

    const response = await fetch(url, params);

    if (response.status < 200 || response.status >= 300) {
      const error = new Error(`${response.status} ${response.statusText}`);
      error.config = { response };

      throw error;
    }

    if (url == this.loginUrl) return response.json();
  },

  logout() {
    try {
      this.http("GET", this.logoutUrl);
    } catch (e) {
      return;
    } finally {
      this.purge();
    }
  },

  isAuthorized() {
    return Boolean(VueCookies.get("access_token"));
  },

  purge() {
    VueCookies.remove("access_token");
    VueCookies.remove("token_expires_in");
    localStorage.removeItem("app_cache");
  },
};
