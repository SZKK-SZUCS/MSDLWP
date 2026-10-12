=== MSDL Kliens (Child) ===
Contributors: MFÜI - Szurofka Márton
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 1.2.9
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

= 1.2.9 =
* Új: saját `msdl_manage_documents` jogosultság (capability) a Fájlkezelő admin menühöz és a mögötte lévő funkciókhoz (átnevezés, leírás, verziókezelés, láthatóság-állítás, lokális törlés). Eddig ez kizárólag Administratornak volt elérhető (`manage_options`) — mostantól egy role-kezelő pluginban (pl. Members, User Role Editor) tetszőleges role-nak (pl. Szerkesztő) megadható anélkül, hogy teljes site-adminná kellene tenni. Administrator továbbra is automatikusan mindent lát.
* Javítva (biztonsági): a `/get-nodes` REST végpont eddig bármelyik bejelentkezett felhasználó (akár Subscriber) számára elérhető volt, és a teljes, szűrés nélküli fájl/mappa-listát adta vissza a láthatósági beállításoktól függetlenül. Mostantól ugyanaz a jogosultság védi, mint a Fájlkezelő menüt.

= 1.2.8 =
* Javítva: ha a SharePoint mappa üresre lett takarítva (minden fájl törölve), a teljes szinkron „0 db feldolgozva” eredménnyel lefutott, de a helyben bent ragadt bejegyzéseket nem törölte (a korábbi biztonsági feltétel 0 élő elemnél kihagyta a takarítást). Mostantól: ha a delta lekérdezés hibátlanul végigfutott és teljes szinkron volt, üres mappánál a helyi lista is kiürül. A szinkron visszajelzése és a log külön kiírja, hány elavult sor lett eltávolítva.
* Új: a Fájlkezelőben minden sorhoz került egy „kuka” (törlés) gomb, amivel egy bejegyzés kézzel eltávolítható a helyi listából (a SharePoint fájlt nem érinti). Mappánál a helyi gyerekeket is törli.

= 1.2.7 =
* Javítva: a „Gyorsítótár ürítése” után a szinkron mégis növekményes (delta) módban futott perzisztens object cache (Redis/Memcached) esetén, mert a delta tokenek nyers SQL-lel törlődtek a WP opció-gyorsítótár frissítése nélkül. Emiatt a SharePointon már törölt, helyben „bent ragadt” fájlok a teljes újraszinkron ellenére sem tűntek el. Mostantól a tokenek szabályosan (delete_option) törlődnek, plusz egy külön jelző garantálja, hogy a következő szinkron a nulláról induljon.
* Új: a Fájlkezelő admin oldalán, jobbra fent egy „Feltöltés a SharePointba” gomb, amely új lapon megnyitja a beállított SharePoint mappát (az azonosítás és a feltöltés magán a SharePoint felületén történik).

= 1.2.6 =
* Javítva: a SharePointból törölt fájlok/mappák nem tűntek el a pluginból és kezelhetetlenné váltak. A delta feldolgozó a törölt elemeket a 'name' mező hiánya miatt eldobta még a törlés-kezelő előtt (a SharePoint / OneDrive for Business a törölt elemeknél nem küld 'name'-et). Mostantól a törlés az azonosító alapján fut le, a '@removed' jelzőt is figyeli, és a törölt mappák helyi gyerekeit rekurzívan takarítja.
* Új: teljes (nulláról induló) szinkronnál a rendszer a helyi adatbázisból eltávolítja azokat az elemeket, amelyek már nincsenek a SharePoint mappában – így a korábban „bent ragadt” törlések is kitakarításra kerülnek (elég egyszer a „Gyorsítótár törlése” után szinkronizálni).
* Módosítva: a Fájlkezelő (Pro) táblázatában a fájlsoroknál a Letöltés és a Részletek gomb sorrendje felcserélődött (előbb Letöltés, majd Részletek), így a Részletek gomb egy vonalba került a mappák Megnyitás gombjával.

= 1.2.5 =
* Javítva: a letöltés gomb nem működött (üres válasz), ha a fájlhoz nem volt rögzített/időzített verzió beállítva, mert a szinkron során cache-elt letöltési link a Graph delta lekérdezés miatt gyakran üresen maradt. Mostantól ilyenkor mindig friss, élő linket kér a rendszer.
* Javítva: jogosultság-ellenőrzés fail-open hibája a letöltési végponton (ha az Elementor osztály nem töltődött be, a hozzáférés-ellenőrzés kimaradt).

= 1.2.4 =
* small fix php

= 1.2.3 =
* Added support for staging

= 1.2.2 =
* Sync bug amikor túl sok a fájl fixálva

= 1.2.1 =
* Sync bug

= 1.2.0 =
* Időzített verzió upgrade
* Automatikus File elnevezés 

= 1.1.0 =
* Új funkciók
* Autoamta hozzáférés öröklődés
* Grafikai javítások
* UX javítások

= 1.0.0 =
* Kezdeti stabil kiadás.
* Központi biztonsági kapu (Global & Local Access Control) bevezetése.
* Elementor widgetek és egyedi Picker (tallózó) control integrálása.
* React admin UI, szinkronizációs állapot figyelése és API kommunikáció a Main szerverrel.