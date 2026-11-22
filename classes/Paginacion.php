<?php 

namespace Classes;

/**
 * Clase Paginacion
 * 
 * Gestiona la lógica y generación de HTML para la paginación de resultados.
 */
class Paginacion {
    /** @var int Página actual en la que se encuentra el usuario */
    public $pagina_actual;

    /** @var int Cantidad de registros a mostrar por cada página */
    public $registros_por_pagina;

    /** @var int Total de registros existentes en la base de datos */
    public $total_registros;

    /**
     * Constructor de la clase Paginacion
     * 
     * @param int $pagina_actual La página que se desea visualizar
     * @param int $registros_por_pagina Cuántos elementos mostrar por página
     * @param int $total_registros El gran total de elementos para calcular las páginas
     */
    public function __construct($pagina_actual = 1, $registros_por_pagina = 10, $total_registros = 0 )
    {
        $this->pagina_actual = (int) $pagina_actual;
        $this->registros_por_pagina = (int) $registros_por_pagina;
        $this->total_registros = (int) $total_registros;
    }

    /**
     * Calcula el offset para la consulta SQL (desde dónde empezar)
     * 
     * @return int El valor del offset
     */
    public function offset() {
        return $this->registros_por_pagina * ($this->pagina_actual - 1);
    }

    /**
     * Calcula el total de páginas necesarias
     * 
     * @return int Número total de páginas
     */
    public function total_paginas() {
        $total = ceil($this->total_registros / $this->registros_por_pagina);
        $total == 0 ? $total = 1 : $total = $total;
        return $total;
    }

    /**
     * Obtiene el número de la página anterior
     * 
     * @return int|bool El número de la página anterior o false si no existe
     */
    public function pagina_anterior() {
        $anterior = $this->pagina_actual - 1;
        return ($anterior > 0) ? $anterior : false;
    }

    /**
     * Obtiene el número de la página siguiente
     * 
     * @return int|bool El número de la página siguiente o false si no existe
     */
    public function pagina_siguiente() {
        $siguiente = $this->pagina_actual + 1;
        return ($siguiente <= $this->total_paginas()) ? $siguiente : false;
    }

    /**
     * Genera el HTML para el enlace de "Anterior"
     * 
     * @return string El código HTML del enlace
     */
    public function enlace_anterior() {
        $html = '';
        if($this->pagina_anterior()) {
            $html .= "<a class=\"paginacion__enlace paginacion__enlace--texto\" href=\"?page={$this->pagina_anterior()}\">&laquo; Anterior </a>";
        }
        return $html;
    }

    /**
     * Genera el HTML para el enlace de "Siguiente"
     * 
     * @return string El código HTML del enlace
     */
    public function enlace_siguiente() {
        $html = '';
        if($this->pagina_siguiente()) {
            $html .= "<a class=\"paginacion__enlace paginacion__enlace--texto\" href=\"?page={$this->pagina_siguiente()}\">Siguiente &raquo;</a>";
        }
        return $html;
    }

    /**
     * Genera el HTML para la lista numerada de páginas
     * 
     * @return string El código HTML con los números de página
     */
    public function numeros_paginas() {
        $html = '';
        for($i = 1; $i <= $this->total_paginas(); $i++) {
            if($i === $this->pagina_actual ) {
                $html .= "<span class=\"paginacion__enlace paginacion__enlace--actual \">{$i}</span>";
            } else {
                $html .= "<a class=\"paginacion__enlace paginacion__enlace--numero \">{$i}</a>";
            }
        }

        return $html;
    }

    /**
     * Genera el HTML completo de la paginación
     * 
     * @return string El bloque HTML de paginación
     */
    public function paginacion() {
        $html = '';
        if($this->total_registros > 1) {
            $html .= '<div class="paginacion">';
            $html .= $this->enlace_anterior();
            $html .= $this->numeros_paginas();
            $html .= $this->enlace_siguiente();
            $html .= '</div>';
        }

        return $html;
    }

}