<?php

declare(strict_types=1);

namespace MVC;

/**
 * Clase Router
 * 
 * Se encarga de gestionar las rutas de la aplicación, tanto GET como POST,
 * y de renderizar las vistas correspondientes.
 */
class Router
{
    /** @var array Almacena las rutas GET y sus funciones asociadas */
    public array $getRoutes = [];

    /** @var array Almacena las rutas POST y sus funciones asociadas */
    public array $postRoutes = [];

    /**
     * Registra una ruta GET
     * 
     * @param string $url La URL de la ruta
     * @param callable $fn La función o controlador que manejará la ruta
     * @return void
     */
    public function get(string $url, callable $fn): void
    {
        $this->getRoutes[$url] = $fn;
    }

    /**
     * Registra una ruta POST
     * 
     * @param string $url La URL de la ruta
     * @param callable $fn La función o controlador que manejará la ruta
     * @return void
     */
    public function post(string $url, callable $fn): void
    {
        $this->postRoutes[$url] = $fn;
    }

    /**
     * Comprueba las rutas registradas contra la URL actual
     * 
     * Verifica si existe una función asociada para la URL y el método HTTP actual.
     * Si no existe, redirecciona a una página 404.
     * 
     * @return void
     */
    public function comprobarRutas(): void
    {
        $url_actual = $_SERVER['PATH_INFO'] ?? '/';
        $method = $_SERVER['REQUEST_METHOD'];

        if ($method === 'GET') {
            $fn = $this->getRoutes[$url_actual] ?? null;
        } else {
            $fn = $this->postRoutes[$url_actual] ?? null;
        }

        if ( $fn ) {
            call_user_func($fn, $this);
        } else {
            header('Location: /404');
        }
    }

    /**
     * Renderiza una vista con los datos proporcionados
     * 
     * @param string $view El nombre de la vista a renderizar
     * @param array $datos Los datos que se pasarán a la vista (opcional)
     * @return void
     */
    public function render(string $view, array $datos = []): void
    {
        foreach ($datos as $key => $value) {
            $$key = $value; 
        }

        ob_start(); 

        include_once __DIR__ . "/views/$view.php";

        $contenido = ob_get_clean(); // Limpia el Buffer

        // Utilizar el Layout de acuerdo a la URL
        $url_actual = $_SERVER['PATH_INFO'] ?? '/';

        if(str_contains($url_actual, '/admin')) {
            include_once __DIR__ . '/views/admin-layout.php';
        } else {
            include_once __DIR__ . '/views/layout.php';
        }
    }
}

