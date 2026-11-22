<div style="padding-top: 9rem;">
    <div style="max-width: 120rem; margin: 0 auto; padding: 0 2rem;">
        <div class="section__header">
            <h1 class="section__title"><i class="fa-solid fa-chalkboard-user" style="color: #10B981;"></i> Asesores Académicos</h1>
            <p class="section__subtitle">Conecta con expertos que te ayudarán a alcanzar tus metas</p>
        </div>

        <?php if(!empty($advisors)): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(28rem, 1fr)); gap: 2rem;">
                <?php foreach($advisors as $advisor): ?>
                    <div class="advisor-card">
                        <img src="/build/img/default-avatar.svg" alt="Avatar" class="advisor-card__avatar">
                        
                        <h3 class="advisor-card__name">
                            <?php echo $advisor->usuario ? s($advisor->usuario->getNombreCompleto()) : 'Asesor'; ?>
                        </h3>

                        <?php if($advisor->usuario): ?>
                            <p class="advisor-card__faculty">
                                <?php echo user_badge($advisor->usuario->reputation_badge, $advisor->usuario->prestige_points); ?>
                            </p>
                        <?php endif; ?>

                        <div class="advisor-card__specialties">
                            <?php foreach($advisor->getEspecialidadesArray() as $spec): ?>
                                <li><?php echo s($spec); ?></li>
                            <?php endforeach; ?>
                        </div>

                        <div class="advisor-card__metrics">
                            <div class="advisor-card__metric">
                                <span class="advisor-card__metric-value"><?php echo $advisor->estudiantes_ayudados; ?></span>
                                <span class="advisor-card__metric-label">Estudiantes</span>
                            </div>
                            <div class="advisor-card__metric">
                                <span class="advisor-card__metric-value"><?php echo number_format($advisor->rating_promedio, 1); ?></span>
                                <span class="advisor-card__metric-label">Rating</span>
                            </div>
                            <div class="advisor-card__metric">
                                <span class="advisor-card__metric-value"><?php echo $advisor->total_sesiones; ?></span>
                                <span class="advisor-card__metric-label">Sesiones</span>
                            </div>
                        </div>

                        <p class="advisor-card__rate">
                            <i class="fa-solid fa-coins"></i> $<?php echo number_format($advisor->tarifa_hora, 2); ?>/hora
                        </p>

                        <a href="/asesores/perfil?id=<?php echo $advisor->id; ?>" class="advisor-card__cta">
                            Ver Perfil <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                <h3 class="empty-state__title">No hay asesores disponibles</h3>
                <p class="empty-state__text">¡Pronto se unirán expertos de toda la universidad!</p>
            </div>
        <?php endif; ?>
    </div>
</div>
