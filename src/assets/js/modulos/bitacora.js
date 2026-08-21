//#region [ IMPORTACIONES ] COMIENZO
import {
  listarDataTable,
  cambiarFormatos,
  alertasAjax
} from '/proyecto-lacruz-j/src/assets/js/modulos/global.js';
import {
  driverAyuda,
  mostrarAyuda
} from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"
//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO

function registrarTutorial() {
  driverAyuda('bitacora', {
    pasos: [{
        element: '.tabla-ajax',
        popover: {
          title: 'Registro de Bitácora',
          description: 'Esta tabla muestra todas las acciones realizadas por los usuarios en el sistema.',
          side: 'top'
        }
      },
      {
        element: '.btnVerCambiosBitacora',
        popover: {
          title: 'Ver Cambios',
          description: 'Haz clic aquí para ver los detalles completos de los cambios realizados.',
          side: 'left'
        }
      },
      {
        element: '.dataTables_filter input',
        popover: {
          title: 'Buscador',
          description: 'Busca eventos por palabra clave.',
          side: 'bottom',
          align: 'start'
        }
      },
      {
        element: '.dataTables_paginate',
        popover: {
          title: 'Paginación',
          description: 'Navega entre las páginas de registros.',
          side: 'top',
          align: 'end'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces cómo funciona la bitácora.',
          side: 'top'
        }
      }
    ]
  });
}

function obtenerNombreItem(item) {
  if (!item || typeof item !== 'object') {
    return String(item) || 'Sin nombre';
  }
  
  for (const [key, value] of Object.entries(item)) {
    const keyLower = key.toLowerCase();
    if ((keyLower.includes('nombre') || keyLower.includes('razon') || keyLower.includes('descripcion')) && 
        typeof value === 'string' && value.trim() !== '' && value !== item.id) {
      return value;
    }
  }
  
  if (item.id) {
    return item.id;
  }
  
  for (const [key, value] of Object.entries(item)) {
    const keyLower = key.toLowerCase();
    if ((keyLower.includes('id') || keyLower.includes('codigo') || keyLower.includes('rif') || keyLower.includes('cedula')) && 
        typeof value === 'string' && value.trim() !== '') {
      return value;
    }
  }
  
  const keys = Object.keys(item);
  if (keys.length === 1) {
    const valor = item[keys[0]];
    if (typeof valor === 'string' || typeof valor === 'number') {
      return String(valor);
    }
  }
  
  try {
    return JSON.stringify(item);
  } catch (e) {
    return 'Sin nombre';
  }
}

function formatearValor(valor) {
  if (valor === null || valor === undefined) {
    return '<span class="text-muted fst-italic">(vacío)</span>';
  }
  if (typeof valor === 'object') {
    return `<pre class="bg-white p-2 rounded small mb-0" style="font-size: 0.75rem; max-height: 150px; overflow-y: auto;">${JSON.stringify(valor, null, 2)}</pre>`;
  }
  if (typeof valor === 'string' && valor.length > 100) {
    return `<div class="bg-light p-2 rounded small" style="word-break: break-all;">${valor}</div>`;
  }
  return `<div class="bg-light p-2 rounded">${valor}</div>`;
}

function renderizarCambios(campo, valor) {
  if (typeof valor === 'object' && valor !== null && valor._lista === true) {
    let html = `
      <div class="card mb-3 shadow-sm border-0">
        <div class="card-header bg-light py-2">
          <h6 class="fw-bold text-primary mb-0">
            <i class="fi fi-rs-list me-2"></i>${campo}
            <span class="badge bg-secondary ms-2">Lista</span>
          </h6>
        </div>
        <div class="card-body">
    `;

    // Modificados
    if (valor._modificados && valor._modificados.length > 0) {
      html += `
        <div class="mb-3">
          <span class="badge bg-warning text-dark mb-2">Modificados</span>
          <div class="bg-light p-2 rounded">
      `;
      for (const item of valor._modificados) {
        const nombre = obtenerNombreItem(item);
        html += `
          <div class="mb-2 border-bottom pb-2">
            <strong>${nombre}</strong>
            <div class="ps-3">
        `;
        if (item.cambios) {
          for (const [key, change] of Object.entries(item.cambios)) {
            if (typeof change === 'object' && change !== null) {
              const anterior = change.anterior !== undefined && change.anterior !== null ? change.anterior : '(vacío)';
              const nuevo = change.nuevo !== undefined && change.nuevo !== null ? change.nuevo : '(vacío)';
              html += `
                <div class="d-flex align-items-center gap-2 small">
                  <span class="text-danger">${anterior}</span>
                  <i class="fi fi-rs-arrow-right"></i>
                  <span class="text-success">${nuevo}</span>
                  <span class="text-muted">(${key})</span>
                </div>
              `;
            }
          }
        }
        html += `
            </div>
          </div>
        `;
      }
      html += `
          </div>
        </div>
      `;
    }

    // Eliminados
    if (valor._eliminados && valor._eliminados.length > 0) {
      html += `
        <div class="mb-3">
          <span class="badge bg-danger mb-2">Eliminados</span>
          <div class="bg-light p-2 rounded">
      `;
      for (const item of valor._eliminados) {
        const nombre = obtenerNombreItem(item);
        html += `
          <div class="d-flex align-items-center gap-2">
            <i class="fi fi-rs-trash text-danger"></i>
            <span>${nombre}</span>
          </div>
        `;
      }
      html += `
          </div>
        </div>
      `;
    }

    // Agregados
    if (valor._agregados && valor._agregados.length > 0) {
      html += `
        <div class="mb-3">
          <span class="badge bg-success mb-2">Agregados</span>
          <div class="bg-light p-2 rounded">
      `;
      for (const item of valor._agregados) {
        const nombre = obtenerNombreItem(item);
        html += `
          <div class="d-flex align-items-center gap-2">
            <i class="fi fi-rs-plus text-success"></i>
            <span>${nombre}</span>
          </div>
        `;
      }
      html += `
          </div>
        </div>
      `;
    }

    html += `
        </div>
      </div>
    `;
    return html;
  }

  if (typeof valor === 'object' && valor !== null && 
      (valor.hasOwnProperty('anterior') || valor.hasOwnProperty('nuevo'))) {
    const anterior = valor.anterior !== null && valor.anterior !== undefined ? valor.anterior : '(vacío)';
    const nuevo = valor.nuevo !== null && valor.nuevo !== undefined ? valor.nuevo : '(vacío)';
    return `
      <div class="card mb-3 shadow-sm border-0">
        <div class="card-header bg-light py-2">
          <h6 class="fw-bold text-primary mb-0">
            <i class="fi fi-rs-pen me-2"></i>${campo}
          </h6>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-6">
              <div class="border-start border-danger ps-3">
                <span class="badge bg-danger mb-2">Antes</span>
                <div class="bg-white p-2 rounded">${anterior}</div>
              </div>
            </div>
            <div class="col-6">
              <div class="border-start border-success ps-3">
                <span class="badge bg-success mb-2">Después</span>
                <div class="bg-white p-2 rounded">${nuevo}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  }

  if (typeof valor === 'object' && valor !== null && !Array.isArray(valor)) {
    const keys = Object.keys(valor);
    
    let tieneObjetos = false;
    for (const k of keys) {
      if (typeof valor[k] === 'object' && valor[k] !== null) {
        tieneObjetos = true;
        break;
      }
    }
    
    if (tieneObjetos || keys.length > 4) {
      return `
        <div class="card mb-3 shadow-sm border-0">
          <div class="card-header bg-light py-2">
            <h6 class="fw-bold text-primary mb-0">
              <i class="fi fi-rs-code me-2"></i>${campo}
            </h6>
          </div>
          <div class="card-body">
            <pre class="bg-light p-3 rounded small mb-0" style="font-size: 0.75rem; max-height: 300px; overflow-y: auto;">${JSON.stringify(valor, null, 2)}</pre>
          </div>
        </div>
      `;
    }
    
    return `
      <div class="card mb-3 shadow-sm border-0">
        <div class="card-header bg-light py-2">
          <h6 class="fw-bold text-primary mb-0">
            <i class="fi fi-rs-pen me-2"></i>${campo}
          </h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm table-borderless mb-0">
              <tbody>
                ${Object.entries(valor).map(([k, v]) => {
                  let valorMostrar = v;
                  if (typeof v === 'object' && v !== null) {
                    valorMostrar = `<pre class="bg-white p-2 rounded small mb-0" style="font-size: 0.7rem; max-height: 100px; overflow-y: auto;">${JSON.stringify(v, null, 2)}</pre>`;
                  } else if (v === null || v === undefined) {
                    valorMostrar = '<span class="text-muted fst-italic">(vacío)</span>';
                  }
                  return `
                    <tr>
                      <td style="width: 140px; font-weight: 600; color: #4e54c8;">${k}</td>
                      <td>${typeof v === 'object' && v !== null ? valorMostrar : v}</td>
                    </tr>
                  `;
                }).join('')}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    `;
  }

  // Array
  if (Array.isArray(valor)) {
    return `
      <div class="card mb-3 shadow-sm border-0">
        <div class="card-header bg-light py-2">
          <h6 class="fw-bold text-primary mb-0">
            <i class="fi fi-rs-list me-2"></i>${campo}
          </h6>
        </div>
        <div class="card-body">
          <pre class="bg-light p-3 rounded small mb-0" style="font-size: 0.75rem; max-height: 300px; overflow-y: auto;">${JSON.stringify(valor, null, 2)}</pre>
        </div>
      </div>
    `;
  }

  // Valor simple
  return `
    <div class="card mb-2 shadow-sm border-0">
      <div class="card-body py-2 d-flex align-items-center">
        <strong class="text-primary me-2" style="min-width: 120px;">${campo}:</strong>
        <div class="bg-light px-3 py-1 rounded flex-grow-1">${valor}</div>
      </div>
    </div>
  `;
}

function verCambiosCompletos() {
  const idBitacora = $(this).attr('value');
  const modal = $('.modalVerCambiosBitacora');
  const contenedor = modal.find('#contenedorCambiosBitacora');

  if (!idBitacora) {
    alertasAjax({
      tipo: 'simple',
      titulo: 'Error',
      texto: 'No se pudo obtener el ID del registro',
      icono: 'error'
    });
    return;
  }

  const tabla = $('.tabla-ajax').DataTable();
  let registroEncontrado = null;

  tabla.rows().every(function() {
    const data = this.data();
    if (data.id_bitacora == idBitacora) {
      registroEncontrado = data;
      return false;
    }
  });

  if (!registroEncontrado) {
    alertasAjax({
      tipo: 'simple',
      titulo: 'Error',
      texto: 'No se encontró el registro en la tabla',
      icono: 'error'
    });
    return;
  }

  const cambios = registroEncontrado.cambios_efectuados;

  if (!cambios || Object.keys(cambios).length === 0) {
    contenedor.html(`
      <div class="text-center text-muted py-5">
        <i class="fi fi-rs-info fs-1 mb-3 d-block"></i>
        <p class="fs-5">Este registro no tiene cambios detallados</p>
      </div>
    `);
    modal.modal('show');
    return;
  }

  let html = '';

  for (const [campo, valor] of Object.entries(cambios)) {
    html += renderizarCambios(campo, valor);
  }

  contenedor.html(html);
  modal.modal('show');
}

//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO

$(document).on('DOMContentLoaded', async function() {
  registrarTutorial();
  
  await new Promise(resolve => setTimeout(resolve, 500));

  await listarDataTable({
    encabezados: {
      "id_bitacora": "ID",
      "fecha_bitacora": "FECHA",
      "nombre_usuario": "USUARIO",
      "apellido_usuario": "APELLIDO",
      "modulo_bitacora": "MÓDULO",
      "accion": "ACCIÓN",
      "resultado_bitacora": "RESULTADO",
      "ip_dispositivo": "IP",
      "cambios_efectuados": "CAMBIOS",
    },
    informacionPe: {
      'modulo': 'bitacora',
      'datosPe': {
        'accion': 'listar'
      }
    },
    infoTratoEspecial: {
      fecha_bitacora: (info) => cambiarFormatos(info.valor, 'fecha_hora'),

      cambios_efectuados: (info) => {
        const cambios = info.valor;
        const tieneCambios = cambios &&
          typeof cambios === 'object' &&
          Object.keys(cambios).length > 0;

        if (tieneCambios) {
          return `
            <ul class="list-inline me-auto mb-0">
              <li class="list-inline-item align-bottom" data-bs-toggle="tooltip" data-bs-placement="top" title="Ver cambios completos">
                <a href="#"
                   value="${info.fila.id_bitacora}"
                   class="btnVerCambiosBitacora avtar avtar-xs btn-link-success btn-pc-default">
                  <i class="fi fi-rs-eye fs-3 iconoCentrado"></i>
                </a>
              </li>
            </ul>
          `;
        }
        return `<span class="text-muted">-</span>`;
      },

      resultado_bitacora: (info) => {
        let color = '';
        const texto = String(info.valor || '').toLowerCase();
        
        if (texto === 'éxito' || texto === 'exito') {
          color = 'bg-success';
        } else if (texto === 'error' || texto === 'fallido') {
          color = 'bg-danger';
        } else if (texto === 'sin cambios') {
          color = 'bg-warning text-dark';
        } else {
          color = 'bg-secondary';
        }
        return `<span class="badge ${color}">${info.valor}</span>`;
      }
    }
  });

  const driverPendiente = sessionStorage.getItem('driver_pendiente');
  if (driverPendiente === 'bitacora') {
    sessionStorage.removeItem('driver_pendiente');
    setTimeout(() => {
      mostrarAyuda();
    }, 1000);
  }
});

$(document).off('click', '.btnVerCambiosBitacora');
$(document).on('click', '.btnVerCambiosBitacora', function(e) {
  e.preventDefault();
  verCambiosCompletos.call(this);
});

//#endregion [ DELEGACIÓN DE EVENTOS ] FIN