import { render, useState, useEffect } from "@wordpress/element";
import {
  PanelBody,
  TextControl,
  Button,
  Notice,
  Modal,
  TabPanel,
  Dashicon,
  Spinner,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const App = () => {
  // --- State-ek: Alap beállítások és Webhelyek ---
  const [options, setOptions] = useState({
    msdl_tenant_id: "",
    msdl_client_id: "",
    msdl_client_secret: "",
    msdl_site_id: "",
    msdl_drive_id: "",
    msdl_internal_api_key: "",
  });
  const [sites, setSites] = useState([]);
  const [statusText, setStatusText] = useState("");

  // --- State-ek: Fő Szerkesztő Modal ---
  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingSite, setEditingSite] = useState(null);
  const [showAdvanced, setShowAdvanced] = useState(false);

  // ÚJ: Ez dönti el, hogy a Modalon belül a Szerkesztőt vagy a Tallózót mutatjuk
  const [isFolderBrowserActive, setIsFolderBrowserActive] = useState(false);

  // --- State-ek: SharePoint Azonosító Kereső Modal (Ez maradhat külön, mert nem a szerkesztőből nyílik) ---
  const [isFinderOpen, setIsFinderOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [isSearching, setIsSearching] = useState(false);
  const [foundSites, setFoundSites] = useState([]);
  const [selectedFoundSite, setSelectedFoundSite] = useState(null);
  const [foundDrives, setFoundDrives] = useState([]);
  const [isLoadingDrives, setIsLoadingDrives] = useState(false);

  // --- State-ek: MAPPA Kereső adatok ---
  const [folderSearchQuery, setFolderSearchQuery] = useState("");
  const [isSearchingFolders, setIsSearchingFolders] = useState(false);
  const [foundFolders, setFoundFolders] = useState([]);

  // --- Adatbetöltés ---
  useEffect(() => {
    loadSettings();
    loadSites();
  }, []);
  const loadSettings = () => {
    apiFetch({ path: "/wp/v2/settings" })
      .then((settings) => {
        setOptions({
          msdl_tenant_id: settings.msdl_tenant_id || "",
          msdl_client_id: settings.msdl_client_id || "",
          msdl_client_secret: settings.msdl_client_secret || "",
          msdl_site_id: settings.msdl_site_id || "",
          msdl_drive_id: settings.msdl_drive_id || "",
          msdl_internal_api_key: settings.msdl_internal_api_key || "",
        });
      })
      .catch(console.error);
  };
  const loadSites = () => {
    apiFetch({ path: "/msdl-main/v1/sites" })
      .then(setSites)
      .catch(console.error);
  };

  // --- Műveletek: Beállítások és Webhelyek ---
  const handleSaveSettings = async () => {
    setStatusText("Mentés...");
    try {
      await apiFetch({
        path: "/wp/v2/settings",
        method: "POST",
        data: options,
      });
      setStatusText("Beállítások sikeresen elmentve!");
      setTimeout(() => setStatusText(""), 3000);
    } catch (e) {
      setStatusText("Hiba a mentéskor!");
    }
  };
  const openModal = (
    site = {
      domain: "",
      folder_path: "",
      custom_site_id: "",
      custom_drive_id: "",
    },
  ) => {
    setEditingSite(site);
    setShowAdvanced(!!(site.custom_site_id || site.custom_drive_id));
    setIsFolderBrowserActive(false); // Biztosítjuk, hogy mindig a szerkesztő nézetben nyíljon meg!
    setIsModalOpen(true);
  };
  const handleSaveSite = async (e) => {
    e.preventDefault();
    try {
      await apiFetch({
        path: "/msdl-main/v1/sites",
        method: "POST",
        data: editingSite,
      });
      setIsModalOpen(false);
      setEditingSite(null);
      loadSites();
    } catch (e) {
      alert("Hiba a webhely mentésekor!");
    }
  };
  const handleDeleteSite = async (id) => {
    if (
      !confirm(
        "Biztosan törlöd ezt a webhelyet? A Child plugin szinkronizációja azonnal leáll az adott oldalon!",
      )
    )
      return;
    try {
      await apiFetch({ path: `/msdl-main/v1/sites/${id}`, method: "DELETE" });
      loadSites();
    } catch (e) {
      alert("Hiba a törléskor!");
    }
  };

  // --- Műveletek: SharePoint Azonosító Kereső ---
  const handleSearchSites = async (e) => {
    e.preventDefault();
    setIsSearching(true);
    setFoundSites([]);
    setSelectedFoundSite(null);
    setFoundDrives([]);
    try {
      const results = await apiFetch({
        path: `/msdl-main/v1/search-sites?q=${encodeURIComponent(searchQuery)}`,
      });
      setFoundSites(results);
    } catch (err) {
      alert("Hiba a keresés során. Ellenőrizd a kulcsokat!");
    }
    setIsSearching(false);
  };
  const handleSelectSite = async (site) => {
    setSelectedFoundSite(site);
    setIsLoadingDrives(true);
    try {
      const drives = await apiFetch({
        path: `/msdl-main/v1/get-drives?site_id=${site.id}`,
      });
      setFoundDrives(drives);
    } catch (err) {
      alert("Nem sikerült lekérni a dokumentumtárakat.");
    }
    setIsLoadingDrives(false);
  };
  const handleApplyIds = (siteId, driveId) => {
    setOptions({ ...options, msdl_site_id: siteId, msdl_drive_id: driveId });
    setIsFinderOpen(false);
    setStatusText(
      "Azonosítók bemásolva! Ne felejtsd el elmenteni a beállításokat.",
    );
  };

  // --- Műveletek: MAPPA Kereső ---
  const openFolderFinder = () => {
    const driveId = editingSite?.custom_drive_id || options.msdl_drive_id;
    if (!driveId) {
      alert(
        "Nincs beállítva Dokumentumtár (Drive ID)! Előbb add meg a központi beállításokban, vagy mentsd el a webhely haladó beállításainál az egyedit.",
      );
      return;
    }
    setFolderSearchQuery("");
    setFoundFolders([]);

    // VÁLTUNK A MODALON BELÜLI NÉZETRE
    setIsFolderBrowserActive(true);
    handleSearchFolders(null, driveId, "");
  };

  const handleSearchFolders = async (
    e,
    driveIdOverride = null,
    queryOverride = null,
  ) => {
    if (e) e.preventDefault();
    setIsSearchingFolders(true);
    setFoundFolders([]);

    const driveId =
      driveIdOverride || editingSite?.custom_drive_id || options.msdl_drive_id;
    const q = queryOverride !== null ? queryOverride : folderSearchQuery;

    try {
      const results = await apiFetch({
        path: `/msdl-main/v1/search-folders?drive_id=${driveId}&q=${encodeURIComponent(
          q,
        )}`,
      });
      setFoundFolders(results);
    } catch (err) {
      alert(
        "Hiba a mappák lekérdezésekor. Ellenőrizd, hogy az adott Drive ID helyes-e.",
      );
    }
    setIsSearchingFolders(false);
  };

  const handleApplyFolder = (folder) => {
    let relativePath = "";
    if (folder.parentReference && folder.parentReference.path) {
      const prefix = "/drive/root:";
      if (folder.parentReference.path.startsWith(prefix)) {
        relativePath = folder.parentReference.path.substring(prefix.length);
        if (relativePath.startsWith("/"))
          relativePath = relativePath.substring(1);
      }
    }
    const fullPath = relativePath
      ? `${relativePath}/${folder.name}`
      : folder.name;

    // Frissítjük a form adatot ÉS visszaváltunk a szerkesztő nézetre!
    setEditingSite({ ...editingSite, folder_path: fullPath });
    setIsFolderBrowserActive(false);
  };

  const tabs = [
    { name: "sites", title: "Kliens Webhelyek", className: "msdl-tab-sites" },
    {
      name: "settings",
      title: "Központi API Beállítások",
      className: "msdl-tab-settings",
    },
  ];

  return (
    <div className="wrap msdl-admin-wrapper">
      <h1 style={{ marginBottom: "20px" }}>
        MSDL Központ - Architektúra Menedzser
      </h1>
      {statusText && (
        <Notice
          status="success"
          isDismissible={false}
          onRemove={() => setStatusText("")}>
          {statusText}
        </Notice>
      )}

      <TabPanel className="msdl-tabs" activeClass="is-active" tabs={tabs}>
        {(tab) => {
          // BEÁLLÍTÁSOK FÜL
          if (tab.name === "settings") {
            return (
              <div style={{ marginTop: "20px", maxWidth: "800px" }}>
                <PanelBody title="Microsoft Graph Hitelesítés és Rendszerkulcsok">
                  <p style={{ color: "#666", marginBottom: "15px" }}>
                    Ezek az azonosítók adják a központi hozzáférést a Microsoft
                    tenant-hoz.
                  </p>
                  <TextControl
                    label="Tenant ID"
                    value={options.msdl_tenant_id}
                    onChange={(v) =>
                      setOptions({ ...options, msdl_tenant_id: v })
                    }
                  />
                  <TextControl
                    label="Client ID"
                    value={options.msdl_client_id}
                    onChange={(v) =>
                      setOptions({ ...options, msdl_client_id: v })
                    }
                  />
                  <TextControl
                    label="Client Secret"
                    type="password"
                    value={options.msdl_client_secret}
                    onChange={(v) =>
                      setOptions({ ...options, msdl_client_secret: v })
                    }
                  />
                  <hr style={{ margin: "20px 0" }} />
                  <div
                    style={{
                      display: "flex",
                      justifyContent: "space-between",
                      alignItems: "center",
                      marginBottom: "10px",
                    }}>
                    <h3 style={{ margin: 0 }}>Központi Tároló Azonosítói</h3>
                    <Button
                      isSecondary
                      icon="search"
                      onClick={() => setIsFinderOpen(true)}>
                      Azonosítók Keresése
                    </Button>
                  </div>
                  <TextControl
                    label="Központi Site ID"
                    value={options.msdl_site_id}
                    onChange={(v) =>
                      setOptions({ ...options, msdl_site_id: v })
                    }
                  />
                  <TextControl
                    label="Központi Drive ID"
                    value={options.msdl_drive_id}
                    onChange={(v) =>
                      setOptions({ ...options, msdl_drive_id: v })
                    }
                  />
                  <hr style={{ margin: "20px 0" }} />
                  <h3>Belső Kommunikációs Kulcs</h3>
                  <TextControl
                    label="Belső API Kulcs (Child hitelesítéshez)"
                    type="password"
                    value={options.msdl_internal_api_key}
                    onChange={(v) =>
                      setOptions({ ...options, msdl_internal_api_key: v })
                    }
                  />
                  <Button isPrimary onClick={handleSaveSettings}>
                    Beállítások Mentése
                  </Button>
                </PanelBody>
              </div>
            );
          }

          // KLIENS WEBHELYEK FÜL
          if (tab.name === "sites") {
            return (
              <div style={{ marginTop: "20px" }}>
                <div
                  style={{
                    display: "flex",
                    justifyContent: "space-between",
                    alignItems: "center",
                    marginBottom: "15px",
                  }}>
                  <p style={{ margin: 0 }}>
                    Az itt listázott webhelyek (Child pluginok) kaptak engedélyt
                    a kapcsolódásra.
                  </p>
                  <Button isPrimary onClick={() => openModal()}>
                    + Új Webhely Hozzáadása
                  </Button>
                </div>
                <table className="wp-list-table widefat fixed striped table-view-list">
                  <thead>
                    <tr>
                      <th style={{ width: "40px", textAlign: "center" }}>
                        Státusz
                      </th>
                      <th>Domain</th>
                      <th>Hozzárendelt Mappa</th>
                      <th>Egyedi Tároló</th>
                      <th style={{ width: "200px", textAlign: "right" }}>
                        Műveletek
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {sites.length === 0 ? (
                      <tr>
                        <td
                          colSpan="5"
                          style={{ textAlign: "center", padding: "20px" }}>
                          Nincs webhely.
                        </td>
                      </tr>
                    ) : (
                      sites.map((site) => {
                        const isPending = !site.folder_path;
                        return (
                          <tr
                            key={site.id}
                            style={{
                              backgroundColor: isPending
                                ? "#fcf0f1"
                                : "transparent",
                            }}>
                            <td
                              style={{
                                textAlign: "center",
                                verticalAlign: "middle",
                              }}>
                              {isPending ? (
                                <Dashicon
                                  icon="warning"
                                  style={{ color: "#d63638" }}
                                />
                              ) : (
                                <Dashicon
                                  icon="yes-alt"
                                  style={{ color: "#00a32a" }}
                                />
                              )}
                            </td>
                            <td style={{ verticalAlign: "middle" }}>
                              <strong>{site.domain}</strong>
                            </td>
                            <td style={{ verticalAlign: "middle" }}>
                              {isPending ? (
                                <span
                                  style={{
                                    color: "#d63638",
                                    fontWeight: "bold",
                                  }}>
                                  Jóváhagyásra vár
                                </span>
                              ) : (
                                <code>/{site.folder_path}</code>
                              )}
                            </td>
                            <td style={{ verticalAlign: "middle" }}>
                              {site.custom_site_id ? (
                                <span style={{ color: "#2271b1" }}>
                                  ✓ Aktív
                                </span>
                              ) : (
                                <span style={{ color: "#a0a5aa" }}>
                                  Központi
                                </span>
                              )}
                            </td>
                            <td
                              style={{
                                textAlign: "right",
                                verticalAlign: "middle",
                              }}>
                              <Button
                                isSmall
                                isSecondary
                                onClick={() => openModal(site)}
                                style={{ marginRight: "8px" }}>
                                {isPending
                                  ? "Jóváhagyás & Beállítás"
                                  : "Szerkesztés"}
                              </Button>
                              <Button
                                isSmall
                                isDestructive
                                onClick={() => handleDeleteSite(site.id)}>
                                Törlés
                              </Button>
                            </td>
                          </tr>
                        );
                      })
                    )}
                  </tbody>
                </table>
              </div>
            );
          }
        }}
      </TabPanel>

      {/* --- AZONOSÍTÓ KERESŐ MODAL --- */}
      {isFinderOpen && (
        <Modal
          title="SharePoint Azonosító Kereső"
          onRequestClose={() => setIsFinderOpen(false)}
          style={{ width: "700px" }}>
          <form
            onSubmit={handleSearchSites}
            style={{ display: "flex", gap: "10px", marginBottom: "20px" }}>
            <div style={{ flexGrow: 1 }}>
              <TextControl
                placeholder="Keresés webhely nevére..."
                value={searchQuery}
                onChange={setSearchQuery}
              />
            </div>
            <Button isPrimary type="submit" isBusy={isSearching}>
              Keresés
            </Button>
          </form>
          {foundSites.length > 0 && (
            <div
              style={{
                border: "1px solid #ccc",
                borderRadius: "4px",
                maxHeight: "200px",
                overflowY: "auto",
                marginBottom: "20px",
              }}>
              <ul style={{ margin: 0, padding: 0, listStyle: "none" }}>
                {foundSites.map((site) => (
                  <li
                    key={site.id}
                    style={{
                      padding: "10px",
                      borderBottom: "1px solid #eee",
                      cursor: "pointer",
                      backgroundColor:
                        selectedFoundSite?.id === site.id ? "#f0f6fc" : "#fff",
                    }}
                    onClick={() => handleSelectSite(site)}>
                    <strong>{site.name}</strong>{" "}
                    <span style={{ color: "#888", fontSize: "12px" }}>
                      ({site.webUrl})
                    </span>
                  </li>
                ))}
              </ul>
            </div>
          )}
          {isLoadingDrives && (
            <div style={{ textAlign: "center", padding: "20px" }}>
              <Spinner /> Dokumentumtárak betöltése...
            </div>
          )}
          {selectedFoundSite && foundDrives.length > 0 && !isLoadingDrives && (
            <div>
              <h4 style={{ marginTop: 0 }}>
                Válassz egy Dokumentumtárat (Drive):
              </h4>
              <div
                style={{
                  border: "1px solid #ccc",
                  borderRadius: "4px",
                  maxHeight: "200px",
                  overflowY: "auto",
                }}>
                <ul style={{ margin: 0, padding: 0, listStyle: "none" }}>
                  {foundDrives.map((drive) => (
                    <li
                      key={drive.id}
                      style={{
                        padding: "10px",
                        borderBottom: "1px solid #eee",
                        display: "flex",
                        justifyContent: "space-between",
                        alignItems: "center",
                      }}>
                      <div>
                        <strong>{drive.name}</strong>
                      </div>
                      <Button
                        isSecondary
                        type="button"
                        isSmall
                        onClick={() =>
                          handleApplyIds(selectedFoundSite.id, drive.id)
                        }>
                        Kiválasztás
                      </Button>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          )}
        </Modal>
      )}

      {/* --- WEBHELY SZERKESZTŐ / MAPPA TALLÓZÓ (EGYETLEN MODAL, KÉT NÉZET!) --- */}
      {isModalOpen && (
        <Modal
          title={
            isFolderBrowserActive
              ? "Mappa Tallózó"
              : editingSite?.id
              ? `Szerkesztés: ${editingSite.domain}`
              : "Új Webhely Felvitele"
          }
          onRequestClose={() => {
            // Ha a tallózóban vagyunk, a bezárás gomb (X) csak visszadob a szerkesztőbe
            if (isFolderBrowserActive) {
              setIsFolderBrowserActive(false);
            } else {
              // Egyébként bezárja az egész ablakot
              setIsModalOpen(false);
            }
          }}
          style={{ width: "600px" }}>
          {isFolderBrowserActive ? (
            // NÉZET 1: MAPPA TALLÓZÓ
            <div>
              <Button
                isLink
                icon="arrow-left-alt2"
                type="button"
                onClick={() => setIsFolderBrowserActive(false)}
                style={{ marginBottom: "15px" }}>
                Vissza a webhely szerkesztéséhez
              </Button>

              <form
                onSubmit={(e) => handleSearchFolders(e)}
                style={{ display: "flex", gap: "10px", marginBottom: "20px" }}>
                <div style={{ flexGrow: 1 }}>
                  <TextControl
                    placeholder="Keresés mappák között (üresen hagyva a gyökeret listázza)..."
                    value={folderSearchQuery}
                    onChange={setFolderSearchQuery}
                  />
                </div>
                <Button isPrimary type="submit" isBusy={isSearchingFolders}>
                  Keresés
                </Button>
              </form>

              {isSearchingFolders ? (
                <div style={{ textAlign: "center", padding: "20px" }}>
                  <Spinner /> Mappák betöltése...
                </div>
              ) : (
                <div
                  style={{
                    border: "1px solid #ccc",
                    borderRadius: "4px",
                    maxHeight: "300px",
                    overflowY: "auto",
                  }}>
                  {foundFolders.length === 0 ? (
                    <p
                      style={{
                        padding: "15px",
                        margin: 0,
                        textAlign: "center",
                        color: "#666",
                      }}>
                      Nem találtam mappát.
                    </p>
                  ) : (
                    <ul style={{ margin: 0, padding: 0, listStyle: "none" }}>
                      {foundFolders.map((folder) => {
                        let relativePath = "";
                        if (
                          folder.parentReference &&
                          folder.parentReference.path
                        ) {
                          const prefix = "/drive/root:";
                          if (folder.parentReference.path.startsWith(prefix)) {
                            relativePath =
                              folder.parentReference.path.substring(
                                prefix.length,
                              );
                            if (relativePath.startsWith("/"))
                              relativePath = relativePath.substring(1);
                          }
                        }
                        const displayPath = relativePath
                          ? `/${relativePath}/${folder.name}`
                          : `/${folder.name}`;

                        return (
                          <li
                            key={folder.id}
                            style={{
                              padding: "10px",
                              borderBottom: "1px solid #eee",
                              display: "flex",
                              justifyContent: "space-between",
                              alignItems: "center",
                            }}>
                            <div>
                              <strong>
                                <Dashicon
                                  icon="category"
                                  style={{
                                    verticalAlign: "text-bottom",
                                    marginRight: "5px",
                                    color: "#f5c342",
                                  }}
                                />
                                {folder.name}
                              </strong>
                              <div
                                style={{
                                  color: "#888",
                                  fontSize: "11px",
                                  marginTop: "3px",
                                }}>
                                Útvonal: {displayPath}
                              </div>
                            </div>
                            {/* type="button" extra védelem a form küldés ellen */}
                            <Button
                              isSecondary
                              type="button"
                              isSmall
                              onClick={() => handleApplyFolder(folder)}>
                              Kiválasztás
                            </Button>
                          </li>
                        );
                      })}
                    </ul>
                  )}
                </div>
              )}
            </div>
          ) : (
            // NÉZET 2: WEBHELY SZERKESZTŐ FORM
            <form onSubmit={handleSaveSite}>
              <TextControl
                label="Kliens Domain"
                help="A csatlakozó weboldal címe (pl. gepesz.sze.hu)."
                value={editingSite.domain}
                onChange={(val) =>
                  setEditingSite({ ...editingSite, domain: val })
                }
                required
              />

              <div
                style={{
                  display: "flex",
                  alignItems: "flex-end",
                  gap: "10px",
                }}>
                <div style={{ flexGrow: 1 }}>
                  <TextControl
                    label="Gyökér Mappa Útvonala"
                    help="A kiválasztott mappa a SharePointból."
                    value={editingSite.folder_path}
                    onChange={(val) =>
                      setEditingSite({ ...editingSite, folder_path: val })
                    }
                    required
                  />
                </div>
                <div style={{ marginBottom: "24px" }}>
                  <Button
                    isSecondary
                    type="button"
                    icon="search"
                    onClick={openFolderFinder}>
                    Tallózás
                  </Button>
                </div>
              </div>

              <div style={{ marginTop: "20px" }}>
                <Button
                  isLink
                  type="button"
                  onClick={() => setShowAdvanced(!showAdvanced)}>
                  {showAdvanced
                    ? "▼ Egyedi SharePoint Tároló elrejtése"
                    : "► Egyedi SharePoint Tároló megadása (Haladó)"}
                </Button>
                {showAdvanced && (
                  <div
                    style={{
                      marginTop: "15px",
                      padding: "15px",
                      backgroundColor: "#f6f7f7",
                      borderLeft: "4px solid #72aee6",
                    }}>
                    <TextControl
                      label="Egyedi Site ID"
                      value={editingSite.custom_site_id}
                      onChange={(val) =>
                        setEditingSite({ ...editingSite, custom_site_id: val })
                      }
                    />
                    <TextControl
                      label="Egyedi Drive ID"
                      value={editingSite.custom_drive_id}
                      onChange={(val) =>
                        setEditingSite({ ...editingSite, custom_drive_id: val })
                      }
                    />
                  </div>
                )}
              </div>

              <div
                style={{
                  display: "flex",
                  justifyContent: "flex-end",
                  marginTop: "20px",
                }}>
                <Button
                  isSecondary
                  type="button"
                  onClick={() => setIsModalOpen(false)}
                  style={{ marginRight: "10px" }}>
                  Mégsem
                </Button>
                <Button isPrimary type="submit">
                  Mentés és Jóváhagyás
                </Button>
              </div>
            </form>
          )}
        </Modal>
      )}
    </div>
  );
};

document.addEventListener("DOMContentLoaded", () => {
  const root = document.getElementById("msdl-main-app");
  if (root) render(<App />, root);
});
