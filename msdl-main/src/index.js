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
  ToggleControl,
  CheckboxControl,
} from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const App = () => {
  // --- State-ek ---
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

  const [siteSearchFilter, setSiteSearchFilter] = useState("");
  const [selectedSites, setSelectedSites] = useState([]);
  const [bulkAction, setBulkAction] = useState("");
  const [isBulkProcessing, setIsBulkProcessing] = useState(false);

  const [isModalOpen, setIsModalOpen] = useState(false);
  const [editingSite, setEditingSite] = useState(null);
  const [showAdvanced, setShowAdvanced] = useState(false);
  const [isFolderBrowserActive, setIsFolderBrowserActive] = useState(false);

  const [isFinderOpen, setIsFinderOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState("");
  const [isSearching, setIsSearching] = useState(false);
  const [foundSites, setFoundSites] = useState([]);
  const [selectedFoundSite, setSelectedFoundSite] = useState(null);
  const [foundDrives, setFoundDrives] = useState([]);
  const [isLoadingDrives, setIsLoadingDrives] = useState(false);

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

  // --- Műveletek ---
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
      is_active: 1,
    },
  ) => {
    setEditingSite(site);
    setShowAdvanced(!!(site.custom_site_id || site.custom_drive_id));
    setIsFolderBrowserActive(false);
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
    if (!confirm("Biztosan törlöd ezt a webhelyet?")) return;
    try {
      await apiFetch({ path: `/msdl-main/v1/sites/${id}`, method: "DELETE" });
      loadSites();
      setSelectedSites(selectedSites.filter((sId) => sId !== id));
    } catch (e) {
      alert("Hiba a törléskor!");
    }
  };

  const handleRemoteCommand = async (site, command) => {
    const actionName = command === "ping" ? "Pingelés" : "Szinkronizáció";
    setStatusText(`${site.domain}: ${actionName} folyamatban...`);
    try {
      const response = await apiFetch({
        path: "/msdl-main/v1/remote-command",
        method: "POST",
        data: { domain: site.domain, command: command },
      });
      if (response && response.success) {
        setStatusText(`${site.domain}: Sikeres! ${response.message || ""}`);
        if (command === "sync") loadSites();
      } else {
        setStatusText(
          `${site.domain}: Hiba! ${
            response?.message || "Nem érkezett válasz."
          }`,
        );
      }
    } catch (e) {
      setStatusText(`${site.domain}: Kapcsolódási hiba!`);
    }
  };

  // ÚJ: Dinamikus URL nyitó
  const handleOpenSharePoint = async (type, siteId = null) => {
    setStatusText("SharePoint URL lekérése a Microsofttól...");
    try {
      const query =
        type === "central" ? "?type=central" : `?type=folder&site_id=${siteId}`;
      const response = await apiFetch({
        path: `/msdl-main/v1/get-sp-url${query}`,
      });
      if (response && response.url) {
        setStatusText(""); // Sáv törlése
        window.open(response.url, "_blank");
      } else {
        setStatusText("Hiba: Nem kaptam vissza URL-t.");
      }
    } catch (err) {
      setStatusText(
        `Hiba az URL lekérésekor: ${err.message || "Ismeretlen hiba"}`,
      );
    }
  };

  // --- Tömeges műveletek ---
  const filteredSites = sites.filter((s) =>
    s.domain.toLowerCase().includes(siteSearchFilter.toLowerCase()),
  );
  const handleSelectAll = (isChecked) => {
    if (isChecked) setSelectedSites(filteredSites.map((s) => s.id));
    else setSelectedSites([]);
  };
  const handleSelectSite = (siteId) => {
    if (selectedSites.includes(siteId))
      setSelectedSites(selectedSites.filter((id) => id !== siteId));
    else setSelectedSites([...selectedSites, siteId]);
  };
  const handleBulkAction = async () => {
    if (!bulkAction || selectedSites.length === 0) return;
    setIsBulkProcessing(true);
    setStatusText("Tömeges művelet végrehajtása folyamatban...");
    const sitesToProcess = sites.filter((s) => selectedSites.includes(s.id));
    for (const site of sitesToProcess) {
      if (bulkAction === "ping" || bulkAction === "sync") {
        if (bulkAction === "sync" && (!site.folder_path || site.is_active == 0))
          continue;
        await handleRemoteCommand(site, bulkAction);
      } else if (bulkAction === "suspend") {
        if (site.is_active == 1)
          await apiFetch({
            path: "/msdl-main/v1/sites",
            method: "POST",
            data: { ...site, is_active: 0 },
          });
      } else if (bulkAction === "activate") {
        if (site.is_active == 0)
          await apiFetch({
            path: "/msdl-main/v1/sites",
            method: "POST",
            data: { ...site, is_active: 1 },
          });
      }
    }
    setStatusText("Tömeges művelet befejezve!");
    setSelectedSites([]);
    setIsBulkProcessing(false);
    setBulkAction("");
    loadSites();
  };

  const handleToggleActive = async (site) => {
    const updatedSite = { ...site, is_active: site.is_active == 1 ? 0 : 1 };
    try {
      await apiFetch({
        path: "/msdl-main/v1/sites",
        method: "POST",
        data: updatedSite,
      });
      loadSites();
    } catch (e) {
      alert("Hiba az állapot mentésekor!");
    }
  };

  // --- Graph Kereső Metódusok ---
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
      alert("Hiba a keresés során.");
    }
    setIsSearching(false);
  };
  const handleSelectSiteAPI = async (site) => {
    setSelectedFoundSite(site);
    setIsLoadingDrives(true);
    try {
      const drives = await apiFetch({
        path: `/msdl-main/v1/get-drives?site_id=${site.id}`,
      });
      setFoundDrives(drives);
    } catch (err) {
      alert("Hiba a lekérésnél.");
    }
    setIsLoadingDrives(false);
  };
  const handleApplyIds = (siteId, driveId) => {
    setOptions({ ...options, msdl_site_id: siteId, msdl_drive_id: driveId });
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
      alert("Hiba a mappák lekérdezésekor.");
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
    setEditingSite({
      ...editingSite,
      folder_path: relativePath
        ? `${relativePath}/${folder.name}`
        : folder.name,
    });
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
          status="info"
          isDismissible={false}
          onRemove={() => setStatusText("")}>
          {statusText}
        </Notice>
      )}

      <TabPanel className="msdl-tabs" activeClass="is-active" tabs={tabs}>
        {(tab) => {
          if (tab.name === "settings") {
            return (
              <div style={{ marginTop: "20px", maxWidth: "800px" }}>
                <PanelBody title="Microsoft Graph Hitelesítés és Rendszerkulcsok">
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
                    label="Belső API Kulcs"
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
                  <div
                    style={{
                      display: "flex",
                      gap: "15px",
                      alignItems: "center",
                    }}>
                    <div
                      style={{
                        display: "flex",
                        gap: "5px",
                        alignItems: "center",
                      }}>
                      <select
                        value={bulkAction}
                        onChange={(e) => setBulkAction(e.target.value)}
                        style={{
                          padding: "0 8px",
                          lineHeight: "2.2",
                          height: "32px",
                        }}>
                        <option value="">Tömeges műveletek</option>
                        <option value="ping">Állapot Ping (Ellenőrzés)</option>
                        <option value="sync">Azonnali Szinkronizálás</option>
                        <option value="suspend">
                          Felfüggesztés (Karbantartás)
                        </option>
                        <option value="activate">Aktiválás</option>
                      </select>
                      <Button
                        isSecondary
                        isBusy={isBulkProcessing}
                        onClick={handleBulkAction}
                        disabled={!bulkAction || selectedSites.length === 0}>
                        Alkalmaz ({selectedSites.length})
                      </Button>
                    </div>
                    <TextControl
                      placeholder="Keresés domain szerint..."
                      value={siteSearchFilter}
                      onChange={setSiteSearchFilter}
                      style={{ margin: 0, width: "250px" }}
                    />
                  </div>

                  <div style={{ display: "flex", gap: "10px" }}>
                    {/* JAVÍTÁS: Központi SharePoint Gomb most már dinamikus */}
                    <Button
                      isSecondary
                      icon="external"
                      onClick={() => handleOpenSharePoint("central")}>
                      Központi SharePoint
                    </Button>
                    <Button isPrimary onClick={() => openModal()}>
                      + Új Webhely
                    </Button>
                  </div>
                </div>

                <table className="wp-list-table widefat fixed striped table-view-list">
                  <thead>
                    <tr>
                      <th style={{ width: "40px", textAlign: "center" }}>
                        <input
                          type="checkbox"
                          checked={
                            selectedSites.length === filteredSites.length &&
                            filteredSites.length > 0
                          }
                          onChange={(e) => handleSelectAll(e.target.checked)}
                        />
                      </th>
                      <th style={{ width: "70px", textAlign: "center" }}>
                        Státusz
                      </th>
                      <th>Domain</th>
                      <th>Mappa / Tároló</th>
                      <th style={{ width: "180px" }}>Utolsó Szinkronizáció</th>
                      <th style={{ width: "120px" }}>Karbantartás</th>
                      <th style={{ width: "260px", textAlign: "right" }}>
                        Műveletek
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {filteredSites.length === 0 ? (
                      <tr>
                        <td
                          colSpan="7"
                          style={{ textAlign: "center", padding: "20px" }}>
                          Nincs a keresésnek megfelelő webhely.
                        </td>
                      </tr>
                    ) : (
                      filteredSites.map((site) => {
                        const isPending = !site.folder_path;
                        const isSuspended = site.is_active == 0;
                        const siteUrl = site.domain.startsWith("http")
                          ? site.domain
                          : `https://${site.domain}`;

                        return (
                          <tr
                            key={site.id}
                            style={{
                              backgroundColor: isSuspended
                                ? "#f0f0f0"
                                : isPending
                                ? "#fcf0f1"
                                : "transparent",
                              opacity: isSuspended ? 0.7 : 1,
                            }}>
                            <td
                              style={{
                                textAlign: "center",
                                verticalAlign: "middle",
                              }}>
                              <input
                                type="checkbox"
                                checked={selectedSites.includes(site.id)}
                                onChange={() => handleSelectSite(site.id)}
                              />
                            </td>
                            <td
                              style={{
                                textAlign: "center",
                                verticalAlign: "middle",
                              }}>
                              {isSuspended ? (
                                <Dashicon
                                  icon="hidden"
                                  style={{ color: "#666" }}
                                  title="Felfüggesztve"
                                />
                              ) : isPending ? (
                                <Dashicon
                                  icon="warning"
                                  style={{ color: "#d63638" }}
                                  title="Függőben"
                                />
                              ) : (
                                <Dashicon
                                  icon="yes-alt"
                                  style={{ color: "#00a32a" }}
                                  title="Aktív"
                                />
                              )}
                            </td>
                            <td style={{ verticalAlign: "middle" }}>
                              <a
                                href={siteUrl}
                                target="_blank"
                                rel="noreferrer"
                                style={{
                                  fontWeight: "bold",
                                  textDecoration: isSuspended
                                    ? "line-through"
                                    : "none",
                                  fontSize: "14px",
                                }}>
                                {site.domain}{" "}
                                <Dashicon
                                  icon="external"
                                  style={{
                                    fontSize: "12px",
                                    width: "12px",
                                    height: "12px",
                                    color: "#888",
                                  }}
                                />
                              </a>
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
                                <div>
                                  <div
                                    style={{
                                      display: "flex",
                                      alignItems: "center",
                                      gap: "8px",
                                    }}>
                                    <code>/{site.folder_path}</code>
                                    {/* JAVÍTÁS: Mappa gomb most már dinamikus */}
                                    <Button
                                      isSmall
                                      isSecondary
                                      icon="admin-links"
                                      title="Mappa megnyitása a SharePointban"
                                      onClick={() =>
                                        handleOpenSharePoint("folder", site.id)
                                      }
                                    />
                                  </div>
                                  {site.custom_site_id && (
                                    <div
                                      style={{
                                        fontSize: "11px",
                                        color: "#2271b1",
                                        marginTop: "4px",
                                      }}>
                                      ✓ Egyedi tároló felülbírálás
                                    </div>
                                  )}
                                </div>
                              )}
                            </td>
                            <td style={{ verticalAlign: "middle" }}>
                              <div
                                style={{
                                  display: "flex",
                                  alignItems: "center",
                                  gap: "8px",
                                }}>
                                <span
                                  style={{
                                    color: site.last_sync ? "#1d2327" : "#888",
                                  }}>
                                  {site.last_sync
                                    ? site.last_sync
                                    : "Még nem szinkronizált"}
                                </span>
                                {!isPending && !isSuspended && (
                                  <Button
                                    isSmall
                                    isSecondary
                                    icon="update"
                                    title="Szinkronizáció Indítása"
                                    onClick={() =>
                                      handleRemoteCommand(site, "sync")
                                    }
                                  />
                                )}
                              </div>
                            </td>
                            <td style={{ verticalAlign: "middle" }}>
                              <ToggleControl
                                label={isSuspended ? "Szünetel" : "Aktív"}
                                checked={!isSuspended}
                                onChange={() => handleToggleActive(site)}
                                style={{ margin: 0 }}
                              />
                            </td>
                            <td style={{ verticalAlign: "middle" }}>
                              <div
                                style={{
                                  display: "flex",
                                  justifyContent: "flex-end",
                                  gap: "5px",
                                  flexWrap: "wrap",
                                }}>
                                <Button
                                  isSmall
                                  isSecondary
                                  icon="admin-network"
                                  title="Állapot Ping"
                                  onClick={() =>
                                    handleRemoteCommand(site, "ping")
                                  }
                                />
                                <Button
                                  isSmall
                                  isSecondary
                                  icon="admin-users"
                                  href={`${siteUrl}/wp-admin`}
                                  target="_blank"
                                  title="WP Admin"
                                />
                                <Button
                                  isSmall
                                  isSecondary
                                  onClick={() => openModal(site)}>
                                  {isPending ? "Jóváhagyás" : "Szerk."}
                                </Button>
                                <Button
                                  isSmall
                                  isDestructive
                                  onClick={() => handleDeleteSite(site.id)}>
                                  Törlés
                                </Button>
                              </div>
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

      {/* --- Modálok --- */}
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
                    onClick={() => handleSelectSiteAPI(site)}>
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
              <h4 style={{ marginTop: 0 }}>Válassz egy Dokumentumtárat:</h4>
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
            if (isFolderBrowserActive) setIsFolderBrowserActive(false);
            else setIsModalOpen(false);
          }}
          style={{ width: "600px" }}>
          {isFolderBrowserActive ? (
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
                    placeholder="Keresés mappák között..."
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
            <form onSubmit={handleSaveSite}>
              <TextControl
                label="Kliens Domain"
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
                  marginTop: "20px",
                  padding: "10px",
                  border: "1px solid #ddd",
                  borderRadius: "4px",
                }}>
                <ToggleControl
                  label="Webhely engedélyezése (Aktív kapcsolat)"
                  checked={editingSite.is_active == 1}
                  onChange={(val) =>
                    setEditingSite({ ...editingSite, is_active: val ? 1 : 0 })
                  }
                  style={{ margin: 0 }}
                />
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
