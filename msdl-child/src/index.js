import { render, useState, useEffect } from "@wordpress/element";
import {
  PanelBody,
  TextControl,
  Button,
  Notice,
  Dashicon,
  Spinner,
  Modal,
  RadioControl,
  CheckboxControl,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

// --- ÚJ: Natív WordPress TinyMCE Komponens ---
const WpTinyMceEditor = ({ value, onChange }) => {
  useEffect(() => {
    const id = "msdl-tinymce-editor";

    // Ha maradt korábbról egy editor példány a DOM-ban, takarítjuk
    if (
      window.wp &&
      window.wp.editor &&
      window.tinymce &&
      window.tinymce.get(id)
    ) {
      window.wp.editor.remove(id);
    }

    if (window.wp && window.wp.editor) {
      window.wp.editor.initialize(id, {
        tinymce: {
          wpautop: true,
          setup: function (editor) {
            editor.on("change keyup", function () {
              onChange(editor.getContent());
            });
          },
        },
        quicktags: true,
        mediaButtons: false,
      });

      // Betöltjük a kezdőértéket az inicializálás után
      setTimeout(() => {
        const ed = window.tinymce && window.tinymce.get(id);
        if (ed && value) ed.setContent(value);
      }, 200);
    }

    return () => {
      if (window.wp && window.wp.editor) {
        window.wp.editor.remove(id);
      }
    };
  }, []);

  return (
    <div style={{ marginTop: "15px", marginBottom: "20px" }}>
      <p style={{ margin: "0 0 8px 0", fontWeight: 500 }}>
        Fájl HTML Leírása (TinyMCE Vizuális Szerkesztő)
      </p>
      <textarea
        id="msdl-tinymce-editor"
        defaultValue={value}
        style={{ width: "100%", height: "200px" }}></textarea>
      <p style={{ fontSize: "11px", color: "#666", margin: "5px 0 0 0" }}>
        Ez a formázott szöveg fog megjelenni a Widgetekben.
      </p>
    </div>
  );
};

const FileManagerApp = () => {
  const [nodes, setNodes] = useState([]);
  const [isLoading, setIsLoading] = useState(true);
  const [wpRoles, setWpRoles] = useState({});

  const [currentFolderId, setCurrentFolderId] = useState(null);
  const [pathHistory, setPathHistory] = useState([
    { id: null, name: "Gyökérmappa" },
  ]);

  const [isVisModalOpen, setIsVisModalOpen] = useState(false);
  const [editingNode, setEditingNode] = useState(null);

  const [visType, setVisType] = useState("public");
  const [selectedRoles, setSelectedRoles] = useState([]);
  const [applyToChildren, setApplyToChildren] = useState(false);

  const [customTitle, setCustomTitle] = useState("");
  const [customDesc, setCustomDesc] = useState("");
  const [isSaving, setIsSaving] = useState(false);

  useEffect(() => {
    loadNodes(currentFolderId);
    if (Object.keys(wpRoles).length === 0) loadRoles();
  }, [currentFolderId]);

  const loadRoles = async () => {
    try {
      const roles = await apiFetch({ path: "/msdl-child/v1/get-roles" });
      setWpRoles(roles);
    } catch (e) {
      console.error("Szerepkörök betöltése sikertelen.");
    }
  };

  const loadNodes = async (parentId) => {
    setIsLoading(true);
    try {
      const url = parentId
        ? `/msdl-child/v1/get-nodes?parent_id=${parentId}`
        : `/msdl-child/v1/get-nodes`;
      const data = await apiFetch({ path: url });
      setNodes(data);
    } catch (error) {
      console.error("Hiba a fájlok betöltésekor:", error);
    }
    setIsLoading(false);
  };

  const handleFolderClick = (folder) => {
    setCurrentFolderId(folder.graph_id);
    setPathHistory([
      ...pathHistory,
      { id: folder.graph_id, name: folder.name },
    ]);
  };

  const handleBreadcrumbClick = (index) => {
    const newPath = pathHistory.slice(0, index + 1);
    setPathHistory(newPath);
    setCurrentFolderId(newPath[newPath.length - 1].id);
  };

  const formatSize = (bytes) => {
    if (bytes === 0 || !bytes) return "--";
    const k = 1024;
    const sizes = ["B", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
  };

  const openVisibilityModal = (node) => {
    setEditingNode(node);
    setApplyToChildren(false);

    setCustomTitle(node.custom_title || "");
    setCustomDesc(node.custom_description || "");

    if (!node.visibility_roles) {
      setVisType("public");
      setSelectedRoles([]);
    } else if (node.visibility_roles === "public") {
      setVisType("public");
    } else if (node.visibility_roles === "loggedin") {
      setVisType("loggedin");
    } else if (node.visibility_roles === "hidden") {
      setVisType("hidden");
    } else {
      setVisType("roles");
      try {
        setSelectedRoles(JSON.parse(node.visibility_roles));
      } catch (e) {
        setSelectedRoles([]);
      }
    }
    setIsVisModalOpen(true);
  };

  const handleRoleToggle = (roleKey) => {
    if (selectedRoles.includes(roleKey)) {
      setSelectedRoles(selectedRoles.filter((r) => r !== roleKey));
    } else {
      setSelectedRoles([...selectedRoles, roleKey]);
    }
  };

  const saveVisibility = async () => {
    setIsSaving(true);

    let finalRolesString = "";
    if (visType === "public") finalRolesString = "public";
    else if (visType === "loggedin") finalRolesString = "loggedin";
    else if (visType === "hidden") finalRolesString = "hidden";
    else finalRolesString = JSON.stringify(selectedRoles);

    try {
      await apiFetch({
        path: "/msdl-child/v1/update-visibility",
        method: "POST",
        data: {
          id: editingNode.id,
          roles: finalRolesString,
          apply_to_children: applyToChildren,
          custom_title: customTitle,
          custom_description: customDesc,
        },
      });
      setIsVisModalOpen(false);
      loadNodes(currentFolderId);
    } catch (e) {
      alert("Hiba történt a mentéskor!");
    }
    setIsSaving(false);
  };

  const getVisibilityBadge = (roleString) => {
    if (!roleString)
      return (
        <span
          style={{
            padding: "3px 8px",
            borderRadius: "4px",
            backgroundColor: "#f8d7da",
            color: "#d63638",
            fontWeight: "bold",
            fontSize: "11px",
          }}>
          Kezelhetetlen (Új)
        </span>
      );
    if (roleString === "hidden")
      return (
        <span
          style={{
            padding: "3px 8px",
            borderRadius: "4px",
            backgroundColor: "#e2e4e7",
            color: "#50575e",
            fontWeight: "bold",
            fontSize: "11px",
          }}>
          <Dashicon
            icon="hidden"
            style={{
              fontSize: "14px",
              width: "14px",
              height: "14px",
              verticalAlign: "middle",
            }}
          />{" "}
          Rejtett
        </span>
      );
    if (roleString === "public")
      return (
        <span
          style={{
            padding: "3px 8px",
            borderRadius: "4px",
            backgroundColor: "#d4edda",
            color: "#00a32a",
            fontWeight: "bold",
            fontSize: "11px",
          }}>
          Nyilvános
        </span>
      );
    if (roleString === "loggedin")
      return (
        <span
          style={{
            padding: "3px 8px",
            borderRadius: "4px",
            backgroundColor: "#fff3cd",
            color: "#856404",
            fontWeight: "bold",
            fontSize: "11px",
          }}>
          Bejelentkezett
        </span>
      );
    return (
      <span
        style={{
          padding: "3px 8px",
          borderRadius: "4px",
          backgroundColor: "#cce5ff",
          color: "#004085",
          fontWeight: "bold",
          fontSize: "11px",
        }}>
        Szerepkörhöz kötött
      </span>
    );
  };

  return (
    <div className="wrap">
      <h1 style={{ marginBottom: "20px" }}>Fájlkezelő és Jogosultságok</h1>

      <div
        style={{
          marginBottom: "15px",
          padding: "10px",
          backgroundColor: "#fff",
          border: "1px solid #ccd0d4",
          display: "flex",
          alignItems: "center",
          gap: "8px",
        }}>
        <Dashicon icon="category" style={{ color: "#82878c" }} />
        {pathHistory.map((crumb, index) => (
          <span
            key={crumb.id || "root"}
            style={{ display: "flex", alignItems: "center", gap: "8px" }}>
            <Button
              isLink
              style={{
                textDecoration: "none",
                fontWeight:
                  index === pathHistory.length - 1 ? "bold" : "normal",
                color: index === pathHistory.length - 1 ? "#1d2327" : "#2271b1",
              }}
              onClick={() => handleBreadcrumbClick(index)}>
              {crumb.name}
            </Button>
            {index < pathHistory.length - 1 && (
              <span style={{ color: "#82878c" }}>/</span>
            )}
          </span>
        ))}
      </div>

      <table className="wp-list-table widefat fixed striped table-view-list">
        <thead>
          <tr>
            <th style={{ width: "50px", textAlign: "center" }}>Típus</th>
            <th>Eredeti Név</th>
            <th>Megjelenített Cím</th>
            <th style={{ width: "150px" }}>Láthatóság</th>
            <th style={{ width: "100px" }}>Méret</th>
            <th style={{ width: "150px" }}>Műveletek</th>
          </tr>
        </thead>
        <tbody>
          {isLoading ? (
            <tr>
              <td colSpan="6" style={{ textAlign: "center", padding: "30px" }}>
                <Spinner /> Betöltés...
              </td>
            </tr>
          ) : nodes.length === 0 ? (
            <tr>
              <td
                colSpan="6"
                style={{ textAlign: "center", padding: "30px", color: "#666" }}>
                A mappa üres.
              </td>
            </tr>
          ) : (
            nodes.map((node) => {
              const isUntreated = !node.visibility_roles;
              const isHidden = node.visibility_roles === "hidden";
              return (
                <tr
                  key={node.id}
                  style={{
                    backgroundColor: isUntreated ? "#fcf0f1" : "transparent",
                    opacity: isHidden ? 0.6 : 1,
                  }}>
                  <td style={{ textAlign: "center", verticalAlign: "middle" }}>
                    {node.type === "folder" ? (
                      <Dashicon icon="portfolio" style={{ color: "#f5c342" }} />
                    ) : (
                      <Dashicon
                        icon="media-document"
                        style={{ color: "#72aee6" }}
                      />
                    )}
                  </td>
                  <td style={{ verticalAlign: "middle" }}>
                    {node.type === "folder" ? (
                      <Button
                        isLink
                        onClick={() => handleFolderClick(node)}
                        style={{
                          fontWeight: "bold",
                          textDecoration: "none",
                          color: isHidden ? "#777" : "",
                        }}>
                        {node.name}
                      </Button>
                    ) : (
                      <strong>{node.name}</strong>
                    )}
                  </td>
                  <td style={{ verticalAlign: "middle" }}>
                    {node.custom_title ? (
                      <strong style={{ color: "#007cba" }}>
                        {node.custom_title}
                      </strong>
                    ) : (
                      <span style={{ color: "#999" }}>- Nincs -</span>
                    )}
                  </td>
                  <td style={{ verticalAlign: "middle" }}>
                    {getVisibilityBadge(node.visibility_roles)}
                  </td>
                  <td style={{ verticalAlign: "middle" }}>
                    {formatSize(node.size)}
                  </td>
                  <td style={{ verticalAlign: "middle" }}>
                    <Button
                      isSmall
                      isSecondary
                      onClick={() => openVisibilityModal(node)}>
                      Szerkesztés
                    </Button>
                  </td>
                </tr>
              );
            })
          )}
        </tbody>
      </table>

      {isVisModalOpen && editingNode && (
        <Modal
          title={`Beállítások: ${editingNode.name}`}
          onRequestClose={() => setIsVisModalOpen(false)}
          style={{ width: "700px" }}>
          <div
            style={{
              marginBottom: "20px",
              paddingBottom: "15px",
              borderBottom: "1px solid #eee",
            }}>
            <TextControl
              label="Egyedi Cím (Megjelenített Név)"
              value={customTitle}
              onChange={setCustomTitle}
              help="Ha kitöltöd, a widgetek ezt a nevet mutatják az eredeti fájlnév helyett."
            />

            {/* ÚJ TINYMCE VIZUÁLIS SZERKESZTŐ */}
            <WpTinyMceEditor value={customDesc} onChange={setCustomDesc} />
          </div>

          <RadioControl
            label="Láthatóság (Jogosultság)"
            selected={visType}
            options={[
              { label: "Nyilvános (Bárki láthatja)", value: "public" },
              { label: "Csak bejelentkezett felhasználók", value: "loggedin" },
              { label: "Kizárólag specifikus szerepkörök", value: "roles" },
              {
                label: "Rejtett (Lomtár / Teljes elrejtés a frontendről)",
                value: "hidden",
              },
            ]}
            onChange={(value) => setVisType(value)}
          />

          {visType === "roles" && (
            <div
              style={{
                marginTop: "15px",
                padding: "15px",
                border: "1px solid #ccc",
                borderRadius: "4px",
                maxHeight: "200px",
                overflowY: "auto",
              }}>
              <p style={{ margin: "0 0 10px 0", fontWeight: "bold" }}>
                Válassz szerepköröket:
              </p>
              {Object.entries(wpRoles).map(([key, name]) => (
                <CheckboxControl
                  key={key}
                  label={name}
                  checked={selectedRoles.includes(key)}
                  onChange={() => handleRoleToggle(key)}
                />
              ))}
            </div>
          )}

          {editingNode.type === "folder" && (
            <div
              style={{
                marginTop: "20px",
                padding: "15px",
                backgroundColor: "#f0f6fc",
                borderLeft: "4px solid #72aee6",
              }}>
              <CheckboxControl
                label="Öröklődés kényszerítése: Alkalmazás minden almappára és fájlra ebben a mappában."
                checked={applyToChildren}
                onChange={setApplyToChildren}
              />
              <p
                style={{
                  fontSize: "11px",
                  color: "#666",
                  margin: "5px 0 0 0",
                }}>
                Figyelem: Ez azonnal felülírja a mappán belül lévő összes elem
                egyedi beállítását (kivéve a Címet és a Leírást)!
              </p>
            </div>
          )}

          <div
            style={{
              display: "flex",
              justifyContent: "flex-end",
              marginTop: "20px",
            }}>
            <Button
              isSecondary
              onClick={() => setIsVisModalOpen(false)}
              style={{ marginRight: "10px" }}>
              Mégsem
            </Button>
            <Button isPrimary isBusy={isSaving} onClick={saveVisibility}>
              Mentés
            </Button>
          </div>
        </Modal>
      )}
    </div>
  );
};

const SyncApp = () => {
  const [isSyncing, setIsSyncing] = useState(false);
  const [syncResult, setSyncResult] = useState(null);

  const handleManualSync = async () => {
    setIsSyncing(true);
    setSyncResult({
      type: "info",
      msg: "Szinkronizáció folyamatban a Microsoft szervereivel... Kérlek várj!",
    });
    try {
      const response = await apiFetch({
        path: "/msdl-child/v1/sync-now",
        method: "POST",
      });
      if (response.success)
        setSyncResult({ type: "success", msg: response.message });
      else setSyncResult({ type: "error", msg: `Hiba: ${response.message}` });
    } catch (error) {
      setSyncResult({
        type: "error",
        msg: "Hálózati hiba a szinkronizáció során.",
      });
    }
    setIsSyncing(false);
  };

  const handleResetSync = async () => {
    if (
      !window.confirm(
        "Biztosan törlöd a szinkronizációs gyorsítótárat? Ez a következő szinkronizációnál mindent a nulláról tölt le (az eddig beállított jogosultságok természetesen megmaradnak).",
      )
    )
      return;
    setIsSyncing(true);
    setSyncResult({ type: "info", msg: "Gyorsítótár törlése folyamatban..." });
    try {
      const res = await apiFetch({
        path: "/msdl-child/v1/reset-sync",
        method: "POST",
      });
      if (res.success) {
        setSyncResult({
          type: "info",
          msg: "Gyorsítótár törölve. Teljes szinkronizáció indítása...",
        });
        await handleManualSync();
      } else {
        setSyncResult({ type: "error", msg: res.message });
        setIsSyncing(false);
      }
    } catch (error) {
      setSyncResult({
        type: "error",
        msg: "Hálózati hiba a gyorsítótár törlésekor.",
      });
      setIsSyncing(false);
    }
  };

  return (
    <div className="wrap">
      <h1>Szinkronizáció</h1>
      <PanelBody title="Kézi Szinkronizáció és Gyorsítótár">
        <p style={{ marginBottom: "15px", color: "#666" }}>
          Itt indíthatod el manuálisan a Microsoft SharePoint mappa tartalmának
          letöltését a helyi WordPress adatbázisba. A normál szinkronizáció csak
          a változásokat kéri le. Ha strukturális hiba lép fel, használd a
          gyorsítótár ürítését!
        </p>
        {syncResult && (
          <Notice
            status={syncResult.type}
            isDismissible={false}
            style={{ marginBottom: "20px" }}>
            {syncResult.msg}
          </Notice>
        )}
        <div style={{ display: "flex", gap: "10px" }}>
          <Button isPrimary isBusy={isSyncing} onClick={handleManualSync}>
            {isSyncing ? "Szinkronizálás..." : "Kézi Szinkronizáció Indítása"}
          </Button>
          <Button
            isSecondary
            isBusy={isSyncing}
            onClick={handleResetSync}
            style={{ borderColor: "#d63638", color: "#d63638" }}>
            Gyorsítótár ürítése (Teljes Szinkron)
          </Button>
        </div>
      </PanelBody>
    </div>
  );
};

const SettingsApp = () => {
  const [options, setOptions] = useState({
    msdl_main_server_url: "",
    msdl_internal_api_key: "",
    msdl_sync_mode: "central",
    msdl_local_sync_interval: "hourly",
  });
  const [isSaving, setIsSaving] = useState(false);
  const [statusText, setStatusText] = useState("");

  useEffect(() => {
    apiFetch({ path: "/wp/v2/settings" }).then((settings) => {
      setOptions({
        msdl_main_server_url: settings.msdl_main_server_url || "",
        msdl_internal_api_key: settings.msdl_internal_api_key || "",
        msdl_sync_mode: settings.msdl_sync_mode || "central",
        msdl_local_sync_interval: settings.msdl_local_sync_interval || "hourly",
      });
    });
  }, []);

  const handleSave = async () => {
    setIsSaving(true);
    setStatusText("Mentés...");
    try {
      await apiFetch({
        path: "/wp/v2/settings",
        method: "POST",
        data: options,
      });
      await apiFetch({ path: "/msdl-child/v1/update-cron", method: "POST" });
      const testResult = await apiFetch({
        path: "/msdl-child/v1/test-connection",
      });
      if (testResult.success) setStatusText(`Sikeres mentés és kapcsolat!`);
      else
        setStatusText(
          `Mentve, de hiba a kapcsolódáskor: ${testResult.message}`,
        );
    } catch (error) {
      setStatusText("Hiba történt a mentés során.");
    }
    setIsSaving(false);
  };

  return (
    <div className="wrap">
      <h1>Kliens Beállítások</h1>
      <PanelBody title="Központi Kapcsolat">
        {statusText && (
          <Notice
            status="info"
            isDismissible={false}
            onRemove={() => setStatusText("")}>
            {statusText}
          </Notice>
        )}
        <TextControl
          label="Main Szerver URL"
          value={options.msdl_main_server_url}
          onChange={(val) =>
            setOptions({ ...options, msdl_main_server_url: val })
          }
        />
        <TextControl
          label="Belső API Kulcs"
          type="password"
          value={options.msdl_internal_api_key}
          onChange={(val) =>
            setOptions({ ...options, msdl_internal_api_key: val })
          }
        />
      </PanelBody>

      <PanelBody title="Automata Szinkronizáció Irányítása">
        <RadioControl
          selected={options.msdl_sync_mode}
          options={[
            {
              label: "Központi beállítás követése (Ajánlott)",
              value: "central",
            },
            {
              label: "Helyi felülbírálás egyedi időzítővel",
              value: "override",
            },
            {
              label: "Szinkronizáció kikapcsolva ezen az oldalon",
              value: "disabled",
            },
          ]}
          onChange={(value) =>
            setOptions({ ...options, msdl_sync_mode: value })
          }
        />
        {options.msdl_sync_mode === "override" && (
          <div
            style={{
              marginTop: "15px",
              padding: "15px",
              backgroundColor: "#f0f6fc",
              borderLeft: "4px solid #72aee6",
            }}>
            <p style={{ margin: "0 0 10px 0", fontWeight: "bold" }}>
              Egyedi időzítés megadása:
            </p>
            <select
              value={options.msdl_local_sync_interval}
              onChange={(e) =>
                setOptions({
                  ...options,
                  msdl_local_sync_interval: e.target.value,
                })
              }
              style={{ width: "100%", padding: "8px" }}>
              <option value="msdl_1min">1 percenként (CSAK TESZTRE)</option>
              <option value="msdl_15min">15 percenként</option>
              <option value="msdl_30min">30 percenként</option>
              <option value="hourly">Óránként</option>
              <option value="twicedaily">Naponta kétszer</option>
              <option value="daily">Naponta egyszer</option>
            </select>
          </div>
        )}
        <Button
          isPrimary
          isBusy={isSaving}
          onClick={handleSave}
          style={{ marginTop: "20px" }}>
          {isSaving ? "Mentés..." : "Beállítások Mentése"}
        </Button>
      </PanelBody>
    </div>
  );
};

document.addEventListener("DOMContentLoaded", () => {
  const filemanagerRoot = document.getElementById("msdl-admin-filemanager");
  const syncRoot = document.getElementById("msdl-admin-sync");
  const settingsRoot = document.getElementById("msdl-admin-settings");

  if (filemanagerRoot) render(<FileManagerApp />, filemanagerRoot);
  else if (syncRoot) render(<SyncApp />, syncRoot);
  else if (settingsRoot) render(<SettingsApp />, settingsRoot);
});
