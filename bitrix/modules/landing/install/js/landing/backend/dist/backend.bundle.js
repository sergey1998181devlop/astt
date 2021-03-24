this.BX = this.BX || {};
(function (exports, main_core, landing_env) {
	'use strict';

	/**
	 * @memberOf BX.Landing
	 */
	var Backend =
	/*#__PURE__*/
	function () {
	  function Backend() {
	    babelHelpers.classCallCheck(this, Backend);
	    babelHelpers.defineProperty(this, "cache", new main_core.Cache.MemoryCache());
	  }

	  babelHelpers.createClass(Backend, [{
	    key: "getControllerUrl",
	    value: function getControllerUrl() {
	      var _this = this;

	      return this.cache.remember('controllerUrl', function () {
	        var uri = new main_core.Uri('/bitrix/tools/landing/ajax.php');
	        uri.setQueryParams({
	          site: main_core.Loc.getMessage('SITE_ID') || undefined,
	          type: _this.getSitesType()
	        });
	        return uri.toString();
	      });
	    }
	  }, {
	    key: "getSiteId",
	    value: function getSiteId() {
	      return this.cache.remember('siteId', function () {
	        var landing = main_core.Reflection.getClass('BX.Landing.Main');

	        if (landing) {
	          var instance = landing.getInstance();

	          if ('options' in instance && 'site_id' in instance.options && !main_core.Type.isUndefined(instance.options.site_id)) {
	            return instance.options.site_id;
	          }
	        }

	        return -1;
	      });
	    }
	  }, {
	    key: "getLandingId",
	    value: function getLandingId() {
	      return this.cache.remember('landingId', function () {
	        var landing = main_core.Reflection.getClass('BX.Landing.Main');

	        if (landing) {
	          return landing.getInstance().id;
	        }

	        return -1;
	      });
	    }
	  }, {
	    key: "getSitesType",
	    value: function getSitesType() {
	      return this.cache.remember('siteType', function () {
	        return landing_env.Env.getInstance().getType();
	      });
	    }
	  }, {
	    key: "action",
	    value: function action(_action) {
	      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
	      var queryParams = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
	      var uploadParams = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
	      queryParams.site_id = this.getSiteId();
	      var requestBody = {
	        sessid: main_core.Loc.getMessage('bitrix_sessid'),
	        action: uploadParams.action || _action.replace('Landing\\Block', 'Block'),
	        data: babelHelpers.objectSpread({}, data, {
	          uploadParams: uploadParams,
	          lid: data.lid || this.getLandingId()
	        })
	      };
	      var uri = new main_core.Uri(this.getControllerUrl());
	      uri.setQueryParams(babelHelpers.objectSpread({
	        action: requestBody.action
	      }, queryParams));
	      return Backend.request({
	        url: uri.toString(),
	        data: requestBody
	      }).then(function (response) {
	        if (requestBody.action === 'Block::updateNodes' || requestBody.action === 'Block::removeCard' || requestBody.action === 'Block::cloneCard' || requestBody.action === 'Block::addCard' || requestBody.action === 'Block::updateStyles') {
	          // eslint-disable-next-line
	          BX.Landing.UI.Panel.StatusPanel.getInstance().update();
	        }

	        return response.result;
	      }).catch(function (err) {
	        if (requestBody.action !== 'Block::getById') {
	          var error = main_core.Type.isString(err) ? {
	            type: 'error'
	          } : err;
	          err.action = requestBody.action; // eslint-disable-next-line

	          BX.Landing.ErrorManager.getInstance().add(error);
	        }

	        return Promise.reject(err);
	      });
	    }
	  }, {
	    key: "batch",
	    value: function batch(action) {
	      var data = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
	      var queryParams = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
	      queryParams.site_id = this.getSiteId();
	      var requestBody = {
	        sessid: main_core.Loc.getMessage('bitrix_sessid'),
	        action: action.replace('Landing\\Block', 'Block'),
	        data: {
	          lid: data.lid || this.getLandingId()
	        },
	        batch: data
	      };
	      var uri = new main_core.Uri(this.getControllerUrl());
	      uri.setQueryParams(babelHelpers.objectSpread({
	        action: requestBody.action
	      }, queryParams));
	      return Backend.request({
	        url: uri.toString(),
	        data: requestBody
	      }).then(function (response) {
	        // eslint-disable-next-line
	        BX.Landing.UI.Panel.StatusPanel.getInstance().update();
	        return response;
	      }).catch(function (err) {
	        if (requestBody.action !== 'Block::getById') {
	          var error = main_core.Type.isString(err) ? {
	            type: 'error'
	          } : err;
	          error.action = requestBody.action; // eslint-disable-next-line

	          BX.Landing.ErrorManager.getInstance().add(error);
	        }

	        return Promise.reject(err);
	      });
	    }
	  }, {
	    key: "upload",
	    value: function upload(file) {
	      var uploadParams = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
	      var formData = new FormData();
	      formData.append('sessid', main_core.Loc.getMessage('bitrix_sessid'));
	      formData.append('picture', file, file.name);

	      if ('block' in uploadParams) {
	        formData.append('action', 'Block::uploadFile');
	        formData.append('data[block]', uploadParams.block);
	      }

	      if ('lid' in uploadParams) {
	        formData.set('action', 'Landing::uploadFile');
	        formData.append('data[lid]', uploadParams.lid);
	      }

	      if ('id' in uploadParams) {
	        formData.set('action', 'Site::uploadFile');
	        formData.append('data[id]', uploadParams.id);
	      }

	      var uri = new main_core.Uri(this.getControllerUrl());
	      uri.setQueryParams({
	        action: formData.get('action'),
	        site_id: this.getSiteId()
	      });

	      if (uploadParams.context) {
	        uri.setQueryParam('context', uploadParams.context);
	      }

	      return Backend.request({
	        url: uri.toString(),
	        data: formData
	      }).then(function (response) {
	        return response.result;
	      }).catch(function (err) {
	        var error = main_core.Type.isString(err) ? {
	          type: 'error'
	        } : err;
	        error.action = 'Block::uploadFile'; // eslint-disable-next-line

	        BX.Landing.ErrorManager.getInstance().add(error);
	        return Promise.reject(err);
	      });
	    }
	  }, {
	    key: "getSites",
	    value: function getSites() {
	      var _this2 = this;

	      var _ref = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
	          _ref$filter = _ref.filter,
	          filter = _ref$filter === void 0 ? {} : _ref$filter;

	      return this.cache.remember("sites+".concat(JSON.stringify(filter)), function () {
	        return _this2.action('Site::getList', {
	          params: {
	            order: {
	              ID: 'DESC'
	            },
	            filter: babelHelpers.objectSpread({
	              TYPE: _this2.getSitesType()
	            }, filter)
	          }
	        }).then(function (response) {
	          return response;
	        });
	      });
	    }
	  }, {
	    key: "getLandings",
	    value: function getLandings() {
	      var _this3 = this;

	      var _ref2 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
	          _ref2$siteId = _ref2.siteId,
	          siteId = _ref2$siteId === void 0 ? [] : _ref2$siteId;

	      var ids = main_core.Type.isArray(siteId) ? siteId : [siteId];

	      var getBathItem = function getBathItem(id) {
	        return {
	          action: 'Landing::getList',
	          data: {
	            params: {
	              filter: {
	                SITE_ID: id
	              },
	              order: {
	                ID: 'DESC'
	              },
	              get_preview: true,
	              check_area: 1
	            }
	          }
	        };
	      };

	      var prepareResponse = function prepareResponse(response) {
	        return response.reduce(function (acc, item) {
	          return [].concat(babelHelpers.toConsumableArray(acc), babelHelpers.toConsumableArray(item.result));
	        }, []);
	      };

	      return this.cache.remember("landings+".concat(JSON.stringify(ids)), function () {
	        if (ids.filter(function (id) {
	          return !main_core.Type.isNil(id);
	        }).length === 0) {
	          return _this3.getSites().then(function (sites) {
	            var data = sites.map(function (site) {
	              return getBathItem(site.ID);
	            });
	            return _this3.batch('Landing::getList', data);
	          }).then(function (response) {
	            return prepareResponse(response);
	          }).then(function (response) {
	            response.forEach(function (landing) {
	              _this3.cache.set("landing+".concat(landing.ID), Promise.resolve(landing));
	            });
	          });
	        }

	        var data = ids.map(function (id) {
	          return getBathItem(id);
	        });
	        return _this3.batch('Landing::getList', data).then(function (response) {
	          return prepareResponse(response);
	        }).then(function (response) {
	          response.forEach(function (landing) {
	            _this3.cache.set("landing+".concat(landing.ID), Promise.resolve(landing));
	          });
	          return response;
	        });
	      });
	    }
	  }, {
	    key: "getLanding",
	    value: function getLanding(_ref3) {
	      var _this4 = this;

	      var landingId = _ref3.landingId;
	      return this.cache.remember("landing+".concat(landingId), function () {
	        return _this4.action('Landing::getList', {
	          params: {
	            filter: {
	              ID: landingId
	            },
	            get_preview: true
	          }
	        }).then(function (response) {
	          if (main_core.Type.isArray(response) && response.length > 0) {
	            return response[0];
	          }

	          return null;
	        });
	      });
	    }
	  }, {
	    key: "getBlocks",
	    value: function getBlocks(_ref4) {
	      var _this5 = this;

	      var landingId = _ref4.landingId;
	      return this.cache.remember("blocks+".concat(landingId), function () {
	        return _this5.action('Block::getList', {
	          lid: landingId,
	          params: {
	            get_content: true,
	            edit_mode: true
	          }
	        }).then(function (blocks) {
	          blocks.forEach(function (block) {
	            _this5.cache.set("block+".concat(block.id), Promise.resolve(block));
	          });
	          return blocks;
	        });
	      });
	    }
	  }, {
	    key: "getBlock",
	    value: function getBlock(_ref5) {
	      var _this6 = this;

	      var blockId = _ref5.blockId;
	      return this.cache.remember("blockId+".concat(blockId), function () {
	        return _this6.action('Block::getById', {
	          block: blockId,
	          params: {
	            edit_mode: true
	          }
	        });
	      });
	    }
	  }, {
	    key: "getTemplates",
	    value: function getTemplates() {
	      var _this7 = this;

	      var _ref6 = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {},
	          _ref6$type = _ref6.type,
	          type = _ref6$type === void 0 ? 'page' : _ref6$type,
	          _ref6$filter = _ref6.filter,
	          filter = _ref6$filter === void 0 ? {} : _ref6$filter;

	      return this.cache.remember("templates+".concat(JSON.stringify(filter)), function () {
	        return _this7.action('Demos::getPageList', {
	          type: type,
	          filter: filter
	        }).then(function (response) {
	          return Object.values(response);
	        });
	      });
	    }
	  }, {
	    key: "getDynamicTemplates",
	    value: function getDynamicTemplates() {
	      var _this8 = this;

	      var sourceId = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '';
	      return this.cache.remember("dynamicTemplates:".concat(sourceId), function () {
	        return _this8.getTemplates({
	          filter: {
	            section: "dynamic".concat(sourceId ? ":".concat(sourceId) : '')
	          }
	        });
	      });
	    }
	  }, {
	    key: "createPage",
	    value: function createPage() {
	      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
	      var envOptions = landing_env.Env.getInstance().getOptions();
	      var title = options.title,
	          _options$siteId = options.siteId,
	          siteId = _options$siteId === void 0 ? envOptions.site_id : _options$siteId,
	          _options$code = options.code,
	          code = _options$code === void 0 ? main_core.Text.getRandom(16) : _options$code,
	          blockId = options.blockId,
	          menuCode = options.menuCode,
	          folderId = options.folderId;

	      var templateCode = function () {
	        var theme = envOptions.theme;

	        if (main_core.Type.isPlainObject(theme) && main_core.Type.isArray(theme.newPageTemplate) && main_core.Type.isStringFilled(theme.newPageTemplate[0])) {
	          return theme.newPageTemplate[0];
	        }

	        return 'empty';
	      }();

	      var requestBody = {
	        siteId: siteId,
	        code: templateCode,
	        fields: {
	          TITLE: title,
	          CODE: code
	        }
	      };

	      if (main_core.Type.isNumber(blockId) && main_core.Type.isString(menuCode)) {
	        requestBody.fields.BLOCK_ID = blockId;
	        requestBody.fields.MENU_CODE = menuCode;
	      }

	      if (main_core.Type.isNumber(folderId)) {
	        requestBody.fields.FOLDER_ID = folderId;
	      }

	      return this.action('Landing::addByTemplate', requestBody);
	    }
	  }], [{
	    key: "getInstance",
	    value: function getInstance() {
	      if (!Backend.instance) {
	        Backend.instance = new Backend();
	      }

	      return Backend.instance;
	    }
	  }, {
	    key: "request",
	    value: function request(_ref7) {
	      var url = _ref7.url,
	          data = _ref7.data;
	      return new Promise(function (resolve, reject) {
	        var fd = data instanceof FormData ? data : main_core.Http.Data.convertObjectToFormData(data);
	        var xhr = main_core.ajax({
	          method: 'POST',
	          dataType: 'json',
	          url: url,
	          data: fd,
	          start: false,
	          preparePost: false,
	          onsuccess: function onsuccess(response) {
	            if (main_core.Type.isPlainObject(response) && response.type === 'error') {
	              reject(response);
	              return;
	            }

	            resolve(response);
	          },
	          onfailure: reject
	        });
	        xhr.send(fd);
	      });
	    }
	  }]);
	  return Backend;
	}();

	exports.Backend = Backend;

}(this.BX.Landing = this.BX.Landing || {}, BX, BX.Landing));
//# sourceMappingURL=backend.bundle.js.map
