# README.md pro projekt CareerExpo

## 1. Účel aplikace
CareerExpo je platforma pro organizaci a provádění career fór, kde společnosti a kandidáti mohou se sejít a najít se vztahy mezi sebou. Aplikace poskytuje všechny potřebné funkce pro efektivní organizaci a provádění fóru.

## 2. Instalace
1. Kopírujte soubor `inc/connect.template.php` do `inc/connect.php` a nahraďte v něm cesty a údaje pro vaši databázi.
2. Ujistěte se, že databáze a tabulky jsou vytvořeny a jsou připojeni správné údaje do souboru `inc/connect.php`.
3. Pro provedení testování, použijte `php test_run.php`.

## 3. Role
1. **Admin**: admin@careerexpo.cz
2. **Company**: hr@company.cz
3. **Candidate**: jan.novak@email.cz

## 4. Funkce
1. **CSRF**: Zajištění bezpečnosti při odesílání formulářů.
2. **Matching**: Mechanismus pro uspořádání setkání mezi společnostmi a kandidáty.
3. **On-site pairing (QR/Kód)**: Funkce pro poskytnutí kandidátům QR kódu, který jim pomůže získat přístup k webovému formuláři nebo k odběru pro konkrétní společnost.
4. **Upload CV (bezpečný)**: Bezpečný upload souborů zpráv a CV, které jsou zpracovány pro ochranu dat.
5. **Meetings**: Plánování a sledování setkání mezi kandidáty a zaměstnavateli.
6. **Program**: Zobrazení plánování fóra pro uživatele.

## 5. Bezpečnost
1. **No display_errors**: Ukázkové chybové zprávy nejsou zobrazeny na webové stránce, aby se zabrání pokusům o vyláknutí informací o webové aplikaci.
2. **Logging**: Záznam všech klíčových akcí a chyb.
3. **finfo MIME check**: Kontrola typu souboru při uploadu.

## 6. Omezení prototypu
1. **Mailing**: Reálné odesílání zpráv na e-mail nebylo implementováno.
2. **Mapy**: Zatím pouze textové/seznamové znázornění místa setkání.
3. **Extrahování textu z CV**: Pouze placeholder (návrh) pro budoucí implementaci.

## 7. Požadavky na PHP
Aplikace CarrerExpo vyžaduje PHP 8.5+.

## 8. Testování
1. Vytvořte novou konzoli v terminálu nebo příkazovém řádku.
2. Pro spuštění testovacího skriptu, zadejte příkaz:
   ```bash
   php test_run.php
   ```
3. Výsledky testů budou vypsány v terminálu.

## Použití
1. Načtěte a přečtěte si `inc/connect.php` a ujistěte se, že jsou všechny potřebné údaje správně nastaveny.
2. Přihlaste se jako správce, společnost nebo kandidát a prozkoumejte funkce aplikace.

Při potížích s instalací nebo fungováním, neváhejte se obrátit na správcu systému nebo vývojáře.

