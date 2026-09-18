import { rutaAbsoluta, alertasAjax, pedirDatosAjax } from './global.js';
export function initExportarDB() {
  // Abrir modal
  $(document).off('click', '#btnExportarBD');
  $(document).on('click', '#btnExportarBD', function (e) {
    e.preventDefault();
    $('#modalExportarBD').modal('show');
  });

  // Confirmar exportación
  $(document).off('click', '#btnConfirmarExportar');
  $(document).on('click', '#btnConfirmarExportar', async function () {
    const basesSeleccionadas = [];
    $('.checkExportBD:checked').each(function () {
      basesSeleccionadas.push($(this).val());
    });

    if (basesSeleccionadas.length === 0) {
      await alertasAjax({
        tipo: 'simple',
        icono: 'warning',
        titulo: 'Sin selección',
        texto: 'Debe seleccionar al menos una base de datos'
      });
      return;
    }

    // Mostrar estado
    const btn = $(this);
    btn.prop('disabled', true);
    $('#exportarStatus').removeClass('d-none');

    // Exportar cada base seleccionada
    for (const base of basesSeleccionadas) {
      const nombre = base === 'proyecto_lacruz_seguridad' ? 'proyecto_lacruz_seguridad' : 'proyecto_lacruz';
      try {
        let r = await pedirDatosAjax({
          'modulo': 'exportar',
          'datosPe': {
            'accion': 'exportar',
            'BD': nombre
          }
        })
        setTimeout(() => { }, 1500)
        const enlace = document.createElement('a');
        enlace.href = r;
        console.log(r.split())
        let nombreArchivo = r.split()[r.split().length - 1]
        enlace.download = nombreArchivo;
        document.body.appendChild(enlace);
        enlace.click();
        document.body.removeChild(enlace);
      } catch (error) {
        console.error('Error al exportar ' + nombre + ':', error);
      }
    }

    // Ocultar estado y cerrar modal
    $('#exportarStatus').addClass('d-none');
    btn.prop('disabled', false);
    $('#modalExportarBD').modal('hide');

    await alertasAjax({
      tipo: 'simple',
      icono: 'success',
      titulo: 'Exportación iniciada',
      texto: 'Se descargarán ' + basesSeleccionadas.length + ' archivo(s) SQL'
    });
  });
}