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

const WpTinyMceEditor = ({ value, onChange }) => {
  useEffect(() => {
    const id = "msdl-tinymce-editor";
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
          plugins:
            "charmap hr lists paste textcolor wordpress wpdialogs wpeditimage wpemoji wpgallery wplink wpview table",
          toolbar1:
            "formatselect bold italic bullist numlist blockquote alignleft aligncenter alignright link unlink wp_adv",
          toolbar2:
            "strikethrough hr forecolor pastetext removeformat charmap outdent indent undo redo table",
          setup: function (editor) {
            editor.on("change keyup", function () {
              onChange(editor.getContent());
            });
          },
        },
        quicktags: true,
        mediaButtons: true,
      });
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
        Fájl HTML Leírása (Vizuális Szerkesztő)
      </p>
      <textarea
        id="msdl-tinymce-editor"
        defaultValue={value}
        style={{ width: "100%", height: "250px" }}></textarea>
      <p style={{ fontSize: "11px", color: "#666", margin: "5px 0 0 0" }}>
        A "Média hozzáadása" gombbal képeket, az eszköztárból pedig táblázatokat
        és formázásokat szúrhatsz be.
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
  const [searchQuery, setSearchQuery] = useState("");

  const [selectedNodes, setSelectedNodes] = useState([]);

  const [isRootModalOpen, setIsRootModalOpen] = useState(false);
  const [rootVisType, setRootVisType] = useState("public");
  const [rootSelectedRoles, setRootSelectedRoles] = useState([]);
  const [isRootSaving, setIsRootSaving] = useState(false);

  const [isVisModalOpen, setIsVisModalOpen] = useState(false);
  const [editingNode, setEditingNode] = useState(null);
  const [visType, setVisType] = useState("public");
  const [selectedRoles, setSelectedRoles] = useState([]);
  const [applyToChildren, setApplyToChildren] = useState(false);
  const [customTitle, setCustomTitle] = useState("");
  const [customDesc, setCustomDesc] = useState("");
  const [isSaving, setIsSaving] = useState(false);

  const [isBatchModalOpen, setIsBatchModalOpen] = useState(false);
  const [batchVisType, setBatchVisType] = useState("public");
  const [batchSelectedRoles, setBatchSelectedRoles] = useState([]);
  const [batchApplyToChildren, setBatchApplyToChildren] = useState(false);
  const [isBatchSaving, setIsBatchSaving] = useState(false);

  useEffect(() => {
    loadNodes(currentFolderId);
    if (Object.keys(wpRoles).length === 0) loadRoles();
  }, [currentFolderId]);

  const loadRoles = async () => {
    try {
      const roles = await apiFetch({ path: "/msdl-child/v1/get-roles" });
      setWpRoles(roles);
    } catch (e) {}
  };

  const loadNodes = async (parentId) => {
    setIsLoading(true);
    setSelectedNodes([]);
    try {
      const url = parentId
        ? `/msdl-child/v1/get-nodes?parent_id=${parentId}`
        : `/msdl-child/v1/get-nodes`;
      const data = await apiFetch({ path: url });
      setNodes(data);
    } catch (error) {}
    setIsLoading(false);
  };

  const openRootModal = async () => {
    try {
      const settings = await apiFetch({ path: "/wp/v2/settings" });
      const rootVis = settings.msdl_root_visibility || "public";

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
    let finalRolesString =
      rootVisType === "roles" ? JSON.stringify(rootSelectedRoles) : rootVisType;
    try {
      await apiFetch({
        path: "/wp/v2/settings",
        method: "POST",
        data: { msdl_root_visibility: finalRolesString },
      });
      setIsRootModalOpen(false);
    } catch (e) {
      alert("Hiba a gyökérmappa mentésekor!");
    }
    setIsRootSaving(false);
  };

  const filteredNodes = nodes.filter((node) => {
    if (!searchQuery) return true;
    const q = searchQuery.toLowerCase();
    const nameMatch = node.name && node.name.toLowerCase().includes(q);
    const titleMatch =
      node.custom_title && node.custom_title.toLowerCase().includes(q);
    return nameMatch || titleMatch;
  });

  const handleFolderClick = (e, folder) => {
    e.preventDefault();
    setSearchQuery("");
    setCurrentFolderId(folder.graph_id);
    setPathHistory([
      ...pathHistory,
      { id: folder.graph_id, name: folder.custom_title || folder.name },
    ]);
  };

  const handleBreadcrumbClick = (e, index) => {
    e.preventDefault();
    setSearchQuery("");
    const newPath = pathHistory.slice(0, index + 1);
    setPathHistory(newPath);
    setCurrentFolderId(newPath[newPath.length - 1].id);
  };

  const handleSelectAll = (checked) => {
    if (checked) setSelectedNodes(filteredNodes.map((n) => n.id));
    else setSelectedNodes([]);
  };

  const handleSelectNode = (id, checked) => {
    if (checked) setSelectedNodes([...selectedNodes, id]);
    else setSelectedNodes(selectedNodes.filter((nId) => nId !== id));
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
    } else if (
      ["public", "loggedin", "hidden"].includes(node.visibility_roles)
    ) {
      setVisType(node.visibility_roles);
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

  const saveVisibility = async () => {
    setIsSaving(true);
    let finalRolesString =
      visType === "roles" ? JSON.stringify(selectedRoles) : visType;
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

  const saveBatchVisibility = async () => {
    setIsBatchSaving(true);
    let finalRolesString =
      batchVisType === "roles"
        ? JSON.stringify(batchSelectedRoles)
        : batchVisType;
    try {
      await apiFetch({
        path: "/msdl-child/v1/batch-update-visibility",
        method: "POST",
        data: {
          ids: selectedNodes,
          roles: finalRolesString,
          apply_to_children: batchApplyToChildren,
        },
      });
      setIsBatchModalOpen(false);
      setSelectedNodes([]);
      loadNodes(currentFolderId);
    } catch (e) {
      alert("Hiba történt a tömeges mentéskor!");
    }
    setIsBatchSaving(false);
  };

  const hasFolderSelected = selectedNodes.some(
    (id) => nodes.find((n) => n.id === id)?.type === "folder",
  );

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
              marginTop: "-2px",
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
      <div
        style={{
          display: "flex",
          justifyContent: "space-between",
          alignItems: "center",
          marginBottom: "20px",
        }}>
        <h1 style={{ margin: 0 }}>Fájlkezelő és Jogosultságok</h1>
        <Button
          isSecondary
          onClick={openRootModal}
          style={{ borderColor: "#2271b1", color: "#2271b1" }}>
          <Dashicon icon="admin-network" style={{ marginRight: "5px" }} />{" "}
          Gyökérmappa (Teljes Tár) Levédése
        </Button>
      </div>

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
            <a
              href="#"
              style={{
                textDecoration: "none",
                fontWeight:
                  index === pathHistory.length - 1 ? "bold" : "normal",
                color: index === pathHistory.length - 1 ? "#1d2327" : "#2271b1",
              }}
              onClick={(e) => handleBreadcrumbClick(e, index)}>
              {crumb.name}
            </a>
            {index < pathHistory.length - 1 && (
              <span style={{ color: "#82878c" }}>/</span>
            )}
          </span>
        ))}
      </div>

      <div
        style={{
          display: "flex",
          justifyContent: "space-between",
          marginBottom: "15px",
          alignItems: "center",
        }}>
        <div>
          <Button
            isSecondary
            disabled={selectedNodes.length === 0}
            onClick={() => {
              setBatchVisType("public");
              setBatchSelectedRoles([]);
              setBatchApplyToChildren(false);
              setIsBatchModalOpen(true);
            }}>
            Tömeges Beállítás ({selectedNodes.length})
          </Button>
        </div>
        <div style={{ width: "300px" }}>
          <input
            type="text"
            placeholder="Keresés név vagy cím alapján..."
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            style={{
              width: "100%",
              padding: "6px 10px",
              borderRadius: "4px",
              border: "1px solid #8c8f94",
              background: "#ffffff",
              color: "#1d2327",
              fontSize: "14px",
            }}
          />
        </div>
      </div>

      <table className="wp-list-table widefat fixed striped table-view-list">
        <thead>
          <tr>
            <th style={{ width: "40px", textAlign: "center" }}>
              <CheckboxControl
                checked={
                  filteredNodes.length > 0 &&
                  selectedNodes.length === filteredNodes.length
                }
                onChange={handleSelectAll}
                style={{ marginBottom: 0 }}
              />
            </th>
            <th style={{ width: "50px", textAlign: "center" }}>Típus</th>
            <th>Név / Cím</th>
            <th style={{ width: "80px", textAlign: "center" }}>Leírás</th>
            <th style={{ width: "150px" }}>Láthatóság</th>
            <th style={{ width: "100px" }}>Méret</th>
            <th style={{ width: "150px" }}>Műveletek</th>
          </tr>
        </thead>
        <tbody>
          {isLoading ? (
            <tr>
              <td colSpan="7" style={{ textAlign: "center", padding: "30px" }}>
                <Spinner /> Betöltés...
              </td>
            </tr>
          ) : filteredNodes.length === 0 ? (
            <tr>
              <td
                colSpan="7"
                style={{ textAlign: "center", padding: "30px", color: "#666" }}>
                Nincs megjeleníthető elem.
              </td>
            </tr>
          ) : (
            filteredNodes.map((node) => {
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
                    <CheckboxControl
                      checked={selectedNodes.includes(node.id)}
                      onChange={(val) => handleSelectNode(node.id, val)}
                      style={{ marginBottom: 0 }}
                    />
                  </td>
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
                      <a
                        href="#"
                        onClick={(e) => handleFolderClick(e, node)}
                        style={{
                          fontWeight: "bold",
                          textDecoration: "none",
                          color: isHidden ? "#777" : "#2271b1",
                          fontSize: "14px",
                        }}>
                        {node.custom_title || node.name}
                      </a>
                    ) : (
                      <strong
                        style={{
                          color: isHidden ? "#777" : "#1d2327",
                          fontSize: "14px",
                        }}>
                        {node.custom_title || node.name}
                      </strong>
                    )}
                    {node.custom_title && (
                      <div
                        style={{
                          fontSize: "12px",
                          color: "#8c8f94",
                          marginTop: "4px",
                        }}>
                        Eredeti név: {node.name}
                      </div>
                    )}
                  </td>
                  <td style={{ textAlign: "center", verticalAlign: "middle" }}>
                    {node.custom_description &&
                    node.custom_description.trim() !== "" ? (
                      <span title="Van leírása" style={{ color: "#00a32a" }}>
                        <Dashicon icon="yes" />
                      </span>
                    ) : (
                      <span title="Nincs leírás" style={{ color: "#ccd0d4" }}>
                        -
                      </span>
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

      {isRootModalOpen && (
        <Modal
          title="Teljes Dokumentumtár (Gyökérmappa) Jogosultsága"
          onRequestClose={() => setIsRootModalOpen(false)}
          style={{ width: "500px" }}>
          <p style={{ color: "#666", marginBottom: "20px" }}>
            Aki itt nincs engedélyezve, az <strong>semmit sem fog látni</strong>{" "}
            a fájlkezelőben (akkor sem, ha az almappák nyilvánosak)!
          </p>
          <RadioControl
            selected={rootVisType}
            options={[
              {
                label: "Nyilvános (Bárki láthatja a fájlkezelőt)",
                value: "public",
              },
              { label: "Csak bejelentkezett felhasználók", value: "loggedin" },
              { label: "Kizárólag specifikus szerepkörök", value: "roles" },
            ]}
            onChange={setRootVisType}
          />
          {rootVisType === "roles" && (
            <div
              style={{
                marginTop: "15px",
                padding: "15px",
                border: "1px solid #ccc",
                borderRadius: "4px",
                maxHeight: "200px",
                overflowY: "auto",
              }}>
              {Object.entries(wpRoles).map(([key, name]) => (
                <CheckboxControl
                  key={key}
                  label={name}
                  checked={rootSelectedRoles.includes(key)}
                  onChange={(val) => {
                    if (val) setRootSelectedRoles([...rootSelectedRoles, key]);
                    else
                      setRootSelectedRoles(
                        rootSelectedRoles.filter((r) => r !== key),
                      );
                  }}
                />
              ))}
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
              onClick={() => setIsRootModalOpen(false)}
              style={{ marginRight: "10px" }}>
              Mégsem
            </Button>
            <Button
              isPrimary
              isBusy={isRootSaving}
              onClick={saveRootVisibility}>
              Beállítás Mentése
            </Button>
          </div>
        </Modal>
      )}

      {isVisModalOpen && editingNode && (
        <Modal
          title={`Beállítások: ${editingNode.name}`}
          onRequestClose={() => setIsVisModalOpen(false)}
          style={{ width: "800px" }}>
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
            />
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
                  onChange={(val) => {
                    if (val) setSelectedRoles([...selectedRoles, key]);
                    else
                      setSelectedRoles(selectedRoles.filter((r) => r !== key));
                  }}
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

      {isBatchModalOpen && (
        <Modal
          title={`Tömeges beállítás (${selectedNodes.length} elem)`}
          onRequestClose={() => setIsBatchModalOpen(false)}
          style={{ width: "500px" }}>
          <p style={{ color: "#666", marginBottom: "20px" }}>
            A beállított jogosultság az összes kijelölt elemre alkalmazásra
            kerül.
          </p>
          <RadioControl
            label="Láthatóság (Jogosultság)"
            selected={batchVisType}
            options={[
              { label: "Nyilvános (Bárki láthatja)", value: "public" },
              { label: "Csak bejelentkezett felhasználók", value: "loggedin" },
              { label: "Kizárólag specifikus szerepkörök", value: "roles" },
              {
                label: "Rejtett (Lomtár / Teljes elrejtés a frontendről)",
                value: "hidden",
              },
            ]}
            onChange={(value) => setBatchVisType(value)}
          />

          {batchVisType === "roles" && (
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
                  checked={batchSelectedRoles.includes(key)}
                  onChange={(val) => {
                    if (val)
                      setBatchSelectedRoles([...batchSelectedRoles, key]);
                    else
                      setBatchSelectedRoles(
                        batchSelectedRoles.filter((r) => r !== key),
                      );
                  }}
                />
              ))}
            </div>
          )}

          {hasFolderSelected && (
            <div
              style={{
                marginTop: "20px",
                padding: "15px",
                backgroundColor: "#f0f6fc",
                borderLeft: "4px solid #72aee6",
              }}>
              <CheckboxControl
                label="Öröklődés kényszerítése: Mivel a kijelölés mappát is tartalmaz, a jogok ráerőszakolhatók minden almappára és fájlra is!"
                checked={batchApplyToChildren}
                onChange={setBatchApplyToChildren}
              />
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
              onClick={() => setIsBatchModalOpen(false)}
              style={{ marginRight: "10px" }}>
              Mégsem
            </Button>
            <Button
              isPrimary
              isBusy={isBatchSaving}
              onClick={saveBatchVisibility}>
              Tömeges Mentés
            </Button>
          </div>
        </Modal>
      )}
    </div>
  );
};

const SyncApp = () => {
  const [isSyncing, setIsSyncing] = useState(false);
  const [syncInfo, setSyncInfo] = useState(null);
  const [syncResult, setSyncResult] = useState(null);

  const fetchSyncInfo = async () => {
    try {
      const data = await apiFetch({ path: "/msdl-child/v1/sync-status" });
      setSyncInfo(data);
    } catch (e) {}
  };

  useEffect(() => {
    fetchSyncInfo();
  }, []);

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
    fetchSyncInfo(); // UI Frissítése a futás után
    setIsSyncing(false);
  };

  const handleResetSync = async () => {
    if (
      !window.confirm(
        "Biztosan törlöd a szinkronizációs gyorsítótárat? Ez a következő szinkronizációnál mindent a nulláról tölt le.",
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
      <h1>Szinkronizáció Állapota</h1>

      {/* ÚJ: Szinkronizációs Státuszkártyák */}
      <div
        style={{
          display: "grid",
          gridTemplateColumns: "repeat(auto-fit, minmax(300px, 1fr))",
          gap: "20px",
          marginBottom: "25px",
        }}>
        <div
          style={{
            background: "#fff",
            border: "1px solid #ccd0d4",
            padding: "20px",
            borderRadius: "4px",
            borderLeft: "4px solid #2271b1",
            boxShadow: "0 1px 1px rgba(0,0,0,0.04)",
          }}>
          <div
            style={{
              fontSize: "13px",
              color: "#50575e",
              textTransform: "uppercase",
              fontWeight: 600,
            }}>
            Utolsó Szinkronizáció
          </div>
          <div
            style={{
              fontSize: "18px",
              fontWeight: "bold",
              color: "#1d2327",
              marginTop: "8px",
            }}>
            {syncInfo ? syncInfo.last_sync : <Spinner />}
          </div>
        </div>

        <div
          style={{
            background: "#fff",
            border: "1px solid #ccd0d4",
            padding: "20px",
            borderRadius: "4px",
            borderLeft: "4px solid #00a32a",
            boxShadow: "0 1px 1px rgba(0,0,0,0.04)",
          }}>
          <div
            style={{
              fontSize: "13px",
              color: "#50575e",
              textTransform: "uppercase",
              fontWeight: 600,
            }}>
            Következő Ütemezett (Cron)
          </div>
          <div
            style={{
              fontSize: "18px",
              fontWeight: "bold",
              color: "#1d2327",
              marginTop: "8px",
            }}>
            {syncInfo ? syncInfo.next_sync : <Spinner />}
          </div>
          {syncInfo && (
            <div
              style={{ fontSize: "12px", color: "#8c8f94", marginTop: "6px" }}>
              <span
                className="dashicons dashicons-admin-settings"
                style={{
                  fontSize: "14px",
                  width: "14px",
                  height: "14px",
                }}></span>{" "}
              Mód: {syncInfo.mode}{" "}
              {syncInfo.interval !== "-" ? `(${syncInfo.interval})` : ""}
            </div>
          )}
        </div>
      </div>

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
