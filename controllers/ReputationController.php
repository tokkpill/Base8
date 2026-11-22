<?php

declare(strict_types=1);

namespace Controllers;

use Model\ReputationLog;
use Model\Usuario;
use MVC\Router;

/**
 * Clase ReputationController
 * 
 * Gestiona el sistema de reputación y prestigio.
 * Incluye leaderboard, perfiles de reputación y cálculos.
 */
class ReputationController
{
    /**
     * Leaderboard de prestigio
     */
    public static function leaderboard(Router $router): void
    {
        $topUsers = Usuario::getLeaderboard(20);
        $badgeThresholds = ReputationLog::getBadgeThresholds();

        $router->render('reputation/leaderboard', [
            'titulo' => 'Leaderboard — Base8',
            'topUsers' => $topUsers,
            'badgeThresholds' => $badgeThresholds,
        ]);
    }

    /**
     * Perfil de reputación de un usuario
     */
    public static function profile(Router $router): void
    {
        $id = (int)($_GET['id'] ?? 0);
        
        if (!$id && is_auth()) {
            $id = (int)$_SESSION['id'];
        }

        if (!$id) {
            header('Location: /login');
            exit;
        }

        $usuario = Usuario::find($id);
        if (!$usuario) {
            header('Location: /404');
            exit;
        }

        $history = ReputationLog::getUserHistory($id, 30);
        $badgeThresholds = ReputationLog::getBadgeThresholds();

        // Calcular progreso al siguiente nivel
        $currentPoints = $usuario->prestige_points;
        $nextBadge = '';
        $nextThreshold = 0;
        $progress = 100;

        foreach (array_reverse($badgeThresholds, true) as $badge => $threshold) {
            if ($threshold > $currentPoints) {
                $nextBadge = $badge;
                $nextThreshold = $threshold;
            }
        }

        if ($nextThreshold > 0) {
            // Encontrar el threshold actual
            $currentThreshold = 0;
            foreach ($badgeThresholds as $badge => $threshold) {
                if ($threshold <= $currentPoints && $threshold > $currentThreshold) {
                    $currentThreshold = $threshold;
                }
            }
            $range = $nextThreshold - $currentThreshold;
            $progress = $range > 0 ? (int)(($currentPoints - $currentThreshold) / $range * 100) : 0;
        }

        $router->render('reputation/profile', [
            'titulo' => $usuario->getNombreCompleto() . ' — Prestigio',
            'usuario' => $usuario,
            'history' => $history,
            'badgeThresholds' => $badgeThresholds,
            'nextBadge' => $nextBadge,
            'nextThreshold' => $nextThreshold,
            'progress' => min(100, max(0, $progress)),
        ]);
    }
}
