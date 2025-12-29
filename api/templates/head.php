<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title_name ?></title>

    <meta name="description" content="Herramienta para hacer estudios de presupuestos,
    permitiendo calcular el margen BSV rápidamente">

    <link rel="icon" type="image/svg" href="<?= BASE_URL ?>/img/square.svg">

    <link rel="stylesheet" href="<?= BASE_URL ?>/css/variables.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/layout.css">

    <?php if (isset($page_css)): ?>
        <link rel="stylesheet" href="<?= BASE_URL ?>/css/<?= $page_css ?>">
    <?php endif; ?>

</head>