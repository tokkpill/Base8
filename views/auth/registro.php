<main class="auth">
    <h2 class="auth__heading"><?php echo $titulo; ?></h2>
    <p class="auth__texto">Únete a la comunidad académica de Base8</p>

    <?php
        require_once __DIR__ . '/../templates/alertas.php';
    ?>

    <form method="POST" action="/registro" class="formulario">
        <div class="formulario__row">
            <div class="formulario__campo">
                <label for="nombre" class="formulario__label">Nombre</label>
                <input
                    type="text"
                    class="formulario__input"
                    placeholder="Tu nombre"
                    id="nombre"
                    name="nombre"
                    value="<?php echo s($usuario->nombre ?? ''); ?>"
                >
            </div>
            <div class="formulario__campo">
                <label for="apellido" class="formulario__label">Apellido</label>
                <input
                    type="text"
                    class="formulario__input"
                    placeholder="Tu apellido"
                    id="apellido"
                    name="apellido"
                    value="<?php echo s($usuario->apellido ?? ''); ?>"
                >
            </div>
        </div>

        <div class="formulario__campo">
            <label for="email" class="formulario__label">Email</label>
            <input
                type="email"
                class="formulario__input"
                placeholder="tu.email@uaemex.mx"
                id="email"
                name="email"
                value="<?php echo s($usuario->email ?? ''); ?>"
            >
        </div>

        <div class="formulario__row">
            <div class="formulario__campo">
                <label for="password" class="formulario__label">Contraseña</label>
                <input
                    type="password"
                    class="formulario__input"
                    placeholder="Mínimo 6 caracteres"
                    id="password"
                    name="password"
                >
            </div>
            <div class="formulario__campo">
                <label for="password2" class="formulario__label">Confirmar Contraseña</label>
                <input
                    type="password"
                    class="formulario__input"
                    placeholder="Repite tu contraseña"
                    id="password2"
                    name="password2"
                >
            </div>
        </div>

        <input type="submit" class="formulario__submit" value="Crear Cuenta">
    </form>

    <div class="acciones">
        <a href="/login" class="acciones__enlace">¿Ya tienes cuenta? Inicia Sesión</a>
        <a href="/olvide" class="acciones__enlace">¿Olvidaste tu contraseña?</a>
    </div>
</main>