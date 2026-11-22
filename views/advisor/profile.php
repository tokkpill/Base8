<div style="padding-top: 9rem;">
    <div style="max-width: 80rem; margin: 0 auto; padding: 0 2rem;">
        
        <!-- Profile Header -->
        <div class="rep-profile__header" style="margin-bottom: 3rem;">
            <img src="/build/img/default-avatar.svg" alt="Avatar" class="rep-profile__avatar">
            
            <h1 class="rep-profile__name">
                <?php echo $advisor->usuario ? s($advisor->usuario->getNombreCompleto()) : 'Asesor'; ?>
            </h1>
            
            <?php if($advisor->usuario): ?>
                <div style="margin-bottom: 1rem;">
                    <?php echo user_badge($advisor->usuario->reputation_badge, $advisor->usuario->prestige_points); ?>
                </div>
            <?php endif; ?>

            <?php if($faculty): ?>
                <p style="color: #94A3B8; font-size: 1.4rem;">
                    <i class="fa-solid fa-building-columns"></i> <?php echo s($faculty->nombre); ?>
                </p>
            <?php endif; ?>

            <div class="advisor-card__metrics" style="justify-content: center; max-width: 40rem; margin: 2rem auto;">
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
                <div class="advisor-card__metric">
                    <span class="advisor-card__metric-value" style="color: #CCFF00;">$<?php echo number_format($advisor->tarifa_hora, 0); ?></span>
                    <span class="advisor-card__metric-label">Por Hora</span>
                </div>
            </div>
        </div>

        <!-- Description -->
        <div class="review-card" style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 1rem;">Acerca de</h3>
            <p style="color: #94A3B8; font-size: 1.5rem; line-height: 1.8;">
                <?php echo nl2br(s($advisor->descripcion)); ?>
            </p>
        </div>

        <!-- Specialties -->
        <div class="review-card" style="margin-bottom: 2rem;">
            <h3 style="font-size: 1.6rem; font-weight: 700; margin-bottom: 1rem;">Especialidades</h3>
            <div class="advisor-card__specialties" style="justify-content: flex-start;">
                <?php foreach($advisor->getEspecialidadesArray() as $spec): ?>
                    <li><?php echo s($spec); ?></li>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- CTA -->
        <div class="review-card" style="text-align: center;">
            <p style="font-size: 1.5rem; color: #94A3B8; margin-bottom: 1.5rem;">
                ¿Necesitas ayuda con tu proyecto o materia?
            </p>
            <p style="font-size: 2.4rem; font-weight: 800; color: #CCFF00; margin-bottom: 1.5rem;">
                $<?php echo number_format($advisor->tarifa_hora, 2); ?> MXN / hora
            </p>
            <?php if(is_auth()): ?>
                <button class="advisor-card__cta" onclick="alert('Función de reservación próximamente. Contacta al asesor directamente.')">
                    <i class="fa-solid fa-calendar-check"></i> Agendar Sesión
                </button>
            <?php else: ?>
                <a href="/login" class="advisor-card__cta">
                    <i class="fa-solid fa-right-to-bracket"></i> Inicia Sesión para Reservar
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>
