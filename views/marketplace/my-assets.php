<div style="padding-top: 9rem;">
    <div style="max-width: 140rem; margin: 0 auto; padding: 0 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">
            <div>
                <h1 style="font-size: 2.8rem; font-weight: 800; margin: 0 0 0.5rem;">Mis Recursos</h1>
                <p style="color: #94A3B8; margin: 0;">Gestiona los recursos que has publicado</p>
            </div>
            <a href="/marketplace/subir" class="asset-detail__buy-btn" style="display:inline-flex; width:auto;">
                <i class="fa-solid fa-plus"></i> Publicar Nuevo
            </a>
        </div>

        <!-- Revenue Card -->
        <div class="stat-card" style="max-width: 30rem; margin-bottom: 3rem;">
            <div class="stat-card__icon"><i class="fa-solid fa-coins"></i></div>
            <div class="stat-card__value" style="color: #CCFF00;">$<?php echo number_format($totalRevenue, 2); ?></div>
            <div class="stat-card__label">Ingresos Totales</div>
        </div>

        <?php if(!empty($assets)): ?>
            <div class="asset-grid">
                <?php foreach($assets as $asset): ?>
                    <div class="asset-card">
                        <div class="asset-card__preview">
                            <i class="fa-solid <?php echo $asset->category ? s($asset->category->icono) : 'fa-file'; ?>"></i>
                            <div class="asset-card__preview-badge">
                                <span class="prestige-badge badge--<?php echo $asset->estado === 'publicado' ? 'scholar' : 'novato'; ?>">
                                    <?php echo s(ucfirst($asset->estado)); ?>
                                </span>
                            </div>
                        </div>
                        <div class="asset-card__body">
                            <h3 class="asset-card__title"><?php echo s($asset->titulo); ?></h3>
                            <div class="asset-card__stats" style="margin-top: 0.5rem;">
                                <span><i class="fa-solid fa-download"></i> <?php echo $asset->descargas; ?></span>
                                <span><?php echo render_stars($asset->rating_promedio, $asset->total_ratings); ?></span>
                            </div>
                        </div>
                        <div class="asset-card__footer">
                            <?php echo format_price($asset->precio); ?>
                            <a href="/marketplace/asset?id=<?php echo $asset->id; ?>" class="asset-card__action" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                Ver <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon"><i class="fa-solid fa-cloud-arrow-up"></i></div>
                <h3 class="empty-state__title">No has publicado recursos aún</h3>
                <p class="empty-state__text">Comparte tus apuntes, guías y proyectos con la comunidad UAEMex</p>
                <a href="/marketplace/subir" class="empty-state__action"><i class="fa-solid fa-plus"></i> Publicar Recurso</a>
            </div>
        <?php endif; ?>
    </div>
</div>
