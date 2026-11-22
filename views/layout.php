<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Base8 — Marketplace Académico de la UAEMex. Descubre, comparte y monetiza recursos educativos.">
    <meta name="theme-color" content="#121212">
    <title>Base8 — <?php echo $titulo; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="/build/css/app.css">
</head>
<body>
    <?php
        $url_actual = $_SERVER['PATH_INFO'] ?? '/';
        $isSearchOrMarketplace = str_contains($url_actual, '/buscar') || str_contains($url_actual, '/marketplace') || str_contains($url_actual, '/asesores') || str_contains($url_actual, '/leaderboard') || str_contains($url_actual, '/perfil');
    ?>

    <?php if($isSearchOrMarketplace): ?>
        <!-- Modern fixed header for app pages -->
        <header class="b8-header">
            <div class="b8-header__inner">
                <a href="/" class="b8-header__logo">
                    <span>Base8</span>
                </a>

                <nav class="b8-header__nav">
                    <a href="/buscar" class="b8-header__link <?php echo pagina_actual('/buscar') ? 'b8-header__link--active' : ''; ?>">
                        <i class="fa-solid fa-search"></i> Buscar
                    </a>
                    <a href="/marketplace" class="b8-header__link <?php echo pagina_actual('/marketplace') ? 'b8-header__link--active' : ''; ?>">
                        <i class="fa-solid fa-store"></i> Marketplace
                    </a>
                    <a href="/asesores" class="b8-header__link <?php echo pagina_actual('/asesores') ? 'b8-header__link--active' : ''; ?>">
                        <i class="fa-solid fa-chalkboard-user"></i> Asesores
                    </a>
                    <a href="/leaderboard" class="b8-header__link <?php echo pagina_actual('/leaderboard') ? 'b8-header__link--active' : ''; ?>">
                        <i class="fa-solid fa-crown"></i> Top
                    </a>

                    <?php if(is_auth()): ?>
                        <a href="/marketplace/subir" class="b8-header__link b8-header__link--cta">
                            <i class="fa-solid fa-plus"></i> Publicar
                        </a>
                        <div class="b8-header__user">
                            <img src="<?php echo '/build/img/default-avatar.svg'; ?>" alt="Avatar">
                            <form method="POST" action="/logout" style="display:inline;">
                                <button type="submit" class="b8-header__logout">Salir</button>
                            </form>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="b8-header__link">Iniciar Sesión</a>
                        <a href="/registro" class="b8-header__link b8-header__link--cta">Registro</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>
    <?php else: ?>
        <?php
            include_once __DIR__ .'/templates/header.php';
        ?>
    <?php endif; ?>

    <?php echo $contenido; ?>

    <!-- Footer -->
    <footer class="b8-footer">
        <div class="b8-footer__grid">
            <div>
                <div class="b8-footer__brand"><span>Base8</span></div>
                <p class="b8-footer__desc">El marketplace académico de la UAEMex. Comparte conocimiento, construye reputación, transforma tu universidad.</p>
            </div>
            <div>
                <h4 class="b8-footer__title">Plataforma</h4>
                <ul class="b8-footer__links">
                    <li><a href="/marketplace">Marketplace</a></li>
                    <li><a href="/buscar">Buscar Recursos</a></li>
                    <li><a href="/asesores">Asesores</a></li>
                    <li><a href="/leaderboard">Leaderboard</a></li>
                </ul>
            </div>
            <div>
                <h4 class="b8-footer__title">Comunidad</h4>
                <ul class="b8-footer__links">
                    <li><a href="/marketplace/subir">Publicar Recurso</a></li>
                    <li><a href="/perfil/prestigio">Mi Prestigio</a></li>
                    <li><a href="/marketplace/mis-recursos">Mis Recursos</a></li>
                </ul>
            </div>
            <div>
                <h4 class="b8-footer__title">UAEMex</h4>
                <ul class="b8-footer__links">
                    <li><a href="#">Sobre Base8</a></li>
                    <li><a href="#">Términos de Uso</a></li>
                    <li><a href="#">Privacidad</a></li>
                    <li><a href="#">Contacto</a></li>
                </ul>
            </div>
        </div>
        <div class="b8-footer__bottom">
            <p>&copy; <?php echo date('Y'); ?> Base8 — UAEMex. Inteligencia Colectiva. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ once: true, duration: 600 });
    </script>
    <script src="/build/js/main.min.js" defer></script>
</body>
</html>