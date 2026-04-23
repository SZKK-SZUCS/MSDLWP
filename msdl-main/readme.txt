=== MSDL Központ (Main) ===
Contributors: MFÜI - Szurofka Márton
Requires at least: 6.0
Tested up to: 6.5
Stable tag: 1.2.2
Requires PHP: 7.4
License: Proprietary

A Microsoft Graph API hálózat központi agya, jogosultság-kezelő és ütemező (Master) rendszere az MSDL ökoszisztémához.

== Description ==

Az MSDL Központ (Main) a hálózat lelke. Ez a bővítmény felel a Microsoft Entra ID (Azure AD) OAuth 2.0 alapú hitelesítéséért, és biztonságos kapuként (Gateway) szolgál a csatlakoztatott weboldalak (Child kliensek) és a Microsoft felhője között.

Nem tartalmaz frontend megjelenítőket; kizárólag a hálózatbiztonságra, az API kulcsok kezelésére, a tenantok (webhelyek) adminisztrációjára és a hálózati terheléselosztásra fókuszál.

### Fő Funkciók (Features)

* **Központi Hitelesítés (OAuth 2.0):** Egyetlen ponton kezeli a Microsoft Graph API tokeneket, így nem kell minden egyes kliens weboldalon külön Azure Appot regisztrálni és karbantartani.
* **Webhely Menedzsment (Tenant Control):** Központi adminisztrációs felület az összes csatlakoztatott webhely (domain) kezelésére. Lehetőség van a kliensek azonnali felfüggesztésére, tiltására vagy egyedi Drive ID-k (Tárhelyek) hozzárendelésére.
* **Biztonságos Belső API (REST):** Kriptografikusan védett, belső API kulcsokra épülő kommunikáció a Main és a Child szerverek között.
* **Master Cron Ütemező (Load Balancing):** A központi szerver diktálja a szinkronizáció ütemét. A hálózati torlódások elkerülése végett sorba állítja (Queue) a klienseket, és késleltetve, biztonságos sávszélesség-használattal küldi ki a szinkronizációs parancsokat.
* **Távoli Vezérlés (Remote Commands):** Lehetőség van a központi admin felületről "Ping" (kapcsolat tesztelése) és "Force Sync" (azonnali szinkronizáció kikényszerítése) parancsokat küldeni a távoli weboldalaknak.
* **Dinamikus Útválasztás:** Automatikusan lekérdezi és biztosítja a helyes SharePoint URL-eket és struktúrákat az újonnan csatlakozó weboldalak számára.

== Changelog ==

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
* Hálózati elosztó (Master Cron) és egyedi feladatkezelő beépítése.
* Token generátor és REST API végpontok publikálása a kliensek számára.
* Teljes körű webhely menedzsment (CRUD) és távoli parancs végrehajtás.