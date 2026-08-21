//#region [ IMPORTACIONES ] COMIENZO
import {
  alertasAjax, encabezadosPeticiones, rutaAbsoluta, enviarFormulario, 
} from './global.js';
import { driverAyuda, mostrarAyuda } from "/proyecto-lacruz-j/src/assets/js/configs/configDriver.js"
//#endregion [ IMPORTACIONES ] FIN

//#region [ FUNCIONES PROPIAS DEL MODULO ] COMIENZO

// Función centralizada para generar y visualizar cualquier reporte PDF
async function ejecutarReporte(formulario, nombreReporte) {
  const formData = new FormData(formulario);
  const datos = {};
  formData.forEach((value, key) => {
    datos[key] = value;
  });
  datos.reporte = nombreReporte;

  if (nombreReporte === 'reporteCierre' && !datos.fecha_cierre) {
    Swal.fire('Atención', 'Debe seleccionar una fecha de cierre', 'warning');
    return;
  }

  if ((nombreReporte === 'reporteVentas' || nombreReporte === 'reporteCompras') && (!datos.fecha_desde || !datos.fecha_hasta)) {
    Swal.fire('Atención', 'Debe seleccionar el intervalo de fechas (Desde y Hasta)', 'warning');
    return;
  }

  Swal.fire({
    title: 'Generando reporte',
    text: 'Por favor espere...',
    allowOutsideClick: false,
    didOpen: () => Swal.showLoading()
  });

  try {
    let resultado = await fetch(rutaAbsoluta + 'reportes', {
      method: 'POST',
      headers: encabezadosPeticiones,
      body: JSON.stringify(datos)
    });
    Swal.close();

    const contentType = resultado.headers.get('content-type') || '';
    if (contentType.includes('application/pdf')) {
      let pdf = await resultado.blob();
      const url = window.URL.createObjectURL(pdf);
      window.open(url, '_blank');
    } else {
      let respuesta = await resultado.json();
      alertasAjax(respuesta);
    }
  } catch (error) {
    Swal.close();
    console.error('Error al generar reporte:', error);
    alertasAjax({ icono: 'error', titulo: 'Error', texto: 'No se pudo generar el reporte.' });
  }
}

// Función para cargar items específicos en ventas
async function cargarItemsVentas() {
  const tipo = document.getElementById('tipo_producto_ventas').value;
  const divItem = document.getElementById('div_item_especifico_ventas');
  const selectItem = document.getElementById('id_item_ventas');

  if (tipo === 'especifico') {
    divItem.style.display = 'block';

    const filtro = document.getElementById('filtro_items_ventas')?.value || 'todos';
    let accion = '';
    switch (filtro) {
      case 'productos':
        accion = 'listar_productos';
        break;
      case 'servicios':
        accion = 'listar_servicios';
        break;
      default:
        accion = 'listar_items';
    }

    // Petición AJAX para cargar los items 
    try {
      let response = await fetch(rutaAbsoluta + 'reportes', {
        method: 'POST',
        headers: encabezadosPeticiones,
        body: JSON.stringify({ accion: accion })
      });

      let data = await response.json();
      selectItem.innerHTML = '<option value="">Seleccione un item</option>';

      if (data.length === 0) {
        selectItem.innerHTML += '<option value="" disabled>No hay items disponibles</option>';
      } else {
        data.forEach(item => {
          selectItem.innerHTML += `<option value="${item.id_producto_servicio}">
            ${item.nombre_producto_servicio} ${item.tipo ? `(${item.tipo})` : ''}
          </option>`;
        });
      }

    } catch (error) {
      console.error('Error cargando items:', error);
      selectItem.innerHTML = '<option value="">Error al cargar items</option>';
    }

  } else {
    divItem.style.display = 'none';
  }
}

// Función específica para cargar solo productos
async function cargarSoloProductos() {
  try {
    let response = await fetch(rutaAbsoluta + 'reportes', {
      method: 'POST',
      headers: encabezadosPeticiones,
      body: JSON.stringify({ accion: 'listar_productos' })
    });

    let data = await response.json();
    const selectItem = document.getElementById('id_item_ventas');

    selectItem.innerHTML = '<option value="">Seleccione un producto</option>';
    data.forEach(producto => {
      selectItem.innerHTML += `<option value="${producto.id_producto_servicio}">
        ${producto.nombre_producto_servicio} - Stock: ${producto.stock || 'N/A'}
      </option>`;
    });

  } catch (error) {
    console.error('Error cargando productos:', error);
  }
}

// Función específica para cargar solo servicios
async function cargarSoloServicios() {
  try {
    let response = await fetch(rutaAbsoluta + 'reportes', {
      method: 'POST',
      headers: encabezadosPeticiones,
      body: JSON.stringify({ accion: 'listar_servicios' })
    });

    let data = await response.json();
    const selectItem = document.getElementById('id_item_ventas');

    selectItem.innerHTML = '<option value="">Seleccione un servicio</option>';
    data.forEach(servicio => {
      selectItem.innerHTML += `<option value="${servicio.id_producto_servicio}">
        ${servicio.nombre_producto_servicio} - Duración: ${servicio.duracion || 'N/A'}
      </option>`;
    });

  } catch (error) {
    console.error('Error cargando servicios:', error);
  }
}

// Función para cambiar el filtro y recargar los items
function cambiarFiltroItems() {
  const filtro = document.getElementById('filtro_items_ventas').value;
  const selectItem = document.getElementById('id_item_ventas');

  selectItem.innerHTML = '<option value="">Cargando...</option>';

  switch (filtro) {
    case 'productos':
      cargarSoloProductos();
      break;
    case 'servicios':
      cargarSoloServicios();
      break;
    default:
      cargarItemsVentas();
  }
}

// Función para cargar materias primas en compras
async function cargarMateriasPrimas() {
  const tipo = document.getElementById('tipo_materia').value;
  const divMateria = document.getElementById('div_materia_especifica');
  const selectMateria = document.getElementById('id_materia');

  if (tipo === 'especifico') {
    divMateria.style.display = 'block';

    let resultado = await fetch(rutaAbsoluta + 'reportes', {
      method: 'POST',
      headers: encabezadosPeticiones,
      body: JSON.stringify({ accion: 'listar_materias_primas' })
    })
      .then(response => response.json())
      .then(data => {
        selectMateria.innerHTML = '<option value="">Seleccione una materia prima</option>';
        data.forEach(materia => {
          selectMateria.innerHTML += `<option value="${materia.id_materia_prima}">${materia.nombre_materia_prima}</option>`;
        });
      })
      .catch(error => console.error('Error cargando materias primas:', error));
  } else {
    divMateria.style.display = 'none';
  }
}

// Función para mostrar campos de período
function mostrarCamposPeriodo(tipo) {
  const periodo = document.getElementById(`periodo_${tipo}`).value;
  const divPersonalizado = document.getElementById(`div_periodo_personalizado_${tipo}`);
  const divEspecifico = document.getElementById(`div_periodo_especifico_${tipo}`);

  divPersonalizado.style.display = periodo === 'personalizado' ? 'flex' : 'none';
  divEspecifico.style.display = (periodo === 'mes' || periodo === 'anio') ? 'flex' : 'none';
}

async function renderizarGraficas() {

  // Ventas por año
  const data = [
    { year: 2010, count: 10 },
    { year: 2011, count: 20 },
    { year: 2012, count: 15 },
    { year: 2013, count: 25 },
    { year: 2014, count: 22 },
    { year: 2015, count: 30 },
    { year: 2016, count: 28 },
    { year: 2017, count: 24 },
    { year: 2018, count: 21 },
    { year: 2019, count: 23 },
    { year: 2020, count: 27 },
    { year: 2021, count: 28 },
    { year: 2022, count: 33 },
    { year: 2023, count: 23 },
    { year: 2024, count: 56 },
    { year: 2025, count: 78 },
    { year: 2026, count: 99 },
    { year: 2027, count: 11 },
    { year: 2028, count: 34 },
    { year: 2029, count: 43 },
    { year: 2030, count: 45 },
    { year: 2031, count: 67 },
    { year: 2032, count: 12 },
    { year: 2033, count: 23 }
  ];
  new Chart($('#grafica1')[0], {
    type: 'bar',
    data: {
      labels: data.map(row => row.year),
      datasets: [
        {
          label: ' Nro de ventas: ',
          data: data.map(row => row.count),
          backgroundColor: [
            'rgba(255, 99, 132, 0.5)',
            'rgba(255, 159, 64, 0.5)',
            'rgba(255, 205, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(201, 203, 207, 0.5)'
          ],
          borderColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ],
          borderWidth: 2
        }
      ]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    },
  });

  // Indice de unidades vendidas de cada producto ventas en la semana
  let datosProductos = [
    { producto: 'Jabon', 'Unidades Vendidas': 111, unidad_medida: 'Litros' },
    { producto: 'Cloro', 'Unidades Vendidas': 257, unidad_medida: 'Litros' },
    { producto: 'Desinfectante', 'Unidades Vendidas': 40, unidad_medida: 'Litros' },
    { producto: 'AZUFRE', 'Unidades Vendidas': 499, unidad_medida: 'Kilos' },
  ];
  new Chart($('#grafica2')[0], {
    type: 'pie',
    data: {
      labels: datosProductos.map(row => row.producto),
      datasets: [
        {
          label: datosProductos.map(producto => producto.unidad_medida),
          data: datosProductos.map(row => row['Unidades Vendidas']),
          backgroundColor: [
            'rgba(255, 99, 132, 0.5)',
            'rgba(255, 205, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(201, 203, 207, 0.5)'
          ],
          borderColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ],
          borderWidth: 2
        }
      ]
    },
    options: {
      plugins: {
        tooltip: {
          callbacks: {
            label: function (context) {
              let value = context.parsed || 0;
              let unidadMedida = context.dataset.label[context.dataIndex]
              return ` ${value} ${unidadMedida} `;
            }
          }
        }
      }
    }
  });

  //Porcentaje de ingresos de productos vs servicios
  let promedioProductosServicios = [
    { item: 'Productos', promedio: 37 },
    { item: 'Servicios', promedio: 63 },
  ];
  new Chart($('#grafica3')[0], {
    type: 'doughnut',
    data: {
      labels: promedioProductosServicios.map(item => item.item),
      datasets: [
        {
          label: ' Porcentaje equivalente',
          data: promedioProductosServicios.map(row => row.promedio),
          backgroundColor: [
            'rgba(255, 99, 132, 0.5)',
            'rgba(54, 162, 235, 0.5)',
          ],
          borderColor: [
            'rgb(255, 99, 132)',
            'rgb(75, 192, 192)',
          ],
          borderWidth: 2
        }
      ]
    },
    options: {
      plugins: {
        tooltip: {
          callbacks: {
            label: function (context) {
              let label = context.label || '';
              let value = context.parsed || 0;
              return ` ${label}: ${value}%`;
            }
          }
        }
      }
    }
  });

  //Top 10 mejores clientes de la empresa
  let comprasPorCliente = [
    { nombre_cliente: 'ANDERSON FREITEZ', promedioCompras: 37 },
    { nombre_cliente: 'CARLOS HURTADO', promedioCompras: 63 },
    { nombre_cliente: 'JANGELY LACRUZ', promedioCompras: 22 },
    { nombre_cliente: 'OMAR SHALOM', promedioCompras: 47 },
    { nombre_cliente: 'YEISON CARREÑO ', promedioCompras: 90 },
    { nombre_cliente: 'ANDERSON FREITEZ', promedioCompras: 37 },
    { nombre_cliente: 'CARLOS HURTADO', promedioCompras: 63 },
    { nombre_cliente: 'JANGELY LACRUZ', promedioCompras: 22 },
    { nombre_cliente: 'OMAR SHALOM', promedioCompras: 47 },
    { nombre_cliente: 'YEISON CARREÑO ', promedioCompras: 90 },
  ];
  new Chart($('#grafica4')[0], {
    type: 'bar',
    data: {
      labels: comprasPorCliente.map(cliente => cliente.nombre_cliente),
      datasets: [
        {
          label: ' Aporte total($) ',
          data: comprasPorCliente.map(row => row.promedioCompras),
          backgroundColor: [
            'rgba(255, 99, 132, 0.5)',
            'rgba(255, 159, 64, 0.5)',
            'rgba(255, 205, 86, 0.5)',
            'rgba(75, 192, 192, 0.5)',
            'rgba(54, 162, 235, 0.5)',
            'rgba(153, 102, 255, 0.5)',
            'rgba(201, 203, 207, 0.5)'
          ],
          borderColor: [
            'rgb(255, 99, 132)',
            'rgb(255, 159, 64)',
            'rgb(255, 205, 86)',
            'rgb(75, 192, 192)',
            'rgb(54, 162, 235)',
            'rgb(153, 102, 255)',
            'rgb(201, 203, 207)'
          ],
          borderWidth: 2
        }
      ]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: function (context) {
              let value = context.formattedValue || 0;
              return ` ${value}$`;
            }
          }
        }
      }
    },
  });
}


//#endregion [ FUNCIONES PROPIAS DEL MODULO ] FIN

//#region [ FUNCIONES DE AYUDA ] COMIENZO

function registrarTutorial() {
  driverAyuda('reportes', {
    pasos: [
      {
        element: '#formReporteVentas',
        popover: {
          title: 'Reporte de Ventas',
          description: 'Genera reportes de ventas. Puedes filtrar por tipo de item y rango de fechas.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: '#formReporteCompras',
        popover: {
          title: 'Reporte de Compras',
          description: 'Genera reportes de compras a proveedores.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: '#formCierreCaja',
        popover: {
          title: 'Cierre de Caja',
          description: 'Genera el reporte de cierre de caja para una fecha específica.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: '#formReporteServicios',
        popover: {
          title: 'Catálogo de Servicios',
          description: 'Genera un listado completo de todos los servicios disponibles.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: '#formReporteProductos',
        popover: {
          title: 'Inventario de Productos',
          description: 'Genera un listado completo de todos los productos disponibles.',
          side: 'top',
          align: 'start'
        }
      },
      {
        element: '#formReporteMateriaPrima',
        popover: {
          title: 'Inventario de Materias Primas',
          description: 'Genera un listado completo de todas las materias primas disponibles.',
          side: 'top',
          align: 'start'
        }
      },
      {
        popover: {
          title: '¡Ayuda completada!',
          description: 'Ya conoces los reportes disponibles.',
          side: 'top'
        }
      }
    ]
  });
}

//#endregion [ FUNCIONES DE AYUDA ] FIN

//#region [ DELEGACIÓN DE EVENTOS ] COMIENZO

// Evento de carga de la pagina
$(document).ready(function () {
  registrarTutorial();
  
  setTimeout(() => {
    const driverPendiente = sessionStorage.getItem('driver_pendiente');
    if (driverPendiente === 'reportes') {
      sessionStorage.removeItem('driver_pendiente');
      setTimeout(() => {
        mostrarAyuda();
      }, 800);
    }
  }, 600);

  let i = 0;
  while ($(`#Datepicker${i}`).length > 0) {
    $(`#Datepicker${i}`).datepicker({
      format: 'dd-mm-yyyy',
      language: 'es',
      todayHighlight: true,
      autoclose: true
    });
    i++;
  }

  $('.input-daterange').datepicker({
    format: 'dd-mm-yyyy',
    language: 'es',
    todayHighlight: true,
    autoclose: true
  });
});

// Delegación de eventos para los 6 formularios de reportes
$(document).off('submit', '#formReporteVentas').on('submit', '#formReporteVentas', function (e) {
  e.preventDefault();
  ejecutarReporte(this, 'reporteVentas');
});

$(document).off('submit', '#formReporteCompras').on('submit', '#formReporteCompras', function (e) {
  e.preventDefault();
  ejecutarReporte(this, 'reporteCompras');
});

$(document).off('submit', '#formCierreCaja').on('submit', '#formCierreCaja', function (e) {
  e.preventDefault();
  ejecutarReporte(this, 'reporteCierre');
});

$(document).off('submit', '#formReporteServicios').on('submit', '#formReporteServicios', function (e) {
  e.preventDefault();
  ejecutarReporte(this, 'reporteServicios');
});

$(document).off('submit', '#formReporteProductos').on('submit', '#formReporteProductos', function (e) {
  e.preventDefault();
  ejecutarReporte(this, 'reporteProductos');
});

$(document).off('submit', '#formReporteMateriaPrima').on('submit', '#formReporteMateriaPrima', function (e) {
  e.preventDefault();
  ejecutarReporte(this, 'reporteMateriaPrima');
});

//#endregion [ DELEGACIÓN DE EVENTOS ] FIN