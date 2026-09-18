<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="TOKEN_CSRF" content="<?php echo ($_SESSION['TOKEN_CSRF'] ?? ''); ?>">
  <title>J. LACRUZ C.A.</title>
  <link rel="shortcut icon" href="/proyecto-lacruz-j/src/assets/images/logo2.png" type="image/x-icon">
  <link rel='stylesheet' href="/proyecto-lacruz-j/src/assets/css/plugins/bootstrap.min.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/plugins/select2.min.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/plugins/select2-bootstrap-5-theme.min.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/plugins/dataTables.bootstrap5.min.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/plugins/sweetalert2.min.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/plugins/notifier.min.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/plugins/datepicker-bs5.min.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/iconos/uicons/css/all/all.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/node_modules/leaflet/dist/leaflet.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/plugins/driver.min.css">
  <link rel="stylesheet" href="/proyecto-lacruz-j/src/assets/css/header.css">

</head>

<body>

  <!-- Espiner de carga -->
  <div id="spinnerCarga" class="d-none">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">Cargando...</span>
    </div>
  </div>