<?php

declare(strict_types=1);

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

/**
 * Clase AuthController
 * 
 * Gestiona los procesos de autenticación, registro, confirmación de cuenta
 * y recuperación de contraseña.
 */
class AuthController {
    /**
     * Maneja el inicio de sesión de los usuarios
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function login(Router $router): void {

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST') {
    
            $usuario = new Usuario($_POST);

            $alertas = $usuario->validarLogin();
            
            if(empty($alertas)) {
                // Verificar quel el usuario exista
                $usuario = Usuario::where('email', $usuario->email);
                if(!$usuario || !$usuario->confirmado ) {
                    Usuario::setAlerta('error', 'El Usuario No Existe o no esta confirmado');
                } else {
                    // El Usuario existe
                    if( password_verify($_POST['password'] ?? '', $usuario->password) ) {
                        
                        // Iniciar la sesión
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['apellido'] = $usuario->apellido;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['admin'] = $usuario->admin ?? null;

                        // Redirección 
                        if($usuario->admin) {
                            header('Location: /admin/dashboard');
                        } else {
                            header('Location: /finalizar-registro');
                        }
                        exit;
                        
                    } else {
                        Usuario::setAlerta('error', 'Password Incorrecto');
                    }
                }
            }
        }

        $alertas = Usuario::getAlertas();
        
        // Render a la vista 
        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    /**
     * Cierra la sesión del usuario actual
     * 
     * @return void
     */
    public static function logout(): void {
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION = [];
            header('Location: /');
            exit;
        }
    }

    /**
     * Maneja el registro de nuevos usuarios
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function registro(Router $router): void {
        $alertas = [];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST') {

            $usuario->sincronizar($_POST);
            
            $alertas = $usuario->validar_cuenta();

            if(empty($alertas)) {
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario) {
                    Usuario::setAlerta('error', 'El Usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                } else {
                    // Hashear el password
                    $usuario->hashPassword();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Generar el Token
                    $usuario->crearToken();

                    // Crear un nuevo usuario
                    $resultado =  $usuario->guardar();

                    // Enviar email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmacion();
                    

                    if($resultado) {
                        header('Location: /mensaje');
                        exit;
                    }
                }
            }
        }

        // Render a la vista
        $router->render('auth/registro', [
            'titulo' => 'Crea tu cuenta en Base8',
            'usuario' => $usuario, 
            'alertas' => $alertas
        ]);
    }

    /**
     * Maneja la solicitud de recuperación de contraseña
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function olvide(Router $router): void {
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)) {
                // Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado) {

                    // Generar un nuevo token
                    $usuario->crearToken();
                    
                    unset($usuario->password2);

                    // Actualizar el usuario
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email( (string)$usuario->email, (string)$usuario->nombre, (string)$usuario->token );
                    $email->enviarInstrucciones();

                    $alertas['exito'][] = 'Hemos enviado las instrucciones a tu email';
                } else {
                    $alertas['error'][] = 'El Usuario no existe o no esta confirmado';
                }
            }
        }


        // Muestra la vista
        $router->render('auth/olvide', [
            'titulo' => 'Olvide mi Password',
            'alertas' => $alertas
        ]);
    }

    /**
     * Maneja el proceso de reestablecer la contraseña mediante un token
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function reestablecer(Router $router): void {

        $token = s($_GET['token'] ?? '');

        $token_valido = true;

        if(!$token) {
            header('Location: /');
            exit;
        }

        // Identificar el usuario con este token
        $usuario = Usuario::where('token', $token);

        if(!$usuario) {
            Usuario::setAlerta('error', 'Token No Válido, intenta de nuevo');
            $token_valido = false;
        }


        if($_SERVER['REQUEST_METHOD'] === 'POST' && $usuario) {

            // Añadir el nuevo password
            $usuario->sincronizar($_POST);

            // Validar el password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)) {
                // Hashear el nuevo password
                $usuario->hashPassword();

                // Eliminar el Token
                $usuario->token = null;

                // Guardar el usuario en la BD
                $resultado = $usuario->guardar();

                // Redireccionar
                if($resultado) {
                    header('Location: /login');
                    exit;
                }
            }
        }

        $alertas = Usuario::getAlertas();
        
        // Muestra la vista
        $router->render('auth/reestablecer', [
            'titulo' => 'Reestablecer Password',
            'alertas' => $alertas,
            'token_valido' => $token_valido
        ]);
    }

    /**
     * Muestra un mensaje después de crear la cuenta
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function mensaje(Router $router): void {

        $router->render('auth/mensaje', [
            'titulo' => 'Cuenta Creada Exitosamente'
        ]);
    }

    /**
     * Confirma la cuenta del usuario mediante un token enviado por email
     * 
     * @param Router $router Instancia del router para renderizar vistas
     * @return void
     */
    public static function confirmar(Router $router): void {
        
        $token = s($_GET['token'] ?? '');

        if(!$token) {
            header('Location: /');
            exit;
        }

        // Encontrar al usuario con este token
        $usuario = Usuario::where('token', $token);

        if(!$usuario) {
            // No se encontró un usuario con ese token
            Usuario::setAlerta('error', 'Token No Válido, la cuenta no se confirmó');
        } else {
            // Confirmar la cuenta
            $usuario->confirmado = 1;
            $usuario->token = '';
            unset($usuario->password2);
            
            // Guardar en la BD
            $usuario->guardar();

            Usuario::setAlerta('exito', 'Cuenta Comprobada éxitosamente');
        }

        $router->render('auth/confirmar', [
            'titulo' => 'Confirma tu cuenta Base8',
            'alertas' => Usuario::getAlertas()
        ]);
    }
}