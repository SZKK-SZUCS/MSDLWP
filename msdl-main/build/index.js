/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "react/jsx-runtime"
/*!**********************************!*\
  !*** external "ReactJSXRuntime" ***!
  \**********************************/
(module) {

module.exports = window["ReactJSXRuntime"];

/***/ },

/***/ "@wordpress/api-fetch"
/*!**********************************!*\
  !*** external ["wp","apiFetch"] ***!
  \**********************************/
(module) {

module.exports = window["wp"]["apiFetch"];

/***/ },

/***/ "@wordpress/components"
/*!************************************!*\
  !*** external ["wp","components"] ***!
  \************************************/
(module) {

module.exports = window["wp"]["components"];

/***/ },

/***/ "@wordpress/element"
/*!*********************************!*\
  !*** external ["wp","element"] ***!
  \*********************************/
(module) {

module.exports = window["wp"]["element"];

/***/ }

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		if (!(moduleId in __webpack_modules__)) {
/******/ 			delete __webpack_module_cache__[moduleId];
/******/ 			var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 			e.code = 'MODULE_NOT_FOUND';
/******/ 			throw e;
/******/ 		}
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!**********************!*\
  !*** ./src/index.js ***!
  \**********************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/element */ "@wordpress/element");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! @wordpress/components */ "@wordpress/components");
/* harmony import */ var _wordpress_components__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! @wordpress/api-fetch */ "@wordpress/api-fetch");
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! react/jsx-runtime */ "react/jsx-runtime");
/* harmony import */ var react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__);




const App = () => {
  // --- State-ek: Alap beállítások és Webhelyek ---
  const [options, setOptions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)({
    msdl_tenant_id: "",
    msdl_client_id: "",
    msdl_client_secret: "",
    msdl_site_id: "",
    msdl_drive_id: "",
    msdl_internal_api_key: ""
  });
  const [sites, setSites] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [statusText, setStatusText] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");

  // --- State-ek: Fő Szerkesztő Modal ---
  const [isModalOpen, setIsModalOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [editingSite, setEditingSite] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [showAdvanced, setShowAdvanced] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);

  // ÚJ: Ez dönti el, hogy a Modalon belül a Szerkesztőt vagy a Tallózót mutatjuk
  const [isFolderBrowserActive, setIsFolderBrowserActive] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);

  // --- State-ek: SharePoint Azonosító Kereső Modal (Ez maradhat külön, mert nem a szerkesztőből nyílik) ---
  const [isFinderOpen, setIsFinderOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [searchQuery, setSearchQuery] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [isSearching, setIsSearching] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [foundSites, setFoundSites] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [selectedFoundSite, setSelectedFoundSite] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [foundDrives, setFoundDrives] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [isLoadingDrives, setIsLoadingDrives] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);

  // --- State-ek: MAPPA Kereső adatok ---
  const [folderSearchQuery, setFolderSearchQuery] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [isSearchingFolders, setIsSearchingFolders] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [foundFolders, setFoundFolders] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);

  // --- Adatbetöltés ---
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    loadSettings();
    loadSites();
  }, []);
  const loadSettings = () => {
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
      path: "/wp/v2/settings"
    }).then(settings => {
      setOptions({
        msdl_tenant_id: settings.msdl_tenant_id || "",
        msdl_client_id: settings.msdl_client_id || "",
        msdl_client_secret: settings.msdl_client_secret || "",
        msdl_site_id: settings.msdl_site_id || "",
        msdl_drive_id: settings.msdl_drive_id || "",
        msdl_internal_api_key: settings.msdl_internal_api_key || ""
      });
    }).catch(console.error);
  };
  const loadSites = () => {
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
      path: "/msdl-main/v1/sites"
    }).then(setSites).catch(console.error);
  };

  // --- Műveletek: Beállítások és Webhelyek ---
  const handleSaveSettings = async () => {
    setStatusText("Mentés...");
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/wp/v2/settings",
        method: "POST",
        data: options
      });
      setStatusText("Beállítások sikeresen elmentve!");
      setTimeout(() => setStatusText(""), 3000);
    } catch (e) {
      setStatusText("Hiba a mentéskor!");
    }
  };
  const openModal = (site = {
    domain: "",
    folder_path: "",
    custom_site_id: "",
    custom_drive_id: ""
  }) => {
    setEditingSite(site);
    setShowAdvanced(!!(site.custom_site_id || site.custom_drive_id));
    setIsFolderBrowserActive(false); // Biztosítjuk, hogy mindig a szerkesztő nézetben nyíljon meg!
    setIsModalOpen(true);
  };
  const handleSaveSite = async e => {
    e.preventDefault();
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-main/v1/sites",
        method: "POST",
        data: editingSite
      });
      setIsModalOpen(false);
      setEditingSite(null);
      loadSites();
    } catch (e) {
      alert("Hiba a webhely mentésekor!");
    }
  };
  const handleDeleteSite = async id => {
    if (!confirm("Biztosan törlöd ezt a webhelyet? A Child plugin szinkronizációja azonnal leáll az adott oldalon!")) return;
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: `/msdl-main/v1/sites/${id}`,
        method: "DELETE"
      });
      loadSites();
    } catch (e) {
      alert("Hiba a törléskor!");
    }
  };

  // --- Műveletek: SharePoint Azonosító Kereső ---
  const handleSearchSites = async e => {
    e.preventDefault();
    setIsSearching(true);
    setFoundSites([]);
    setSelectedFoundSite(null);
    setFoundDrives([]);
    try {
      const results = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: `/msdl-main/v1/search-sites?q=${encodeURIComponent(searchQuery)}`
      });
      setFoundSites(results);
    } catch (err) {
      alert("Hiba a keresés során. Ellenőrizd a kulcsokat!");
    }
    setIsSearching(false);
  };
  const handleSelectSite = async site => {
    setSelectedFoundSite(site);
    setIsLoadingDrives(true);
    try {
      const drives = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: `/msdl-main/v1/get-drives?site_id=${site.id}`
      });
      setFoundDrives(drives);
    } catch (err) {
      alert("Nem sikerült lekérni a dokumentumtárakat.");
    }
    setIsLoadingDrives(false);
  };
  const handleApplyIds = (siteId, driveId) => {
    setOptions({
      ...options,
      msdl_site_id: siteId,
      msdl_drive_id: driveId
    });
    setIsFinderOpen(false);
    setStatusText("Azonosítók bemásolva! Ne felejtsd el elmenteni a beállításokat.");
  };

  // --- Műveletek: MAPPA Kereső ---
  const openFolderFinder = () => {
    const driveId = editingSite?.custom_drive_id || options.msdl_drive_id;
    if (!driveId) {
      alert("Nincs beállítva Dokumentumtár (Drive ID)! Előbb add meg a központi beállításokban, vagy mentsd el a webhely haladó beállításainál az egyedit.");
      return;
    }
    setFolderSearchQuery("");
    setFoundFolders([]);

    // VÁLTUNK A MODALON BELÜLI NÉZETRE
    setIsFolderBrowserActive(true);
    handleSearchFolders(null, driveId, "");
  };
  const handleSearchFolders = async (e, driveIdOverride = null, queryOverride = null) => {
    if (e) e.preventDefault();
    setIsSearchingFolders(true);
    setFoundFolders([]);
    const driveId = driveIdOverride || editingSite?.custom_drive_id || options.msdl_drive_id;
    const q = queryOverride !== null ? queryOverride : folderSearchQuery;
    try {
      const results = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: `/msdl-main/v1/search-folders?drive_id=${driveId}&q=${encodeURIComponent(q)}`
      });
      setFoundFolders(results);
    } catch (err) {
      alert("Hiba a mappák lekérdezésekor. Ellenőrizd, hogy az adott Drive ID helyes-e.");
    }
    setIsSearchingFolders(false);
  };
  const handleApplyFolder = folder => {
    let relativePath = "";
    if (folder.parentReference && folder.parentReference.path) {
      const prefix = "/drive/root:";
      if (folder.parentReference.path.startsWith(prefix)) {
        relativePath = folder.parentReference.path.substring(prefix.length);
        if (relativePath.startsWith("/")) relativePath = relativePath.substring(1);
      }
    }
    const fullPath = relativePath ? `${relativePath}/${folder.name}` : folder.name;

    // Frissítjük a form adatot ÉS visszaváltunk a szerkesztő nézetre!
    setEditingSite({
      ...editingSite,
      folder_path: fullPath
    });
    setIsFolderBrowserActive(false);
  };
  const tabs = [{
    name: "sites",
    title: "Kliens Webhelyek",
    className: "msdl-tab-sites"
  }, {
    name: "settings",
    title: "Központi API Beállítások",
    className: "msdl-tab-settings"
  }];
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
    className: "wrap msdl-admin-wrapper",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h1", {
      style: {
        marginBottom: "20px"
      },
      children: "MSDL K\xF6zpont - Architekt\xFAra Menedzser"
    }), statusText && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Notice, {
      status: "success",
      isDismissible: false,
      onRemove: () => setStatusText(""),
      children: statusText
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TabPanel, {
      className: "msdl-tabs",
      activeClass: "is-active",
      tabs: tabs,
      children: tab => {
        // BEÁLLÍTÁSOK FÜL
        if (tab.name === "settings") {
          return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
            style: {
              marginTop: "20px",
              maxWidth: "800px"
            },
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, {
              title: "Microsoft Graph Hiteles\xEDt\xE9s \xE9s Rendszerkulcsok",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
                style: {
                  color: "#666",
                  marginBottom: "15px"
                },
                children: "Ezek az azonos\xEDt\xF3k adj\xE1k a k\xF6zponti hozz\xE1f\xE9r\xE9st a Microsoft tenant-hoz."
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
                label: "Tenant ID",
                value: options.msdl_tenant_id,
                onChange: v => setOptions({
                  ...options,
                  msdl_tenant_id: v
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
                label: "Client ID",
                value: options.msdl_client_id,
                onChange: v => setOptions({
                  ...options,
                  msdl_client_id: v
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
                label: "Client Secret",
                type: "password",
                value: options.msdl_client_secret,
                onChange: v => setOptions({
                  ...options,
                  msdl_client_secret: v
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("hr", {
                style: {
                  margin: "20px 0"
                }
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                style: {
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center",
                  marginBottom: "10px"
                },
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h3", {
                  style: {
                    margin: 0
                  },
                  children: "K\xF6zponti T\xE1rol\xF3 Azonos\xEDt\xF3i"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                  isSecondary: true,
                  icon: "search",
                  onClick: () => setIsFinderOpen(true),
                  children: "Azonos\xEDt\xF3k Keres\xE9se"
                })]
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
                label: "K\xF6zponti Site ID",
                value: options.msdl_site_id,
                onChange: v => setOptions({
                  ...options,
                  msdl_site_id: v
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
                label: "K\xF6zponti Drive ID",
                value: options.msdl_drive_id,
                onChange: v => setOptions({
                  ...options,
                  msdl_drive_id: v
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("hr", {
                style: {
                  margin: "20px 0"
                }
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h3", {
                children: "Bels\u0151 Kommunik\xE1ci\xF3s Kulcs"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
                label: "Bels\u0151 API Kulcs (Child hiteles\xEDt\xE9shez)",
                type: "password",
                value: options.msdl_internal_api_key,
                onChange: v => setOptions({
                  ...options,
                  msdl_internal_api_key: v
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                isPrimary: true,
                onClick: handleSaveSettings,
                children: "Be\xE1ll\xEDt\xE1sok Ment\xE9se"
              })]
            })
          });
        }

        // KLIENS WEBHELYEK FÜL
        if (tab.name === "sites") {
          return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
            style: {
              marginTop: "20px"
            },
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
              style: {
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center",
                marginBottom: "15px"
              },
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
                style: {
                  margin: 0
                },
                children: "Az itt list\xE1zott webhelyek (Child pluginok) kaptak enged\xE9lyt a kapcsol\xF3d\xE1sra."
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                isPrimary: true,
                onClick: () => openModal(),
                children: "+ \xDAj Webhely Hozz\xE1ad\xE1sa"
              })]
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("table", {
              className: "wp-list-table widefat fixed striped table-view-list",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("thead", {
                children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("tr", {
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    style: {
                      width: "40px",
                      textAlign: "center"
                    },
                    children: "St\xE1tusz"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    children: "Domain"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    children: "Hozz\xE1rendelt Mappa"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    children: "Egyedi T\xE1rol\xF3"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    style: {
                      width: "200px",
                      textAlign: "right"
                    },
                    children: "M\u0171veletek"
                  })]
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("tbody", {
                children: sites.length === 0 ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("tr", {
                  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                    colSpan: "5",
                    style: {
                      textAlign: "center",
                      padding: "20px"
                    },
                    children: "Nincs webhely."
                  })
                }) : sites.map(site => {
                  const isPending = !site.folder_path;
                  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("tr", {
                    style: {
                      backgroundColor: isPending ? "#fcf0f1" : "transparent"
                    },
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        textAlign: "center",
                        verticalAlign: "middle"
                      },
                      children: isPending ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                        icon: "warning",
                        style: {
                          color: "#d63638"
                        }
                      }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                        icon: "yes-alt",
                        style: {
                          color: "#00a32a"
                        }
                      })
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        verticalAlign: "middle"
                      },
                      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
                        children: site.domain
                      })
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        verticalAlign: "middle"
                      },
                      children: isPending ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
                        style: {
                          color: "#d63638",
                          fontWeight: "bold"
                        },
                        children: "J\xF3v\xE1hagy\xE1sra v\xE1r"
                      }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("code", {
                        children: ["/", site.folder_path]
                      })
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        verticalAlign: "middle"
                      },
                      children: site.custom_site_id ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
                        style: {
                          color: "#2271b1"
                        },
                        children: "\u2713 Akt\xEDv"
                      }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
                        style: {
                          color: "#a0a5aa"
                        },
                        children: "K\xF6zponti"
                      })
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("td", {
                      style: {
                        textAlign: "right",
                        verticalAlign: "middle"
                      },
                      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                        isSmall: true,
                        isSecondary: true,
                        onClick: () => openModal(site),
                        style: {
                          marginRight: "8px"
                        },
                        children: isPending ? "Jóváhagyás & Beállítás" : "Szerkesztés"
                      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                        isSmall: true,
                        isDestructive: true,
                        onClick: () => handleDeleteSite(site.id),
                        children: "T\xF6rl\xE9s"
                      })]
                    })]
                  }, site.id);
                })
              })]
            })]
          });
        }
      }
    }), isFinderOpen && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Modal, {
      title: "SharePoint Azonos\xEDt\xF3 Keres\u0151",
      onRequestClose: () => setIsFinderOpen(false),
      style: {
        width: "700px"
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("form", {
        onSubmit: handleSearchSites,
        style: {
          display: "flex",
          gap: "10px",
          marginBottom: "20px"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            flexGrow: 1
          },
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
            placeholder: "Keres\xE9s webhely nev\xE9re...",
            value: searchQuery,
            onChange: setSearchQuery
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isPrimary: true,
          type: "submit",
          isBusy: isSearching,
          children: "Keres\xE9s"
        })]
      }), foundSites.length > 0 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        style: {
          border: "1px solid #ccc",
          borderRadius: "4px",
          maxHeight: "200px",
          overflowY: "auto",
          marginBottom: "20px"
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("ul", {
          style: {
            margin: 0,
            padding: 0,
            listStyle: "none"
          },
          children: foundSites.map(site => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
            style: {
              padding: "10px",
              borderBottom: "1px solid #eee",
              cursor: "pointer",
              backgroundColor: selectedFoundSite?.id === site.id ? "#f0f6fc" : "#fff"
            },
            onClick: () => handleSelectSite(site),
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
              children: site.name
            }), " ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("span", {
              style: {
                color: "#888",
                fontSize: "12px"
              },
              children: ["(", site.webUrl, ")"]
            })]
          }, site.id))
        })
      }), isLoadingDrives && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          textAlign: "center",
          padding: "20px"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Spinner, {}), " Dokumentumt\xE1rak bet\xF6lt\xE9se..."]
      }), selectedFoundSite && foundDrives.length > 0 && !isLoadingDrives && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h4", {
          style: {
            marginTop: 0
          },
          children: "V\xE1lassz egy Dokumentumt\xE1rat (Drive):"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            border: "1px solid #ccc",
            borderRadius: "4px",
            maxHeight: "200px",
            overflowY: "auto"
          },
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("ul", {
            style: {
              margin: 0,
              padding: 0,
              listStyle: "none"
            },
            children: foundDrives.map(drive => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
              style: {
                padding: "10px",
                borderBottom: "1px solid #eee",
                display: "flex",
                justifyContent: "space-between",
                alignItems: "center"
              },
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
                children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
                  children: drive.name
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                isSecondary: true,
                type: "button",
                isSmall: true,
                onClick: () => handleApplyIds(selectedFoundSite.id, drive.id),
                children: "Kiv\xE1laszt\xE1s"
              })]
            }, drive.id))
          })
        })]
      })]
    }), isModalOpen && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Modal, {
      title: isFolderBrowserActive ? "Mappa Tallózó" : editingSite?.id ? `Szerkesztés: ${editingSite.domain}` : "Új Webhely Felvitele",
      onRequestClose: () => {
        // Ha a tallózóban vagyunk, a bezárás gomb (X) csak visszadob a szerkesztőbe
        if (isFolderBrowserActive) {
          setIsFolderBrowserActive(false);
        } else {
          // Egyébként bezárja az egész ablakot
          setIsModalOpen(false);
        }
      },
      style: {
        width: "600px"
      },
      children: isFolderBrowserActive ?
      /*#__PURE__*/
      // NÉZET 1: MAPPA TALLÓZÓ
      (0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isLink: true,
          icon: "arrow-left-alt2",
          type: "button",
          onClick: () => setIsFolderBrowserActive(false),
          style: {
            marginBottom: "15px"
          },
          children: "Vissza a webhely szerkeszt\xE9s\xE9hez"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("form", {
          onSubmit: e => handleSearchFolders(e),
          style: {
            display: "flex",
            gap: "10px",
            marginBottom: "20px"
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
            style: {
              flexGrow: 1
            },
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
              placeholder: "Keres\xE9s mapp\xE1k k\xF6z\xF6tt (\xFCresen hagyva a gy\xF6keret list\xE1zza)...",
              value: folderSearchQuery,
              onChange: setFolderSearchQuery
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
            isPrimary: true,
            type: "submit",
            isBusy: isSearchingFolders,
            children: "Keres\xE9s"
          })]
        }), isSearchingFolders ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          style: {
            textAlign: "center",
            padding: "20px"
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Spinner, {}), " Mapp\xE1k bet\xF6lt\xE9se..."]
        }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            border: "1px solid #ccc",
            borderRadius: "4px",
            maxHeight: "300px",
            overflowY: "auto"
          },
          children: foundFolders.length === 0 ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
            style: {
              padding: "15px",
              margin: 0,
              textAlign: "center",
              color: "#666"
            },
            children: "Nem tal\xE1ltam mapp\xE1t."
          }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("ul", {
            style: {
              margin: 0,
              padding: 0,
              listStyle: "none"
            },
            children: foundFolders.map(folder => {
              let relativePath = "";
              if (folder.parentReference && folder.parentReference.path) {
                const prefix = "/drive/root:";
                if (folder.parentReference.path.startsWith(prefix)) {
                  relativePath = folder.parentReference.path.substring(prefix.length);
                  if (relativePath.startsWith("/")) relativePath = relativePath.substring(1);
                }
              }
              const displayPath = relativePath ? `/${relativePath}/${folder.name}` : `/${folder.name}`;
              return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("li", {
                style: {
                  padding: "10px",
                  borderBottom: "1px solid #eee",
                  display: "flex",
                  justifyContent: "space-between",
                  alignItems: "center"
                },
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("strong", {
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                      icon: "category",
                      style: {
                        verticalAlign: "text-bottom",
                        marginRight: "5px",
                        color: "#f5c342"
                      }
                    }), folder.name]
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                    style: {
                      color: "#888",
                      fontSize: "11px",
                      marginTop: "3px"
                    },
                    children: ["\xDAtvonal: ", displayPath]
                  })]
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                  isSecondary: true,
                  type: "button",
                  isSmall: true,
                  onClick: () => handleApplyFolder(folder),
                  children: "Kiv\xE1laszt\xE1s"
                })]
              }, folder.id);
            })
          })
        })]
      }) :
      /*#__PURE__*/
      // NÉZET 2: WEBHELY SZERKESZTŐ FORM
      (0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("form", {
        onSubmit: handleSaveSite,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
          label: "Kliens Domain",
          help: "A csatlakoz\xF3 weboldal c\xEDme (pl. gepesz.sze.hu).",
          value: editingSite.domain,
          onChange: val => setEditingSite({
            ...editingSite,
            domain: val
          }),
          required: true
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          style: {
            display: "flex",
            alignItems: "flex-end",
            gap: "10px"
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
            style: {
              flexGrow: 1
            },
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
              label: "Gy\xF6k\xE9r Mappa \xDAtvonala",
              help: "A kiv\xE1lasztott mappa a SharePointb\xF3l.",
              value: editingSite.folder_path,
              onChange: val => setEditingSite({
                ...editingSite,
                folder_path: val
              }),
              required: true
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
            style: {
              marginBottom: "24px"
            },
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
              isSecondary: true,
              type: "button",
              icon: "search",
              onClick: openFolderFinder,
              children: "Tall\xF3z\xE1s"
            })
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          style: {
            marginTop: "20px"
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
            isLink: true,
            type: "button",
            onClick: () => setShowAdvanced(!showAdvanced),
            children: showAdvanced ? "▼ Egyedi SharePoint Tároló elrejtése" : "► Egyedi SharePoint Tároló megadása (Haladó)"
          }), showAdvanced && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
            style: {
              marginTop: "15px",
              padding: "15px",
              backgroundColor: "#f6f7f7",
              borderLeft: "4px solid #72aee6"
            },
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
              label: "Egyedi Site ID",
              value: editingSite.custom_site_id,
              onChange: val => setEditingSite({
                ...editingSite,
                custom_site_id: val
              })
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
              label: "Egyedi Drive ID",
              value: editingSite.custom_drive_id,
              onChange: val => setEditingSite({
                ...editingSite,
                custom_drive_id: val
              })
            })]
          })]
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          style: {
            display: "flex",
            justifyContent: "flex-end",
            marginTop: "20px"
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
            isSecondary: true,
            type: "button",
            onClick: () => setIsModalOpen(false),
            style: {
              marginRight: "10px"
            },
            children: "M\xE9gsem"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
            isPrimary: true,
            type: "submit",
            children: "Ment\xE9s \xE9s J\xF3v\xE1hagy\xE1s"
          })]
        })]
      })
    })]
  });
};
document.addEventListener("DOMContentLoaded", () => {
  const root = document.getElementById("msdl-main-app");
  if (root) (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(App, {}), root);
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map