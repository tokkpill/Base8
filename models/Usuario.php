<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase Usuario
 * 
 * Representa a un usuario de la aplicación, manejando su información personal,
 * credenciales de acceso y estados de cuenta (confirmado, admin).
 */
class Usuario extends ActiveRecord {
    /** @var string Nombre de la tabla en la base de datos */
    protected static string $tabla = 'usuarios';

    /** @var array Columnas de la tabla para mapeo y sanitización */
    protected static array $columnasDB = ['id', 'nombre', 'apellido', 'email', 'password', 'confirmado', 'token', 'admin', 'role', 'faculty_id', 'career_id', 'bio', 'avatar', 'prestige_points', 'reputation_badge'];

    /** @var int|null ID único del usuario */
    public ?int $id;
    /** @var string Nombre del usuario */
    public string $nombre;
    /** @var string Apellido del usuario */
    public string $apellido;
    /** @var string Correo electrónico único */
    public string $email;
    /** @var string Hash de la contraseña */
    public string $password;
    /** @var string Campo temporal para confirmar contraseña en registros */
    public ?string $password2;
    /** @var int Estado de confirmación de la cuenta (0/1) */
    public int $confirmado;
    /** @var string Token para procesos de seguridad */
    public ?string $token;
    /** @var int Nivel de privilegios (0: usuario, 1: admin) */
    public ?int $admin;

    // ── Base8 Production Fields ──
    /** @var string Rol del usuario: student, advisor, admin */
    public string $role;
    /** @var int|null ID de la facultad del usuario */
    public ?int $faculty_id;
    /** @var int|null ID de la carrera del usuario */
    public ?int $career_id;
    /** @var string|null Biografía del usuario */
    public ?string $bio;
    /** @var string Avatar del usuario */
    public string $avatar;
    /** @var float Puntos de prestigio acumulados */
    public float $prestige_points;
    /** @var string Insignia de reputación actual */
    public string $reputation_badge;

    /** @var string|null Propiedad temporal para manejar el password actual en cambios */
    public ?string $password_actual;
    /** @var string|null Propiedad temporal para manejar el nuevo password */
    public ?string $password_nuevo;


    /**
     * Constructor de la clase Usuario
     * 
     * @param array $args Datos iniciales para el usuario
     */
    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->confirmado = (int)($args['confirmado'] ?? 0);
        $this->token = $args['token'] ?? '';
        $this->admin = (int)($args['admin'] ?? 0);

        // Base8 fields
        $this->role = $args['role'] ?? 'student';
        $this->faculty_id = isset($args['faculty_id']) ? (int)$args['faculty_id'] : null;
        $this->career_id = isset($args['career_id']) ? (int)$args['career_id'] : null;
        $this->bio = $args['bio'] ?? null;
        $this->avatar = $args['avatar'] ?? 'default.png';
        $this->prestige_points = (float)($args['prestige_points'] ?? 0.00);
        $this->reputation_badge = $args['reputation_badge'] ?? 'Novato';
    }

    /**
     * Valida los datos para el inicio de sesión
     * 
     * @return array Alertas generadas
     */
    public function validarLogin(): array {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email del Usuario es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }
        return self::$alertas;

    }

    /**
     * Valida los datos para la creación de una cuenta nueva
     * 
     * @return array Alertas generadas
     */
    public function validar_cuenta(): array {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es Obligatorio';
        }
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        if($this->password !== $this->password2) {
            self::$alertas['error'][] = 'Los password son diferentes';
        }
        return self::$alertas;
    }

    /**
     * Valida que el email tenga un formato correcto y no esté vacío
     * 
     * @return array Alertas generadas
     */
    public function validarEmail(): array {
        if(!$this->email) {
            self::$alertas['error'][] = 'El Email es Obligatorio';
        }
        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            self::$alertas['error'][] = 'Email no válido';
        }
        return self::$alertas;
    }

    /**
     * Valida que el password cumpla con los requisitos mínimos
     * 
     * @return array Alertas generadas
     */
    public function validarPassword(): array {
        if(!$this->password) {
            self::$alertas['error'][] = 'El Password no puede ir vacio';
        }
        if(strlen($this->password) < 6) {
            self::$alertas['error'][] = 'El password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    /**
     * Valida el cambio de contraseña (actual vs nueva)
     * 
     * @return array Alertas generadas
     */
    public function nuevo_password() : array {
        if(!isset($this->password_actual) || !$this->password_actual) {
            self::$alertas['error'][] = 'El Password Actual no puede ir vacio';
        }
        if(!isset($this->password_nuevo) || !$this->password_nuevo) {
            self::$alertas['error'][] = 'El Password Nuevo no puede ir vacio';
        }
        if(isset($this->password_nuevo) && strlen($this->password_nuevo) < 6) {
            self::$alertas['error'][] = 'El Password debe contener al menos 6 caracteres';
        }
        return self::$alertas;
    }

    /**
     * Comprueba si el password actual coincide con el de la base de datos
     * 
     * @return bool True si coincide, false en caso contrario
     */
    public function comprobar_password() : bool {
        return password_verify($this->password_actual ?? '', $this->password );
    }

    /**
     * Aplica hashing a la contraseña del usuario
     * 
     * @return void
     */
    public function hashPassword() : void {
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    /**
     * Genera un token único para el usuario
     * 
     * @return void
     */
    public function crearToken() : void {
        $this->token = uniqid();
    }

    // ── Base8 Reputation & Profile Methods ──

    /**
     * Devuelve la clase CSS para la insignia del usuario
     */
    public function getBadgeClass(): string
    {
        return match ($this->reputation_badge) {
            'Elite Scholar' => 'badge--elite',
            'Top Contributor' => 'badge--top',
            'Rising Star' => 'badge--rising',
            'Scholar' => 'badge--scholar',
            'Contributor' => 'badge--contributor',
            default => 'badge--novato',
        };
    }

    /**
     * Devuelve el nombre completo del usuario
     */
    public function getNombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    /**
     * Obtiene el leaderboard de usuarios por prestigio
     */
    public static function getLeaderboard(int $limit = 20): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE confirmado = 1 ORDER BY prestige_points DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        $stmt->close();
        return $array;
    }

    /**
     * Obtiene el avatar URL del usuario
     */
    public function getAvatarUrl(): string
    {
        if ($this->avatar && $this->avatar !== 'default.png') {
            return '/uploads/avatars/' . $this->avatar;
        }
        return '/build/img/default-avatar.svg';
    }
}