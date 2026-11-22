<div class="rep-profile">
    <!-- Profile Header -->
    <div class="rep-profile__header">
        <img src="<?php echo $usuario->getAvatarUrl(); ?>" alt="Avatar" class="rep-profile__avatar">
        
        <h1 class="rep-profile__name"><?php echo s($usuario->getNombreCompleto()); ?></h1>
        
        <div style="margin: 1rem 0;">
            <?php echo user_badge($usuario->reputation_badge, $usuario->prestige_points); ?>
        </div>

        <div class="rep-profile__points"><?php echo number_format($usuario->prestige_points, 1); ?></div>
        <p style="color: #64748B; font-size: 1.3rem; margin: 0;">Puntos de Prestigio</p>

        <!-- Progress to next badge -->
        <?php if($nextBadge): ?>
            <div class="rep-profile__progress">
                <div class="rep-profile__progress-bar">
                    <div class="rep-profile__progress-bar-fill" style="width: <?php echo $progress; ?>%;"></div>
                </div>
                <div class="rep-profile__progress-label">
                    <span><?php echo s($usuario->reputation_badge); ?></span>
                    <span><?php echo s($nextBadge); ?> (<?php echo $nextThreshold; ?> pts)</span>
                </div>
            </div>
        <?php else: ?>
            <p style="color: #CCFF00; font-size: 1.3rem; margin-top: 1rem;">
                <i class="fa-solid fa-crown"></i> ¡Nivel máximo alcanzado!
            </p>
        <?php endif; ?>
    </div>

    <!-- Prestige Formula -->
    <div class="review-card" style="margin-bottom: 2rem;">
        <h3 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 1.5rem;">
            <i class="fa-solid fa-calculator" style="color: #10B981;"></i> Cómo se calcula tu prestigio
        </h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(18rem, 1fr)); gap: 1.5rem;">
            <div style="text-align: center; padding: 1.5rem; background: rgba(16,185,129,0.08); border-radius: 0.75rem;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📥</div>
                <div style="font-size: 1.3rem; font-weight: 600; color: #F1F5F9;">Descargas × 0.4</div>
                <div style="font-size: 1.1rem; color: #64748B;">Tus recursos descargados</div>
            </div>
            <div style="text-align: center; padding: 1.5rem; background: rgba(204,255,0,0.08); border-radius: 0.75rem;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">⭐</div>
                <div style="font-size: 1.3rem; font-weight: 600; color: #F1F5F9;">Rating × 0.6</div>
                <div style="font-size: 1.1rem; color: #64748B;">Calificación promedio</div>
            </div>
            <div style="text-align: center; padding: 1.5rem; background: rgba(59,130,246,0.08); border-radius: 0.75rem;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎓</div>
                <div style="font-size: 1.3rem; font-weight: 600; color: #F1F5F9;">Contribuciones × 1.5</div>
                <div style="font-size: 1.1rem; color: #64748B;">Recursos verificados</div>
            </div>
        </div>
    </div>

    <!-- Activity History -->
    <div class="rep-profile__history">
        <h3 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 2rem;">
            <i class="fa-solid fa-clock-rotate-left" style="color: #10B981;"></i> Historial de Actividad
        </h3>

        <?php if(!empty($history)): ?>
            <?php foreach($history as $event): ?>
                <div class="rep-profile__event">
                    <div>
                        <span class="rep-profile__event-desc"><?php echo s($event->descripcion ?? 'Evento de reputación'); ?></span>
                        <span class="rep-profile__event-date" style="display: block; margin-top: 0.3rem;">
                            <?php echo s($event->tipo); ?>
                        </span>
                    </div>
                    <span class="rep-profile__event-points">
                        +<?php echo number_format($event->puntos, 1); ?> pts
                    </span>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="padding: 3rem;">
                <div class="empty-state__icon"><i class="fa-solid fa-seedling"></i></div>
                <h3 class="empty-state__title">Sin actividad aún</h3>
                <p class="empty-state__text">Publica recursos y participa en la comunidad para ganar puntos</p>
            </div>
        <?php endif; ?>
    </div>
</div>
