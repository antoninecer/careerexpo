```markdown
# CareerExpo - Webová platforma pro měření úspěšnosti pracovních veletrhů

## Účel
CareerExpo je webová platforma navržená pro měření úspěšnosti pracovních veletrhů. Provozovatelé a účastníci mohou sledovat výsledky veletrhu v reálném čase, zahájit okamžité kontakty a zaplňovat feedbacky.

## Klíčové funkce
1. **Veřejný dashboard statistik**
   - Počty účastníců (kandidáti, firmy)
   - Počty úspěšných kontraktů
   - Grafické přehledy a trendy

2. **Inteligentní Matching kandidátů**
   - Založené na senioritě, dovednostech a lokalitě
   - Přesnější závazky a lépe vyplněné schůzky

3. **On-site párování**
   - Použití QR kódů nebo 6místných kódů pro okamžité kontaktování
   - Rychlejší a efektivnější komunikace

4. **Program přednášek**
   - Timeline layout pro snadné plánování a sledování přednášek
   - Správa více sálů
   - Rezervace míst ve slychacích sálách

5. **Success Loop: Sledování výsledků schůzek**
   - 'Plácli jsme si!' funkce pro okamžité zaznamenání závazků
   - Reálný reporting pro organizátory a účastníky

## Technické detaily
- **PHP:** 8.5+
- **Database:** MySQL
- **Frontend:** Bootstrap 5.3
- **CSRF ochrana**
- **Secure File Upload**

## Instalace
1. Zkopírujte soubor `inc/connect.template.php` do `inc/connect.php`.
2. Provedte potřebné úpravy v `inc/connect.php` (zadejte své databázové údaje).
3. Nainstalujte závislosti pomocí `composer install`.
4. Spusťte databázový script pro vytvoření tabulek a zakódování účtů.
5. Navštivte vaši domovní adresu v prohlížeči, aby se vytvořily notace v databázi.

## Role a přístupy
- **Admin:** Správce s úplným přístupem k všem funkcím, moci pro změnu nastavení a role.
- **Company:** Uživatel firmy s možností úvodní registrace a následnou úpravou profilu.
- **Candidate:** Uživatel kandidáta s možností registrace, profilové úpravy a sledování schůzek.

## Kontakt
Pro nějaké dotazy nebo potřeby náprav, kontaktujte nás na [e-mailovou adresu] nebo na [telefonickou čísla].
```

Toto README.md zahrnuje hlavní body, které jste uvedli, a pomáhá jasně zdůvodnit cíle a funkce projektu.

