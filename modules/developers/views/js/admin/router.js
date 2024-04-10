import * as routing from './es6-fos-routing.js'

const Routing = routing.default
const routes = {
    "base_url": "",
    "routes": {},
    "prefix": "",
    "host": "localhost",
    "port": "",
    "scheme": "http",
    "locale": ""
}

export default class Router {
    constructor() {
      if (window.prestashop && window.prestashop.customRoutes) {
        Object.assign(routes.routes, window.prestashop.customRoutes);
      }
  
      Routing.setData(routes);
      Routing.setBaseUrl(
        document.
        querySelector('body')
        .dataset.baseUrl,
      );
  
      return this;
    }
  
    /**
     * Decorated "generate" method, with predefined security token in params
     *
     * @param route
     * @param params
     *
     * @returns {String}
     */
    generate(route, params = {}) {
      const tokenizedParams = Object.assign(params, {
        _token: document.
            querySelector('body')
            .dataset.token,
      });
  
      return Routing.generate(route, tokenizedParams);
    }
  }