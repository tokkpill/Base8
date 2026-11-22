<div style="padding-top: 9rem;">
    <div style="max-width: 140rem; margin: 0 auto; padding: 0 2rem;">

        <!-- Search Bar -->
        <form action="/buscar/resultados" method="GET" class="global-search__bar" style="margin-bottom: 2rem;">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input 
                type="text" 
                name="q" 
                value="<?php echo s($keyword); ?>"
                placeholder="Busca apuntes, guías, exámenes, tutoriales..." 
                id="search-input"
            >
            <!-- Preserve filters in hidden fields -->
            <?php if($facultyId): ?><input type="hidden" name="faculty" value="<?php echo $facultyId; ?>"><?php endif; ?>
            <?php if($categoryId): ?><input type="hidden" name="category" value="<?php echo $categoryId; ?>"><?php endif; ?>
            <button type="submit">Buscar</button>
        </form>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-bar__group">
                <label class="filter-bar__label">Facultad</label>
                <select name="faculty" id="filter-faculty" onchange="applyFilters()">
                    <option value="">Todas las facultades</option>
                    <?php foreach($faculties as $f): ?>
                        <option value="<?php echo $f->id; ?>" <?php echo $facultyId === $f->id ? 'selected' : ''; ?>>
                            <?php echo s($f->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-bar__group">
                <label class="filter-bar__label">Categoría</label>
                <select name="category" id="filter-category" onchange="applyFilters()">
                    <option value="">Todas las categorías</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?php echo $c->id; ?>" <?php echo $categoryId === $c->id ? 'selected' : ''; ?>>
                            <?php echo s($c->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-bar__group">
                <label class="filter-bar__label">Ordenar por</label>
                <select name="order" id="filter-order" onchange="applyFilters()">
                    <option value="relevance" <?php echo $orderBy === 'relevance' ? 'selected' : ''; ?>>Relevancia</option>
                    <option value="newest" <?php echo $orderBy === 'newest' ? 'selected' : ''; ?>>Más recientes</option>
                    <option value="rating" <?php echo $orderBy === 'rating' ? 'selected' : ''; ?>>Mejor calificados</option>
                    <option value="downloads" <?php echo $orderBy === 'downloads' ? 'selected' : ''; ?>>Más descargados</option>
                    <option value="price_asc" <?php echo $orderBy === 'price_asc' ? 'selected' : ''; ?>>Precio: menor a mayor</option>
                    <option value="price_desc" <?php echo $orderBy === 'price_desc' ? 'selected' : ''; ?>>Precio: mayor a menor</option>
                </select>
            </div>

            <div class="filter-bar__actions">
                <span class="filter-bar__count"><?php echo $totalResults; ?> resultado<?php echo $totalResults !== 1 ? 's' : ''; ?></span>
            </div>
        </div>

        <!-- Results Grid -->
        <?php if(!empty($results)): ?>
            <div class="asset-grid">
                <?php foreach($results as $asset): ?>
                    <a href="/marketplace/asset?id=<?php echo $asset->id; ?>" class="asset-card" style="text-decoration:none;">
                        <div class="asset-card__preview">
                            <i class="fa-solid <?php echo $asset->category ? s($asset->category->icono) : 'fa-file'; ?>"></i>
                            <?php if($asset->rating_promedio > 4): ?>
                                <div class="asset-card__preview-badge">
                                    <span class="prestige-badge badge--scholar"><i class="fa-solid fa-star"></i> Top</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="asset-card__body">
                            <span class="asset-card__category">
                                <?php echo $asset->category ? s($asset->category->nombre) : 'Recurso'; ?>
                            </span>
                            <h3 class="asset-card__title"><?php echo s($asset->titulo); ?></h3>
                            <p class="asset-card__desc"><?php echo s($asset->descripcion); ?></p>
                            <div class="asset-card__meta">
                                <div class="asset-card__author">
                                    <img src="/build/img/default-avatar.svg" alt="Autor">
                                    <span><?php echo $asset->autor ? s($asset->autor->nombre) : 'Anónimo'; ?></span>
                                </div>
                                <div class="asset-card__stats">
                                    <span><i class="fa-solid fa-download"></i> <?php echo $asset->descargas; ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="asset-card__footer">
                            <?php echo format_price($asset->precio); ?>
                            <?php echo render_stars($asset->rating_promedio, $asset->total_ratings); ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
                <nav class="pagination">
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="/buscar/resultados?q=<?php echo urlencode($keyword); ?>&faculty=<?php echo $facultyId; ?>&category=<?php echo $categoryId; ?>&order=<?php echo $orderBy; ?>&page=<?php echo $i; ?>" 
                           class="pagination__link <?php echo $page === $i ? 'pagination__link--active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon"><i class="fa-solid fa-search"></i></div>
                <h3 class="empty-state__title">No se encontraron resultados</h3>
                <p class="empty-state__text">Intenta con otros términos de búsqueda o ajusta los filtros</p>
                <a href="/buscar" class="empty-state__action">Nueva búsqueda</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function applyFilters() {
    const q = document.getElementById('search-input')?.value || '';
    const faculty = document.getElementById('filter-faculty').value;
    const category = document.getElementById('filter-category').value;
    const order = document.getElementById('filter-order').value;
    
    let url = '/buscar/resultados?q=' + encodeURIComponent(q);
    if (faculty) url += '&faculty=' + faculty;
    if (category) url += '&category=' + category;
    if (order) url += '&order=' + order;
    
    window.location.href = url;
}
</script>
