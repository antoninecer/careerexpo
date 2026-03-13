# CareerExpo – zadání projektu

Cílem je vytvořit plnohodnotnou webovou aplikaci pro veletrh pracovních příležitostí typu Career Expo, postavenou na PHP a MySQL, s grafickým uživatelským rozhraním, rolemi pro pořadatele, firmy a uchazeče, možností předregistrace, nahrání CV, správy otevřených pozic, matchingu kandidátů a firem, plánování schůzek, rezervací přednášek a základní podpory virtuálních stánků.

Nejde už o pouhé POC, ale o návrh funkčního produktu, který bude možné lokálně spustit, otestovat a dále rozvíjet.

## Technologie

- PHP 8.5+
- MySQL / MariaDB
- HTML, CSS, JavaScript
- server-rendered aplikace
- připravené rozhraní pro AI vyhodnocení matchingu
- připravené místo v konfiguraci pro PHPMailer / SMTP

Databázový účet bude použit ve tvaru:

- databáze: `careerexpo`
- uživatel: `careerexpo`
- heslo: `TajneHeslo12345`

## Základní role v systému

### 1. Pořadatel / administrátor
Pořadatel spravuje celý veletrh a musí mít možnost:
- zakládat a upravovat firmy / vystavovatele
- zakládat mapu stánků a přiřazovat firmám umístění
- evidovat fyzické i virtuální vystavovatele
- zakládat program přednášek
- nastavovat kapacity přednášek
- generovat přístupové údaje pro vystavovatele
- sledovat registrace uchazečů
- sledovat metriky, rezervace a matching
- mít administrativní dashboard

### 2. Firma / vystavovatel
Firma musí mít možnost:
- přihlásit se do svého rozhraní
- upravit profil firmy
- vyplnit, co prezentuje
- zadat otevřené pozice
- zadat požadavky na kandidáty
- prohlížet doporučené kandidáty
- spravovat schůzky
- využívat rychlé párování přes QR nebo kód
- mít přehled kandidátů se skóre shody a badge

### 3. Uchazeč
Uchazeč musí mít možnost:
- registrace a přihlášení
- vyplnění profilu
- nahrání CV
- uvedení preferovaného typu práce nebo spolupráce
- zadání dovedností, technologií, zkušeností, seniority a lokality
- zobrazení doporučených firem
- rezervace schůzek
- rezervace přednášek
- zobrazení programu a doporučené trasy po veletrhu
- párování s firmou na místě pomocí QR nebo kódu

## Hlavní myšlenka systému

Smyslem aplikace je, aby už při registraci vznikala strukturovaná data na obou stranách.

Uchazeč by měl mít možnost s dostatečným předstihem:
- nahrát CV
- vyplnit profil
- uvést, o jaký druh práce nebo spolupráce má zájem
- vyjádřit souhlas, zda může být jeho profil zpřístupněn i firmám, které nejsou fyzicky přítomné na veletrhu

Firma by měla mít možnost s dostatečným předstihem:
- vyplnit profil firmy
- zadat otevřené pozice
- určit požadované dovednosti, senioritu, formu spolupráce a lokalitu
- uvést, zda je fyzicky nebo virtuálně přítomna

Na základě těchto dat musí být možné vytvářet relevantní propojení ještě před samotným veletrhem a v ideálním případě umožnit i předběžný kontakt nebo rezervaci času setkání na veletrhu či mimo něj.

## Funkční části systému

## 1. Autentizace a role
Systém musí mít:
- login
- logout
- správu session
- registraci pro uchazeče
- možnost přidání firem a administrátorů
- role-based access kontrolu

## 2. Konfigurace
Musí existovat konfigurační soubor, kde bude možné upravit:
- připojení k databázi
- název aplikace
- základní URL
- nastavení AI služby
- nastavení SMTP / PHPMailer
- e-mail odesílatele
- debug režim

V konfiguraci musí být připravené místo pro SMTP nastavení, například:
- host
- port
- username
- password
- encryption
- from_email
- from_name

## 3. Profil uchazeče
Formulář uchazeče musí obsahovat minimálně:
- jméno
- e-mail
- telefon
- lokalitu
- senioritu
- preferovaný typ spolupráce
- oblast zájmu
- seznam dovedností
- jazykové znalosti
- možnost nahrát CV
- odkaz na LinkedIn / GitHub / portfolio
- dostupnost
- souhlas se sdílením profilu
- souhlas se sdílením i s virtuálními nebo nepřítomnými firmami

## 4. Profil firmy
Formulář firmy musí obsahovat:
- název firmy
- kontaktní osobu
- e-mail
- typ účasti
- popis firmy
- stánek
- web
- seznam otevřených pozic
- požadavky na kandidáty
- možnost více recruiterů

## 5. Pozice
Každá firma může mít více pozic.
Pozice musí obsahovat:
- název pozice
- popis
- požadované dovednosti
- senioritu
- lokalitu
- typ spolupráce
- jazykové požadavky
- prioritu
- volitelné mzdové rozpětí

## 6. CV a profilová data
Po nahrání CV musí být možné:
- soubor uložit
- evidovat jeho název a cestu
- připravit prostor pro textovou extrakci
- připravit data pro AI nebo heuristickou analýzu
- uložit interní profilové metriky kandidáta

## 7. Matching a badge systém
Systém musí porovnávat kandidáta a pozici a vytvářet:
- procentuální skóre shody
- barevnou úroveň shody
- stručný komentář
- badge kandidáta
- badge hledané pozice

Barevná logika:
- zelená = vysoká shoda
- oranžová = částečná shoda
- červená = nízká shoda

Příklad badge kandidáta:
- PHP Backend
- Linux Admin
- DevOps
- QA Automation
- Data Analyst

Příklad badge pozice:
- Hledáme PHP Backend
- Hledáme Senior Linux Admin
- Hledáme Junior QA

Skórování může být v první verzi heuristické, ale návrh musí počítat s budoucím napojením na AI službu.

## 8. Předběžné párování před akcí
Ještě před začátkem akce má systém umět:
- doporučit uchazeči relevantní firmy
- doporučit firmě relevantní kandidáty
- zobrazit seznam prioritních kontaktů
- nabídnout možnost předběžné rezervace schůzky
- připravit přehled, kde má smysl se zastavit

## 9. Párování na místě
Na místě musí fungovat jednoduché rychlé propojení přes:
- QR kód
- krátký kód
- případně profil načtený mobilem

Po propojení se oběma stranám zobrazí:
- jméno / název
- hlavní profil
- badge
- míra shody
- stručné doporučení dalšího kroku

Firma si u kandidáta může nastavit interní stav:
- ano
- možná
- ne
- kontaktovat později

## 10. Mapa stánků a navigace
Systém musí obsahovat správu mapy stánků.

Pořadatel musí mít možnost:
- zadat stánky
- přidělit firmám umístění
- spravovat zóny nebo sektory

Uchazeč musí mít možnost:
- vidět seznam stánků
- vidět, kde se nachází doporučené firmy
- mít přehled trasy
- neobcházet stánky naslepo

Grafické výstupy musí být přehledné a použitelné i bez složitého školení.

## 11. Program přednášek
Systém musí umět:
- zakládat přednášky
- zadat čas, místo, kapacitu a popis
- umožnit rezervaci místa
- zobrazit zbývající kapacitu
- ukázat pořadateli, zda např. 32 míst skutečně stačí

## 12. Schůzky
Systém musí obsahovat modul schůzek mezi firmou a uchazečem:
- návrh termínu
- potvrzení termínu
- evidence stavu schůzky
- vazba na konkrétní firmu a kandidáta

## 13. Virtuální stánky
Systém musí počítat i s firmami, které nejsou fyzicky přítomné.
Virtuální stánek může mít:
- profil firmy
- otevřené pozice
- možnost projevit zájem
- volitelně jednoduchého AI asistenta nebo FAQ bota
- možnost rezervace kontaktu

## 14. Dashboardy a grafické výstupy
Aplikace musí mít grafické rozhraní, ne jen holé formuláře.
Požadovaný styl:
- čistý dashboard
- karty
- tabulky
- badge prvky
- zvýraznění skóre barevně
- jednoduché navigační menu
- responzivní layout

Požadované obrazovky:
- login
- dashboard uchazeče
- dashboard firmy
- dashboard administrátora
- seznam pozic
- seznam kandidátů
- detail kandidáta
- detail firmy
- přednášky
- rezervace
- mapa stánků
- matching přehled

## 15. Reporting
Administrace musí zobrazovat minimálně:
- počet registrovaných uchazečů
- počet nahraných CV
- počet registrovaných firem
- počet otevřených pozic
- počet přednášek
- počet rezervací
- počet schůzek
- počet matchů
- rozdělení podle barevného skóre
- zájem o firmy
- zájem o přednášky

## 16. Databáze
Datový model musí počítat minimálně s entitami:
- users
- candidate_profiles
- company_profiles
- jobs
- candidate_files
- matches
- meetings
- lectures
- lecture_reservations
- stands
- company_users
- audit_logs

Databáze musí být navržená tak, aby šla dál rozšiřovat.

## 17. Testování – povinná součást zadání
Každá větší změna musí být po implementaci otestována.
Nejde jen o napsání kódu, ale i o ověření funkčnosti z pohledu všech hlavních rolí.

Musí se vždy otestovat minimálně:

### Testy z pohledu pořadatele
- přihlášení administrátora
- založení a úprava vystavovatele
- založení mapy stánků
- přiřazení firmy ke stánku
- založení přednášky
- kontrola kapacity
- vygenerování nebo založení přístupových údajů pro vystavovatele
- zobrazení statistik a dashboardu

### Testy z pohledu firmy
- přihlášení firmy
- úprava profilu
- založení pozice
- zobrazení kandidátů
- kontrola matchingu
- vytvoření nebo potvrzení schůzky
- párování přes QR nebo kód

### Testy z pohledu uchazeče
- registrace
- login
- vyplnění profilu
- nahrání CV
- rezervace přednášky
- zobrazení doporučených firem
- párování s firmou
- rezervace nebo potvrzení schůzky

### Technické testy
- validace formulářů
- přístupová práva
- chybové stavy
- práce s databází
- práce se session
- upload CV
- rendering dashboardů

Součástí dodávky má být i popis, co bylo otestováno a jak.

## 18. Přístupové údaje pro vystavovatele
Pořadatel musí mít možnost každému vystavovateli vytvořit nebo vygenerovat přístupové údaje.
Systém musí počítat s tím, že:
- některé účty vzniknou ručně
- některé se vygenerují automaticky
- přihlašovací údaje mohou být následně rozeslány e-mailem

Proto musí být připravené místo pro napojení PHPMaileru.

## 19. E-mailová vrstva
Aplikace zatím nemusí mít plně hotový mailing engine, ale musí mít:
- připravenou konfiguraci SMTP
- oddělené místo pro mail service
- možnost pozdější integrace PHPMailer
- návrh šablon pro:
  - přístupové údaje
  - potvrzení registrace
  - potvrzení schůzky
  - rezervaci přednášky

## 20. Architektura a kvalita
Požaduje se:
- přehledná adresářová struktura
- oddělení konfigurace, databáze, logiky a šablon
- základní MVC nebo podobná čitelná architektura
- znovupoužitelné helpery
- bezpečné SQL dotazy
- hashování hesel
- ochrana proti základním útokům
- komentovaný kód tam, kde to dává smysl

## 21. Co má být výsledkem
Výsledkem má být funkční lokálně spustitelný projekt, který obsahuje:
- SQL schéma
- konfigurační soubor
- PHP aplikaci
- grafické rozhraní
- základní data modely a CRUD
- matching
- správu stánků
- správu přednášek
- rezervace
- připravenost na PHPMailer
- dokumentaci instalace a testování

## 22. Shrnutí
Aplikace nemá být jen evidencí kontaktů, ale skutečným systémem, který zlepší propojení mezi uchazeči, firmami a pořadateli. Má umožnit sběr dat před akcí, inteligentnější párování, plánování schůzek, orientaci po veletrhu, řízení kapacit přednášek a zapojení i virtuálních vystavovatelů. Současně musí být navržena tak, aby byla prakticky použitelná, rozšiřitelná a po každé větší změně důsledně otestovaná z pohledu všech hlavních rolí.

