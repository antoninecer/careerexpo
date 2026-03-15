# CareerExpo - Platforma pro moderní kariérní veletrhy

CareerExpo je komplexní PHP platforma navržená pro správu fyzických, virtuálních i hybridních kariérních veletrhů. Propojuje talentované uchazeče s předními firmami a poskytuje organizátorům nástroje pro měření reálného dopadu akce.

## 🚀 Klíčové Funkce

### 📅 Multi-event Architektura
*   **Správa více ročníků:** Možnost provozovat neomezené množství akcí v rámci jedné instalace.
*   **Historie a kontinuita:** Uchazeči i firmy si ponechávají profily napříč různými veletrhy.

### 🏢 Pro Vystavovatele (Firmy)
*   **Bohaté profily:** Správa popisu firmy, webových stránek a kontaktů.
*   **Virtuální přítomnost:** Integrace YouTube videí, odkazů na online meetingy (Zoom/Jitsi) a PDF brožur.
*   **Správa pozic:** Vystavování pracovních nabídek specificky pro každou akci.
*   **On-site párování:** Unikátní pairing kódy pro rychlé propojení s kandidáty přímo u stánku.

### 🎓 Pro Uchazeče (Kandidáty)
*   **Chytrý Matching:** Algoritmus porovnávající dovednosti a zkušenosti z CV s požadavky firem.
*   **Správa CV:** Nahrávání a správa životopisů.
*   **Program akce:** Rezervace míst na přednáškách a workshopech.
*   **Virtuální účast:** Sledování streamovaných přednášek přímo v platformě.

### 🛠 Pro Administrátory
*   **Komplexní Dashboard:** Real-time statistiky o počtu registrací, schůzek a uzavřených kontraktů.
*   **Správa obsahu:** Plná kontrola nad akcemi, stánky, přednášky a profily firem.
*   **Přidělování stánků:** Mapování firem na konkrétní fyzická místa v hale.

### ❓ Nápověda a podpora
*   **Centrum nápovědy:** Integrovaná stránka `/help.php` s návody pro všechny role.
*   **Kontextová nápověda:** Interaktivní nápovědy (tooltips) přímo u složitějších funkcí v aplikaci.
*   **Quick Start:** Checklisty na dashboardech pro rychlou orientaci nových uživatelů.

## 💻 Technické řešení
*   **Backend:** PHP 8.x (Vanilla) s PDO pro bezpečnou práci s databází.
*   **Databáze:** MySQL / MariaDB (Prepared statements, cizí klíče).
*   **Frontend:** Bootstrap 5, Bi-Icons, čisté a responzivní UI.
*   **Bezpečnost:** CSRF ochrana, hašování hesel (bcrypt), autorizace na úrovni rolí.

### 🤖 Lokální AI & Vývoj
Tento projekt je vyvíjen s prioritou využití **lokálních LLM** skrze platformu Ollama:
*   **coder** (deepseek-coder:6.7b): Generování kódu a refaktorování.
*   **qwen** (qwen2.5:7b): Analýza architektury a debugging.
Tento přístup zajišťuje maximální soukromí kódu a nezávislost na cloudových limitech.

## 🛠 Instalace a nastavení

1.  **Databáze:** Importujte soubor `schema.sql` do vaší MySQL databáze.
2.  **Konfigurace:** Zkopírujte `inc/connect.template.php` do `inc/connect.php` a vyplňte své přístupové údaje k databázi.
3.  **Bootstrap:** Základní nastavení aplikace se nachází v `inc/bootstrap.php`.
4.  **Data:** Pro rychlé otestování můžete spustit `seed.php` pro naplnění databáze ukázkovými daty.

## 📈 Roadmapa
*   [ ] Integrace AI (LLM) pro hloubkovou analýzu CV a automatizovaný matching.
*   [ ] Interaktivní 2D mapa stánků s navigací v reálném čase.
*   [ ] Mobilní aplikace pro rychlé skenování QR kódů kandidátů.
*   [ ] Automatizovaný mailing a notifikační systém pro schůzky.

---
© 2026 CareerExpo Project Team
