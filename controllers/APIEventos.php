<?php

declare(strict_types=1);

namespace Controllers;

use Model\EventoHorario;

/**
 * Clase APIEventos
 * 
 * Proporciona endpoints para consultar información de eventos.
 */
class APIEventos {

    /**
     * Devuelve una lista de eventos filtrados por día y categoría en formato JSON
     * 
     * @return void
     */
    public static function index(): void {

        $dia_id = $_GET['dia_id'] ?? '';
        $categoria_id = $_GET['categoria_id'] ?? '';

        $dia_id = filter_var($dia_id, FILTER_VALIDATE_INT);
        $categoria_id = filter_var($categoria_id, FILTER_VALIDATE_INT);

        if(!$dia_id || !$categoria_id) {
           echo json_encode([]);
           return;
        }

        // Consultar la base de datos
        $eventos = EventoHorario::whereArray(['dia_id' => (string)$dia_id, 'categoria_id' => (string)$categoria_id]) ?? [];
        echo json_encode($eventos);
    }
}