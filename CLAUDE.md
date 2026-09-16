# CLAUDE.md — MSDLWP

Két összetartozó WordPress plugin: **msdl-main** (Microsoft Graph API
hitelesítés/token-szolgáltató központ) és **msdl-child** (SharePoint/
OneDrive dokumentumtár-kliens, Elementor widgetekkel). Lásd a megosztott
`wp-plugin-conventions` skillt az általános struktúráért/release-ért.

Ha a Graph API integrációt módosítod, mindig ellenőrizd mindkét plugint —
a hitelesítés a main-ben él, a child ezt fogyasztja.
