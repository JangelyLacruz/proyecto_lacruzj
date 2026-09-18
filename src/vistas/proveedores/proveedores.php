<?php

use src\config\inc\componentesModelo;

$componente = new componentesModelo();
?>

<input type="hidden" class="nombreVista" value="proveedores">

<?php
$instruccionesLista = [
  'encabezado' => 'Gestionar Proveedores',
  'tituloBtnReg' => 'Agregar Proveedores',
];
echo $componente->listaDataTable($instruccionesLista);
?>

<!-- [ FORMULARIO REGISTRAR ] COMIENZO -->
<div class="modal fade modalRegistrar" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0">
      <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
        <h5 class="modal-title" id="registrarUsuarioModalLabel">
          <i class="fas fa-user-plus me-2"></i> Agregar Nuevo Proveedor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="registrar">
            <div class="col-md-6 mb-3">
              <label for="rif_proveedor" class="form-label">RIF/Cédula</label>
              <div class="input-group">
                <select
                  data-prefijo=".selectCodigoRIF"
                  data-cuerpo=".inputRIF"
                  class="input-group-text selectCodigoRIF"
                  name="codigo_rif_proveedor"
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
                  class=" form-control inputRif noRepetir"
                  name="rif_proveedor"
                  pattern="<?php echo regexCedulaRifLetra ?>"
                  minlength="<?php echo minRegexCedulaRif ?>"
                  maxlength="<?php echo maxRegexCedulaRif ?>"
                  minlengthC="<?php echo minRegexCedulaRif ?>"
                  maxlengthC="<?php echo maxRegexCedulaRif ?>"
                  required
                  data-prefijo=".selectCodigoRIF"
                  data-cuerpo=".inputRIF">
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="razon_social_proveedor" class="form-label">Razón Social</label>
              <input type="text" class="form-control noRepetir" name="razon_social_proveedor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="prefijo_telefono_proveedor" class="form-label">Teléfono</label>
              <div class="input-group">
                <select
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoProveedor"
                  class="input-group-text selectPrefijoTelefono"
                  name="prefijo_telefono_proveedor">
                  <option value="">S/N</option>
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
                  data-cuerpo=".telefonoProveedor"
                  type="text"
                  class="form-control telefonoProveedor noRepetir"
                  name="telefono_proveedor"
                  pattern="<?php echo regexTelefono ?>"
                  minlength="<?php echo minRegexCuerpoTelefono ?>"
                  maxlength="<?php echo maxRegexCuerpoTelefono ?>"
                  minlengthC="<?php echo minRegexTelefono ?>"
                  maxlengthC="<?php echo maxRegexTelefono ?>">
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="correo_proveedor" class="form-label">Correo Electrónico</label>
              <input type="email" class="form-control noRepetir" name="correo_proveedor" pattern="<?php echo regexCorreo ?>" minlength="<?php echo minRegexCorreo ?>" maxlength="<?php echo maxRegexCorreo ?>" >
            </div>
            <div class="col-md-12 mb-3">
              <label for="direccion_proveedor" class="form-label">Dirección</label>
              <textarea class="form-control" name="direccion_proveedor" rows="3" pattern="<?php echo regexDescripcion ?>" minlength="<?php echo minRegexDescripcion ?>" maxlength="<?php echo maxRegexDescripcion ?>"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer justify-content-center" style="background-color: #f8f9fa;">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-2"></i> Cancelar
          </button>
          <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
            <i class="fas fa-save me-2"></i> Guardar Proveedor
          </button>
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
          <i class="fas fa-edit me-2"></i> Editar Proveedor
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form class="formularioAjax validar" method="POST" action="" novalidate>
        <div class="modal-body">
          <div class="row">
            <input type="hidden" name="accion" value="actualizar">
            <input type="hidden" class="formularioActualizar" name="rif_proveedor">
            <div class="col-md-6 mb-3">
              <label for="razon_social_proveedor" class="form-label">Razón Social</label>
              <input type="text" class="form-control formularioActualizar noRepetir" name="razon_social_proveedor" pattern="<?php echo regexNombreObj ?>" minlength="<?php echo minRegexNombreObj ?>" maxlength="<?php echo maxRegexNombreObj ?>" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="prefijo_telefono_proveedor" class="form-label">Teléfono</label>
              <div class="input-group">
                <select
                  data-prefijo=".selectPrefijoTelefono"
                  data-cuerpo=".telefonoProveedor"
                  class="input-group-text formularioActualizar selectPrefijoTelefono"
                  name="prefijo_telefono_proveedor"
                  >
                  <option value="">S/N</option>
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
                  data-cuerpo=".telefonoProveedor"
                  type="text"
                  class="form-control formularioActualizar telefonoProveedor noRepetir"
                  name="telefono_proveedor"
                  pattern="<?php echo regexTelefono ?>"
                  minlength="<?php echo minRegexCuerpoTelefono ?>"
                  maxlength="<?php echo maxRegexCuerpoTelefono ?>"
                  minlengthC="<?php echo minRegexTelefono ?>"
                  maxlengthC="<?php echo maxRegexTelefono ?>"
                  >
              </div>
            </div>
            <div class="col-md-12 mb-3">
              <label for="correo_proveedor" class="form-label">Correo Electrónico</label>
              <input type="email" class="form-control formularioActualizar noRepetir" name="correo_proveedor" pattern="<?php echo regexCorreo ?>" minlength="<?php echo minRegexCorreo ?>" maxlength="<?php echo maxRegexCorreo ?>" >
            </div>
            <div class="col-md-12mb-3">
              <label for="direccion_proveedor" class="form-label">Dirección</label>
              <textarea class="form-control formularioActualizar" name="direccion_proveedor" rows="3" pattern="<?php echo regexDescripcion ?>" minlength="<?php echo minRegexDescripcion ?>" maxlength="<?php echo maxRegexDescripcion ?>" ></textarea>
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