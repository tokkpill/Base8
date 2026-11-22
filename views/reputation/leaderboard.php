<div style="padding-top: 9rem;">
    <div style="max-width: 80rem; margin: 0 auto; padding: 0 2rem;">
        <div class="section__header" style="margin-bottom: 4rem;">
            <h1 class="section__title"><i class="fa-solid fa-crown" style="color: #CCFF00;"></i> Leaderboard de Prestigio</h1>
            <p class="section__subtitle">Los miembros más valiosos de la comunidad Base8</p>
        </div>

        <!-- Badge Legend -->
        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem; margin-bottom: 3rem;">
            <?php foreach($badgeThresholds as $badge => $threshold): ?>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <?php echo user_badge($badge); ?>
                    <span style="font-size: 1.2rem; color: #64748B;"><?php echo $threshold; ?>+ pts</span>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if(!empty($topUsers)): ?>
            <div class="leaderboard">
                <table class="leaderboard__table">
                    <tbody>
                        <?php foreach($topUsers as $index => $user): ?>
                            <tr class="leaderboard__row">
                                <td class="leaderboard__rank <?php 
                                    echo match($index) {
                                        0 => 'leaderboard__rank--gold',
                                        1 => 'leaderboard__rank--silver',
                                        2 => 'leaderboard__rank--bronze',
                                        default => ''
                                    };
                                ?>">
                                    <?php if($index < 3): ?>
                                        <i class="fa-solid <?php echo match($index) { 0 => 'fa-trophy', 1 => 'fa-medal', 2 => 'fa-award', default => '' }; ?>"></i>
                                    <?php else: ?>
                                        #<?php echo $index + 1; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="leaderboard__user">
                                        <img src="/build/img/default-avatar.svg" alt="Avatar">
                                        <div>
                                            <div class="leaderboard__name">
                                                <?php echo s($user->getNombreCompleto()); ?>
                                            </div>
                                            <?php echo user_badge($user->reputation_badge); ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="leaderboard__points">
                                    <?php echo number_format($user->prestige_points, 1); ?> pts
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-state__icon"><i class="fa-solid fa-crown"></i></div>
                <h3 class="empty-state__title">El leaderboard está vacío</h3>
                <p class="empty-state__text">¡Sé el primero en ganar puntos de prestigio publicando y compartiendo recursos!</p>
                <a href="/marketplace/subir" class="empty-state__action"><i class="fa-solid fa-plus"></i> Publicar Recurso</a>
            </div>
        <?php endif; ?>
    </div>
</div>
