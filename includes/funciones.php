<?php

/**
 * Función de utilidad para depuración avanzada.
 * Muestra el contenido de una variable en formato pre y detiene la ejecución.
 * 
 * @param mixed $variable La variable a inspeccionar
 * @return string (Aunque hace exit, PHPDoc requiere retorno si está definido)
 */
function debuguear($variable) : string {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

/**
 * Sanitiza el HTML para evitar ataques XSS
 * 
 * @param string $html El contenido a sanitizar
 * @return string El contenido escapado
 */
function s($html) : string {
    $s = htmlspecialchars($html);
    return $s;
}

/**
 * Comprueba si el path proporcionado coincide con la URL actual
 * 
 * @param string $path El segmento de URL a buscar
 * @return bool True si es la página actual, false en caso contrario
 */
function pagina_actual($path ) : bool {
    return str_contains( $_SERVER['PATH_INFO'] ?? '/', $path  ) ? true : false;
}

/**
 * Verifica si el usuario está autenticado comprobando la sesión
 * 
 * @return bool True si el usuario tiene sesión iniciada
 */
function is_auth() : bool {
    if(!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['nombre']) && !empty($_SESSION);
}

/**
 * Verifica si el usuario autenticado tiene privilegios de administrador
 * 
 * @return bool True si es administrador
 */
function is_admin() : bool {
    if(!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['admin']) && !empty($_SESSION['admin']);
}

/**
 * Genera un atributo de datos para animaciones AOS de forma aleatoria
 * 
 * @return void
 */
function aos_animacion() : void {
    $efectos = ['fade-up', 'fade-down', 'fade-left', 'fade-right', 'flip-left', 'flip-right', 'zoom-in', 'zoom-in-up', 'zoom-in-down', 'zoom-out'];
    $efecto = array_rand($efectos, 1);
    echo ' data-aos="' . $efectos[$efecto] . '" ';
}

// ══════════════════════════════════════════════
// BASE8 PRODUCTION HELPERS
// ══════════════════════════════════════════════

/**
 * Genera y devuelve un token CSRF
 */
function csrf_token(): string {
    return \Classes\Security::getCSRFToken();
}

/**
 * Genera el campo HTML oculto del CSRF token
 */
function csrf_field(): string {
    return \Classes\Security::csrfField();
}

/**
 * Valida el token CSRF del POST
 */
function verify_csrf(): bool {
    $token = $_POST['csrf_token'] ?? '';
    return \Classes\Security::validateCSRFToken($token);
}

/**
 * Verifica si el usuario tiene rol de advisor
 */
function is_advisor(): bool {
    if (!isset($_SESSION)) {
        session_start();
    }
    return isset($_SESSION['role']) && $_SESSION['role'] === 'advisor';
}

/**
 * Renderiza la insignia de reputación de un usuario
 * 
 * @param string $badge Nombre de la insignia
 * @param float $points Puntos de prestigio
 * @return string HTML de la insignia
 */
function user_badge(string $badge, float $points = 0): string {
    $class = match ($badge) {
        'Elite Scholar' => 'badge--elite',
        'Top Contributor' => 'badge--top',
        'Rising Star' => 'badge--rising',
        'Scholar' => 'badge--scholar',
        'Contributor' => 'badge--contributor',
        default => 'badge--novato',
    };
    
    $icon = match ($badge) {
        'Elite Scholar' => 'fa-crown',
        'Top Contributor' => 'fa-trophy',
        'Rising Star' => 'fa-star',
        'Scholar' => 'fa-book-open',
        'Contributor' => 'fa-hand-holding-heart',
        default => 'fa-seedling',
    };

    return '<span class="prestige-badge ' . $class . '"><i class="fa-solid ' . $icon . '"></i> ' . s($badge) . '</span>';
}

/**
 * Formatea un precio en MXN
 */
function format_price(float $price): string {
    if ($price <= 0) {
        return '<span class="price price--free">Gratis</span>';
    }
    return '<span class="price">$' . number_format($price, 2) . ' MXN</span>';
}

/**
 * Genera estrellas de rating en HTML
 * 
 * @param float $rating Rating promedio (0-5)
 * @param int $total Total de ratings
 * @return string HTML de las estrellas
 */
function render_stars(float $rating, int $total = 0): string {
    $html = '<div class="stars-rating">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= floor($rating)) {
            $html .= '<i class="fa-solid fa-star stars-rating__star stars-rating__star--filled"></i>';
        } elseif ($i - 0.5 <= $rating) {
            $html .= '<i class="fa-solid fa-star-half-stroke stars-rating__star stars-rating__star--half"></i>';
        } else {
            $html .= '<i class="fa-regular fa-star stars-rating__star"></i>';
        }
    }
    $html .= '<span class="stars-rating__text">' . number_format($rating, 1) . '</span>';
    if ($total > 0) {
        $html .= '<span class="stars-rating__count">(' . $total . ')</span>';
    }
    $html .= '</div>';
    return $html;
}

/**
 * Formatea tiempo relativo (hace X minutos/horas/días)
 */
function time_ago(string $datetime): string {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return 'hace un momento';
    if ($diff < 3600) return 'hace ' . floor($diff / 60) . ' min';
    if ($diff < 86400) return 'hace ' . floor($diff / 3600) . 'h';
    if ($diff < 604800) return 'hace ' . floor($diff / 86400) . 'd';
    if ($diff < 2592000) return 'hace ' . floor($diff / 604800) . ' sem';
    return date('d M Y', $time);
}
