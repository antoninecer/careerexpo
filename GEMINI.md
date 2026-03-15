# CareerExpo - Projektová pravidla a AI správa

## 🤖 Lokální AI (Ollama) - Priorita č. 1

Tento projekt využívá pro veškerou generativní práci výhradně lokální modely běžící přes Ollama. Gemini Cloud slouží POUZE jako orchestrátor, validátor a plánovač.

### Dostupní agenti a jejich Pre-prompty:
- **coder** (`deepseek-coder:6.7b`): Pro veškerý kód, refaktorování a tvorbu souborů.
  - **Instrukce:** „Jsi Senior PHP/Web Developer. Prováděj chirurgicky přesné úpravy kódu. Nepřepisuj celé soubory, pokud to není nutné. Zachovej veškerou stávající logiku, komentáře a styl (Bootstrap 5, PDO, Vanilla PHP). Výstupem budiž pouze kód bez vysvětlujících řečí.“
- **qwen** (`qwen2.5:7b`): Pro analýzu architektury, bezpečnostní review a debugging.
  - **Instrukce:** „Jsi Systémový Architekt. Analyzuj kód z hlediska bezpečnosti (SQLi, XSS, CSRF), výkonu a škálovatelnosti. Buď technický a stručný.“

### Pravidla použití:
1. **Zákaz generování velkých bloků kódu v cloudu:** Pokud je potřeba kód, orchestrátor ho musí vyžádat od lokálního `coder`.
2. **Kontext:** Při volání lokálního modelu mu orchestrátor musí poslat relevantní výřez kódu a jasné zadání.
3. **Validace:** Orchestrátor musí před uložením ověřit, že lokální model nevymazal důležitou logiku.

---

## 🏗 Architektura a standardy

### Struktura PHP
- `public/`: Vstupní body (stránky).
- `inc/`: Bootstrap, konfigurace (`connect.php`).
- `templates/`: Opakující se části (header, footer).
- `src/`: Třídy a složitější logika (Matching, Upload).
- `uploads/`: Nahrávané soubory (CV, brožury).

### Kód a Bezpečnost
- **Vanilla PHP & PDO:** Používej výhradně prepared statements.
- **CSRF:** Každý POST požadavek musí obsahovat a validovat CSRF token.
- **XSS:** Všechny výstupy do HTML musí být ošetřeny funkcí `e()`.
- **Secrets:** Nikdy neukládej hesla nebo API klíče do gitu. Použij `inc/connect.php` (mimo git).

### UI a UX
- **Bootstrap 5:** Čistý, moderní a responzivní vzhled.
- **Nápověda:** Používej ikony s tooltipy (`data-bs-toggle="tooltip"`) pro vysvětlení funkcí.

---

## 🛠 Pracovní postup (Git)
1. **Prozkoumej:** Najdi relevantní kód pomocí `grep_search`.
2. **Navrhni:** Připrav strategii.
3. **Generuj:** Použij lokální `coder` pro tvorbu kódu.
4. **Validuj:** Zkontroluj syntaxi a logiku.
5. **Commit:** Dělej malé, atomické commity s jasným popisem.

## 🚫 Co nedělat
- Nepoužívat cloudové modely pro psaní PHP kódu (šetři limit).
- Nepřepisovat funkční části jen kvůli „úklidu“.
- Neměnit cesty k souborům bez aktualizace všech includů.
- Nezapomínat na `requireRole()` a `requireEvent()` u chráněných stránek.
