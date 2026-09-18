<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="repartidores">

<?php
$instruccionesLista = [
  'encabezado' => 'Gestionar Repartidores',
  'tituloBtnReg' => 'Agregar Repartidores',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ FORMULARIO REGISTRAR ] COMIENZO -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title" id="registrarRepartidoresModalLabel">
          <i class="fas fa-user-plus me-2"></i> Agregar Nuevo Repartidor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrar">
            <div class="col-md-6 mb-3">
              <label for="codigo_cedula_repartidor" class="form-label">Cédula</label>
              <div class="input-group">
                <select
                  data-prefijo=".selectCodigoCedula"
                  data-cuerpo=".inputCedula"
                  class="input-group-text selectCodigoCedula"
                  name="codigo_cedula_repartidor"
                  required>
                  <option value="V">V</option>
                  <option value="E">E</option>
                  <option value="J">J</option>
                  <option value="G">G</option>
                  <option value="C">C</option>
                  <option value="P">P</option>
                </select>
                <input
                  type="text"
                  class=" form-control inputCedula noRepetir"
                  name="cedula_repartidor"
                  pattern="<?php echo regexCedulaRifLetra ?>"
                  minlength="<?php echo minRegexCedulaRif ?>"
                  maxlength="<?php echo maxRegexCedulaRif ?>"
                  minlengthC="<?php echo minRegexCedulaRif ?>"
                  maxlengthC="<?php echo maxRegexCedulaRif ?>"
                  required
                  data-prefijo=".selectCodigoCedula"
                  data-cuerpo=".inputCedula">
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="nombre_repartidor" class="form-label">Nombre</label>
              <input type="text" class="form-control" name="nombre_repartidor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="apellido_repartidor" class="form-label">Apellido</label>
              <input type="text" class="form-control" name="apellido_repartidor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="prefijo_telefono_repartidor" class="form-label">Teléfono</label>
              <div class="input-group">
                <select
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoRepartidor"
                  class="input-group-text selectPrefijoTelefono"
                  name="prefijo_telefono_repartidor"
                  required>
                  <option value="0416">0416</option>
                  <option value="0426">0426</option>
                  <option value="0424">0424</option>
                  <option value="0414">0414</option>
                  <option value="0412">0412</option>
                  <option value="0422">0422</option>
                  <option value="0212">0212</option>
                  <option value="0251">0251</option>
                  <option value="0241">0241</option>
                  <option value="0257">0257</option>
                  <option value="0257">0257</option>
                </select>
                <input
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoRepartidor"
                  type="text"
                  class="form-control telefonoRepartidor noRepetir"
                  name="telefono_repartidor"
                  pattern="<?php echo regexTelefono ?>"
                  minlength="<?php echo minRegexCuerpoTelefono ?>"
                  maxlength="<?php echo maxRegexCuerpoTelefono ?>"
                  minlengthC="<?php echo minRegexTelefono ?>"
                  maxlengthC="<?php echo maxRegexTelefono ?>"
                  required>
              </div>
            </div>
            <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i> Cancelar
              </button>
              <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                <i class="fas fa-save me-2"></i> Guardar Repartidor
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- [ FORMULARIO REGISTRAR ] FIN -->


<!-- FORMULARIO EDITAR COMIENZO -->
<div class="modal fade modalActualizar" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title">
          <i class="fas fa-edit me-2"></i> Editar Repartidor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" class=" form-control inputCedula formularioActualizar" name="cedula_repartidor">
            <div class="col-md-4 mb-3">
              <label for="nombre_repartidor" class="form-label">Nombre</label>
              <input type="text" class="form-control formularioActualizar" name="nombre_repartidor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="apellido_repartidor" class="form-label">Apellido</label>
              <input type="text" class="form-control formularioActualizar" name="apellido_repartidor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-4 mb-3">
              <label for="prefijo_telefono_repartidor" class="form-label">Teléfono</label>
              <div class="input-group">
                <select
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoRepartidor"
                  class="input-group-text selectPrefijoTelefono"
                  name="prefijo_telefono_repartidor"
                  required>
                  <option value="0416">0416</option>
                  <option value="0426">0426</option>
                  <option value="0424">0424</option>
                  <option value="0414">0414</option>
                  <option value="0412">0412</option>
                  <option value="0422">0422</option>
                  <option value="0212">0212</option>
                  <option value="0251">0251</option>
                  <option value="0241">0241</option>
                  <option value="0257">0257</option>
                  <option value="0257">0257</option>
                </select>
                <input
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoRepartidor"
                  type="text"
                  class="form-control formularioActualizar telefonoRepartidor noRepetir"
                  name="telefono_repartidor"
                  pattern="<?php echo regexTelefono ?>"
                  minlength="<?php echo minRegexCuerpoTelefono ?>"
                  maxlength="<?php echo maxRegexCuerpoTelefono ?>"
                  minlengthC="<?php echo minRegexTelefono ?>"
                  maxlengthC="<?php echo maxRegexTelefono ?>"
                  required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Guardar Cambios
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- FORMULARIO EDITAR FIN -->