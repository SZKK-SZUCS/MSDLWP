import { render, useState, useEffect } from "@wordpress/element";
import { PanelBody, TextControl, Button, Notice } from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const FileManagerApp = () => (
  <div className="wrap">
    <h1>Fájlkezelő</h1>
    <p>Itt lesz a lokális adatbázis fa-struktúrás böngészője.</p>
  </div>
);
const SyncApp = () => (
  <div className="wrap">
    <h1>Szinkronizáció</h1>
    <p>A szinkronizációs logok és cron beállítások helye.</p>
  </div>
);

const SettingsApp = () => {
  const [options, setOptions] = useState({
    msdl_main_server_url: "",
    msdl_internal_api_key: "",
  });
  const [isSaving, setIsSaving] = useState(false);
  const [statusText, setStatusText] = useState("");

  useEffect(() => {
    apiFetch({ path: "/wp/v2/settings" }).then((settings) => {
      setOptions({
        msdl_main_server_url: settings.msdl_main_server_url || "",
        msdl_internal_api_key: settings.msdl_internal_api_key || "",
      });
    });
  }, []);

  const handleSave = async () => {
    setIsSaving(true);
    setStatusText("Mentés és kapcsolódás a központhoz...");
    try {
      // 1. Lementjük a beállításokat a WP adatbázisba
      await apiFetch({
        path: "/wp/v2/settings",
        method: "POST",
        data: options,
      });

      // 2. Pingeljük a Main szervert az új adatokkal
      const testResult = await apiFetch({
        path: "/msdl-child/v1/test-connection",
      });

      if (testResult.success) {
        setStatusText(`Sikeres! ${testResult.message}`);
      } else {
        setStatusText(`Mentve. Központ válasza: ${testResult.message}`);
      }
    } catch (error) {
      setStatusText("Hiba történt a folyamat során.");
    }
    setIsSaving(false);
  };

  return (
    <div className="wrap">
      <h1>Kliens Beállítások</h1>
      <PanelBody title="Központi Kapcsolat">
        {statusText && (
          <Notice status="info" isDismissible={false}>
            {statusText}
          </Notice>
        )}

        <TextControl
          label="Main Szerver URL"
          help="A központi WP oldal címe (pl. https://kozpont.hu)"
          value={options.msdl_main_server_url}
          onChange={(val) =>
            setOptions({ ...options, msdl_main_server_url: val })
          }
        />
        <TextControl
          label="Belső API Kulcs"
          help="A Main pluginban beállított közös jelszó."
          type="password"
          value={options.msdl_internal_api_key}
          onChange={(val) =>
            setOptions({ ...options, msdl_internal_api_key: val })
          }
        />

        <Button isPrimary isBusy={isSaving} onClick={handleSave}>
          {isSaving ? "Mentés..." : "Mentés"}
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
