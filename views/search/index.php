<section class="global-search">
    <h1 class="global-search__title">¿Qué quieres aprender hoy?</h1>
    <p class="global-search__subtitle">Descubre miles de recursos académicos de la comunidad UAEMex</p>

    <form action="/buscar/resultados" method="GET" class="global-search__bar" id="search-form">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input 
            type="text" 
            name="q" 
            placeholder="Busca apuntes, guías, exámenes, tutoriales..." 
            autocomplete="off"
            id="search-input"
        >
        <button type="submit">Buscar</button>
    </form>

    <div class="global-search__categories">
        <?php foreach($categories as $cat): ?>
            <a href="/buscar/resultados?category=<?php echo $cat->id; ?>" class="global-search__pill">
                <i class="fa-solid <?php echo s($cat->icono); ?>"></i>
                <?php echo s($cat->nombre); ?>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <div class="section__header">
        <h2 class="section__title">Explorar por Facultad</h2>
        <p class="section__subtitle">Filtra recursos por tu área de estudio</p>
    </div>

    <div style="max-width: 100rem; margin: 0 auto;">
        <div class="asset-grid" style="grid-template-columns: repeat(auto-fill, minmax(22rem, 1fr));">
            <?php foreach($faculties as $faculty): ?>
                <a href="/buscar/resultados?faculty=<?php echo $faculty->id; ?>" class="stat-card" style="text-decoration:none;">
                    <div class="stat-card__icon">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <p class="stat-card__label" style="font-size: 1.3rem; text-transform: none; letter-spacing: 0;">
                        <?php echo s($faculty->nombre); ?>
                    </p>
                    <p style="color: #64748B; font-size: 1.2rem; margin: 0.5rem 0 0;"><?php echo s($faculty->codigo); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
