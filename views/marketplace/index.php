<div style="padding-top: 9rem;">
    <div style="max-width: 140rem; margin: 0 auto; padding: 0 2rem;">

        <!-- Featured Section -->
        <?php if(!empty($featured)): ?>
            <section class="section" style="padding: 3rem 0;">
                <div class="section__header" style="text-align: left; margin-bottom: 2rem;">
                    <h2 class="section__title" style="font-size: 2.4rem;">
                        <i class="fa-solid fa-fire" style="color: #CCFF00;"></i> Destacados
                    </h2>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(30rem, 1fr)); gap: 2rem; margin-bottom: 4rem;">
                    <?php foreach($featured as $feat): ?>
                        <a href="/marketplace/asset?id=<?php echo $feat->id; ?>" class="featured-card" style="text-decoration:none; grid-template-columns: 1fr;">
                            <div class="featured-card__visual" style="min-height: 14rem;">
                                <i class="fa-solid fa-gem"></i>
                            </div>
                            <div class="featured-card__content" style="padding: 2rem;">
                                <span class="featured-card__label">DESTACADO</span>
                                <h3 class="featured-card__title" style="font-size: 1.8rem;"><?php echo s($feat->titulo); ?></h3>
                                <p class="featured-card__desc" style="font-size: 1.3rem;"><?php echo s(substr($feat->descripcion, 0, 100)); ?>...</p>
                                <div style="display:flex; justify-content:space-between; align-items:center;">
                                    <?php echo format_price($feat->precio); ?>
                                    <?php echo render_stars($feat->rating_promedio); ?>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-bar__group">
                <label class="filter-bar__label">Categoría</label>
                <select onchange="window.location.href='/marketplace?category=' + this.value + '&order=<?php echo $orderBy; ?>'">
                    <option value="">Todas</option>
                    <?php foreach($categories as $cat): ?>
                        <option value="<?php echo s($cat->slug); ?>" <?php echo $categorySlug === $cat->slug ? 'selected' : ''; ?>>
                            <?php echo s($cat->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-bar__group">
                <label class="filter-bar__label">Facultad</label>
                <select onchange="window.location.href='/marketplace?faculty=' + this.value + '&order=<?php echo $orderBy; ?>'">
                    <option value="">Todas</option>
                    <?php foreach($faculties as $f): ?>
                        <option value="<?php echo $f->id; ?>" <?php echo $facultyId === $f->id ? 'selected' : ''; ?>>
                            <?php echo s($f->nombre); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-bar__group">
                <label class="filter-bar__label">Ordenar</label>
                <select onchange="window.location.href='/marketplace?order=' + this.value">
                    <option value="newest" <?php echo $orderBy === 'newest' ? 'selected' : ''; ?>>Más recientes</option>
                    <option value="rating" <?php echo $orderBy === 'rating' ? 'selected' : ''; ?>>Mejor calificados</option>
                    <option value="downloads" <?php echo $orderBy === 'downloads' ? 'selected' : ''; ?>>Más descargados</option>
                    <option value="price_asc" <?php echo $orderBy === 'price_asc' ? 'selected' : ''; ?>>Precio ↑</option>
                    <option value="price_desc" <?php echo $orderBy === 'price_desc' ? 'selected' : ''; ?>>Precio ↓</option>
                </select>
            </div>

            <span class="filter-bar__count"><?php echo $total; ?> recursos</span>
        </div>

        <!-- Assets Grid -->
        <?php if(!empty($assets)): ?>
            <div class="asset-grid">
                <?php foreach($assets as $asset): ?>
                    <a href="/marketplace/asset?id=<?php echo $asset->id; ?>" class="asset-card" style="text-decoration:none;">
                        <div class="asset-card__preview">
                            <i class="fa-solid <?php echo $asset->category ? s($asset->category->icono) : 'fa-file'; ?>"></i>
                        </div>
                        <div class="asset-card__body">
                            <span class="asset-card__category">
                                <?php echo $asset->category ? s($asset->category->nombre) : ''; ?>
                            </span>
                            <h3 class="asset-card__title"><?php echo s($asset->titulo); ?></h3>
                            <p class="asset-card__desc"><?php echo s(substr($asset->descripcion, 0, 120)); ?></p>
                            <div class="asset-card__meta">
                                <div class="asset-card__author">
                                    <img src="/build/img/default-avatar.svg" alt="Avatar">
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

            <?php if($totalPages > 1): ?>
                <nav class="pagination">
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="/marketplace?page=<?php echo $i; ?>&category=<?php echo urlencode($categorySlug); ?>&faculty=<?php echo $facultyId; ?>&order=<?php echo $orderBy; ?>" 
                           class="pagination__link <?php echo $page === $i ? 'pagination__link--active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </nav>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon"><i class="fa-solid fa-store"></i></div>
                <h3 class="empty-state__title">Aún no hay recursos publicados</h3>
                <p class="empty-state__text">¡Sé el primero en compartir tus apuntes y contribuir a la comunidad!</p>
                <a href="/marketplace/subir" class="empty-state__action"><i class="fa-solid fa-plus"></i> Publicar Recurso</a>
            </div>
        <?php endif; ?>
    </div>
</div>
