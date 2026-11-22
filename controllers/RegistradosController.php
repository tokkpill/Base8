<?php

declare(strict_types=1);

namespace Controllers;

use MVC\Router;
use Model\Registro;
use Classes\Paginacion;
use Model\Paquete;
use Model\Usuario;

/**
 * Clase RegistradosController
 * 
 * Gestiona la visualización de la lista de usuarios que han completado 
 * su registro en el evento.
 */
class RegistradosController {

    /**
     * Muestra la lista paginada de usuarios registrados
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function index(Router $router): void {
        if(!is_admin()) {
            header('Location: /login');
            exit;
        }
        
        $pagina_actual = $_GET['page'] ?? 1;
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);

        if(!$pagina_actual || $pagina_actual < 1) {
            header('Location: /admin/registrados?page=1');
            exit;
        }
        $registros_por_pagina = 10;
        $total = Registro::total();
        $paginacion = new Paginacion((int)$pagina_actual, $registros_por_pagina, $total);


        if($paginacion->total_paginas() < $pagina_actual) {
            header('Location: /admin/registrados?page=1');
            exit;
        } 
        
        $registros = Registro::paginar($registros_por_pagina, $paginacion->offset());
        foreach($registros as $registro) {
            $registro->usuario = Usuario::find((int)$registro->usuario_id);
            $registro->paquete = Paquete::find((int)$registro->paquete_id);
        }

        $router->render('admin/registrados/index', [
            'titulo' => 'Usuarios Registrados',
            'registros' => $registros,
            'paginacion' => $paginacion->paginacion()
        ]);
    }
}