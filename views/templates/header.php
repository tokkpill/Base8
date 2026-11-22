<header class="header">
    <div class="header__contenedor">
        <nav class="header__navegacion">
            <?php if(is_auth()) { ?>
                <a href="<?php echo is_admin() ? '/admin/dashboard' : '/marketplace'; ?>" class="header__enlace">
                    <?php echo is_admin() ? 'Panel Admin' : 'Marketplace'; ?>
                </a>
                <form method="POST" action="/logout" class="header__form">
                    <input type="submit" value="Cerrar Sesión" class="header__submit">
                </form>
            <?php } else { ?>
                <a href="/registro" class="header__enlace">Registro</a>
                <a href="/login" class="header__enlace">Iniciar Sesión</a>
            <?php } ?>
        </nav>

        <div class="header__contenido">
            <a href="/">
                <h1 class="header__logo">Base8</h1>
            </a>

            <p class="header__texto">Marketplace Académico — UAEMex</p>
            <p class="header__texto header__texto--modalidad">Comparte conocimiento · Construye reputación</p>

            <a href="/marketplace" class="header__boton">Explorar Marketplace</a>
        </div>
    </div>
</header>
<div class="barra">
    <div class="barra__contenido">
        <a href="/">
            <h2 class="barra__logo">Base8</h2>
        </a>
        <nav class="navegacion">
            <a href="/marketplace" class="navegacion__enlace <?php echo pagina_actual('/marketplace') ? 'navegacion__enlace--actual' : ''; ?>">Marketplace</a>
            <a href="/buscar" class="navegacion__enlace <?php echo pagina_actual('/buscar') ? 'navegacion__enlace--actual' : ''; ?>">Buscar</a>
            <a href="/asesores" class="navegacion__enlace <?php echo pagina_actual('/asesores') ? 'navegacion__enlace--actual' : ''; ?>">Asesores</a>
            <a href="/leaderboard" class="navegacion__enlace <?php echo pagina_actual('/leaderboard') ? 'navegacion__enlace--actual' : ''; ?>">Leaderboard</a>
        </nav>
    </div>
</div>