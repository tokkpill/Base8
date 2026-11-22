<div class="asset-detail">
    <div class="asset-detail__main">
        <div class="asset-detail__main-card">
            <span class="asset-card__category" style="font-size: 1.2rem;">
                <i class="fa-solid <?php echo $asset->category ? s($asset->category->icono) : 'fa-file'; ?>"></i>
                <?php echo $asset->category ? s($asset->category->nombre) : 'Recurso'; ?>
            </span>
            <h1 class="asset-detail__title"><?php echo s($asset->titulo); ?></h1>

            <div class="asset-detail__meta">
                <?php echo render_stars($asset->rating_promedio, $asset->total_ratings); ?>
                <span class="asset-detail__meta-item">
                    <i class="fa-solid fa-download"></i> <?php echo $asset->descargas; ?> descargas
                </span>
                <span class="asset-detail__meta-item">
                    <i class="fa-solid fa-building-columns"></i> <?php echo $asset->faculty ? s($asset->faculty->nombre) : ''; ?>
                </span>
                <?php if($asset->career): ?>
                    <span class="asset-detail__meta-item">
                        <i class="fa-solid fa-graduation-cap"></i> <?php echo s($asset->career->nombre); ?>
                    </span>
                <?php endif; ?>
                <span class="asset-detail__meta-item">
                    <i class="fa-solid fa-file"></i> <?php echo strtoupper($asset->formato); ?>
                </span>
            </div>

            <div class="asset-detail__description">
                <?php echo nl2br(s($asset->descripcion)); ?>
            </div>

            <!-- Reviews -->
            <section style="margin-top: 3rem;">
                <h3 style="font-size: 2rem; font-weight: 700; margin-bottom: 2rem;">
                    <i class="fa-solid fa-star" style="color: #CCFF00;"></i> Reseñas (<?php echo count($reviews); ?>)
                </h3>

                <?php if(!empty($reviews)): ?>
                    <?php foreach($reviews as $review): ?>
                        <div class="review-card">
                            <div class="review-card__header">
                                <img src="/build/img/default-avatar.svg" alt="Avatar" class="review-card__avatar">
                                <div>
                                    <div class="review-card__author">
                                        <?php echo $review->usuario ? s($review->usuario->nombre . ' ' . $review->usuario->apellido) : 'Usuario'; ?>
                                    </div>
                                    <?php echo render_stars((float)$review->rating); ?>
                                </div>
                            </div>
                            <?php if($review->comentario): ?>
                                <div class="review-card__body">
                                    <?php echo nl2br(s($review->comentario)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #64748B;">Aún no hay reseñas para este recurso.</p>
                <?php endif; ?>

                <!-- Review Form -->
                <?php if($hasPurchased && !$hasReviewed): ?>
                    <div style="margin-top: 2rem;">
                        <h4 style="font-size: 1.6rem; margin-bottom: 1rem;">Deja tu reseña</h4>
                        <form method="POST" action="/review/crear" class="formulario" style="padding: 2rem;">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="asset_id" value="<?php echo $asset->id; ?>">
                            
                            <div class="formulario__campo">
                                <label class="formulario__label">Calificación</label>
                                <select name="rating" class="formulario__select">
                                    <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                                    <option value="4">⭐⭐⭐⭐ Muy bueno</option>
                                    <option value="3">⭐⭐⭐ Bueno</option>
                                    <option value="2">⭐⭐ Regular</option>
                                    <option value="1">⭐ Malo</option>
                                </select>
                            </div>

                            <div class="formulario__campo">
                                <label class="formulario__label">Comentario (opcional)</label>
                                <textarea name="comentario" class="formulario__textarea" placeholder="¿Qué te pareció este recurso?"></textarea>
                            </div>

                            <input type="submit" class="formulario__submit" value="Enviar Reseña">
                        </form>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="asset-detail__sidebar">
        <div class="asset-detail__sidebar-card">
            <div class="asset-detail__price">
                <?php echo $asset->precio > 0 ? '$' . number_format($asset->precio, 2) . ' MXN' : 'Gratis'; ?>
            </div>

            <?php if(is_auth() && (int)$_SESSION['id'] !== $asset->autor_id): ?>
                <?php if($hasPurchased): ?>
                    <a href="/uploads/assets/<?php echo s($asset->archivo); ?>" class="asset-detail__buy-btn" style="background: #22C55E;" download>
                        <i class="fa-solid fa-download"></i> Descargar
                    </a>
                <?php else: ?>
                    <form method="POST" action="/marketplace/comprar">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="asset_id" value="<?php echo $asset->id; ?>">
                        <button type="submit" class="asset-detail__buy-btn">
                            <i class="fa-solid fa-cart-shopping"></i> 
                            <?php echo $asset->precio > 0 ? 'Comprar Ahora' : 'Obtener Gratis'; ?>
                        </button>
                    </form>
                <?php endif; ?>
            <?php elseif(!is_auth()): ?>
                <a href="/login" class="asset-detail__buy-btn">
                    <i class="fa-solid fa-right-to-bracket"></i> Inicia Sesión para Obtener
                </a>
            <?php else: ?>
                <p style="color: #64748B; text-align:center; font-size:1.3rem;">Este es tu recurso</p>
            <?php endif; ?>

            <!-- Author Info -->
            <?php if($asset->autor): ?>
                <div class="asset-detail__author-card">
                    <img src="/build/img/default-avatar.svg" alt="Avatar del autor">
                    <div>
                        <div class="asset-detail__author-name">
                            <?php echo s($asset->autor->nombre . ' ' . $asset->autor->apellido); ?>
                        </div>
                        <div class="asset-detail__author-faculty" style="margin-top:0.3rem;">
                            <?php echo user_badge($asset->autor->reputation_badge, $asset->autor->prestige_points); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Similar Assets -->
        <?php if(!empty($similar)): ?>
            <div style="margin-top: 2rem;">
                <h4 style="font-size: 1.5rem; margin-bottom: 1.5rem; font-weight: 700;">Recursos Similares</h4>
                <?php foreach($similar as $sim): ?>
                    <a href="/marketplace/asset?id=<?php echo $sim->id; ?>" style="display:flex; gap:1rem; padding:1rem; border-radius: 0.75rem; margin-bottom:0.8rem; text-decoration:none; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); transition: all 150ms ease;" onmouseover="this.style.borderColor='rgba(16,185,129,0.3)'" onmouseout="this.style.borderColor='rgba(255,255,255,0.05)'">
                        <div style="width: 4rem; height: 4rem; display: flex; align-items: center; justify-content: center; background: rgba(16,185,129,0.1); border-radius: 0.5rem; flex-shrink: 0;">
                            <i class="fa-solid fa-file" style="color: #10B981;"></i>
                        </div>
                        <div>
                            <p style="font-size: 1.3rem; font-weight: 600; color: #F1F5F9; margin: 0; line-height: 1.3;"><?php echo s(substr($sim->titulo, 0, 50)); ?></p>
                            <span style="font-size: 1.1rem; color: #64748B;"><?php echo format_price($sim->precio); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
