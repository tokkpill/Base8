<?php

declare(strict_types=1);

namespace Controllers;

use Model\Dia;
use Model\Hora;
use MVC\Router;
use Model\Evento;
use Model\Paquete;
use Model\Ponente;
use Model\Usuario;
use Model\Registro;
use Model\Categoria;
use Model\EventosRegistros;
use Model\Regalo;

/**
 * Clase RegistroController
 * 
 * Gestiona el proceso de elección de paquetes, pago y selección 
 * de conferencias para los asistentes.
 */
class RegistroController {

    /**
     * Muestra las opciones de paquetes disponibles para el registro
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function crear(Router $router): void {

        if(!is_auth()) {
            header('Location: /');
            exit;
        }

        // Verificar si el usuario ya esta registrado
        $registro = Registro::where('usuario_id', (string)$_SESSION['id']);

        if(isset($registro) && ((string)$registro->paquete_id === "3" || (string)$registro->paquete_id === "2" )) {
            header('Location: /boleto?id=' . urlencode($registro->token));
            exit;
        }

        if(isset($registro) && (string)$registro->paquete_id === "1") {
            header('Location: /finalizar-registro/conferencias');
            exit;
        }

        $router->render('registro/crear', [
            'titulo' => 'Finalizar Registro'
        ]);
    }

    /**
     * Procesa el registro para el plan gratuito (pase virtual)
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function gratis(Router $router): void {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(!is_auth()) {
                header('Location: /login');
                exit;
            }

            // Verificar si el usuario ya esta registrado
            $registro = Registro::where('usuario_id', (string)$_SESSION['id']);
            if(isset($registro) && (string)$registro->paquete_id === "3") {
                header('Location: /boleto?id=' . urlencode($registro->token));
                exit;
            }

            $token = substr(md5((string)uniqid((string)rand(), true)), 0, 8);
            
            // Crear registro
            $datos = [
                'paquete_id' => 3,
                'pago_id' => '',
                'token' => $token,
                'usuario_id' => $_SESSION['id']
            ];

            $registro = new Registro($datos);
            $resultado = $registro->guardar();

            if($resultado) {
                header('Location: /boleto?id=' . urlencode($registro->token));
                exit;
            }

        }
    }

    /**
     * Muestra el boleto virtual del usuario registrado
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function boleto(Router $router): void {

        // Validar la URL
        $id = $_GET['id'] ?? '';

        if(!$id || strlen($id) !== 8 ) {
            header('Location: /');
            exit;
        }

        // buscarlo en la BD
        $registro = Registro::where('token', $id);
        if(!$registro) {
            header('Location: /');
            exit;
        }
        // Llenar las tablas de referencia
        $registro->usuario = Usuario::find((int)$registro->usuario_id);
        $registro->paquete = Paquete::find((int)$registro->paquete_id);

        $router->render('registro/boleto', [
            'titulo' => 'Asistencia a DevWebCamp',
            'registro' => $registro
        ]);
    }

    /**
     * Procesa el pago de los paquetes (presencial o virtual pagado)
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function pagar(Router $router): void {

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if(!is_auth()) {
                header('Location: /login');
                exit;
            }

            // Validar que Post no venga vacio
            if(empty($_POST)) {
                echo json_encode([]);
                return;
            }

            // Crear el registro
            $datos = $_POST;
            $datos['token'] = substr(md5((string)uniqid((string)rand(), true)), 0, 8);
            $datos['usuario_id'] = $_SESSION['id'];
            
            try {
                $registro = new Registro($datos);
          
                $resultado = $registro->guardar();
                echo json_encode($resultado);
            } catch (\Throwable $th) {
                echo json_encode([
                    'resultado' => 'error'
                ]);
            }

        }
    }

    /**
     * Muestra la interfaz para que el usuario elija sus conferencias y workshops
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function conferencias(Router $router): void {

        if(!is_auth()) {
            header('Location: /login');
            exit;
        }        

        // Validar que el usuario tenga el plan presencial
        $usuario_id = $_SESSION['id'];
        $registro = Registro::where('usuario_id', (string)$usuario_id);

        if(isset($registro) && (string)$registro->paquete_id === "2") {
            header('Location: /boleto?id=' . urlencode($registro->token));
            exit;
        }
        
        if(!$registro || (string)$registro->paquete_id !== "1") {
            header('Location: /');
            exit;
        }

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
        
        $regalos = Regalo::all('ASC');

        // Manejando el registro mediante $_POST
        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Revisar que el usuario este autenticado
            if(!is_auth()) {
                header('Location: /login');
                exit;
            }

            $eventos_ids = explode(',', $_POST['eventos'] ?? '');
            if(empty($eventos_ids)) {
                echo json_encode(['resultado' => false]);
                return;
            }

            // Obtener el registro de usuario
            $registro = Registro::where('usuario_id', (string)$_SESSION['id']);
            if(!isset($registro) || (string)$registro->paquete_id !== "1") {
                echo json_encode(['resultado' => false]);
                return;
            }

            $eventos_array = [];
            // Validar la disponibilidad de los eventos seleccionados
            foreach($eventos_ids as $evento_id) {
                $evento = Evento::find((int)$evento_id);
                // Comprobar que el evento exista
                if(!isset($evento) || (int)$evento->disponibles === 0) {
                    echo json_encode(['resultado' => false]);
                    return;
                }
                $eventos_array[] = $evento;
            }

            foreach($eventos_array as $evento) {
                $evento->disponibles -= 1;
                $evento->guardar();

                // Almacenar el registro
                $datos_evento = [
                    'evento_id' =>  (int) $evento->id,
                    'registro_id' => (int)  $registro->id
                ];

                $registro_usuario = new EventosRegistros($datos_evento);
                $registro_usuario->guardar();
            }

            // Almacenar el regalo
            $registro->sincronizar(['regalo_id' => $_POST['regalo_id'] ?? 1]);
            $resultado = $registro->guardar();

            if($resultado) {
                echo json_encode([
                    'resultado' => $resultado, 
                    'token' => $registro->token
                ]);
            } else {
                echo json_encode(['resultado' => false]);
            }

            return;
        }


        $router->render('registro/conferencias', [
            'titulo' => 'Elige Workshops y Conferencias',
            'eventos' => $eventos_formateados,
            'regalos' => $regalos
        ]);
    }
}