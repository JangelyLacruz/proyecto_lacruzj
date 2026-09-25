<?php
session_name(APP_SESSION_NAME);
session_set_cookie_params([
  'lifetime' => 0,
  'path' => '/',
  'domain' => '',
  'secure' => false,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();
header_remove("X-Powered-By");
$_SESSION['nonce'] = $nonce = base64_encode(random_bytes(16));
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'nonce-$nonce' 'sha256-qMWU74fUn+oWjY516C2ZONVkLq1zz9pFt8JwQ2EmJpo=' https://unpkg.com https://cdn.datatables.net https://www.google.com https://www.gstatic.com; frame-src https://www.google.com; style-src 'self' 'unsafe-inline' https://cdn.datatables.net https://fonts.googleapis.com https://cdn-uicons.flaticon.com https://unpkg.com; img-src 'self' data: https://*.tile.openstreetmap.org https://tile.openstreetmap.org https://www.openstreetmap.org https://unpkg.com; font-src 'self' https://fonts.gstatic.com https://cdn-uicons.flaticon.com; connect-src 'self' https://api.ipify.org https://ve.dolarapi.com ws://localhost:1234 ws://localhost:1235 http://localhost:1235 http://localhost:1234  https://api.tomtom.com https://router.project-osrm.org https://nominatim.openstreetmap.org https://project-osrm.org https://api-the-vina-node.onrender.com https://apithevinanode-production.up.railway.app wss://api-the-vina-node.onrender.com wss://apithevinanode-production.up.railway.app https://www.google.com; frame-ancestors 'self'; form-action 'self'; base-uri 'self';");
header("X-Content-Type-Options: nosniff");
ob_start();
