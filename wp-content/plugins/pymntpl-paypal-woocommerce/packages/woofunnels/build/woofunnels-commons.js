"use strict";
(self["webpackChunkwcPPCP"] = self["webpackChunkwcPPCP"] || []).push([["woofunnels-commons"],{

/***/ "./node_modules/@paypal/paypal-js/dist/esm/paypal-js.js":
/*!**************************************************************!*\
  !*** ./node_modules/@paypal/paypal-js/dist/esm/paypal-js.js ***!
  \**************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "loadCustomScript": () => (/* binding */ loadCustomScript),
/* harmony export */   "loadScript": () => (/* binding */ loadScript),
/* harmony export */   "version": () => (/* binding */ version)
/* harmony export */ });
/*!
 * paypal-js v5.1.1 (2022-08-03T17:21:59.218Z)
 * Copyright 2020-present, PayPal, Inc. All rights reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
function findScript(url, attributes) {
    var currentScript = document.querySelector("script[src=\"".concat(url, "\"]"));
    if (currentScript === null)
        return null;
    var nextScript = createScriptElement(url, attributes);
    // ignore the data-uid-auto attribute that gets auto-assigned to every script tag
    var currentScriptClone = currentScript.cloneNode();
    delete currentScriptClone.dataset.uidAuto;
    // check if the new script has the same number of data attributes
    if (Object.keys(currentScriptClone.dataset).length !==
        Object.keys(nextScript.dataset).length) {
        return null;
    }
    var isExactMatch = true;
    // check if the data attribute values are the same
    Object.keys(currentScriptClone.dataset).forEach(function (key) {
        if (currentScriptClone.dataset[key] !== nextScript.dataset[key]) {
            isExactMatch = false;
        }
    });
    return isExactMatch ? currentScript : null;
}
function insertScriptElement(_a) {
    var url = _a.url, attributes = _a.attributes, onSuccess = _a.onSuccess, onError = _a.onError;
    var newScript = createScriptElement(url, attributes);
    newScript.onerror = onError;
    newScript.onload = onSuccess;
    document.head.insertBefore(newScript, document.head.firstElementChild);
}
function processOptions(options) {
    var sdkBaseURL = "https://www.paypal.com/sdk/js";
    if (options.sdkBaseURL) {
        sdkBaseURL = options.sdkBaseURL;
        delete options.sdkBaseURL;
    }
    processMerchantID(options);
    var _a = Object.keys(options)
        .filter(function (key) {
        return (typeof options[key] !== "undefined" &&
            options[key] !== null &&
            options[key] !== "");
    })
        .reduce(function (accumulator, key) {
        var value = options[key].toString();
        if (key.substring(0, 5) === "data-") {
            accumulator.dataAttributes[key] = value;
        }
        else {
            accumulator.queryParams[key] = value;
        }
        return accumulator;
    }, {
        queryParams: {},
        dataAttributes: {},
    }), queryParams = _a.queryParams, dataAttributes = _a.dataAttributes;
    return {
        url: "".concat(sdkBaseURL, "?").concat(objectToQueryString(queryParams)),
        dataAttributes: dataAttributes,
    };
}
function objectToQueryString(params) {
    var queryString = "";
    Object.keys(params).forEach(function (key) {
        if (queryString.length !== 0)
            queryString += "&";
        queryString += key + "=" + params[key];
    });
    return queryString;
}
/**
 * Parse the error message code received from the server during the script load.
 * This function search for the occurrence of this specific string "/* Original Error:".
 *
 * @param message the received error response from the server
 * @returns the content of the message if the string string was found.
 *          The whole message otherwise
 */
function parseErrorMessage(message) {
    var originalErrorText = message.split("/* Original Error:")[1];
    return originalErrorText
        ? originalErrorText.replace(/\n/g, "").replace("*/", "").trim()
        : message;
}
function createScriptElement(url, attributes) {
    if (attributes === void 0) { attributes = {}; }
    var newScript = document.createElement("script");
    newScript.src = url;
    Object.keys(attributes).forEach(function (key) {
        newScript.setAttribute(key, attributes[key]);
        if (key === "data-csp-nonce") {
            newScript.setAttribute("nonce", attributes["data-csp-nonce"]);
        }
    });
    return newScript;
}
function processMerchantID(options) {
    var merchantID = options["merchant-id"], dataMerchantID = options["data-merchant-id"];
    var newMerchantID = "";
    var newDataMerchantID = "";
    if (Array.isArray(merchantID)) {
        if (merchantID.length > 1) {
            newMerchantID = "*";
            newDataMerchantID = merchantID.toString();
        }
        else {
            newMerchantID = merchantID.toString();
        }
    }
    else if (typeof merchantID === "string" && merchantID.length > 0) {
        newMerchantID = merchantID;
    }
    else if (typeof dataMerchantID === "string" &&
        dataMerchantID.length > 0) {
        newMerchantID = "*";
        newDataMerchantID = dataMerchantID;
    }
    options["merchant-id"] = newMerchantID;
    options["data-merchant-id"] = newDataMerchantID;
    return options;
}

/**
 * Load the Paypal JS SDK script asynchronously.
 *
 * @param {Object} options - used to configure query parameters and data attributes for the JS SDK.
 * @param {PromiseConstructor} [PromisePonyfill=window.Promise] - optional Promise Constructor ponyfill.
 * @return {Promise<Object>} paypalObject - reference to the global window PayPal object.
 */
function loadScript(options, PromisePonyfill) {
    if (PromisePonyfill === void 0) { PromisePonyfill = getDefaultPromiseImplementation(); }
    validateArguments(options, PromisePonyfill);
    // resolve with null when running in Node
    if (typeof window === "undefined")
        return PromisePonyfill.resolve(null);
    var _a = processOptions(options), url = _a.url, dataAttributes = _a.dataAttributes;
    var namespace = dataAttributes["data-namespace"] || "paypal";
    var existingWindowNamespace = getPayPalWindowNamespace(namespace);
    // resolve with the existing global paypal namespace when a script with the same params already exists
    if (findScript(url, dataAttributes) && existingWindowNamespace) {
        return PromisePonyfill.resolve(existingWindowNamespace);
    }
    return loadCustomScript({
        url: url,
        attributes: dataAttributes,
    }, PromisePonyfill).then(function () {
        var newWindowNamespace = getPayPalWindowNamespace(namespace);
        if (newWindowNamespace) {
            return newWindowNamespace;
        }
        throw new Error("The window.".concat(namespace, " global variable is not available."));
    });
}
/**
 * Load a custom script asynchronously.
 *
 * @param {Object} options - used to set the script url and attributes.
 * @param {PromiseConstructor} [PromisePonyfill=window.Promise] - optional Promise Constructor ponyfill.
 * @return {Promise<void>} returns a promise to indicate if the script was successfully loaded.
 */
function loadCustomScript(options, PromisePonyfill) {
    if (PromisePonyfill === void 0) { PromisePonyfill = getDefaultPromiseImplementation(); }
    validateArguments(options, PromisePonyfill);
    var url = options.url, attributes = options.attributes;
    if (typeof url !== "string" || url.length === 0) {
        throw new Error("Invalid url.");
    }
    if (typeof attributes !== "undefined" && typeof attributes !== "object") {
        throw new Error("Expected attributes to be an object.");
    }
    return new PromisePonyfill(function (resolve, reject) {
        // resolve with undefined when running in Node
        if (typeof window === "undefined")
            return resolve();
        insertScriptElement({
            url: url,
            attributes: attributes,
            onSuccess: function () { return resolve(); },
            onError: function () {
                var defaultError = new Error("The script \"".concat(url, "\" failed to load."));
                if (!window.fetch) {
                    return reject(defaultError);
                }
                // Fetch the error reason from the response body for validation errors
                return fetch(url)
                    .then(function (response) {
                    if (response.status === 200) {
                        reject(defaultError);
                    }
                    return response.text();
                })
                    .then(function (message) {
                    var parseMessage = parseErrorMessage(message);
                    reject(new Error(parseMessage));
                })
                    .catch(function (err) {
                    reject(err);
                });
            },
        });
    });
}
function getDefaultPromiseImplementation() {
    if (typeof Promise === "undefined") {
        throw new Error("Promise is undefined. To resolve the issue, use a Promise polyfill.");
    }
    return Promise;
}
function getPayPalWindowNamespace(namespace) {
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    return window[namespace];
}
function validateArguments(options, PromisePonyfill) {
    if (typeof options !== "object" || options === null) {
        throw new Error("Expected an options object.");
    }
    if (typeof PromisePonyfill !== "undefined" &&
        typeof PromisePonyfill !== "function") {
        throw new Error("Expected PromisePonyfill to be a function.");
    }
}

// replaced with the package.json version at build time
var version = "5.1.1";




/***/ }),

/***/ "./assets/js/ppcp/cart.js":
/*!********************************!*\
  !*** ./assets/js/ppcp/cart.js ***!
  \********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @babel/runtime/regenerator */ "@babel/runtime/regenerator");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _ppcp_utils__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! @ppcp/utils */ "@ppcp/utils");
/* harmony import */ var _ppcp_utils__WEBPACK_IMPORTED_MODULE_10___default = /*#__PURE__*/__webpack_require__.n(_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11__);
/* harmony import */ var _event__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(/*! ./event */ "./assets/js/ppcp/event.js");









function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }



function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_7__["default"])(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_7__["default"])(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0,_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_6__["default"])(this, result); }; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }






var Cart = /*#__PURE__*/function (_Event) {
  (0,_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_5__["default"])(Cart, _Event);

  var _super = _createSuper(Cart);

  function Cart() {
    var _this;

    (0,_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_2__["default"])(this, Cart);

    _this = _super.call(this);
    _this.data = (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.getSetting)('cart');
    _this.page = (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.getSetting)('generalData').page;
    _this.processing = false;
    jquery__WEBPACK_IMPORTED_MODULE_9___default()(document.body).on('updated_wc_div', _this.onCartUpdated.bind((0,_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_4__["default"])(_this)));
    jquery__WEBPACK_IMPORTED_MODULE_9___default()(document.body).on('updated_cart_totals', _this.onCartUpdated.bind((0,_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_4__["default"])(_this)));
    jquery__WEBPACK_IMPORTED_MODULE_9___default()(document.body).on('updated_checkout', _this.onUpdatedCheckout.bind((0,_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_4__["default"])(_this)));
    jquery__WEBPACK_IMPORTED_MODULE_9___default()(document.body).on('wc_fragments_refreshed wc_fragments_loaded', _this.onCartFragmentsChanged.bind((0,_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_4__["default"])(_this)));
    return _this;
  }

  (0,_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_3__["default"])(Cart, [{
    key: "onCartUpdated",
    value: function () {
      var _onCartUpdated = (0,_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__["default"])( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().mark(function _callee(e) {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().wrap(function _callee$(_context) {
          while (1) {
            switch (_context.prev = _context.next) {
              case 0:
                if (!window.wcPPCPCartData) {
                  _context.next = 5;
                  break;
                }

                _context.next = 3;
                return this.refreshData(window.wcPPCPCartData);

              case 3:
                _context.next = 7;
                break;

              case 5:
                _context.next = 7;
                return this.refreshData(null);

              case 7:
                (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.setSetting)('queryParams', this.data.queryParams);
                this.trigger('cartUpdated', this);

              case 9:
              case "end":
                return _context.stop();
            }
          }
        }, _callee, this);
      }));

      function onCartUpdated(_x) {
        return _onCartUpdated.apply(this, arguments);
      }

      return onCartUpdated;
    }()
  }, {
    key: "onUpdatedCheckout",
    value: function () {
      var _onUpdatedCheckout = (0,_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__["default"])( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().mark(function _callee2(e) {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().wrap(function _callee2$(_context2) {
          while (1) {
            switch (_context2.prev = _context2.next) {
              case 0:
                _context2.next = 2;
                return this.refreshData(window.wcPPCPCartData ? window.wcPPCPCartData : null);

              case 2:
                (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.setSetting)('queryParams', this.data.queryParams);
                this.trigger('updatedCheckout', this);

              case 4:
              case "end":
                return _context2.stop();
            }
          }
        }, _callee2, this);
      }));

      function onUpdatedCheckout(_x2) {
        return _onUpdatedCheckout.apply(this, arguments);
      }

      return onUpdatedCheckout;
    }()
  }, {
    key: "onCartFragmentsChanged",
    value: function onCartFragmentsChanged() {
      var _this2 = this;

      // fetch updated cart data
      setTimeout(function () {
        if (window.wcPPCPMiniCartUpdate) {
          _this2.data = _objectSpread(_objectSpread({}, _this2.data), wcPPCPMiniCartUpdate);
        }

        _this2.trigger('fragmentsChanged', _this2);
      }, 250);
    }
  }, {
    key: "getData",
    value: function getData() {
      return (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.getSetting)('cart');
    }
  }, {
    key: "needsShipping",
    value: function needsShipping() {
      var _this$data;

      return (_this$data = this.data) === null || _this$data === void 0 ? void 0 : _this$data.needsShipping;
    }
  }, {
    key: "refreshData",
    value: function () {
      var _refreshData = (0,_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__["default"])( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().mark(function _callee3() {
        var data,
            response,
            _args3 = arguments;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().wrap(function _callee3$(_context3) {
          while (1) {
            switch (_context3.prev = _context3.next) {
              case 0:
                data = _args3.length > 0 && _args3[0] !== undefined ? _args3[0] : null;

                if (!data) {
                  _context3.next = 5;
                  break;
                }

                this.data = _objectSpread(_objectSpread({}, this.data), data);
                _context3.next = 21;
                break;

              case 5:
                if (this.processing) {
                  _context3.next = 21;
                  break;
                }

                _context3.prev = 6;
                this.processing = true;
                _context3.next = 10;
                return _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11___default()({
                  method: 'POST',
                  url: (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.getRestRoute)('cart/refresh'),
                  data: {
                    page: this.page
                  }
                });

              case 10:
                response = _context3.sent;
                this.data = _objectSpread(_objectSpread({}, this.data), response.cart);
                this.data.queryParams = response.queryParams;
                _context3.next = 18;
                break;

              case 15:
                _context3.prev = 15;
                _context3.t0 = _context3["catch"](6);
                console.log(_context3.t0);

              case 18:
                _context3.prev = 18;
                this.processing = false;
                return _context3.finish(18);

              case 21:
              case "end":
                return _context3.stop();
            }
          }
        }, _callee3, this, [[6, 15, 18, 21]]);
      }));

      function refreshData() {
        return _refreshData.apply(this, arguments);
      }

      return refreshData;
    }()
  }, {
    key: "addToCart",
    value: function () {
      var _addToCart = (0,_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__["default"])( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().mark(function _callee4(data) {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().wrap(function _callee4$(_context4) {
          while (1) {
            switch (_context4.prev = _context4.next) {
              case 0:
                _context4.prev = 0;
                return _context4.abrupt("return", _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11___default()({
                  method: 'POST',
                  url: (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.getRestRoute)('cart/item'),
                  data: data
                }));

              case 4:
                _context4.prev = 4;
                _context4.t0 = _context4["catch"](0);
                throw _context4.t0;

              case 7:
              case "end":
                return _context4.stop();
            }
          }
        }, _callee4, null, [[0, 4]]);
      }));

      function addToCart(_x3) {
        return _addToCart.apply(this, arguments);
      }

      return addToCart;
    }()
  }, {
    key: "createOrder",
    value: function () {
      var _createOrder = (0,_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__["default"])( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().mark(function _callee5(data) {
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().wrap(function _callee5$(_context5) {
          while (1) {
            switch (_context5.prev = _context5.next) {
              case 0:
                _context5.prev = 0;
                return _context5.abrupt("return", _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11___default()({
                  method: 'POST',
                  url: (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.getRestRoute)('cart/order'),
                  data: data
                }));

              case 4:
                _context5.prev = 4;
                _context5.t0 = _context5["catch"](0);
                throw _context5.t0;

              case 7:
              case "end":
                return _context5.stop();
            }
          }
        }, _callee5, null, [[0, 4]]);
      }));

      function createOrder(_x4) {
        return _createOrder.apply(this, arguments);
      }

      return createOrder;
    }()
  }, {
    key: "doOrderPay",
    value: function () {
      var _doOrderPay = (0,_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_1__["default"])( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().mark(function _callee6(payment_method) {
        var order;
        return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_8___default().wrap(function _callee6$(_context6) {
          while (1) {
            switch (_context6.prev = _context6.next) {
              case 0:
                order = (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.getSetting)('order');
                _context6.prev = 1;
                return _context6.abrupt("return", _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_11___default()({
                  method: 'POST',
                  url: (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_10__.getRestRoute)('order/pay'),
                  data: _objectSpread({
                    payment_method: payment_method
                  }, order)
                }));

              case 5:
                _context6.prev = 5;
                _context6.t0 = _context6["catch"](1);
                throw _context6.t0;

              case 8:
              case "end":
                return _context6.stop();
            }
          }
        }, _callee6, null, [[1, 5]]);
      }));

      function doOrderPay(_x5) {
        return _doOrderPay.apply(this, arguments);
      }

      return doOrderPay;
    }()
  }, {
    key: "getTotal",
    value: function getTotal() {
      var _this$data2;

      return (_this$data2 = this.data) === null || _this$data2 === void 0 ? void 0 : _this$data2.total;
    }
  }]);

  return Cart;
}(_event__WEBPACK_IMPORTED_MODULE_12__["default"]);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (new Cart());

/***/ }),

/***/ "./assets/js/ppcp/event.js":
/*!*********************************!*\
  !*** ./assets/js/ppcp/event.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__);




var Event = /*#__PURE__*/function () {
  function Event() {
    (0,_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_0__["default"])(this, Event);

    this.hooks = (0,_wordpress_hooks__WEBPACK_IMPORTED_MODULE_2__.createHooks)();
  }

  (0,_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_1__["default"])(Event, [{
    key: "on",
    value: function on(event, callback) {
      var priority = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 10;
      this.hooks.addAction(event, 'wcPPCP', callback, priority);
    }
  }, {
    key: "trigger",
    value: function trigger(event) {
      var _this$hooks;

      for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
        args[_key - 1] = arguments[_key];
      }

      (_this$hooks = this.hooks).doAction.apply(_this$hooks, [event].concat(args));
    }
  }]);

  return Event;
}();

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Event);

/***/ }),

/***/ "./assets/js/ppcp/index.js":
/*!*********************************!*\
  !*** ./assets/js/ppcp/index.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Product": () => (/* reexport safe */ _product__WEBPACK_IMPORTED_MODULE_2__.Product),
/* harmony export */   "SHIPPING_OPTION_REGEX": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.SHIPPING_OPTION_REGEX),
/* harmony export */   "convertCartAddressToPayPal": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.convertCartAddressToPayPal),
/* harmony export */   "convertPayPalAddressToCart": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.convertPayPalAddressToCart),
/* harmony export */   "extractFullName": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.extractFullName),
/* harmony export */   "extractShippingMethod": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.extractShippingMethod),
/* harmony export */   "fieldsToJson": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.fieldsToJson),
/* harmony export */   "getErrorMessage": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.getErrorMessage),
/* harmony export */   "getFieldValue": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.getFieldValue),
/* harmony export */   "getPage": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.getPage),
/* harmony export */   "getPayPalQueryParams": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.getPayPalQueryParams),
/* harmony export */   "getRestPath": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.getRestPath),
/* harmony export */   "getRestRoute": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.getRestRoute),
/* harmony export */   "getSetting": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.getSetting),
/* harmony export */   "isPluginConnected": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.isPluginConnected),
/* harmony export */   "isValidAddress": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.isValidAddress),
/* harmony export */   "isValidFieldValue": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.isValidFieldValue),
/* harmony export */   "loadPayPalSdk": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.loadPayPalSdk),
/* harmony export */   "setFieldValue": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.setFieldValue),
/* harmony export */   "setSetting": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.setSetting),
/* harmony export */   "submitErrorMessage": () => (/* reexport safe */ _utils__WEBPACK_IMPORTED_MODULE_0__.submitErrorMessage)
/* harmony export */ });
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./utils */ "./assets/js/ppcp/utils.js");
/* harmony import */ var _cart__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./cart */ "./assets/js/ppcp/cart.js");
/* harmony import */ var _product__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./product */ "./assets/js/ppcp/product.js");




/***/ }),

/***/ "./assets/js/ppcp/product.js":
/*!***********************************!*\
  !*** ./assets/js/ppcp/product.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Product": () => (/* binding */ Product),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var _babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/classCallCheck */ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js");
/* harmony import */ var _babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/createClass */ "./node_modules/@babel/runtime/helpers/esm/createClass.js");
/* harmony import */ var _babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @babel/runtime/helpers/assertThisInitialized */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");
/* harmony import */ var _babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! @babel/runtime/helpers/inherits */ "./node_modules/@babel/runtime/helpers/esm/inherits.js");
/* harmony import */ var _babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @babel/runtime/helpers/possibleConstructorReturn */ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js");
/* harmony import */ var _babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @babel/runtime/helpers/getPrototypeOf */ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_7___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_7__);
/* harmony import */ var _ppcp_utils__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(/*! @ppcp/utils */ "@ppcp/utils");
/* harmony import */ var _ppcp_utils__WEBPACK_IMPORTED_MODULE_8___default = /*#__PURE__*/__webpack_require__.n(_ppcp_utils__WEBPACK_IMPORTED_MODULE_8__);
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_9___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_9__);
/* harmony import */ var _event__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(/*! ./event */ "./assets/js/ppcp/event.js");








function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_0__["default"])(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = (0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__["default"])(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = (0,_babel_runtime_helpers_getPrototypeOf__WEBPACK_IMPORTED_MODULE_6__["default"])(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return (0,_babel_runtime_helpers_possibleConstructorReturn__WEBPACK_IMPORTED_MODULE_5__["default"])(this, result); }; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); return true; } catch (e) { return false; } }






var Product = /*#__PURE__*/function (_Event) {
  (0,_babel_runtime_helpers_inherits__WEBPACK_IMPORTED_MODULE_4__["default"])(Product, _Event);

  var _super = _createSuper(Product);

  function Product() {
    var _this;

    (0,_babel_runtime_helpers_classCallCheck__WEBPACK_IMPORTED_MODULE_1__["default"])(this, Product);

    _this = _super.call(this);
    _this.data = _this.default_data = (0,_ppcp_utils__WEBPACK_IMPORTED_MODULE_8__.getSetting)('product');
    _this.variation = false;
    jquery__WEBPACK_IMPORTED_MODULE_7___default()(document.body).on('change', '[name="quantity"]', _this.onQuantityChange.bind((0,_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_3__["default"])(_this)));
    jquery__WEBPACK_IMPORTED_MODULE_7___default()(document.body).on('found_variation', _this.foundVariation.bind((0,_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_3__["default"])(_this)));
    jquery__WEBPACK_IMPORTED_MODULE_7___default()(document.body).on('reset_data', _this.resetVariationData.bind((0,_babel_runtime_helpers_assertThisInitialized__WEBPACK_IMPORTED_MODULE_3__["default"])(_this)));
    return _this;
  }

  (0,_babel_runtime_helpers_createClass__WEBPACK_IMPORTED_MODULE_2__["default"])(Product, [{
    key: "needsShipping",
    value: function needsShipping() {
      var _this$data;

      return (_this$data = this.data) === null || _this$data === void 0 ? void 0 : _this$data.needsShipping;
    }
  }, {
    key: "onQuantityChange",
    value: function onQuantityChange(e) {
      var _this2 = this;

      if (e !== null && e !== void 0 && e.isTrigger) {
        //cause a short delay so this won't execute before foundVariation
        setTimeout(function () {
          _this2.trigger('quantityChange', _this2.getQuantity(), _this2);
        }, 50);
      } else {
        this.trigger('quantityChange', this.getQuantity(), this);
      }
    }
  }, {
    key: "foundVariation",
    value: function foundVariation(e, variation) {
      this.variation = variation;

      var prevData = _objectSpread({}, this.data);

      this.data = _objectSpread(_objectSpread({}, this.data), {
        price: variation.display_price,
        needsShipping: !variation.is_virtual
      });
      this.trigger('foundVariation', !(0,lodash__WEBPACK_IMPORTED_MODULE_9__.isEqual)(this.data, prevData), this);
    }
  }, {
    key: "resetVariationData",
    value: function resetVariationData() {
      this.variation = null;
      this.data = this.default_data;
      this.trigger('resetVariation', this);
    }
  }, {
    key: "getQuantity",
    value: function getQuantity() {
      return parseInt(jquery__WEBPACK_IMPORTED_MODULE_7___default()('[name="quantity"]').val());
    }
  }, {
    key: "getPrice",
    value: function getPrice() {
      var _this$data2;

      return (_this$data2 = this.data) === null || _this$data2 === void 0 ? void 0 : _this$data2.price;
    }
  }, {
    key: "getTotal",
    value: function getTotal() {
      return this.getQuantity() * this.getPrice();
    }
  }, {
    key: "isVariableProduct",
    value: function isVariableProduct() {
      return jquery__WEBPACK_IMPORTED_MODULE_7___default()('[name="variation_id"]').length > 0;
    }
  }, {
    key: "isVariableProductSelected",
    value: function isVariableProductSelected() {
      var val = jquery__WEBPACK_IMPORTED_MODULE_7___default()('input[name="variation_id"]').val();
      return !!val && "0" !== val;
    }
  }, {
    key: "getVariationData",
    value: function getVariationData() {
      if (this.isVariableProduct()) {
        var data = {};
        var elements = document.querySelectorAll('.variations [name^="attribute_"]');

        if (elements) {
          elements.forEach(function (element) {
            data[element.name] = element.value;
          });
        }

        return data;
      }

      return null;
    }
  }, {
    key: "getId",
    value: function getId() {
      var _this$data3;

      return parseInt((_this$data3 = this.data) === null || _this$data3 === void 0 ? void 0 : _this$data3.id);
    }
  }, {
    key: "getVariationId",
    value: function getVariationId() {
      return jquery__WEBPACK_IMPORTED_MODULE_7___default()('[name="variation_id"]').val();
    }
  }]);

  return Product;
}(_event__WEBPACK_IMPORTED_MODULE_10__["default"]);


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Product);

/***/ }),

/***/ "./assets/js/ppcp/utils.js":
/*!*********************************!*\
  !*** ./assets/js/ppcp/utils.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "SHIPPING_OPTION_REGEX": () => (/* binding */ SHIPPING_OPTION_REGEX),
/* harmony export */   "convertCartAddressToPayPal": () => (/* binding */ convertCartAddressToPayPal),
/* harmony export */   "convertPayPalAddressToCart": () => (/* binding */ convertPayPalAddressToCart),
/* harmony export */   "extractFullName": () => (/* binding */ extractFullName),
/* harmony export */   "extractShippingMethod": () => (/* binding */ extractShippingMethod),
/* harmony export */   "fieldsToJson": () => (/* binding */ fieldsToJson),
/* harmony export */   "getErrorMessage": () => (/* binding */ getErrorMessage),
/* harmony export */   "getFieldValue": () => (/* binding */ getFieldValue),
/* harmony export */   "getPage": () => (/* binding */ getPage),
/* harmony export */   "getPayPalQueryParams": () => (/* binding */ getPayPalQueryParams),
/* harmony export */   "getRestPath": () => (/* binding */ getRestPath),
/* harmony export */   "getRestRoute": () => (/* binding */ getRestRoute),
/* harmony export */   "getSetting": () => (/* binding */ getSetting),
/* harmony export */   "isPluginConnected": () => (/* binding */ isPluginConnected),
/* harmony export */   "isValidAddress": () => (/* binding */ isValidAddress),
/* harmony export */   "isValidFieldValue": () => (/* binding */ isValidFieldValue),
/* harmony export */   "loadPayPalSdk": () => (/* binding */ loadPayPalSdk),
/* harmony export */   "setFieldValue": () => (/* binding */ setFieldValue),
/* harmony export */   "setSetting": () => (/* binding */ setSetting),
/* harmony export */   "submitErrorMessage": () => (/* binding */ submitErrorMessage)
/* harmony export */ });
/* harmony import */ var _babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/slicedToArray */ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js");
/* harmony import */ var _babel_runtime_helpers_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/helpers/objectWithoutProperties */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js");
/* harmony import */ var _babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @babel/runtime/helpers/defineProperty */ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! lodash */ "lodash");
/* harmony import */ var lodash__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(lodash__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_4___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_4__);
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(/*! @wordpress/hooks */ "@wordpress/hooks");
/* harmony import */ var _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5___default = /*#__PURE__*/__webpack_require__.n(_wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__);
/* harmony import */ var _paypal_paypal_js__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(/*! @paypal/paypal-js */ "./node_modules/@paypal/paypal-js/dist/esm/paypal-js.js");



var _excluded = ["locale"];

function _createForOfIteratorHelper(o, allowArrayLike) { var it = typeof Symbol !== "undefined" && o[Symbol.iterator] || o["@@iterator"]; if (!it) { if (Array.isArray(o) || (it = _unsupportedIterableToArray(o)) || allowArrayLike && o && typeof o.length === "number") { if (it) o = it; var i = 0; var F = function F() {}; return { s: F, n: function n() { if (i >= o.length) return { done: true }; return { done: false, value: o[i++] }; }, e: function e(_e) { throw _e; }, f: F }; } throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); } var normalCompletion = true, didErr = false, err; return { s: function s() { it = it.call(o); }, n: function n() { var step = it.next(); normalCompletion = step.done; return step; }, e: function e(_e2) { didErr = true; err = _e2; }, f: function f() { try { if (!normalCompletion && it.return != null) it.return(); } finally { if (didErr) throw err; } } }; }

function _unsupportedIterableToArray(o, minLen) { if (!o) return; if (typeof o === "string") return _arrayLikeToArray(o, minLen); var n = Object.prototype.toString.call(o).slice(8, -1); if (n === "Object" && o.constructor) n = o.constructor.name; if (n === "Map" || n === "Set") return Array.from(o); if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return _arrayLikeToArray(o, minLen); }

function _arrayLikeToArray(arr, len) { if (len == null || len > arr.length) len = arr.length; for (var i = 0, arr2 = new Array(len); i < len; i++) { arr2[i] = arr[i]; } return arr2; }

function ownKeys(object, enumerableOnly) { var keys = Object.keys(object); if (Object.getOwnPropertySymbols) { var symbols = Object.getOwnPropertySymbols(object); enumerableOnly && (symbols = symbols.filter(function (sym) { return Object.getOwnPropertyDescriptor(object, sym).enumerable; })), keys.push.apply(keys, symbols); } return keys; }

function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = null != arguments[i] ? arguments[i] : {}; i % 2 ? ownKeys(Object(source), !0).forEach(function (key) { (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_2__["default"])(target, key, source[key]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) { Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key)); }); } return target; }





var urlParams = {};
var isLoading = false;
var fields = new Map();
var locales = null;
var SHIPPING_OPTION_REGEX = /^([\w]+)\:(.+)$/;
var ADDRESS_MAPPING = {
  address_1: 'address_line_1|line1',
  address_2: 'address_line_2|line2',
  state: 'admin_area_1|state',
  city: 'admin_area_2|city',
  postcode: 'postal_code',
  country: 'country_code'
};
var INTERMEDIATE_ADDRESS_MAPPING = {
  city: 'city',
  state: 'state',
  postal_code: 'postcode',
  country_code: 'country'
}; //export const hooks = createHooks();

var removeScriptById = function removeScriptById(id) {
  var element = document.getElementById(id);

  if (element) {
    element.remove();
  }
};

var hasPayPalScript = function hasPayPalScript(id) {
  var element = document.getElementById(id);
  return !!element;
};

var loadPayPalSdk = function loadPayPalSdk() {
  var params = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : null;
  return new Promise(function (resolve, reject) {
    // params may have changed so reload script¬
    if (params && !(0,lodash__WEBPACK_IMPORTED_MODULE_3__.isEmpty)(params) && !(0,lodash__WEBPACK_IMPORTED_MODULE_3__.isEqual)(params, urlParams)) {
      urlParams = params;

      if (window.paypal) {
        // cleanup
        _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__.defaultHooks.doAction('paypalInstanceCleanup', window.paypal);
      }

      isLoading = true;
      (0,_paypal_paypal_js__WEBPACK_IMPORTED_MODULE_6__.loadScript)(_objectSpread({}, params)).then(function (paypal) {
        resolve(paypal);
        _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__.defaultHooks.doAction('paypalInstanceCreated', paypal, urlParams);
      }).catch(function (error) {
        var _error$message;

        console.log(error);

        if (error !== null && error !== void 0 && (_error$message = error.message) !== null && _error$message !== void 0 && _error$message.includes('locale')) {
          var locale = params.locale,
              newParams = (0,_babel_runtime_helpers_objectWithoutProperties__WEBPACK_IMPORTED_MODULE_1__["default"])(params, _excluded);

          return loadPayPalSdk(newParams).then(function (paypal) {
            resolve(paypal);
          });
        }

        reject();
        _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__.defaultHooks.doAction('paypalLoadError');
      }).finally(function () {
        isLoading = false;
      });
    } else {
      if (window.paypal && !isLoading) {
        resolve(window.paypal);
      } else {
        _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__.defaultHooks.addAction('paypalInstanceCreated', 'wcPPCP', function (paypal) {
          resolve(paypal);
        });
        _wordpress_hooks__WEBPACK_IMPORTED_MODULE_5__.defaultHooks.addAction('paypalLoadError', 'wcPPCP', function () {
          reject();
        });
      }
    }
  });
};
var getSetting = function getSetting(key) {
  if (typeof window.wcPPCPSettings !== 'undefined') {
    return window.wcPPCPSettings[key] || {};
  }

  return {};
};
var setSetting = function setSetting(key, value) {
  if (typeof window.wcPPCPSettings !== 'undefined') {
    return window.wcPPCPSettings[key] = value;
  }
};
var getPayPalQueryParams = function getPayPalQueryParams() {
  return getSetting('queryParams');
};
var getFieldValue = function getFieldValue(key) {
  var prefix = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'billing';

  if (key.substring(0, 'shipping'.length) != 'shipping' && key.substring(0, 'billing'.length) != 'billing') {
    key = "".concat(prefix, "_").concat(key);
  }

  if (jquery__WEBPACK_IMPORTED_MODULE_4___default()("[name=\"".concat(key, "\"]")).length) {
    return jquery__WEBPACK_IMPORTED_MODULE_4___default()("[name=\"".concat(key, "\"]")).val();
  }

  return fields.get(key);
};
var fieldsToJson = function fieldsToJson() {
  var json = {};
  fields.forEach(function (value, key) {
    json[key] = value;
  });
  return json;
};
var setFieldValue = function setFieldValue(key, value) {
  var prefix = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'billing';

  if (!!prefix && key.substring(0, 'shipping'.length) != 'shipping' && key.substring(0, 'billing'.length) != 'billing') {
    key = "".concat(prefix, "_").concat(key);
  }

  fields.set(key, value);

  if (jquery__WEBPACK_IMPORTED_MODULE_4___default()("[name=\"".concat(key, "\"]")).length) {
    jquery__WEBPACK_IMPORTED_MODULE_4___default()("[name=\"".concat(key, "\"]")).val(value);
  }
};
var getErrorMessage = function getErrorMessage(error) {
  var messages = getSetting('errorMessages');

  if (typeof error == 'string') {
    return error;
  }

  if (error !== null && error !== void 0 && error.code && messages !== null && messages !== void 0 && messages[error.code]) {
    return messages[error.code];
  }

  if (error !== null && error !== void 0 && error.message) {
    return error.message;
  }
};
var submitErrorMessage = function submitErrorMessage(error, container) {
  var _error$message2, _error$message2$toLow;

  var context = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'checkout';

  if (error !== null && error !== void 0 && (_error$message2 = error.message) !== null && _error$message2 !== void 0 && (_error$message2$toLow = _error$message2.toLowerCase()) !== null && _error$message2$toLow !== void 0 && _error$message2$toLow.match(/detected popup close|window is closed/)) {
    return;
  }

  var msg = getErrorMessage(error);
  var classes = 'woocommerce-NoticeGroup';
  var $container = jquery__WEBPACK_IMPORTED_MODULE_4___default()(container);

  if (context == 'checkout') {
    classes += ' woocommerce-NoticeGroup-checkout';
  }

  msg = '<div class="' + classes + '"><ul class="woocommerce-error"><li>' + msg + '</li></ul></div>';
  jquery__WEBPACK_IMPORTED_MODULE_4___default()('.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message').remove();
  $container.prepend(msg);

  if ((jquery__WEBPACK_IMPORTED_MODULE_4___default().scroll_to_notices)) {
    jquery__WEBPACK_IMPORTED_MODULE_4___default().scroll_to_notices($container);
  } else {
    jquery__WEBPACK_IMPORTED_MODULE_4___default()('html, body').animate({
      scrollTop: $container.offset().top - 100
    }, 1000);
  }
};
var isValidAddress = function isValidAddress(address) {
  var _locales;

  var exclude = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  var i18n_params = typeof wc_address_i18n_params == 'undefined' ? getSetting('i18n') : wc_address_i18n_params;

  if ((0,lodash__WEBPACK_IMPORTED_MODULE_3__.isEmpty)(address)) {
    return false;
  }

  if (!locales) {
    locales = JSON.parse(i18n_params.locale.replace(/&quot;/g, '"'));
  }

  if (!address.country || (0,lodash__WEBPACK_IMPORTED_MODULE_3__.isEmpty)(address)) {
    return false;
  }

  var locale = (_locales = locales) !== null && _locales !== void 0 && _locales[address.country] ? locales[address.country] : locales['default'];
  locale = jquery__WEBPACK_IMPORTED_MODULE_4___default().extend(true, {}, locales['default'], locale);
  var entries = Object.entries(locale).filter(function (_ref) {
    var _ref2 = (0,_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref, 2),
        key = _ref2[0],
        value = _ref2[1];

    return !exclude.includes(key);
  });
  locale = Object.fromEntries(entries);

  for (var key in locale) {
    var _locale$key;

    if ((_locale$key = locale[key]) !== null && _locale$key !== void 0 && _locale$key.required) {
      var value = (address === null || address === void 0 ? void 0 : address[key]) || null;

      if (!value || !(value !== null && value !== void 0 && value.trim())) {
        return false;
      }
    }
  }

  return true;
};
/**
 * Converts a WC cart address to a PayPal formatted address
 */

var convertCartAddressToPayPal = function convertCartAddressToPayPal(address) {
  var newAddress = {};

  for (var key in address) {
    if (ADDRESS_MAPPING !== null && ADDRESS_MAPPING !== void 0 && ADDRESS_MAPPING[key]) {
      if (ADDRESS_MAPPING[key].includes('|')) {
        var _ADDRESS_MAPPING$key$ = ADDRESS_MAPPING[key].split('|'),
            _ADDRESS_MAPPING$key$2 = (0,_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__["default"])(_ADDRESS_MAPPING$key$, 2),
            k1 = _ADDRESS_MAPPING$key$2[0],
            k2 = _ADDRESS_MAPPING$key$2[1];

        newAddress[k1] = address[key];
      } else {
        newAddress[ADDRESS_MAPPING[key]] = address[key];
      }
    }
  }

  return newAddress;
};
var convertPayPalAddressToCart = function convertPayPalAddressToCart(address) {
  var intermediate = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : false;
  var mappings = {};

  if (intermediate) {
    mappings = INTERMEDIATE_ADDRESS_MAPPING;
  } else {
    mappings = Object.fromEntries(Object.entries(ADDRESS_MAPPING).map(function (_ref3) {
      var _ref4 = (0,_babel_runtime_helpers_slicedToArray__WEBPACK_IMPORTED_MODULE_0__["default"])(_ref3, 2),
          key = _ref4[0],
          key2 = _ref4[1];

      return [key2, key];
    }));
  }

  var newAddress = {};

  for (var key in mappings) {
    if (key.includes('|')) {
      var keys = key.split('|');

      var _iterator = _createForOfIteratorHelper(keys),
          _step;

      try {
        for (_iterator.s(); !(_step = _iterator.n()).done;) {
          var k1 = _step.value;

          if (address.hasOwnProperty(k1)) {
            newAddress[mappings[key]] = address[k1];
            break;
          } else {
            newAddress[mappings[key]] = '';
          }
        }
      } catch (err) {
        _iterator.e(err);
      } finally {
        _iterator.f();
      }
    } else {
      if (address.hasOwnProperty(key)) {
        newAddress[mappings[key]] = address[key];
      } else {
        newAddress[mappings[key]] = '';
      }
    }
  }

  return newAddress;
};
var isValidFieldValue = function isValidFieldValue(value) {
  var _value;

  value = (_value = value) === null || _value === void 0 ? void 0 : _value.trim();
  return !!value;
};
/**
 * Given a formatted shipping method, extract it into the WC format.
 * @param selectedMethod
 */

var extractShippingMethod = function extractShippingMethod(selectedMethod) {
  var matches = selectedMethod.match(SHIPPING_OPTION_REGEX);

  if (matches) {
    var packageId = matches[1],
        method = matches[2];
    return (0,_babel_runtime_helpers_defineProperty__WEBPACK_IMPORTED_MODULE_2__["default"])({}, packageId, method);
  }

  return null;
};
var extractFullName = function extractFullName(name) {
  name = name.trim();
  var firstName = name.split(' ').slice(0, -1).join(' ');
  var lastName = name.split(' ').pop();
  return [firstName, lastName];
};
/**
 * Returns a rest route in ajax form given a route key
 * @param route
 * @returns {*|null}
 */

var getRestRoute = function getRestRoute(route) {
  var _getSetting, _getSetting$restRoute, _getSetting$restRoute2;

  return ((_getSetting = getSetting('generalData')) === null || _getSetting === void 0 ? void 0 : (_getSetting$restRoute = _getSetting.restRoutes) === null || _getSetting$restRoute === void 0 ? void 0 : (_getSetting$restRoute2 = _getSetting$restRoute[route]) === null || _getSetting$restRoute2 === void 0 ? void 0 : _getSetting$restRoute2.url) || null;
};
/**
 * Returns a rest route in ajax form given a route path.
 * @param path
 * @returns {*}
 */

var getRestPath = function getRestPath(path) {
  var _getSetting2, _getSetting2$ajaxRest;

  path = path.replace(/^\//, '');
  return (_getSetting2 = getSetting('generalData')) === null || _getSetting2 === void 0 ? void 0 : (_getSetting2$ajaxRest = _getSetting2.ajaxRestPath) === null || _getSetting2$ajaxRest === void 0 ? void 0 : _getSetting2$ajaxRest.replace('%s', path);
};
var getPage = function getPage() {
  return getSetting('generalData').page;
};
var isPluginConnected = function isPluginConnected() {
  var _getSetting3, _getSetting3$clientId;

  return ((_getSetting3 = getSetting('generalData')) === null || _getSetting3 === void 0 ? void 0 : (_getSetting3$clientId = _getSetting3.clientId) === null || _getSetting3$clientId === void 0 ? void 0 : _getSetting3$clientId.length) > 0;
};

/***/ }),

/***/ "./packages/woofunnels/assets/js/upsell/paypal.js":
/*!********************************************************!*\
  !*** ./packages/woofunnels/assets/js/upsell/paypal.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/asyncToGenerator */ "./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @babel/runtime/regenerator */ "@babel/runtime/regenerator");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! jquery */ "jquery");
/* harmony import */ var jquery__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(jquery__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _ppcp_utils__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! @ppcp/utils */ "@ppcp/utils");
/* harmony import */ var _ppcp_utils__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_ppcp_utils__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _assets_js_ppcp__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ../../../../../assets/js/ppcp */ "./assets/js/ppcp/index.js");





var bucket, paypal;
var data = {};

var getOption = function getOption(key, defaultValue) {
  if (!data.hasOwnProperty(key)) {
    data[key] = defaultValue;
  }

  return data[key];
};

jquery__WEBPACK_IMPORTED_MODULE_2___default()(document).on('wfocuBucketCreated', /*#__PURE__*/function () {
  var _ref = (0,_babel_runtime_helpers_asyncToGenerator__WEBPACK_IMPORTED_MODULE_0__["default"])( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1___default().mark(function _callee(e, bucket) {
    var _window, _window$wfocu_vars;

    var button;
    return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_1___default().wrap(function _callee$(_context) {
      while (1) {
        switch (_context.prev = _context.next) {
          case 0:
            bucket = bucket;

            if ((_window = window) !== null && _window !== void 0 && (_window$wfocu_vars = _window.wfocu_vars) !== null && _window$wfocu_vars !== void 0 && _window$wfocu_vars.wcPPCPData) {
              data = window.wfocu_vars.wcPPCPData;
            } // load the PayPal script


            try {
              paypal = (0,_assets_js_ppcp__WEBPACK_IMPORTED_MODULE_4__.loadPayPalSdk)(getOption('queryParams'));
              button = createPayPalButton(); //button.render();

              if (jquery__WEBPACK_IMPORTED_MODULE_2___default()('.wfocu_upsell').length) {
                jquery__WEBPACK_IMPORTED_MODULE_2___default()('.wfocu_upsell').after('<div id="wc-ppcp-buttons"></div>');
                button.render(document.getElementById('wc-ppcp-buttons'));
              }
            } catch (error) {
              console.log(error);
            }

          case 3:
          case "end":
            return _context.stop();
        }
      }
    }, _callee);
  }));

  return function (_x, _x2) {
    return _ref.apply(this, arguments);
  };
}());

var createPayPalButton = function createPayPalButton() {
  var button = paypal.Buttons({
    fundingSource: 'paypal',
    style: {},
    onInit: function onInit() {},
    onClick: function onClick() {},
    onApprove: function onApprove() {},
    onError: function onError() {},
    onCancel: function onCancel() {},
    createOrder: createOrder
  });
  return button;
};

var createOrder = function createOrder() {};

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _arrayLikeToArray)
/* harmony export */ });
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _arrayWithHoles)
/* harmony export */ });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js":
/*!**************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js ***!
  \**************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _assertThisInitialized)
/* harmony export */ });
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _asyncToGenerator)
/* harmony export */ });
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/classCallCheck.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _classCallCheck)
/* harmony export */ });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/createClass.js":
/*!****************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/createClass.js ***!
  \****************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _createClass)
/* harmony export */ });
function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  Object.defineProperty(Constructor, "prototype", {
    writable: false
  });
  return Constructor;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/defineProperty.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/defineProperty.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _defineProperty)
/* harmony export */ });
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _getPrototypeOf)
/* harmony export */ });
function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/inherits.js":
/*!*************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/inherits.js ***!
  \*************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _inherits)
/* harmony export */ });
/* harmony import */ var _setPrototypeOf_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./setPrototypeOf.js */ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js");

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  Object.defineProperty(subClass, "prototype", {
    writable: false
  });
  if (superClass) (0,_setPrototypeOf_js__WEBPACK_IMPORTED_MODULE_0__["default"])(subClass, superClass);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js":
/*!*************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js ***!
  \*************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _iterableToArrayLimit)
/* harmony export */ });
function _iterableToArrayLimit(arr, i) {
  var _i = arr == null ? null : typeof Symbol !== "undefined" && arr[Symbol.iterator] || arr["@@iterator"];

  if (_i == null) return;
  var _arr = [];
  var _n = true;
  var _d = false;

  var _s, _e;

  try {
    for (_i = _i.call(arr); !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);

      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }

  return _arr;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js ***!
  \********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _nonIterableRest)
/* harmony export */ });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js":
/*!****************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js ***!
  \****************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _objectWithoutProperties)
/* harmony export */ });
/* harmony import */ var _objectWithoutPropertiesLoose_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./objectWithoutPropertiesLoose.js */ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js");

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = (0,_objectWithoutPropertiesLoose_js__WEBPACK_IMPORTED_MODULE_0__["default"])(source, excluded);
  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js":
/*!*********************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _objectWithoutPropertiesLoose)
/* harmony export */ });
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js":
/*!******************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js ***!
  \******************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _possibleConstructorReturn)
/* harmony export */ });
/* harmony import */ var _typeof_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./typeof.js */ "./node_modules/@babel/runtime/helpers/esm/typeof.js");
/* harmony import */ var _assertThisInitialized_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./assertThisInitialized.js */ "./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js");


function _possibleConstructorReturn(self, call) {
  if (call && ((0,_typeof_js__WEBPACK_IMPORTED_MODULE_0__["default"])(call) === "object" || typeof call === "function")) {
    return call;
  } else if (call !== void 0) {
    throw new TypeError("Derived constructors may only return object or undefined");
  }

  return (0,_assertThisInitialized_js__WEBPACK_IMPORTED_MODULE_1__["default"])(self);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _setPrototypeOf)
/* harmony export */ });
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };
  return _setPrototypeOf(o, p);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/slicedToArray.js":
/*!******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js ***!
  \******************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _slicedToArray)
/* harmony export */ });
/* harmony import */ var _arrayWithHoles_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithHoles.js */ "./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js");
/* harmony import */ var _iterableToArrayLimit_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArrayLimit.js */ "./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js");
/* harmony import */ var _unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js");
/* harmony import */ var _nonIterableRest_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./nonIterableRest.js */ "./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js");




function _slicedToArray(arr, i) {
  return (0,_arrayWithHoles_js__WEBPACK_IMPORTED_MODULE_0__["default"])(arr) || (0,_iterableToArrayLimit_js__WEBPACK_IMPORTED_MODULE_1__["default"])(arr, i) || (0,_unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__["default"])(arr, i) || (0,_nonIterableRest_js__WEBPACK_IMPORTED_MODULE_3__["default"])();
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/typeof.js":
/*!***********************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/typeof.js ***!
  \***********************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _typeof)
/* harmony export */ });
function _typeof(obj) {
  "@babel/helpers - typeof";

  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
    return typeof obj;
  } : function (obj) {
    return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
  }, _typeof(obj);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _unsupportedIterableToArray)
/* harmony export */ });
/* harmony import */ var _arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__["default"])(o, minLen);
}

/***/ })

}]);
//# sourceMappingURL=woofunnels-commons.js.map