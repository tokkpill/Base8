<aside class="dashboard__sidebar">
    <nav class="dashboard__menu">
        <a href="/admin/dashboard" class="dashboard__enlace <?php echo pagina_actual('/dashboard') ? 'dashboard__enlace--actual' : ''; ?>">
            <i class="fa-solid fa-house dashboard__icono"></i>
            <span class="dashboard__menu-texto">Inicio</span>
        </a>

        <a href="/admin/ponentes" class="dashboard__enlace <?php echo pagina_actual('/ponentes') ? 'dashboard__enlace--actual' : ''; ?>">
            <i class="fa-solid fa-microphone dashboard__icono"></i>
            <span class="dashboard__menu-texto">Ponentes</span>
        </a>

        <a href="/admin/eventos" class="dashboard__enlace <?php echo pagina_actual('/eventos') ? 'dashboard__enlace--actual' : ''; ?>">
            <i class="fa-solid fa-calendar dashboard__icono"></i>
            <span class="dashboard__menu-texto">Eventos</span>
        </a>

        <a href="/admin/registrados" class="dashboard__enlace <?php echo pagina_actual('/registrados') ? 'dashboard__enlace--actual' : ''; ?>">
            <i class="fa-solid fa-users dashboard__icono"></i>
            <span class="dashboard__menu-texto">Registrados</span>
        </a>

        <a href="/admin/regalos" class="dashboard__enlace <?php echo pagina_actual('/regalos') ? 'dashboard__enlace--actual' : ''; ?>">
            <i class="fa-solid fa-gift dashboard__icono"></i>
            <span class="dashboard__menu-texto">Regalos</span>
        </a>

        <div style="border-top: 1px solid rgba(255,255,255,0.08); margin: 1.5rem 0;"></div>

        <a href="/marketplace" class="dashboard__enlace <?php echo pagina_actual('/marketplace') ? 'dashboard__enlace--actual' : ''; ?>">
            <i class="fa-solid fa-store dashboard__icono"></i>
            <span class="dashboard__menu-texto">Marketplace</span>
        </a>

        <a href="/leaderboard" class="dashboard__enlace <?php echo pagina_actual('/leaderboard') ? 'dashboard__enlace--actual' : ''; ?>">
            <i class="fa-solid fa-crown dashboard__icono"></i>
            <span class="dashboard__menu-texto">Prestigio</span>
        </a>

        <a href="/asesores" class="dashboard__enlace <?php echo pagina_actual('/asesores') ? 'dashboard__enlace--actual' : ''; ?>">
            <i class="fa-solid fa-chalkboard-user dashboard__icono"></i>
            <span class="dashboard__menu-texto">Asesores</span>
        </a>
    </nav>
</aside>