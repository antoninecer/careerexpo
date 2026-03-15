<?php
require_once __DIR__ . '/../inc/bootstrap.php';
include_once __DIR__ . '/../templates/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="text-center mb-5">
            <h1 class="fw-bold text-primary">Centrum nápovědy</h1>
            <p class="lead text-muted">Vše, co potřebujete vědět k úspěšnému využívání platformy CareerExpo.</p>
        </div>

        <div class="row g-4">
            <!-- Admin Sekce -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 border-top border-5 border-primary">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-shield-lock text-primary fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-0">Administrátor</h4>
                        </div>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Správa akcí:</strong> Zakládejte nové veletrhy (fyzické i virtuální) v sekci <em>Globální Admin > Správa akcí</em>.</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Vystavovatelé:</strong> Přidávejte firmy do systému a následně je přiřazujte ke konkrétním akcím.</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Stánky:</strong> U fyzických akcí definujte zóny a čísla stánků, které pak přidělíte firmám.</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Monitoring:</strong> Sledujte v reálném čase počty schůzek a uzavřených kontraktů na dashboardu akce.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Firma Sekce -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 border-top border-5 border-success">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-building text-success fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-0">Vystavovatel</h4>
                        </div>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Profil firmy:</strong> Vyplňte popis, web a nahrajte PDF brožuru pro kandidáty.</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Pracovní pozice:</strong> Vypište pozice, které na daném veletrhu nabízíte. Zvýšíte tím svůj matching.</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Virtuální stánek:</strong> Vložte YouTube video a link na Zoom/Jitsi pro online interakci.</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Pairing Code:</strong> Ukažte kandidátům svůj 6místný kód u stánku pro okamžité propojení profilů.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kandidát Sekce -->
            <div class="col-md-4">
                <div class="card h-100 shadow-sm border-0 border-top border-5 border-info">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-info bg-opacity-10 p-3 rounded-circle me-3">
                                <i class="bi bi-person text-info fs-4"></i>
                            </div>
                            <h4 class="fw-bold mb-0">Uchazeč</h4>
                        </div>
                        <ul class="list-unstyled small">
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Nahrání CV:</strong> Nahrajte svůj životopis v PDF. Systém z něj vyčte klíčové dovednosti pro matching.</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Matching:</strong> Na dashboardu uvidíte firmy, které nejlépe odpovídají vašemu profilu (zelená/oranžová barva).</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Přednášky:</strong> Rezervujte si místo na workshopech. U virtuálních přednášek uvidíte video přímo v detailu programu.</li>
                            <li class="mb-2"><i class="bi bi-check2 text-success me-2"></i><strong>Schůzky:</strong> Žádejte firmy o schůzku nebo se s nimi spojte přes jejich pairing kód.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-5 bg-white p-5 rounded shadow-sm">
            <h3 class="fw-bold mb-4"><i class="bi bi-question-circle me-2 text-primary"></i>Často kladené dotazy (FAQ)</h3>
            
            <div class="accordion accordion-flush" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Jak funguje barevný Matching?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Algoritmus porovnává vaše dovednosti, senioritu a lokalitu s požadavky v inzerátech. 
                            <span class="badge bg-success">Zelená</span> znamená vysokou shodu, 
                            <span class="badge bg-warning text-dark">Oranžová</span> částečnou a 
                            <span class="badge bg-danger">Červená</span> nízkou shodu.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Kde najdu svůj Pairing Code?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            Firmy najdou svůj kód v horní části dashboardu nebo v nastavení profilu. Uchazeči kód nezadávají, ale diktují ho firmě u stánku, nebo ho zadávají do pole "Zadat kód firmy" pro okamžité propojení.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            Jak se připojím k online přednášce?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted">
                            V sekci "Přednášky" si nejprve rezervujte místo. Poté se v detailu přednášky (pokud je virtuální) objeví tlačítko "Sledovat stream" nebo přímo vložené video.
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="text-center mt-5 mb-4">
            <p class="text-muted">Nenašli jste, co jste hledali? Kontaktujte organizátora akce.</p>
            <a href="/index.php" class="btn btn-outline-primary rounded-pill px-4">Zpět na úvod</a>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/../templates/footer.php'; ?>
