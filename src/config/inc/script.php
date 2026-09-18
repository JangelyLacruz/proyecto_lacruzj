<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/popper.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/bootstrap.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/jquery-3.7.1.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/jquery.mask.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/jquery.dataTables.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/select2.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/sweetalert2.all.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/chart.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/notifier.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/datepicker-full.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/datepicker.min.es.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/library/socket.io.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/node_modules/leaflet/dist/leaflet.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/driver.min.js"></script><!--Driverjs -->
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/plugins/jspdf.umd.min.js"></script>
<script nonce="<?php echo $_SESSION['nonce']; ?>" src="https://www.google.com/recaptcha/api.js" async defer></script>

<?php
$directorioJs = '/proyecto-lacruz-j/src/assets/js/modulos/';
if ($_SESSION['vistaActual'] == 'login') {
  $nombreModulo = 'usuarios.js';
} else {
  $nombreModulo = $_SESSION['vistaActual'] . '.js';
}
$rutaFisicaJs = dirname(__DIR__, 2) . '/assets/js/modulos/' . $nombreModulo;
$versionJs = file_exists($rutaFisicaJs) ? '?v=' . filemtime($rutaFisicaJs) : '';
$archivoModulo = $directorioJs . $nombreModulo . $versionJs;
?>
<script type="module" nonce="<?php echo $_SESSION['nonce']; ?>" src="<?php echo $archivoModulo ?>"></script>
<?php if ($_SESSION['vistaActual'] == 'reportes'): ?>
<script type="module" nonce="<?php echo $_SESSION['nonce']; ?>" src="<?php echo $directorioJs . 'reportesEstadisticos.js'; ?>"></script>
<?php endif; ?>

<!-- CHAT BOT -->
<?php
use src\modelos\accesosModelo;
$objAcceso = new accesosModelo();
if ($_SESSION['vistaActual'] != 'login' && !$objAcceso->validarPermisos('chatbot', 'ver chatbot')): ?>
  <!-- Chatbot Toggle Button -->
  <button id="chatbot-toggle-btn" class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="position: fixed; bottom: 20px; right: 20px; width: 60px; height: 60px; z-index: 1050; font-size: 24px; background-color: #0d6efd; border: none;">
    <i class="fi fi-rr-headset"></i>
  </button>

  <!-- Chatbot Window -->
  <div id="chatbot-window" class="d-none shadow-lg border bg-white" style="position: fixed; bottom: 85px; right: 20px; width: 360px; max-width: calc(100vw - 40px); height: 450px; max-height: calc(100vh - 160px); z-index: 1050; display: flex; flex-direction: column; overflow: hidden; border-radius: 12px;">
    <!-- Header -->
    <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white flex-shrink-0">
      <div class="d-flex align-items-center">
        <div class="bg-primary text-white rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px; background-color: #0d6efd !important;">
          <i class="fi fi-rr-headset"></i>
        </div>
        <div>
          <h6 class="m-0 fw-bold text-dark" style="font-size: 15px;">MicroBot AI</h6>
          <small class="text-success d-flex align-items-center" style="font-size: 12px;">
            <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; border: 1px solid #198754; margin-right: 4px;"></span>
            En línea
          </small>
        </div>
      </div>
      <button id="chatbot-close-btn" class="btn-close" aria-label="Close" style="font-size: 14px;"></button>
    </div>

    <!-- Messages Area -->
    <div id="chatbot-messages" class="flex-grow-1 p-3 overflow-auto" style="background-color: #f8f9fa;">
      <div class="text-center mb-3">
        <small class="text-muted" style="font-size: 11px;">HOY</small>
      </div>
      <div class="d-flex mb-3">
        <div class="bot-msg-bubble bg-white border p-3 shadow-sm">
          ¡Hola! Soy tu asistente de MicroBot. Puedo ayudarte a recomendar productos o realizar presupuestos. ¿En qué puedo apoyarte hoy?
          <div class="text-end text-muted mt-1" style="font-size: 0.65rem;">09:41 AM</div>
        </div>
      </div>
    </div>

    <!-- Suggestions -->
    <div id="chatbot-suggestions" class="px-3 py-2 bg-white d-flex overflow-auto flex-shrink-0" style="white-space: nowrap; border-top: 1px solid #dee2e6;">
      <button class="btn btn-sm btn-outline-secondary rounded-pill me-2 px-3">Soporte técnico</button>
      <button class="btn btn-sm btn-outline-secondary rounded-pill me-2 px-3">Presupuesto</button>
      <button class="btn btn-sm btn-outline-secondary rounded-pill px-3">Hablar con huma...</button>
    </div>

    <!-- Input Area -->
    <div class="px-3 pt-2 pb-1 bg-white flex-shrink-0">
      <form id="chatbot-form" class="d-flex position-relative">
        <input type="text" id="chatbot-input" class="form-control rounded-pill ps-3 pe-5 py-2" placeholder="Escribe tu mensaje..." required autocomplete="off" style="border: 1px solid #dee2e6; font-size: 14px;">
        <button type="submit" class="btn btn-primary rounded-circle d-flex align-items-center justify-content-center position-absolute" style="width: 36px; height: 36px; right: 4px; top: 4px; background-color: #0d6efd; border: none;">
          <i class="fi fi-rr-paper-plane" style="font-size: 14px;"></i>
        </button>
      </form>
      <div class="text-center mt-2 mb-1">
        <small class="text-muted" style="font-size: 10px;">Desarrollado por MicroBot AI Engine</small>
      </div>
    </div>
  </div>

  <!-- Chatbot JS -->
  <script nonce="<?php echo $_SESSION['nonce']; ?>" src="/proyecto-lacruz-j/src/assets/js/modulos/chatbot.js"></script>

<?php endif; ?>

</body>

</html>

<?php
ob_end_flush();
?>