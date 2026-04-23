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




const WpTinyMceEditor = ({
  value,
  onChange
}) => {
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    const id = "msdl-tinymce-editor";
    if (window.wp && window.wp.editor && window.tinymce && window.tinymce.get(id)) window.wp.editor.remove(id);
    if (window.wp && window.wp.editor) {
      window.wp.editor.initialize(id, {
        tinymce: {
          wpautop: true,
          plugins: "charmap hr lists paste textcolor wordpress wpdialogs wpeditimage wpemoji wpgallery wplink wpview",
          toolbar1: "formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link unlink wp_adv",
          toolbar2: "strikethrough hr forecolor pastetext removeformat charmap outdent indent undo redo",
          setup: function (editor) {
            editor.on("change keyup ExecCommand NodeChange", function () {
              onChange(editor.getContent());
            });
          }
        },
        quicktags: true,
        mediaButtons: true
      });
      setTimeout(() => {
        const ed = window.tinymce && window.tinymce.get(id);
        if (ed && value) ed.setContent(value);
      }, 200);
    }
    return () => {
      if (window.wp && window.wp.editor) window.wp.editor.remove(id);
    };
  }, []);
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
    style: {
      marginTop: "15px",
      marginBottom: "20px"
    },
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
      style: {
        margin: "0 0 8px 0",
        fontWeight: 500
      },
      children: "F\xE1jl HTML Le\xEDr\xE1sa (Vizu\xE1lis Szerkeszt\u0151)"
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("textarea", {
      id: "msdl-tinymce-editor",
      defaultValue: value,
      style: {
        width: "100%",
        height: "250px"
      }
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
      style: {
        fontSize: "11px",
        color: "#666",
        margin: "5px 0 0 0"
      },
      children: "A \"M\xE9dia hozz\xE1ad\xE1sa\" gombbal k\xE9peket, az eszk\xF6zt\xE1rb\xF3l pedig form\xE1z\xE1sokat sz\xFArhatsz be."
    })]
  });
};
const FileManagerApp = () => {
  const urlParams = new URLSearchParams(window.location.search);
  const initialFolder = urlParams.get("folder") || null;
  const initialOpenFile = urlParams.get("open_file") || null;
  const [nodes, setNodes] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [isLoading, setIsLoading] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(true);
  const [wpRoles, setWpRoles] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)({});
  const [currentFolderId, setCurrentFolderId] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialFolder);
  const [pathHistory, setPathHistory] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialFolder ? [{
    id: null,
    name: "Gyökérmappa"
  }, {
    id: initialFolder,
    name: "Keresett mappa (Linkből)"
  }] : [{
    id: null,
    name: "Gyökérmappa"
  }]);
  const [autoOpenFileId, setAutoOpenFileId] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(initialOpenFile);
  const [searchQuery, setSearchQuery] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [showUnhandledOnly, setShowUnhandledOnly] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [selectedNodes, setSelectedNodes] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [isRootModalOpen, setIsRootModalOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [rootVisType, setRootVisType] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("public");
  const [rootSelectedRoles, setRootSelectedRoles] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [rootAutoInherit, setRootAutoInherit] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [isRootSaving, setIsRootSaving] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [isVisModalOpen, setIsVisModalOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [editingNode, setEditingNode] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [visType, setVisType] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("public");
  const [selectedRoles, setSelectedRoles] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [applyToChildren, setApplyToChildren] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [autoInherit, setAutoInherit] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [customTitle, setCustomTitle] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [customDesc, setCustomDesc] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  const [isSaving, setIsSaving] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [versions, setVersions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [versionRules, setVersionRules] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)({
    active_version: null,
    schedules: {}
  });
  const [isLoadingVersions, setIsLoadingVersions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [isBatchModalOpen, setIsBatchModalOpen] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [batchVisType, setBatchVisType] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("public");
  const [batchSelectedRoles, setBatchSelectedRoles] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)([]);
  const [batchApplyToChildren, setBatchApplyToChildren] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [isBatchSaving, setIsBatchSaving] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    loadNodes(currentFolderId);
    if (Object.keys(wpRoles).length === 0) loadRoles();
  }, [currentFolderId]);
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    if (autoOpenFileId && nodes.length > 0) {
      const nodeToOpen = nodes.find(n => String(n.id) === String(autoOpenFileId));
      if (nodeToOpen) {
        openVisibilityModal(nodeToOpen);
        setAutoOpenFileId(null);
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete("folder");
        newUrl.searchParams.delete("open_file");
        window.history.replaceState({}, "", newUrl);
      }
    }
  }, [nodes, autoOpenFileId]);
  const loadRoles = async () => {
    try {
      const roles = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-child/v1/get-roles"
      });
      setWpRoles(roles);
    } catch (e) {}
  };
  const loadNodes = async parentId => {
    setIsLoading(true);
    setSelectedNodes([]);
    try {
      const url = parentId ? `/msdl-child/v1/get-nodes?parent_id=${parentId}` : `/msdl-child/v1/get-nodes`;
      const data = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: url
      });
      setNodes(data);
    } catch (error) {}
    setIsLoading(false);
  };
  const openRootModal = async () => {
    try {
      const settings = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/wp/v2/settings"
      });
      const rootVis = settings.msdl_root_visibility || "public";
      const rAuto = settings.msdl_root_auto_inherit === "1";
      setRootAutoInherit(rAuto);
      if (["public", "loggedin", "hidden"].includes(rootVis)) {
        setRootVisType(rootVis);
        setRootSelectedRoles([]);
      } else {
        setRootVisType("roles");
        try {
          setRootSelectedRoles(JSON.parse(rootVis));
        } catch (e) {
          setRootSelectedRoles([]);
        }
      }
    } catch (e) {}
    setIsRootModalOpen(true);
  };
  const saveRootVisibility = async () => {
    setIsRootSaving(true);
    let finalRolesString = rootVisType === "roles" ? JSON.stringify(rootSelectedRoles) : rootVisType;
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/wp/v2/settings",
        method: "POST",
        data: {
          msdl_root_visibility: finalRolesString,
          msdl_root_auto_inherit: rootAutoInherit ? "1" : "0"
        }
      });
      setIsRootModalOpen(false);
    } catch (e) {
      alert("Hiba a gyökérmappa mentésekor!");
    }
    setIsRootSaving(false);
  };
  const filteredNodes = nodes.filter(node => {
    if (showUnhandledOnly && node.visibility_roles && node.visibility_roles !== "") return false;
    if (!searchQuery) return true;
    const q = searchQuery.toLowerCase();
    const nameMatch = node.name && node.name.toLowerCase().includes(q);
    const titleMatch = node.custom_title && node.custom_title.toLowerCase().includes(q);
    return nameMatch || titleMatch;
  });
  const handleFolderClick = (e, folder) => {
    e.preventDefault();
    setSearchQuery("");
    setShowUnhandledOnly(false);
    setCurrentFolderId(folder.graph_id);
    setPathHistory([...pathHistory, {
      id: folder.graph_id,
      name: folder.custom_title || folder.name
    }]);
  };
  const handleBreadcrumbClick = (e, index) => {
    e.preventDefault();
    setSearchQuery("");
    setShowUnhandledOnly(false);
    const newPath = pathHistory.slice(0, index + 1);
    setPathHistory(newPath);
    setCurrentFolderId(newPath[newPath.length - 1].id);
  };
  const handleSelectAll = checked => {
    if (checked) setSelectedNodes(filteredNodes.map(n => n.id));else setSelectedNodes([]);
  };
  const handleSelectNode = (id, checked) => {
    if (checked) setSelectedNodes([...selectedNodes, id]);else setSelectedNodes(selectedNodes.filter(nId => nId !== id));
  };
  const formatSize = bytes => {
    if (bytes === 0 || !bytes) return "--";
    const k = 1024;
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
  };
  const openVisibilityModal = node => {
    setEditingNode(node);
    setApplyToChildren(false);
    setCustomTitle(node.custom_title || "");
    setCustomDesc(node.custom_description || "");
    setAutoInherit(node.auto_inherit == 1);
    try {
      setVersionRules(node.version_rules ? JSON.parse(node.version_rules) : {
        active_version: null,
        schedules: {}
      });
    } catch (e) {
      setVersionRules({
        active_version: null,
        schedules: {}
      });
    }
    if (!node.visibility_roles) {
      setVisType("public");
      setSelectedRoles([]);
    } else if (["public", "loggedin", "hidden"].includes(node.visibility_roles)) {
      setVisType(node.visibility_roles);
    } else {
      setVisType("roles");
      try {
        setSelectedRoles(JSON.parse(node.visibility_roles));
      } catch (e) {
        setSelectedRoles([]);
      }
    }
    if (node.type === "file") {
      setIsLoadingVersions(true);
      setVersions([]);
      _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: `/msdl-child/v1/get-file-versions?id=${node.id}`
      }).then(res => {
        setVersions(res);
        setIsLoadingVersions(false);
      }).catch(() => {
        setVersions([]);
        setIsLoadingVersions(false);
      });
    }
    setIsVisModalOpen(true);
  };
  const saveVisibility = async () => {
    setIsSaving(true);
    let finalRolesString = visType === "roles" ? JSON.stringify(selectedRoles) : visType;
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-child/v1/update-visibility",
        method: "POST",
        data: {
          id: editingNode.id,
          roles: finalRolesString,
          apply_to_children: applyToChildren,
          custom_title: customTitle,
          custom_description: customDesc,
          auto_inherit: autoInherit,
          version_rules: versionRules
        }
      });
      setIsVisModalOpen(false);
      loadNodes(currentFolderId);
    } catch (e) {
      alert("Hiba történt a mentéskor!");
    }
    setIsSaving(false);
  };
  const saveBatchVisibility = async () => {
    setIsBatchSaving(true);
    let finalRolesString = batchVisType === "roles" ? JSON.stringify(batchSelectedRoles) : batchVisType;
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-child/v1/batch-update-visibility",
        method: "POST",
        data: {
          ids: selectedNodes,
          roles: finalRolesString,
          apply_to_children: batchApplyToChildren
        }
      });
      setIsBatchModalOpen(false);
      setSelectedNodes([]);
      loadNodes(currentFolderId);
    } catch (e) {
      alert("Hiba történt a tömeges mentéskor!");
    }
    setIsBatchSaving(false);
  };
  const handleActivateVersion = vid => {
    setVersionRules({
      ...versionRules,
      active_version: vid
    });
  };
  const handleScheduleVersion = (vid, dateString) => {
    setVersionRules({
      ...versionRules,
      schedules: {
        ...versionRules.schedules,
        [vid]: dateString
      }
    });
  };
  const clearVersionRules = () => {
    setVersionRules({
      active_version: null,
      schedules: {}
    });
  };

  // ÚJ: Kiszámolja a jelenleg publikált verzió szövegét a listához
  const getPublishedVersionText = node => {
    if (node.type === "folder") return "-";
    try {
      const rules = node.version_rules ? JSON.parse(node.version_rules) : {};
      const now = new Date().getTime();
      let activeVid = rules.active_version;
      let bestTime = 0;
      if (rules.schedules) {
        Object.entries(rules.schedules).forEach(([vid, timeStr]) => {
          if (!timeStr) return;
          const t = new Date(timeStr).getTime();
          if (t <= now && t > bestTime) {
            bestTime = t;
            activeVid = vid;
          }
        });
      }
      if (!activeVid) return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
        style: {
          color: "#8c8f94"
        },
        children: "Legfrissebb"
      });
      return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("span", {
        style: {
          fontWeight: "bold",
          color: "#2271b1"
        },
        children: ["V", activeVid]
      });
    } catch (e) {
      return "Hiba";
    }
  };
  const now = new Date().getTime();
  let currentActiveVid = versionRules.active_version;
  let bestTime = 0;
  Object.entries(versionRules.schedules || {}).forEach(([vid, timeStr]) => {
    if (!timeStr) return;
    const t = new Date(timeStr).getTime();
    if (t <= now && t > bestTime) {
      bestTime = t;
      currentActiveVid = vid;
    }
  });
  const hasFolderSelected = selectedNodes.some(id => nodes.find(n => n.id === id)?.type === "folder");
  const getVisibilityBadge = roleString => {
    if (!roleString) return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
      style: {
        padding: "3px 8px",
        borderRadius: "4px",
        backgroundColor: "#f8d7da",
        color: "#d63638",
        fontWeight: "bold",
        fontSize: "11px"
      },
      children: "Kezelhetetlen (\xDAj)"
    });
    if (roleString === "hidden") return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("span", {
      style: {
        padding: "3px 8px",
        borderRadius: "4px",
        backgroundColor: "#e2e4e7",
        color: "#50575e",
        fontWeight: "bold",
        fontSize: "11px"
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
        icon: "hidden",
        style: {
          fontSize: "14px",
          width: "14px",
          height: "14px",
          verticalAlign: "middle",
          marginTop: "-2px"
        }
      }), " ", "Rejtett"]
    });
    if (roleString === "public") return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
      style: {
        padding: "3px 8px",
        borderRadius: "4px",
        backgroundColor: "#d4edda",
        color: "#00a32a",
        fontWeight: "bold",
        fontSize: "11px"
      },
      children: "Nyilv\xE1nos"
    });
    if (roleString === "loggedin") return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
      style: {
        padding: "3px 8px",
        borderRadius: "4px",
        backgroundColor: "#fff3cd",
        color: "#856404",
        fontWeight: "bold",
        fontSize: "11px"
      },
      children: "Bejelentkezett"
    });
    return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
      style: {
        padding: "3px 8px",
        borderRadius: "4px",
        backgroundColor: "#cce5ff",
        color: "#004085",
        fontWeight: "bold",
        fontSize: "11px"
      },
      children: "Szerepk\xF6rh\xF6z k\xF6t\xF6tt"
    });
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
    className: "wrap",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      style: {
        display: "flex",
        justifyContent: "space-between",
        alignItems: "center",
        marginBottom: "20px"
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h1", {
        style: {
          margin: 0
        },
        children: "F\xE1jlkezel\u0151 \xE9s Jogosults\xE1gok"
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        isSecondary: true,
        onClick: openRootModal,
        style: {
          borderColor: "#2271b1",
          color: "#2271b1"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
          icon: "admin-network",
          style: {
            marginRight: "5px"
          }
        }), " ", "Gy\xF6k\xE9rmappa (Teljes T\xE1r) Lev\xE9d\xE9se"]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      style: {
        marginBottom: "15px",
        padding: "10px",
        backgroundColor: "#fff",
        border: "1px solid #ccd0d4",
        display: "flex",
        alignItems: "center",
        gap: "8px"
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
        icon: "category",
        style: {
          color: "#82878c"
        }
      }), pathHistory.map((crumb, index) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("span", {
        style: {
          display: "flex",
          alignItems: "center",
          gap: "8px"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("a", {
          href: "#",
          style: {
            textDecoration: "none",
            fontWeight: index === pathHistory.length - 1 ? "bold" : "normal",
            color: index === pathHistory.length - 1 ? "#1d2327" : "#2271b1"
          },
          onClick: e => handleBreadcrumbClick(e, index),
          children: crumb.name
        }), index < pathHistory.length - 1 && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
          style: {
            color: "#82878c"
          },
          children: "/"
        })]
      }, crumb.id || "root"))]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      style: {
        display: "flex",
        justifyContent: "space-between",
        marginBottom: "15px",
        alignItems: "center"
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isSecondary: true,
          disabled: selectedNodes.length === 0,
          onClick: () => {
            setBatchVisType("public");
            setBatchSelectedRoles([]);
            setBatchApplyToChildren(false);
            setIsBatchModalOpen(true);
          },
          children: ["T\xF6meges Be\xE1ll\xEDt\xE1s (", selectedNodes.length, ")"]
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          display: "flex",
          gap: "15px",
          alignItems: "center"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.ToggleControl, {
          label: "Csak kezeletlen \xFAj f\xE1jlok",
          checked: showUnhandledOnly,
          onChange: setShowUnhandledOnly,
          style: {
            margin: 0,
            marginTop: "6px"
          }
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("input", {
          type: "text",
          placeholder: "Keres\xE9s n\xE9v vagy c\xEDm alapj\xE1n...",
          value: searchQuery,
          onChange: e => setSearchQuery(e.target.value),
          style: {
            width: "300px",
            padding: "6px 10px",
            borderRadius: "4px",
            border: "1px solid #8c8f94"
          }
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
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.CheckboxControl, {
              checked: filteredNodes.length > 0 && selectedNodes.length === filteredNodes.length,
              onChange: handleSelectAll,
              style: {
                marginBottom: 0
              }
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
            style: {
              width: "50px",
              textAlign: "center"
            },
            children: "T\xEDpus"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
            children: "N\xE9v / C\xEDm"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
            style: {
              width: "100px"
            },
            children: "Publik\xE1lva"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
            style: {
              width: "150px"
            },
            children: "L\xE1that\xF3s\xE1g"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
            style: {
              width: "100px"
            },
            children: "M\xE9ret"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
            style: {
              width: "150px"
            },
            children: "M\u0171veletek"
          })]
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("tbody", {
        children: isLoading ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("tr", {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("td", {
            colSpan: "7",
            style: {
              textAlign: "center",
              padding: "30px"
            },
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Spinner, {}), " Bet\xF6lt\xE9s..."]
          })
        }) : filteredNodes.length === 0 ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("tr", {
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
            colSpan: "7",
            style: {
              textAlign: "center",
              padding: "30px",
              color: "#666"
            },
            children: "Nincs megjelen\xEDthet\u0151 elem."
          })
        }) : filteredNodes.map(node => {
          const isUntreated = !node.visibility_roles;
          const isHidden = node.visibility_roles === "hidden";
          return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("tr", {
            style: {
              backgroundColor: isUntreated ? "#fcf0f1" : "transparent",
              opacity: isHidden ? 0.6 : 1
            },
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
              style: {
                textAlign: "center",
                verticalAlign: "middle"
              },
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.CheckboxControl, {
                checked: selectedNodes.includes(node.id),
                onChange: val => handleSelectNode(node.id, val),
                style: {
                  marginBottom: 0
                }
              })
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
              style: {
                textAlign: "center",
                verticalAlign: "middle"
              },
              children: node.type === "folder" ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                icon: "portfolio",
                style: {
                  color: "#f5c342"
                }
              }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                icon: "media-document",
                style: {
                  color: "#72aee6"
                }
              })
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("td", {
              style: {
                verticalAlign: "middle"
              },
              children: [node.type === "folder" ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                style: {
                  display: "flex",
                  alignItems: "center"
                },
                children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("a", {
                  href: "#",
                  onClick: e => handleFolderClick(e, node),
                  style: {
                    fontWeight: "bold",
                    textDecoration: "none",
                    color: isHidden ? "#777" : "#2271b1",
                    fontSize: "14px"
                  },
                  children: node.custom_title || node.name
                }), node.has_unhandled && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
                  style: {
                    marginLeft: "8px",
                    color: "#d63638",
                    fontSize: "12px",
                    fontWeight: "bold",
                    display: "flex",
                    alignItems: "center"
                  },
                  title: "A mapp\xE1ban m\xE9lyen kezeletlen f\xE1jl tal\xE1lhat\xF3!",
                  children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                    icon: "warning",
                    style: {
                      fontSize: "14px",
                      width: "14px",
                      height: "14px"
                    }
                  })
                })]
              }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
                style: {
                  color: isHidden ? "#777" : "#1d2327",
                  fontSize: "14px"
                },
                children: node.custom_title || node.name.substring(0, node.name.lastIndexOf(".")) || node.name
              }), node.custom_title && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
                style: {
                  fontSize: "12px",
                  color: "#8c8f94",
                  marginTop: "4px"
                },
                children: ["Eredeti n\xE9v: ", node.name]
              })]
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
              style: {
                verticalAlign: "middle"
              },
              children: getPublishedVersionText(node)
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
              style: {
                verticalAlign: "middle"
              },
              children: getVisibilityBadge(node.visibility_roles)
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
              style: {
                verticalAlign: "middle"
              },
              children: formatSize(node.size)
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
              style: {
                verticalAlign: "middle"
              },
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                isSmall: true,
                isSecondary: true,
                onClick: () => openVisibilityModal(node),
                children: "Szerkeszt\xE9s"
              })
            })]
          }, node.id);
        })
      })]
    }), isRootModalOpen && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Modal, {
      title: "Teljes Dokumentumt\xE1r (Gy\xF6k\xE9rmappa) Jogosults\xE1ga",
      onRequestClose: () => setIsRootModalOpen(false),
      style: {
        width: "500px"
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("p", {
        style: {
          color: "#666",
          marginBottom: "20px"
        },
        children: ["Aki itt nincs enged\xE9lyezve, az ", /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
          children: "semmit sem fog l\xE1tni"
        }), " ", "a f\xE1jlkezel\u0151ben (akkor sem, ha az almapp\xE1k nyilv\xE1nosak)!"]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.RadioControl, {
        selected: rootVisType,
        options: [{
          label: "Nyilvános (Bárki láthatja a fájlkezelőt)",
          value: "public"
        }, {
          label: "Csak bejelentkezett felhasználók",
          value: "loggedin"
        }, {
          label: "Kizárólag specifikus szerepkörök",
          value: "roles"
        }],
        onChange: setRootVisType
      }), rootVisType === "roles" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        style: {
          marginTop: "15px",
          padding: "15px",
          border: "1px solid #ccc",
          borderRadius: "4px",
          maxHeight: "200px",
          overflowY: "auto"
        },
        children: Object.entries(wpRoles).map(([key, name]) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.CheckboxControl, {
          label: name,
          checked: rootSelectedRoles.includes(key),
          onChange: val => {
            if (val) setRootSelectedRoles([...rootSelectedRoles, key]);else setRootSelectedRoles(rootSelectedRoles.filter(r => r !== key));
          }
        }, key))
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        style: {
          marginTop: "20px",
          padding: "15px",
          backgroundColor: "#f0f6fc",
          borderLeft: "4px solid #72aee6"
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.CheckboxControl, {
          label: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
            children: "Automata \xF6r\xF6kl\u0151d\xE9s bekapcsol\xE1sa"
          }),
          help: "Ha bekapcsolod, a szinkroniz\xE1ci\xF3 sor\xE1n bet\xF6lt\xF6tt teljesen \xDAJ f\xE1jlok \xE9s mapp\xE1k a Gy\xF6k\xE9rben automatikusan megkapj\xE1k a fenti jogosults\xE1got, \xE9s nem lesznek 'Kezeletlenek'.",
          checked: rootAutoInherit,
          onChange: setRootAutoInherit
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          display: "flex",
          justifyContent: "flex-end",
          marginTop: "20px"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isSecondary: true,
          onClick: () => setIsRootModalOpen(false),
          style: {
            marginRight: "10px"
          },
          children: "M\xE9gsem"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isPrimary: true,
          isBusy: isRootSaving,
          onClick: saveRootVisibility,
          children: "Be\xE1ll\xEDt\xE1s Ment\xE9se"
        })]
      })]
    }), isVisModalOpen && editingNode && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Modal, {
      title: `Beállítások: ${editingNode.name}`,
      onRequestClose: () => setIsVisModalOpen(false),
      style: {
        width: "900px"
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          marginBottom: "20px",
          paddingBottom: "15px",
          borderBottom: "1px solid #eee"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
          label: "Egyedi C\xEDm (Kiterjeszt\xE9s n\xE9lk\xFCl!)",
          value: customTitle,
          onChange: val => setCustomTitle(val)
        }), editingNode.type !== "folder" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(WpTinyMceEditor, {
          value: customDesc,
          onChange: val => setCustomDesc(val)
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.RadioControl, {
        label: "L\xE1that\xF3s\xE1g (Jogosults\xE1g)",
        selected: visType,
        options: [{
          label: "Nyilvános (Bárki láthatja)",
          value: "public"
        }, {
          label: "Csak bejelentkezett felhasználók",
          value: "loggedin"
        }, {
          label: "Kizárólag specifikus szerepkörök",
          value: "roles"
        }, {
          label: "Rejtett (Lomtár / Teljes elrejtés a frontendről)",
          value: "hidden"
        }],
        onChange: value => setVisType(value)
      }), visType === "roles" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          marginTop: "15px",
          padding: "15px",
          border: "1px solid #ccc",
          borderRadius: "4px",
          maxHeight: "200px",
          overflowY: "auto"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
          style: {
            margin: "0 0 10px 0",
            fontWeight: "bold"
          },
          children: "V\xE1lassz szerepk\xF6r\xF6ket:"
        }), Object.entries(wpRoles).map(([key, name]) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.CheckboxControl, {
          label: name,
          checked: selectedRoles.includes(key),
          onChange: val => {
            if (val) setSelectedRoles([...selectedRoles, key]);else setSelectedRoles(selectedRoles.filter(r => r !== key));
          }
        }, key))]
      }), editingNode.type === "folder" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          marginTop: "20px",
          display: "flex",
          flexDirection: "column",
          gap: "10px"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            padding: "15px",
            backgroundColor: "#f0f6fc",
            borderLeft: "4px solid #72aee6"
          },
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.CheckboxControl, {
            label: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
              children: "\xD6r\xF6kl\u0151d\xE9s k\xE9nyszer\xEDt\xE9se: Alkalmaz\xE1s azonnal minden MEGL\xC9V\u0150 almapp\xE1ra \xE9s f\xE1jlra."
            }),
            checked: applyToChildren,
            onChange: setApplyToChildren
          })
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            padding: "15px",
            backgroundColor: "#e2ffe8",
            borderLeft: "4px solid #00a32a"
          },
          children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.CheckboxControl, {
            label: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("strong", {
              children: "J\xF6v\u0151beni automatikus \xF6r\xF6kl\u0151d\xE9s (Szinkroniz\xE1ci\xF3n\xE1l)"
            }),
            help: "Ha bekapcsolod, az ezut\xE1n a mapp\xE1ba felt\xF6lt\xF6tt teljesen \xDAJ f\xE1jlok automatikusan megkapj\xE1k a fenti jogosults\xE1got.",
            checked: autoInherit,
            onChange: setAutoInherit
          })
        })]
      }), editingNode.type === "file" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          marginTop: "30px",
          padding: "20px",
          backgroundColor: "#f6f7f7",
          border: "1px solid #ccd0d4",
          borderRadius: "4px"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          style: {
            display: "flex",
            justifyContent: "space-between",
            alignItems: "center",
            marginBottom: "15px"
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
            children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("h3", {
              style: {
                margin: "0 0 5px 0",
                display: "flex",
                alignItems: "center",
                gap: "8px"
              },
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Dashicon, {
                icon: "backup",
                style: {
                  color: "#2271b1"
                }
              }), " ", "SharePoint Verzi\xF3el\u0151zm\xE9nyek"]
            }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
              style: {
                fontSize: "12px",
                color: "#666",
                margin: 0
              },
              children: "Be\xE1ll\xEDthatod, hogy melyik verzi\xF3 legyen el\xE9rhet\u0151 a weboldalon."
            })]
          }), Object.keys(versionRules.schedules || {}).length > 0 || versionRules.active_version ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
            isSmall: true,
            isDestructive: true,
            onClick: clearVersionRules,
            children: "Vissza\xE1ll\xEDt\xE1s (Mindig a leg\xFAjabb)"
          }) : null]
        }), isLoadingVersions ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Spinner, {}) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("table", {
          className: "wp-list-table widefat fixed striped",
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("thead", {
            children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("tr", {
              children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                style: {
                  width: "85px"
                },
                children: "Verzi\xF3"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                style: {
                  width: "130px"
                },
                children: "L\xE9trehozva"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                style: {
                  width: "170px"
                },
                children: "Id\u0151z\xEDt\xE9s (\xC9les\xEDt\xE9s)"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                style: {
                  width: "80px"
                },
                children: "M\xE9ret"
              }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("th", {
                style: {
                  width: "140px",
                  textAlign: "right"
                },
                children: "M\u0171velet"
              })]
            })
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("tbody", {
            children: versions.length === 0 ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("tr", {
              children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                colSpan: "5",
                children: "Nincsenek kor\xE1bbi verzi\xF3k."
              })
            }) : (() => {
              const activeIndex = versions.findIndex(v => currentActiveVid ? v.id === currentActiveVid : false);
              const resolvedActiveIndex = activeIndex !== -1 ? activeIndex : 0;
              return versions.map((v, i) => {
                const isLatest = i === 0;
                const isActive = currentActiveVid ? v.id === currentActiveVid : isLatest;
                const scheduleDate = versionRules?.schedules?.[v.id] || "";
                const showScheduleInput = i < resolvedActiveIndex;
                return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("tr", {
                  style: {
                    fontWeight: isActive ? "bold" : "normal",
                    backgroundColor: isActive ? "#e2ffe8" : "transparent"
                  },
                  children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                    children: isActive ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("span", {
                      style: {
                        color: "#00a32a"
                      },
                      children: ["Akt\xEDv (V", v.id, ")"]
                    }) : `V${v.id}`
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                    children: new Date(v.lastModifiedDateTime).toLocaleString("hu-HU", {
                      year: "numeric",
                      month: "2-digit",
                      day: "2-digit",
                      hour: "2-digit",
                      minute: "2-digit"
                    })
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                    children: showScheduleInput ? /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("input", {
                      type: "datetime-local",
                      value: scheduleDate,
                      onChange: e => handleScheduleVersion(v.id, e.target.value),
                      style: {
                        fontSize: "12px",
                        padding: "2px 4px",
                        width: "100%",
                        fontWeight: "normal"
                      }
                    }) : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
                      style: {
                        color: "#8c8f94",
                        fontSize: "12px"
                      },
                      children: "-"
                    })
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("td", {
                    children: formatSize(v.size)
                  }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("td", {
                    style: {
                      textAlign: "right"
                    },
                    children: [!isActive && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
                      isSmall: true,
                      isSecondary: true,
                      onClick: () => handleActivateVersion(v.id),
                      style: {
                        marginRight: "5px"
                      },
                      title: "Azonnali \xE9les\xEDt\xE9s",
                      children: "Aktiv\xE1l\xE1s"
                    }), v["@microsoft.graph.downloadUrl"] && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("a", {
                      href: v["@microsoft.graph.downloadUrl"],
                      target: "_blank",
                      className: "button button-small",
                      style: {
                        borderColor: isActive ? "#00a32a" : "#8c8f94",
                        color: isActive ? "#00a32a" : "#8c8f94"
                      },
                      children: "Let\xF6lt\xE9s"
                    })]
                  })]
                }, v.id);
              });
            })()
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
          onClick: () => setIsVisModalOpen(false),
          style: {
            marginRight: "10px"
          },
          children: "M\xE9gsem"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isPrimary: true,
          isBusy: isSaving,
          onClick: saveVisibility,
          children: "Ment\xE9s"
        })]
      })]
    }), isBatchModalOpen && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Modal, {
      title: `Tömeges beállítás (${selectedNodes.length} elem)`,
      onRequestClose: () => setIsBatchModalOpen(false),
      style: {
        width: "500px"
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
        style: {
          color: "#666",
          marginBottom: "20px"
        },
        children: "A be\xE1ll\xEDtott jogosults\xE1g az \xF6sszes kijel\xF6lt elemre alkalmaz\xE1sra ker\xFCl."
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.RadioControl, {
        label: "L\xE1that\xF3s\xE1g (Jogosults\xE1g)",
        selected: batchVisType,
        options: [{
          label: "Nyilvános (Bárki láthatja)",
          value: "public"
        }, {
          label: "Csak bejelentkezett felhasználók",
          value: "loggedin"
        }, {
          label: "Kizárólag specifikus szerepkörök",
          value: "roles"
        }, {
          label: "Rejtett (Lomtár / Teljes elrejtés a frontendről)",
          value: "hidden"
        }],
        onChange: value => setBatchVisType(value)
      }), batchVisType === "roles" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        style: {
          marginTop: "15px",
          padding: "15px",
          border: "1px solid #ccc",
          borderRadius: "4px",
          maxHeight: "200px",
          overflowY: "auto"
        },
        children: Object.entries(wpRoles).map(([key, name]) => /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.CheckboxControl, {
          label: name,
          checked: batchSelectedRoles.includes(key),
          onChange: val => {
            if (val) setBatchSelectedRoles([...batchSelectedRoles, key]);else setBatchSelectedRoles(batchSelectedRoles.filter(r => r !== key));
          }
        }, key))
      }), hasFolderSelected && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
        style: {
          marginTop: "20px",
          padding: "15px",
          backgroundColor: "#f0f6fc",
          borderLeft: "4px solid #72aee6"
        },
        children: /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.CheckboxControl, {
          label: "\xD6r\xF6kl\u0151d\xE9s k\xE9nyszer\xEDt\xE9se: Mivel a kijel\xF6l\xE9s mapp\xE1t is tartalmaz, a jogok r\xE1er\u0151szakolhat\xF3k minden almapp\xE1ra \xE9s f\xE1jlra is!",
          checked: batchApplyToChildren,
          onChange: setBatchApplyToChildren
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          display: "flex",
          justifyContent: "flex-end",
          marginTop: "20px"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isSecondary: true,
          onClick: () => setIsBatchModalOpen(false),
          style: {
            marginRight: "10px"
          },
          children: "M\xE9gsem"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isPrimary: true,
          isBusy: isBatchSaving,
          onClick: saveBatchVisibility,
          children: "T\xF6meges Ment\xE9s"
        })]
      })]
    })]
  });
};
const SyncApp = () => {
  const [isSyncing, setIsSyncing] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [syncInfo, setSyncInfo] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const [syncResult, setSyncResult] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(null);
  const fetchDashboardData = async () => {
    try {
      const sInfo = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-child/v1/sync-status"
      });
      setSyncInfo(sInfo);
    } catch (e) {}
  };
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    fetchDashboardData();
  }, []);
  const handleManualSync = async () => {
    setIsSyncing(true);
    setSyncResult({
      type: "info",
      msg: "Szinkronizáció folyamatban a Microsoft szervereivel... Kérlek várj!"
    });
    try {
      const response = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-child/v1/sync-now",
        method: "POST"
      });
      if (response.success) setSyncResult({
        type: "success",
        msg: response.message
      });else setSyncResult({
        type: "error",
        msg: `Hiba: ${response.message}`
      });
    } catch (error) {
      setSyncResult({
        type: "error",
        msg: "Hálózati hiba a szinkronizáció során."
      });
    }
    fetchDashboardData();
    setIsSyncing(false);
  };
  const handleResetSync = async () => {
    if (!window.confirm("Biztosan törlöd a szinkronizációs gyorsítótárat? Ez a következő szinkronizációnál mindent a nulláról tölt le.")) return;
    setIsSyncing(true);
    setSyncResult({
      type: "info",
      msg: "Gyorsítótár törlése folyamatban..."
    });
    try {
      const res = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-child/v1/reset-sync",
        method: "POST"
      });
      if (res.success) {
        setSyncResult({
          type: "info",
          msg: "Gyorsítótár törölve. Teljes szinkronizáció indítása..."
        });
        await handleManualSync();
      } else {
        setSyncResult({
          type: "error",
          msg: res.message
        });
        setIsSyncing(false);
      }
    } catch (error) {
      setSyncResult({
        type: "error",
        msg: "Hálózati hiba a gyorsítótár törlésekor."
      });
      setIsSyncing(false);
    }
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
    className: "wrap",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h1", {
      children: "Szinkroniz\xE1ci\xF3 \xC1llapota"
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
      style: {
        display: "grid",
        gridTemplateColumns: "repeat(auto-fit, minmax(300px, 1fr))",
        gap: "20px",
        marginBottom: "25px"
      },
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          background: "#fff",
          border: "1px solid #ccd0d4",
          padding: "20px",
          borderRadius: "4px",
          borderLeft: "4px solid #2271b1",
          boxShadow: "0 1px 1px rgba(0,0,0,0.04)"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            fontSize: "13px",
            color: "#50575e",
            textTransform: "uppercase",
            fontWeight: 600
          },
          children: "Utols\xF3 Szinkroniz\xE1ci\xF3"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            fontSize: "18px",
            fontWeight: "bold",
            color: "#1d2327",
            marginTop: "8px"
          },
          children: syncInfo ? syncInfo.last_sync : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Spinner, {})
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          background: "#fff",
          border: "1px solid #ccd0d4",
          padding: "20px",
          borderRadius: "4px",
          borderLeft: "4px solid #00a32a",
          boxShadow: "0 1px 1px rgba(0,0,0,0.04)"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            fontSize: "13px",
            color: "#50575e",
            textTransform: "uppercase",
            fontWeight: 600
          },
          children: "K\xF6vetkez\u0151 \xDCtemezett (Cron)"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("div", {
          style: {
            fontSize: "18px",
            fontWeight: "bold",
            color: "#1d2327",
            marginTop: "8px"
          },
          children: syncInfo ? syncInfo.next_sync : /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Spinner, {})
        }), syncInfo && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
          style: {
            fontSize: "12px",
            color: "#8c8f94",
            marginTop: "6px"
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("span", {
            className: "dashicons dashicons-admin-settings",
            style: {
              fontSize: "14px",
              width: "14px",
              height: "14px"
            }
          }), " ", "M\xF3d: ", syncInfo.mode, " ", syncInfo.interval !== "-" ? `(${syncInfo.interval})` : ""]
        })]
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, {
      title: "K\xE9zi Szinkroniz\xE1ci\xF3 \xE9s Gyors\xEDt\xF3t\xE1r",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
        style: {
          marginBottom: "15px",
          color: "#666"
        },
        children: "Itt ind\xEDthatod el manu\xE1lisan a Microsoft SharePoint mappa tartalm\xE1nak let\xF6lt\xE9s\xE9t a helyi WordPress adatb\xE1zisba. A norm\xE1l szinkroniz\xE1ci\xF3 csak a v\xE1ltoz\xE1sokat k\xE9ri le. Ha struktur\xE1lis hiba l\xE9p fel, haszn\xE1ld a gyors\xEDt\xF3t\xE1r \xFCr\xEDt\xE9s\xE9t!"
      }), syncResult && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Notice, {
        status: syncResult.type,
        isDismissible: false,
        style: {
          marginBottom: "20px"
        },
        children: syncResult.msg
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          display: "flex",
          gap: "10px"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isPrimary: true,
          isBusy: isSyncing,
          onClick: handleManualSync,
          children: isSyncing ? "Szinkronizálás..." : "Kézi Szinkronizáció Indítása"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
          isSecondary: true,
          isBusy: isSyncing,
          onClick: handleResetSync,
          style: {
            borderColor: "#d63638",
            color: "#d63638"
          },
          children: "Gyors\xEDt\xF3t\xE1r \xFCr\xEDt\xE9se (Teljes Szinkron)"
        })]
      })]
    })]
  });
};
const SettingsApp = () => {
  const [options, setOptions] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)({
    msdl_main_server_url: "",
    msdl_internal_api_key: "",
    msdl_sync_mode: "central",
    msdl_local_sync_interval: "hourly"
  });
  const [isSaving, setIsSaving] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)(false);
  const [statusText, setStatusText] = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useState)("");
  (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.useEffect)(() => {
    _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
      path: "/wp/v2/settings"
    }).then(settings => {
      setOptions({
        msdl_main_server_url: settings.msdl_main_server_url || "",
        msdl_internal_api_key: settings.msdl_internal_api_key || "",
        msdl_sync_mode: settings.msdl_sync_mode || "central",
        msdl_local_sync_interval: settings.msdl_local_sync_interval || "hourly"
      });
    });
  }, []);
  const handleSave = async () => {
    setIsSaving(true);
    setStatusText("Mentés...");
    try {
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/wp/v2/settings",
        method: "POST",
        data: options
      });
      await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-child/v1/update-cron",
        method: "POST"
      });
      const testResult = await _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_2___default()({
        path: "/msdl-child/v1/test-connection"
      });
      if (testResult.success) setStatusText(`Sikeres mentés és kapcsolat!`);else setStatusText(`Mentve, de hiba a kapcsolódáskor: ${testResult.message}`);
    } catch (error) {
      setStatusText("Hiba történt a mentés során.");
    }
    setIsSaving(false);
  };
  return /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
    className: "wrap",
    children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("h1", {
      children: "Kliens Be\xE1ll\xEDt\xE1sok"
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, {
      title: "K\xF6zponti Kapcsolat",
      children: [statusText && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Notice, {
        status: "info",
        isDismissible: false,
        onRemove: () => setStatusText(""),
        children: statusText
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
        label: "Main Szerver URL",
        value: options.msdl_main_server_url,
        onChange: val => setOptions({
          ...options,
          msdl_main_server_url: val
        })
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.TextControl, {
        label: "Bels\u0151 API Kulcs",
        type: "password",
        value: options.msdl_internal_api_key,
        onChange: val => setOptions({
          ...options,
          msdl_internal_api_key: val
        })
      })]
    }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.PanelBody, {
      title: "Automata Szinkroniz\xE1ci\xF3 Ir\xE1ny\xEDt\xE1sa",
      children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.RadioControl, {
        selected: options.msdl_sync_mode,
        options: [{
          label: "Központi beállítás követése (Ajánlott)",
          value: "central"
        }, {
          label: "Helyi felülbírálás egyedi időzítővel",
          value: "override"
        }, {
          label: "Szinkronizáció kikapcsolva ezen az oldalon",
          value: "disabled"
        }],
        onChange: value => setOptions({
          ...options,
          msdl_sync_mode: value
        })
      }), options.msdl_sync_mode === "override" && /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("div", {
        style: {
          marginTop: "15px",
          padding: "15px",
          backgroundColor: "#f0f6fc",
          borderLeft: "4px solid #72aee6"
        },
        children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("p", {
          style: {
            margin: "0 0 10px 0",
            fontWeight: "bold"
          },
          children: "Egyedi id\u0151z\xEDt\xE9s megad\xE1sa:"
        }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsxs)("select", {
          value: options.msdl_local_sync_interval,
          onChange: e => setOptions({
            ...options,
            msdl_local_sync_interval: e.target.value
          }),
          style: {
            width: "100%",
            padding: "8px"
          },
          children: [/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
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
            value: "msdl_thricedaily",
            children: "Naponta h\xE1romszor (8, 12, 16)"
          }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)("option", {
            value: "daily",
            children: "Naponta egyszer"
          })]
        })]
      }), /*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(_wordpress_components__WEBPACK_IMPORTED_MODULE_1__.Button, {
        isPrimary: true,
        isBusy: isSaving,
        onClick: handleSave,
        style: {
          marginTop: "20px"
        },
        children: isSaving ? "Mentés..." : "Beállítások Mentése"
      })]
    })]
  });
};
document.addEventListener("DOMContentLoaded", () => {
  const filemanagerRoot = document.getElementById("msdl-admin-filemanager");
  const syncRoot = document.getElementById("msdl-admin-sync");
  const settingsRoot = document.getElementById("msdl-admin-settings");
  if (filemanagerRoot) (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(FileManagerApp, {}), filemanagerRoot);else if (syncRoot) (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(SyncApp, {}), syncRoot);else if (settingsRoot) (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.render)(/*#__PURE__*/(0,react_jsx_runtime__WEBPACK_IMPORTED_MODULE_3__.jsx)(SettingsApp, {}), settingsRoot);
});
})();

/******/ })()
;
//# sourceMappingURL=index.js.map