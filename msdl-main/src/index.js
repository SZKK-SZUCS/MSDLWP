import { render, useState, useEffect } from "@wordpress/element";
import { PanelBody, TextControl, Button, Notice } from "@wordpress/components";
import apiFetch from "@wordpress/api-fetch";

const App = () => {
  const [options, setOptions] = useState({
    msdl_tenant_id: "",
    msdl_client_id: "",
    msdl_client_secret: "",
    msdl_site_id: "",
    msdl_drive_id: "",
    msdl_internal_api_key: "",
  });
  const [status, setStatus] = useState("");

  useEffect(() => {
    apiFetch({ path: "/wp/v2/settings" }).then((settings) => {
      setOptions({
        msdl_tenant_id: settings.msdl_tenant_id || "",
        msdl_client_id: settings.msdl_client_id || "",
        msdl_client_secret: settings.msdl_client_secret || "",
        msdl_site_id: settings.msdl_site_id || "",
        msdl_drive_id: settings.msdl_drive_id || "",
        msdl_internal_api_key: settings.msdl_internal_api_key || "",
      });
    });
  }, []);

  const handleSave = async () => {
    setStatus("Mentés...");
    try {
      await apiFetch({
        path: "/wp/v2/settings",
        method: "POST",
        data: options,
      });
      setStatus("Sikeresen elmentve!");
    } catch (e) {
      setStatus("Hiba a mentéskor!");
    }
  };

  return (
    <div className="wrap">
      <h1>MSDL Központ Beállítások</h1>
      <PanelBody title="Hitelesítő adatok és Belső API Kulcs">
        {status && (
          <Notice status="info" isDismissible={false}>
            {status}
          </Notice>
        )}
        <TextControl
          label="Tenant ID"
          value={options.msdl_tenant_id}
          onChange={(v) => setOptions({ ...options, msdl_tenant_id: v })}
        />
        <TextControl
          label="Client ID"
          value={options.msdl_client_id}
          onChange={(v) => setOptions({ ...options, msdl_client_id: v })}
        />
        <TextControl
          label="Client Secret"
          type="password"
          value={options.msdl_client_secret}
          onChange={(v) => setOptions({ ...options, msdl_client_secret: v })}
        />
        <TextControl
          label="Site ID"
          value={options.msdl_site_id}
          onChange={(v) => setOptions({ ...options, msdl_site_id: v })}
        />
        <TextControl
          label="Drive ID"
          value={options.msdl_drive_id}
          onChange={(v) => setOptions({ ...options, msdl_drive_id: v })}
        />
        <hr />
        <TextControl
          label="Belső API Kulcs (Child pluginokhoz)"
          help="Ezt a jelszót kell majd megadni a weblapokon."
          value={options.msdl_internal_api_key}
          onChange={(v) => setOptions({ ...options, msdl_internal_api_key: v })}
        />
        <Button isPrimary onClick={handleSave}>
          Mentés
        </Button>
      </PanelBody>
    </div>
  );
};

document.addEventListener("DOMContentLoaded", () => {
  const root = document.getElementById("msdl-main-app");
  if (root) render(<App />, root);
});
