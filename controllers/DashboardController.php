<?php

declare(strict_types=1);

namespace Controllers;

use Model\Evento;
use Model\Registro;
use Model\Usuario;
use MVC\Router;

/**
 * Clase DashboardController
 * 
 * Gestiona la visualización del panel de administración, 
 * mostrando estadísticas globales y registros recientes.
 */
class DashboardController {

    /**
     * Muestra el panel de administración con estadísticas y métricas clave
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function index(Router $router): void {
        if(!is_admin()) {
            header('Location: /login');
            exit;
        }

        // Obtener ultimos registros
        $registros = Registro::get(5);
        foreach($registros as $registro) {
            $registro->usuario = Usuario::find((int)($registro->usuario_id ?? 0));
        }

        // Calcular los ingresos
        $virtuales = Registro::total('paquete_id', '2');
        $presenciales = Registro::total('paquete_id', '1');

        $ingresos = ((int)$virtuales * 46.41) + ((int)$presenciales * 189.54);

        // Obtener eventos con más y menos lugares disponibles
        $menos_disponibles = Evento::ordenarLimite('disponibles', 'ASC', 5);
        $mas_disponibles = Evento::ordenarLimite('disponibles', 'DESC', 5);


        $router->render('admin/dashboard/index', [
            'titulo' => 'Panel de Administración',
            'registros' => $registros,
            'ingresos' => $ingresos,
            'menos_disponibles' => $menos_disponibles,
            'mas_disponibles' => $mas_disponibles
        ]);
    }
}