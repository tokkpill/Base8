<?php

declare(strict_types=1);

namespace Controllers;

use Model\AdvisorProfile;
use Model\Usuario;
use Model\Faculty;
use MVC\Router;

/**
 * Clase AdvisorController
 * 
 * Gestión de perfiles de asesores y sistema de reservaciones.
 */
class AdvisorController
{
    /**
     * Grid de asesores disponibles
     */
    public static function index(Router $router): void
    {
        $advisors = AdvisorProfile::getActive(20);

        $router->render('advisor/index', [
            'titulo' => 'Asesores — Base8',
            'advisors' => $advisors,
        ]);
    }

    /**
     * Perfil individual de un asesor
     */
    public static function profile(Router $router): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: /asesores');
            exit;
        }

        $advisor = AdvisorProfile::find($id);
        if (!$advisor || !$advisor->activo) {
            header('Location: /asesores');
            exit;
        }

        $advisor->usuario = Usuario::find($advisor->usuario_id);
        $faculty = null;
        if ($advisor->usuario && $advisor->usuario->faculty_id) {
            $faculty = Faculty::find($advisor->usuario->faculty_id);
        }

        $router->render('advisor/profile', [
            'titulo' => ($advisor->usuario ? $advisor->usuario->getNombreCompleto() : 'Asesor') . ' — Base8',
            'advisor' => $advisor,
            'faculty' => $faculty,
        ]);
    }
}
