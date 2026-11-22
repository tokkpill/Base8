<?php

declare(strict_types=1);

namespace Controllers;

use MVC\Router;

/**
 * Clase RegalosController
 * 
 * Gestiona la visualización de la sección de regalos en el panel de administración.
 */
class RegalosController {

    /**
     * Muestra la vista de listado de regalos
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function index(Router $router): void {
        if(!is_admin()) {
            header('Location: /login');
            exit;
        }

        $router->render('admin/regalos/index', [
            'titulo' => 'Regalos'
        ]);
    }
}