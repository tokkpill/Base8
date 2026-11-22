<header class="admin-header">
    <a href="/admin/dashboard" class="admin-header__logo">
        <span>Base8</span> Admin
    </a>
    <div class="b8-header__user">
        <?php if(is_auth()): ?>
            <span style="color: #94A3B8; font-size: 1.3rem;"><?php echo s($_SESSION['nombre'] ?? ''); ?></span>
            <form method="POST" action="/logout" style="display:inline;">
                <button type="submit" class="b8-header__logout">Salir</button>
            </form>
        <?php endif; ?>
    </div>
</header>