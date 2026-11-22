<?php

declare(strict_types=1);

namespace Controllers;

use Model\Dia;
use Model\Hora;
use MVC\Router;
use Model\Evento;
use Model\Ponente;
use Model\Categoria;

/**
 * Clase PaginasController
 * 
 * Gestiona la visualización de las páginas públicas del sitio web, 
 * incluyendo la página de inicio, información del evento y conferencias.
 */
class PaginasController {
    /**
     * Muestra la página de inicio con los eventos destacados y estadísticas
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function index(Router $router): void {

        $eventos = Evento::ordenar('hora_id', 'ASC');

        $eventos_formateados = [];
        foreach($eventos as $evento) {
            $evento->categoria = Categoria::find((int)$evento->categoria_id);
            $evento->dia = Dia::find((int)$evento->dia_id);
            $evento->hora = Hora::find((int)$evento->hora_id);
            $evento->ponente = Ponente::find((int)$evento->ponente_id);
            
            if((string)$evento->dia_id === "1" && (string)$evento->categoria_id === "1") {
                $eventos_formateados['conferencias_v'][] = $evento;
            }

            if((string)$evento->dia_id === "2" && (string)$evento->categoria_id === "1") {
                $eventos_formateados['conferencias_s'][] = $evento;
            }

            if((string)$evento->dia_id === "1" && (string)$evento->categoria_id === "2") {
                $eventos_formateados['workshops_v'][] = $evento;
            }

            if((string)$evento->dia_id === "2" && (string)$evento->categoria_id === "2") {
                $eventos_formateados['workshops_s'][] = $evento;
            }
        }

        // Obtener el total de cada bloque
        $ponentes_total = Ponente::total();
        $conferencias_total = Evento::total('categoria_id', '1');
        $workshops_total = Evento::total('categoria_id', '2');

        // Obtener todos los ponentes
        $ponentes = Ponente::all();

        $router->render('paginas/index', [
            'titulo' => 'Inicio',
            'eventos' => $eventos_formateados,
            'ponentes_total' => $ponentes_total,
            'conferencias_total' => $conferencias_total,
            'workshops_total' => $workshops_total,
            'ponentes' => $ponentes
        ]);
    }

    /**
     * Muestra la página informativa "Sobre Base8"
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function evento(Router $router): void {

        $router->render('paginas/devwebcamp', [
            'titulo' => 'Sobre Base8'
        ]);
    }
    
    /**
     * Muestra la página de paquetes y opciones de registro
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function paquetes(Router $router): void {

        $router->render('paginas/paquetes', [
            'titulo' => 'Paquetes Base8'
        ]);
    }

    /**
     * Muestra la agenda completa de conferencias y workshops
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function conferencias(Router $router): void {

        $eventos = Evento::ordenar('hora_id', 'ASC');

        $eventos_formateados = [];
        foreach($eventos as $evento) {
            $evento->categoria = Categoria::find((int)$evento->categoria_id);
            $evento->dia = Dia::find((int)$evento->dia_id);
            $evento->hora = Hora::find((int)$evento->hora_id);
            $evento->ponente = Ponente::find((int)$evento->ponente_id);
            
            if((string)$evento->dia_id === "1" && (string)$evento->categoria_id === "1") {
                $eventos_formateados['conferencias_v'][] = $evento;
            }

            if((string)$evento->dia_id === "2" && (string)$evento->categoria_id === "1") {
                $eventos_formateados['conferencias_s'][] = $evento;
            }

            if((string)$evento->dia_id === "1" && (string)$evento->categoria_id === "2") {
                $eventos_formateados['workshops_v'][] = $evento;
            }

            if((string)$evento->dia_id === "2" && (string)$evento->categoria_id === "2") {
                $eventos_formateados['workshops_s'][] = $evento;
            }
        }


        $router->render('paginas/conferencias', [
            'titulo' => 'Conferencias & Workshops',
            'eventos' => $eventos_formateados
        ]);
    }

    /**
     * Muestra la página de error 404
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function error(Router $router): void {

        $router->render('paginas/error', [
            'titulo' => 'Página no encontrada'
        ]);
    }
}