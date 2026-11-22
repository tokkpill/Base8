<?php

declare(strict_types=1);

namespace Controllers;

use Model\Regalo;
use Model\Registro;

/**
 * Clase APIRegalos
 * 
 * Proporciona endpoints para consultar estadísticas de regalos.
 */
class APIRegalos {

    /**
     * Devuelve una lista de regalos con el total de veces que han sido 
     * elegidos en registros presenciales.
     * 
     * @return void
     */
    public static function index(): void {

        if(!is_admin()) {
            echo json_encode([]);
            return;
        }

        $regalos = Regalo::all();

        foreach($regalos as $regalo) {
            $regalo->total = Registro::totalArray(['regalo_id' => (string)$regalo->id, 'paquete_id' => "1"]);
        }

        echo json_encode($regalos);
    }
}