=== MSDL Kliens (Child) ===
Contributors: MFÜI - Szurofka Márton
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 0.9.3
Requires PHP: 7.4
License: Proprietary

Microsoft SharePoint szinkronizációs kliens, dokumentumtár-kezelő és prémium Elementor widget csomag.

== Description ==

Az MSDL Kliens (Child) egy robusztus, modern WordPress bővítmény, amely közvetlenül csatlakozik a központi MSDL szerverhez. Célja, hogy a Microsoft SharePointban (vagy OneDrive-ban) tárolt fájlokat és mappákat biztonságosan, gyorsan és vizuálisan vonzó módon tegye elérhetővé a weboldal látogatói számára.

A beépített React alapú admin felületen keresztül a weboldal adminisztrátorai teljes kontrollt kapnak a szinkronizált fájlok felett, beleértve az átnevezéseket, a HTML leírások hozzáadását és a szigorú jogosultságkezelést.

### Fő Funkciók (Features)

* **Villámgyors Szinkronizáció:** A Microsoft Graph Delta API-t használva csak a módosított elemeket tölti le, kímélve a szerver erőforrásait. A szinkronizáció központilag (cron) vagy helyileg is vezérelhető.
* **Fejlett Jogosultságkezelés (ACL):** Szigorú hozzáférés-szabályozás globális (Gyökérmappa) és lokális (Fájl/Mappa) szinten. A tartalmak lehetnek nyilvánosak, bejelentkezéshez kötöttek, vagy specifikus WordPress szerepkörökre (Roles) korlátozottak.
* **Modern React Admin Felület:** Letisztult, azonnal reagáló fájlkezelő felület, tömeges szerkesztési lehetőséggel és öröklődés (cascade) kezeléssel az almappákhoz.
* **Metaadatok és Testreszabás:** Eredeti fájlnevek felülírása egyedi, felhasználóbarát Címekkel, és TinyMCE integráció a rich-text (HTML) leírások hozzáadásához.
* **Golyóálló Letöltés Proxy:** A fájlok letöltése a WordPressen keresztül történik, így a jogosulatlan felhasználók még a letöltési link (URL) birtokában sem férhetnek hozzá a védett dokumentumokhoz.
* **Beépített Elementor Widgetek:** * Fájl Letöltés Gomb (Dinamikus ikonokkal és védett mód kijelzéssel).
    * Fájl Kártya (Részletes metaadatokkal és leírással).
    * Interaktív Mappa Lista (Navigálható breadcrumb, listás, rácsos és carousel elrendezésekkel).
    * Komplex Fájlkezelő (Keresővel és Single-View támogatással).
* **Automata Tisztítás:** A SharePointból törölt mappák és fájlok (orphaned data) automatikus rekurzív takarítása a helyi adatbázisból.

== Changelog ==

= 1.0.0 =
* Kezdeti stabil kiadás.
* Központi biztonsági kapu (Global & Local Access Control) bevezetése.
* Elementor widgetek és egyedi Picker (tallózó) control integrálása.
* React admin UI, szinkronizációs állapot figyelése és API kommunikáció a Main szerverrel.