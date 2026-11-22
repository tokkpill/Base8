<?php

declare(strict_types=1);

namespace Controllers;
use Model\Ponente;

/**
 * Clase APIPonentes
 * 
 * Proporciona endpoints para la gestión y consulta de ponentes vía API.
 */
class APIPonentes {

    /**
     * Devuelve la lista completa de ponentes en formato JSON
     * 
     * @return void
     */
    public static function index(): void {
        $ponentes = Ponente::all();
        echo json_encode($ponentes);
    }

    /**
     * Devuelve la información de un ponente específico por su ID en formato JSON
     * 
     * @return void
     */
    public static function ponente(): void {
        $id = $_GET['id'] ?? '';
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if(!$id || $id < 1) {
            echo json_encode([]);
            return;
        }

        $ponente = Ponente::find((int)$id);
        echo json_encode($ponente, JSON_UNESCAPED_SLASHES);
    }
}