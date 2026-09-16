# CLAUDE.md — MSDLWP

Két összetartozó WordPress plugin: **msdl-main** (Microsoft Graph API
hitelesítés/token-szolgáltató központ) és **msdl-child** (SharePoint/
OneDrive dokumentumtár-kliens, Elementor widgetekkel). Lásd a megosztott
`wp-plugin-conventions` skillt az általános struktúráért/release-ért.

Ha a Graph API integrációt módosítod, mindig ellenőrizd mindkét plugint —
a hitelesítés a main-ben él, a child ezt fogyasztja.

## Lint / static-analysis
Mindkét al-plugin (`msdl-main/`, `msdl-child/`) saját `composer.json`-nal
rendelkezik (phpcs WPCS + phpstan level 5, baseline-olt jelenlegi hibákkal):
- `composer lint` — msdl-main: 1065 hiba, msdl-child: 5723 hiba (túlnyomó
  többség auto-javítható `composer lint:fix`-fel)
- `composer stan` — mindkettő "No errors" a baseline-nal (msdl-main: 62,
  msdl-child: 71 baseline-olt hiba). A msdl-child-ben `--memory-limit=3G`
  kell, 1G-nél kifogy a memóriából.
- 5 Elementor widget/control fájl ki van zárva a phpstan-ból
  msdl-child-ben (`includes/widgets/*.php`, `includes/controls/*.php`),
  mert Elementor osztályai nincsenek stub-olva.
