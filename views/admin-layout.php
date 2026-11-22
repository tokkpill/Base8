<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#121212">
    <title>Base8 Admin — <?php echo $titulo; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="/build/css/app.css">
</head>
<body class="dashboard">
    <?php include_once __DIR__ .'/templates/admin-header.php'; ?>

    <div class="dashboard__grid">
        <?php include_once __DIR__ .'/templates/admin-sidebar.php'; ?>

        <main class="dashboard__contenido">
            <?php echo $contenido; ?>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="/build/js/main.min.js" defer></script>
</body>
</html>