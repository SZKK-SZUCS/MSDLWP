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
  // --- State-ek ---
  const [options, setOptions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)({
    msdl_tenant_id: "",
    msdl_client_id: "",
    msdl_client_secret: "",
    msdl_site_id: "",
    msdl_drive_id: "",
    msdl_internal_api_key: "",
    msdl_global_sync_interval: ""
  });
  const [sites, setSites] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [statusText, setStatusText] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");

  // Visszaszámláló state-ek
  const [nextSyncTimestamp, setNextSyncTimestamp] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(0);
  const [countdownText, setCountdownText] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [siteSearchFilter, setSiteSearchFilter] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [selectedSites, setSelectedSites] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [bulkAction, setBulkAction] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [isBulkProcessing, setIsBulkProcessing] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [isModalOpen, setIsModalOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [editingSite, setEditingSite] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [showAdvanced, setShowAdvanced] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [isFolderBrowserActive, setIsFolderBrowserActive] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [isFinderOpen, setIsFinderOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [searchQuery, setSearchQuery] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [isSearching, setIsSearching] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [foundSites, setFoundSites] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [selectedFoundSite, setSelectedFoundSite] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [foundDrives, setFoundDrives] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [isLoadingDrives, setIsLoadingDrives] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [folderSearchQuery, setFolderSearchQuery] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [isSearchingFolders, setIsSearchingFolders] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [foundFolders, setFoundFolders] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);

  // Visszaszámláló és folyamatjelző state-ek
  const [isProcessingBatch, setIsProcessingBatch] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [syncProgress, setSyncProgress] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)({
    current: 0,
    total: 0
  });
  const syncSnapshots = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useRef)({});

  // --- Adatbetöltés ---
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    loadSettings();
    loadSites();
    fetchNextSyncTime();
  }, []);

  // Visszaszámláló logika (1 másodperces frissítés)
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const timer = setInterval(() => {
      const now = Math.floor(Date.now() / 1000);
      if (isProcessingBatch) {
        handleBatchPolling();
        return;
      }
      if (nextSyncTimestamp === 0) {
        setCountdownText("Nincs ütemezve");
        return;
      }
      const diff = nextSyncTimestamp - now;
      if (diff <= 0) {
        startProgressTracking();
      } else {
        const h = Math.floor(diff / 3600);
        const m = Math.floor(diff % 3600 / 60);
        const s = diff % 60;

        // Ha több mint egy óra van hátra, kiírjuk az órát is
        if (h > 0) {
          setCountdownText(`${h}:${m < 10 ? "0" : ""}${m}:${s < 10 ? "0" : ""}${s}`);
        } else {
          // Ha kevesebb mint egy óra, marad a megszokott MM:SS
          setCountdownText(`${m}:${s < 10 ? "0" : ""}${s}`);
        }
      }
    }, 1000);
    return () => clearInterval(timer);
  }, [nextSyncTimestamp, isProcessingBatch, sites]);
  const fetchNextSyncTime = async () => {
    try {
      const data = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-main/v1/get-next-sync"
      });
      setNextSyncTimestamp(data.next_sync);
    } catch (e) {
      console.error(e);
    }
  };
  const startProgressTracking = () => {
    const centralSites = sites.filter(s => s.is_active == 1 && s.sync_mode === "central" && s.folder_path);
    if (centralSites.length === 0) {
      fetchNextSyncTime();
      return;
    }

    // Snapshot készítése: elmentjük, mi volt a dátum a kezdéskor
    const snapshots = {};
    centralSites.forEach(s => {
      snapshots[s.id] = s.last_sync;
    });
    syncSnapshots.current = snapshots;
    setSyncProgress({
      current: 0,
      total: centralSites.length
    });
    setIsProcessingBatch(true);
    setCountdownText(`0 / ${centralSites.length}`);
  };
  const handleBatchPolling = async () => {
    // 3 másodpercenként kérünk új adatokat a szervertől (Soft Refresh)
    if (Date.now() % 3000 < 1000) {
      try {
        const freshSites = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
          path: "/msdl-main/v1/sites"
        });
        setSites(freshSites); // Frissítjük a táblázatot az oldalon belül

        const centralSites = freshSites.filter(s => s.is_active == 1 && s.sync_mode === "central" && s.folder_path);

        // Ellenőrizzük, hánynak változott meg a dátuma a kezdés óta
        const updatedCount = centralSites.filter(s => {
          return s.last_sync !== syncSnapshots.current[s.id];
        }).length;
        setSyncProgress({
          current: updatedCount,
          total: centralSites.length
        });
        setCountdownText(`${updatedCount} / ${centralSites.length}`);
        if (updatedCount >= centralSites.length) {
          setIsProcessingBatch(false);
          fetchNextSyncTime();
        }
      } catch (e) {
        console.error(e);
      }
    }
  };
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
        msdl_internal_api_key: settings.msdl_internal_api_key || "",
        msdl_global_sync_interval: settings.msdl_global_sync_interval || ""
      });
    }).catch(console.error);
  };
  const loadSites = () => {
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
      path: "/msdl-main/v1/sites"
    }).then(setSites).catch(console.error);
  };

  // --- Műveletek ---
  const handleSaveSettings = async () => {
    setStatusText("Mentés...");
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/wp/v2/settings",
        method: "POST",
        data: options
      });
      setStatusText("Beállítások sikeresen elmentve!");
      fetchNextSyncTime(); // Újraütemezés miatt lekérjük az új időpontot
      setTimeout(() => setStatusText(""), 3000);
    } catch (e) {
      setStatusText("Hiba a mentéskor!");
    }
  };
  const openModal = (site = {
    domain: "",
    folder_path: "",
    custom_site_id: "",
    custom_drive_id: "",
    is_active: 1
  }) => {
    setEditingSite(site);
    setShowAdvanced(!!(site.custom_site_id || site.custom_drive_id));
    setIsFolderBrowserActive(false);
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
    if (!confirm("Biztosan törlöd ezt a webhelyet?")) return;
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: `/msdl-main/v1/sites/${id}`,
        method: "DELETE"
      });
      loadSites();
      setSelectedSites(selectedSites.filter(sId => sId !== id));
    } catch (e) {
      alert("Hiba a törléskor!");
    }
  };
  const handleRemoteCommand = async (site, command) => {
    const actionName = command === "ping" ? "Pingelés" : "Szinkronizáció";
    setStatusText(`${site.domain}: ${actionName} folyamatban...`);
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-main/v1/remote-command",
        method: "POST",
        data: {
          domain: site.domain,
          command: command
        }
      });
      if (response && response.success) {
        setStatusText(`${site.domain}: Sikeres! ${response.message || ""}`);
        if (command === "sync") loadSites();
      } else {
        setStatusText(`${site.domain}: Hiba! ${response?.message || "Nem érkezett válasz."}`);
      }
    } catch (e) {
      setStatusText(`${site.domain}: Kapcsolódási hiba!`);
    }
  };

  // Dinamikus URL nyitó
  const handleOpenSharePoint = async (type, siteId = null) => {
    setStatusText("SharePoint URL lekérése a Microsofttól...");
    try {
      const query = type === "central" ? "?type=central" : `?type=folder&site_id=${siteId}`;
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: `/msdl-main/v1/get-sp-url${query}`
      });
      if (response && response.url) {
        setStatusText(""); // Sáv törlése
        window.open(response.url, "_blank");
      } else {
        setStatusText("Hiba: Nem kaptam vissza URL-t.");
      }
    } catch (err) {
      setStatusText(`Hiba az URL lekérésekor: ${err.message || "Ismeretlen hiba"}`);
    }
  };

  // --- Tömeges műveletek ---
  const filteredSites = sites.filter(s => s.domain.toLowerCase().includes(siteSearchFilter.toLowerCase()));
  const handleSelectAll = isChecked => {
    if (isChecked) setSelectedSites(filteredSites.map(s => s.id));else setSelectedSites([]);
  };
  const handleSelectSite = siteId => {
    if (selectedSites.includes(siteId)) setSelectedSites(selectedSites.filter(id => id !== siteId));else setSelectedSites([...selectedSites, siteId]);
  };
  const handleBulkAction = async () => {
    if (!bulkAction || selectedSites.length === 0) return;
    setIsBulkProcessing(true);
    setStatusText("Tömeges művelet végrehajtása folyamatban...");
    const sitesToProcess = sites.filter(s => selectedSites.includes(s.id));
    for (const site of sitesToProcess) {
      if (bulkAction === "ping" || bulkAction === "sync") {
        if (bulkAction === "sync" && (!site.folder_path || site.is_active == 0)) continue;
        await handleRemoteCommand(site, bulkAction);
      } else if (bulkAction === "suspend") {
        if (site.is_active == 1) await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
          path: "/msdl-main/v1/sites",
          method: "POST",
          data: {
            ...site,
            is_active: 0
          }
        });
      } else if (bulkAction === "activate") {
        if (site.is_active == 0) await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
          path: "/msdl-main/v1/sites",
          method: "POST",
          data: {
            ...site,
            is_active: 1
          }
        });
      }
    }
    setStatusText("Tömeges művelet befejezve!");
    setSelectedSites([]);
    setIsBulkProcessing(false);
    setBulkAction("");
    loadSites();
  };
  const handleToggleActive = async site => {
    const updatedSite = {
      ...site,
      is_active: site.is_active == 1 ? 0 : 1
    };
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-main/v1/sites",
        method: "POST",
        data: updatedSite
      });
      loadSites();
    } catch (e) {
      alert("Hiba az állapot mentésekor!");
    }
  };

  // --- Graph Kereső Metódusok ---
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
      alert("Hiba a keresés során.");
    }
    setIsSearching(false);
  };
  const handleSelectSiteAPI = async site => {
    setSelectedFoundSite(site);
    setIsLoadingDrives(true);
    try {
      const drives = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: `/msdl-main/v1/get-drives?site_id=${site.id}`
      });
      setFoundDrives(drives);
    } catch (err) {
      alert("Hiba a lekérésnél.");
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
    setStatusText("Azonosítók bemásolva!");
  };
  const openFolderFinder = () => {
    const driveId = editingSite?.custom_drive_id || options.msdl_drive_id;
    if (!driveId) {
      alert("Nincs beállítva Drive ID!");
      return;
    }
    setFolderSearchQuery("");
    setFoundFolders([]);
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
      alert("Hiba a mappák lekérdezésekor.");
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
    setEditingSite({
      ...editingSite,
      folder_path: relativePath ? `${relativePath}/${folder.name}` : folder.name
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
      status: "info",
      isDismissible: false,
      onRemove: () => setStatusText(""),
      children: statusText
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TabPanel, {
      className: "msdl-tabs",
      activeClass: "is-active",
      tabs: tabs,
      children: tab => {
        if (tab.name === "settings") {
          return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
            style: {
              marginTop: "20px",
              maxWidth: "800px"
            },
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, {
              title: "Microsoft Graph Hiteles\xEDt\xE9s \xE9s Rendszerkulcsok",
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
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
                label: "Bels\u0151 API Kulcs",
                type: "password",
                value: options.msdl_internal_api_key,
                onChange: v => setOptions({
                  ...options,
                  msdl_internal_api_key: v
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("hr", {
                style: {
                  margin: "20px 0"
                }
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h3", {
                children: "K\xF6zponti Automata Szinkroniz\xE1ci\xF3"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
                style: {
                  color: "#666"
                },
                children: "Ezzel a be\xE1ll\xEDt\xE1ssal fel\xFCl\xEDrhatod az \xF6sszes bek\xF6t\xF6tt webhely szinkroniz\xE1ci\xF3s idej\xE9t, hacsak \u0151k azt helyileg m\xE1sk\xE9nt nem \xE1ll\xEDtj\xE1k be."
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("select", {
                value: options.msdl_global_sync_interval,
                onChange: e => setOptions({
                  ...options,
                  msdl_global_sync_interval: e.target.value
                }),
                style: {
                  width: "100%",
                  padding: "8px",
                  marginBottom: "15px",
                  maxWidth: "400px"
                },
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                  value: "",
                  children: "-- Kikapcsolva (Nincs k\xF6zponti szinkron) --"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                  value: "msdl_5min",
                  children: "5 percenk\xE9nt"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                  value: "msdl_15min",
                  children: "15 percenk\xE9nt"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                  value: "msdl_30min",
                  children: "30 percenk\xE9nt"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                  value: "hourly",
                  children: "\xD3r\xE1nk\xE9nt"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                  value: "twicedaily",
                  children: "Naponta k\xE9tszer"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                  value: "daily",
                  children: "Naponta egyszer"
                })]
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("br", {}), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                isPrimary: true,
                onClick: handleSaveSettings,
                children: "Be\xE1ll\xEDt\xE1sok Ment\xE9se"
              })]
            })
          });
        }
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
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                style: {
                  display: "flex",
                  gap: "15px",
                  alignItems: "center"
                },
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                  style: {
                    display: "flex",
                    gap: "5px",
                    alignItems: "center"
                  },
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("select", {
                    value: bulkAction,
                    onChange: e => setBulkAction(e.target.value),
                    style: {
                      padding: "0 8px",
                      lineHeight: "2.2",
                      height: "32px"
                    },
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                      value: "",
                      children: "T\xF6meges m\u0171veletek"
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                      value: "ping",
                      children: "\xC1llapot Ping (Ellen\u0151rz\xE9s)"
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                      value: "sync",
                      children: "Azonnali Szinkroniz\xE1l\xE1s"
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                      value: "suspend",
                      children: "Felf\xFCggeszt\xE9s (Karbantart\xE1s)"
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
                      value: "activate",
                      children: "Aktiv\xE1l\xE1s"
                    })]
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                    isSecondary: true,
                    isBusy: isBulkProcessing,
                    onClick: handleBulkAction,
                    disabled: !bulkAction || selectedSites.length === 0,
                    children: ["Alkalmaz (", selectedSites.length, ")"]
                  })]
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
                  placeholder: "Keres\xE9s domain szerint...",
                  value: siteSearchFilter,
                  onChange: setSiteSearchFilter,
                  style: {
                    margin: 0,
                    width: "200px"
                  }
                })]
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                style: {
                  display: "flex",
                  gap: "12px",
                  alignItems: "center"
                },
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                  style: {
                    backgroundColor: isProcessingBatch ? "#f6f7f7" : "#fff",
                    padding: "5px 12px",
                    borderRadius: "4px",
                    border: "1px solid #ccd0d4",
                    display: "flex",
                    alignItems: "center",
                    gap: "6px"
                  },
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                    icon: "clock",
                    style: {
                      color: isProcessingBatch ? "#d63638" : "#2271b1"
                    }
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
                    style: {
                      fontWeight: "bold",
                      fontSize: "13px"
                    },
                    children: isProcessingBatch ? `Szinkron: ${countdownText}` : countdownText
                  })]
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                  isSecondary: true,
                  icon: "external",
                  onClick: () => handleOpenSharePoint("central"),
                  children: "K\xF6zponti SharePoint"
                }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                  isPrimary: true,
                  onClick: () => openModal(),
                  children: "+ \xDAj Webhely"
                })]
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
                    children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("input", {
                      type: "checkbox",
                      checked: selectedSites.length === filteredSites.length && filteredSites.length > 0,
                      onChange: e => handleSelectAll(e.target.checked)
                    })
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    style: {
                      width: "70px",
                      textAlign: "center"
                    },
                    children: "St\xE1tusz"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    children: "Domain"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    children: "Mappa / T\xE1rol\xF3"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    style: {
                      width: "180px"
                    },
                    children: "Utols\xF3 Szinkroniz\xE1ci\xF3"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    style: {
                      width: "120px"
                    },
                    children: "Karbantart\xE1s"
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                    style: {
                      width: "260px",
                      textAlign: "right"
                    },
                    children: "M\u0171veletek"
                  })]
                })
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("tbody", {
                children: filteredSites.length === 0 ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("tr", {
                  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                    colSpan: "7",
                    style: {
                      textAlign: "center",
                      padding: "20px"
                    },
                    children: "Nincs a keres\xE9snek megfelel\u0151 webhely."
                  })
                }) : filteredSites.map(site => {
                  const isPending = !site.folder_path;
                  const isSuspended = site.is_active == 0;
                  const siteUrl = site.domain.startsWith("http") ? site.domain : `https://${site.domain}`;
                  let syncModeIcon = "clock";
                  let syncModeColor = "#2271b1";
                  let syncModeText = "Központi ütemezés követése";
                  if (site.sync_mode === "override") {
                    syncModeIcon = "admin-settings";
                    syncModeColor = "#f5c342";
                    syncModeText = "Egyéni (helyi) felülbírálás";
                  } else if (site.sync_mode === "disabled") {
                    syncModeIcon = "hidden";
                    syncModeColor = "#888";
                    syncModeText = "Automata szinkronizáció kikapcsolva";
                  }
                  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("tr", {
                    style: {
                      backgroundColor: isSuspended ? "#f0f0f0" : isPending ? "#fcf0f1" : "transparent",
                      opacity: isSuspended ? 0.7 : 1
                    },
                    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        textAlign: "center",
                        verticalAlign: "middle"
                      },
                      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("input", {
                        type: "checkbox",
                        checked: selectedSites.includes(site.id),
                        onChange: () => handleSelectSite(site.id)
                      })
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        textAlign: "center",
                        verticalAlign: "middle"
                      },
                      children: isSuspended ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                        icon: "hidden",
                        style: {
                          color: "#666"
                        },
                        title: "Felf\xFCggesztve"
                      }) : isPending ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                        icon: "warning",
                        style: {
                          color: "#d63638"
                        },
                        title: "F\xFCgg\u0151ben"
                      }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                        icon: "yes-alt",
                        style: {
                          color: "#00a32a"
                        },
                        title: "Akt\xEDv"
                      })
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        verticalAlign: "middle"
                      },
                      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("a", {
                        href: siteUrl,
                        target: "_blank",
                        rel: "noreferrer",
                        style: {
                          fontWeight: "bold",
                          textDecoration: isSuspended ? "line-through" : "none",
                          fontSize: "14px"
                        },
                        children: [site.domain, " ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                          icon: "external",
                          style: {
                            fontSize: "12px",
                            width: "12px",
                            height: "12px",
                            color: "#888"
                          }
                        })]
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
                      }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                          style: {
                            display: "flex",
                            alignItems: "center",
                            gap: "8px"
                          },
                          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("code", {
                            children: ["/", site.folder_path]
                          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                            isSmall: true,
                            isSecondary: true,
                            icon: "admin-links",
                            title: "Mappa megnyit\xE1sa a SharePointban",
                            onClick: () => handleOpenSharePoint("folder", site.id)
                          })]
                        }), site.custom_site_id && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
                          style: {
                            fontSize: "11px",
                            color: "#2271b1",
                            marginTop: "4px"
                          },
                          children: "\u2713 Egyedi t\xE1rol\xF3 fel\xFClb\xEDr\xE1l\xE1s"
                        })]
                      })
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        verticalAlign: "middle"
                      },
                      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                        style: {
                          display: "flex",
                          alignItems: "center",
                          gap: "8px"
                        },
                        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
                          style: {
                            color: site.last_sync ? "#1d2327" : "#888"
                          },
                          children: site.last_sync ? site.last_sync : "Még nem szinkronizált"
                        }), !isPending && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                          icon: syncModeIcon,
                          style: {
                            color: syncModeColor,
                            cursor: "help",
                            width: "18px",
                            height: "18px"
                          },
                          title: `Ütemezés: ${syncModeText}`
                        }), !isPending && !isSuspended && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                          isSmall: true,
                          isSecondary: true,
                          icon: "update",
                          title: "Szinkroniz\xE1ci\xF3 Ind\xEDt\xE1sa",
                          onClick: () => handleRemoteCommand(site, "sync")
                        })]
                      })
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        verticalAlign: "middle"
                      },
                      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
                        label: isSuspended ? "Szünetel" : "Aktív",
                        checked: !isSuspended,
                        onChange: () => handleToggleActive(site),
                        style: {
                          margin: 0
                        }
                      })
                    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                      style: {
                        verticalAlign: "middle"
                      },
                      children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                        style: {
                          display: "flex",
                          justifyContent: "flex-end",
                          gap: "5px",
                          flexWrap: "wrap"
                        },
                        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                          isSmall: true,
                          isSecondary: true,
                          icon: "admin-network",
                          title: "\xC1llapot Ping",
                          onClick: () => handleRemoteCommand(site, "ping")
                        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                          isSmall: true,
                          isSecondary: true,
                          icon: "admin-users",
                          href: `${siteUrl}/wp-admin`,
                          target: "_blank",
                          title: "WP Admin"
                        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                          isSmall: true,
                          isSecondary: true,
                          onClick: () => openModal(site),
                          children: isPending ? "Jóváhagyás" : "Szerk."
                        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                          isSmall: true,
                          isDestructive: true,
                          onClick: () => handleDeleteSite(site.id),
                          children: "T\xF6rl\xE9s"
                        })]
                      })
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
            onClick: () => handleSelectSiteAPI(site),
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
          children: "V\xE1lassz egy Dokumentumt\xE1rat:"
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
        if (isFolderBrowserActive) setIsFolderBrowserActive(false);else setIsModalOpen(false);
      },
      style: {
        width: "600px"
      },
      children: isFolderBrowserActive ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
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
              placeholder: "Keres\xE9s mapp\xE1k k\xF6z\xF6tt...",
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
      }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("form", {
        onSubmit: handleSaveSite,
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
          label: "Kliens Domain",
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
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            marginTop: "20px",
            padding: "10px",
            border: "1px solid #ddd",
            borderRadius: "4px"
          },
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
            label: "Webhely enged\xE9lyez\xE9se (Akt\xEDv kapcsolat)",
            checked: editingSite.is_active == 1,
            onChange: val => setEditingSite({
              ...editingSite,
              is_active: val ? 1 : 0
            }),
            style: {
              margin: 0
            }
          })
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