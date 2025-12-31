<?php

$pdo = require_once __DIR__ . "/../../config/bootstrap.php";

//redirigir a buscador si no hay info en $_GET
if (!isset($_GET['id'])) redirigir("/buscador");

$id = limpiar_dato($_GET['id']);
$id_visual = limpiar_dato($_GET['id_visual'] ?? $id);

//si version no esta definida, redirigir a version 1
if (!isset($_GET['version'])) {
  redirigir("/calculadora/?id=$id_visual&version=1");
}

$version = limpiar_dato($_GET['version']);



try {

  // CONSULTA PARA OBTENER LA INFORMACIÓN GENERAL DEL PRESUPUESTO
  $queryHead = "SELECT * FROM presupuestos WHERE cod_presupuesto=:id AND version=:version";
  $stmtHead = $pdo->prepare($queryHead);
  $stmtHead->execute([':id' => $id, ':version' => $version]);
  $presupuesto = $stmtHead->fetch(PDO::FETCH_ASSOC);

  //CONSULTA PARA OBTENER TODA LA INFORMACIÓN DEL PRESUPUESTO CON PDO
  $query = "SELECT * FROM articulos WHERE cod_presupuesto=:id AND version=:version ORDER BY id ASC";
  $stmt = $pdo->prepare($query);
  $stmt->execute([':id' => $id, ':version' => $version]);
  $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
  $nart = sizeof($data);

  // CONSULTA PARA OBTENER LA LISTA DE VERSIONES DEL PRESUPUESTO
  $query = "SELECT DISTINCT version, nombre_version FROM presupuestos WHERE cod_presupuesto=:id ORDER BY version DESC";
  $stmt = $pdo->prepare($query);
  $stmt->execute([':id' => $id]);
  $versiones = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Si no existe el presupuesto o la versión, redirigir a errores
  if (!$presupuesto || $nart == 0) {
    redirigir("errores");
  }
} catch (PDOException $e) {
  error_log("Error en calculadora: " . $e->getMessage());
  redirigir("errores");
}

// CORREOS CORPORATIVOS DE PRUEBA
$correos = [
  'juan.perez@marginlab.com',
  'maria.garcia@marginlab.com',
  'carlos.lopez@marginlab.com',
  'ana.martinez@marginlab.com',
  'luis.gonzalez@marginlab.com',
  'sofia.rodriguez@marginlab.com',
  'david.fernandez@marginlab.com',
  'lucia.sanchez@marginlab.com',
  'miguel.torres@marginlab.com'
];

// FUNCION PARA CALCULAR EL RAPPEL DE LA FILA (USADA PARA MOSTRAR LA ALERTA)
function calcularRappel($linea)
{
  // Preparamos variables para que la fórmula sea legible
  $pv = $linea['precio_venta'];
  $dv = $linea['descuento_venta'];
  $u  = $linea['unidades'];
  $pc = $linea['precio_compra'];

  // Suma de descuentos del proveedor
  $dtos_prov = $linea['descuento_factura'] + $linea['palet'] + $linea['cantidad'] + $linea['plv'];
  $extra = $linea['extra'];
  $rap = $linea['rappel'];
  $porte = $linea['porte'];

  // 1. Calcular Ingreso Neto (Lo que paga el cliente)
  $ingreso_neto = ($pv - ($pv * ($dv / 100))) * $u;

  // Evitar división por cero si regalamos el producto o no hay unidades
  if ($ingreso_neto == 0) return 0;

  // 2. Calcular Coste Neto (Precio compra menos descuentos cascada)
  // Precio tras primer bloque de descuentos
  $coste_base = $pc - ($pc * ($dtos_prov / 100));
  // Precio tras descuento extra
  $coste_con_extra = $coste_base - ($coste_base * ($extra / 100));

  // Coste total de las unidades
  $coste_total_base = $coste_con_extra * $u;

  // Restamos el Rappel (dinero que nos devuelven)
  $coste_menos_rappel = $coste_total_base - ($coste_total_base * ($rap / 100));

  // Sumamos el porte (coste de transporte)
  // IMPORTANTE: Asegúrate de que 'porte' en DB sea numérico. Si es texto '60€', fallará.
  // En tu seed es varchar(50), así que forzamos conversión float
  $coste_final = $coste_menos_rappel + (floatval($porte) * $u);

  // 3. Cálculo del Margen %
  $margen_bsv = (($ingreso_neto - $coste_final) / $ingreso_neto) * 100;

  return $margen_bsv;
}

$alertaNeto = false;
$alertaRappel = false;

?>

<!DOCTYPE html>
<html lang="es">

<!--HEAD-->
<?php
$title_name = "Calculadora - Presupuesto $id_visual";
$page_css = "modules/calculadora.css";
require_once BASE_DIR . "/api/templates/head.php";
?>

<!--IMPORTACIÓN DE LOS SCRIPTS-->
<script src="<?= BASE_URL . "/javascript/calculadora/calcLine.js" ?>"></script> <!--funciones para calcular los valores de la fila-->
<script src="<?= BASE_URL . "/javascript/calculadora/calcFooter.js" ?>"></script> <!--funciones para calcular los valores del footer-->
<script src="<?= BASE_URL . "/javascript/calculadora/formatLine.js" ?>"></script> <!--funciones para dar formato a los valores de la fila-->
<script src="https://cdn.jsdelivr.net/npm/exceljs@4.3.0/dist/exceljs.min.js"></script> <!--libreria para exportar a excel con estilos-->
<script src="<?= BASE_URL . "/javascript/calculadora/excel.js" ?>" defer></script> <!--script para exportar a excel-->
<script src="<?= BASE_URL . "/javascript/calculadora/checkCambios.js" ?>" defer></script>
<script defer>
  var peticionManual = false; //indica si el formulario se ha enviado manualmente

  function submitForm(action) {
    var form = document.getElementById('formTabla');
    form.action = action;
    form.submit();
  }
  //funcion para mostrar dialogo de crear nueva versión
  function addDescription(tipo) {
    if (tipo == 'crear') {
      var input = document.querySelector('#valor-input');
      input.value = '';
      input.style.borderColor = 'var(--black)';

      var count = document.querySelector('#count');
      count.innerHTML = '0 / 40';
      count.style.color = 'var(--gris5)';

      var dialog = document.querySelector('.dialog-version');
      dialog.showModal(); // Mostrar el dialogo para introducir la descripción
    } else {
      var input = document.querySelector('#valor-input-change');
      input.value = '';
      input.style.borderColor = 'var(--black)';

      var count = document.querySelector('#count-change');
      count.innerHTML = '0 / 40';
      count.style.color = 'var(--gris5)';
      var dialog = document.querySelector('.dialog-cambiar-nombre');
      dialog.showModal(); // Mostrar el dialogo para introducir la descripción
    }
  }

  function enviarVersion() {
    var form = document.getElementById('formTabla');
    var input = document.querySelector('#valor-input');
    var value = input.value;

    if (value.length > 0 && value.length <= 40) {
      form.insertAdjacentHTML('beforeend', '<input type="hidden" name="nombre_version" value="' + value + '">');
      console.log('Enviando formulario...');
      peticionManual = true;
      submitForm('<?= BASE_URL . "/actions/calculadora/insert.php" ?>?id=<?= $id_visual ?>&v=<?= $version ?>&n=<?= $nart ?>');
    }
  }

  function closeGuardarWarning() {
    var dialog = document.querySelector('.guardar-warning');
    dialog.close();
    var selector = document.getElementById('selectVersion');
    selector.value = '<?= $version ?>';
    selector.blur();
  }
  //funcion para eliminar version
  function deleteVersion() {
    window.location.href = '<?= BASE_URL . "/actions/calculadora/deleteVersion.php" ?>?id=<?= $id_visual ?>&version=<?= $version ?>';
  }
  //funcion para añadir un correo al formulario
  function addMail() {
    const inputs = document.querySelector('.inputs');

    //obtener el name del ultimo input del div (eliminar el string "correo" y convertir a entero)
    const lastInput = inputs.lastElementChild.querySelector('input').name;
    const lastNumber = parseInt(lastInput.replace('correo', ''));

    //contruye el name para el nuevo input
    const newNumber = lastNumber + 1;
    const newName = 'correo' + newNumber;
    const newId = 'email' + newNumber;

    //crea contenedor de .input-line
    const inputLine = document.createElement('div');
    inputLine.classList.add('input-line');

    //crea input de correo
    const newInput = document.createElement('input');
    newInput.type = 'email';
    newInput.name = newName;
    newInput.id = newId;
    newInput.setAttribute('list', 'correos');
    newInput.placeholder = 'user@marginlab.com';
    newInput.required = true;
    newInput.autocomplete = 'off';

    //crea boton de eliminar
    const deleteButton = document.createElement('button');
    deleteButton.type = 'button';
    deleteButton.id = 'delete-mail';
    deleteButton.innerHTML = `
        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-square-rounded-minus">
            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M9 12h6" />
            <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9 -9 9s-9 -1.8 -9 -9s1.8 -9 9 -9z" />
        </svg>
        `;
    deleteButton.onclick = function() {
      inputLine.remove();
    }


    //añade los elementos al contenedor
    inputLine.appendChild(newInput);
    inputLine.appendChild(deleteButton);

    inputs.appendChild(inputLine);

  }
  //funcion que se ejecuta al cambiar la version en el selector de la tabla
  function cambiarVersion(sinGuardar) {

    //sinGuardar=true indica que se quiere cambiar de version aunque haya cambios sin guardar

    var versionSeleccionada = document.getElementById('selectVersion').value;
    versionSeleccionada = versionSeleccionada.replace('Versión ', '');
    var url = '?id=<?= $id_visual ?>&version=' + versionSeleccionada;

    if (sinGuardar) {
      window.location = url;
      return;
    }

    //no deja cambiar de versión si hay cambios sin guardar (mirar archivo ../scripts/checkCambios.js)
    if (hayUnsaved) {
      var dialog = document.querySelector('.guardar-warning');
      dialog.showModal(); //muestra el dialogo de cambios sin guardar
      return;
    }

    window.location.href = url;
  }


  document.addEventListener('DOMContentLoaded', function() {

    //listener para mostrar panel de alertas al pulsar en primera columna
    var colWarning = document.querySelectorAll('.fila-warning #c0');
    colWarning.forEach(col => {
      //estilar para dar cursor de ayuda
      col.style.cursor = 'pointer';
      col.addEventListener('click', function() {
        document.querySelector('.dialog-warning').showModal(); //muestra el dialogo de explicacion de los warnings
      });
    });


    // Listener para procesar el formulario de seleccionar versiones al cambiar el select
    var selectVersion = document.getElementById('selectVersion');
    if (selectVersion) {
      selectVersion.addEventListener('change', function() {
        cambiarVersion(false);
      });
    }

    // Listener para procesar el formulario de crear versiones al pulsar enter
    document.getElementById('formVersion').addEventListener('submit', function(event) {
      if (!peticionManual) {
        event.preventDefault(); // Evita el envío del formulario
        enviarVersion(); // Llama a la función para manejar el envío
      } else {
        peticionManual = false; // Restablece la bandera para el próximo envío
      }
    });


    //listener de todos los dialog para cerrarlos al hacer clic fuera de ellos
    var dialog = document.querySelectorAll('dialog');
    dialog.forEach(d => {
      d.addEventListener('click', function(event) {
        // Comprobar si el clic ocurrió fuera del contenido del dialog
        var rect = d.getBoundingClientRect();
        if (event.clientX < rect.left || event.clientX > rect.right ||
          event.clientY < rect.top || event.clientY > rect.bottom) {
          d.close();
        }
      });
    });

    //calcular los totales de la tabla
    calcFooter(<?= $nart ?>);
  });
</script>

<body>

  <?php require_once BASE_DIR . "/api/templates/header.php" ?>


  <main>

    <section class="top">

      <div class="versiones" <?= ($version == 0) ? 'style="padding: 30px 0px;"' : '' ?>>
        <p id="cod-presupuesto">Presupuesto - <?= $id_visual ?></p>

        <svg width="1440" height="3" viewBox="0 0 1440 2" preserveAspectRatio="none" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
          <path d="M0 1H1440" stroke="currentColor" />
        </svg>

        <?php if (sizeof($versiones) > 1) { ?>
          <form id='formVersiones' method='post'>
            <select id='selectVersion' name='version'>
              <?php foreach ($versiones as $v) { ?>
                <option value="<?= $v['version'] ?>" <?= ($v['version'] == $version) ? "selected" : "" ?>>
                  Versión <?= $v['version'] ?>
                </option>
              <?php } ?>
            </select>
          </form>
        <?php } else { ?>
          <div>
            <p>Versión <?= $version ?></p>
          </div>
        <?php } ?>

        <p id="nombre-version">
          <svg id="svgAlerta" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16">
            <path fill="currentColor" fill-rule="evenodd" d="M6.285 1.975C7.06.68 8.939.68 9.715 1.975l5.993 9.997c.799 1.333-.161 3.028-1.716 3.028H2.008C.453 15-.507 13.305.292 11.972zM8 5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 5m1 6.5a1 1 0 1 1-2 0a1 1 0 0 1 2 0" clip-rule="evenodd" />
          </svg>
          <?php foreach ($versiones as $v) {
            if ($v['version'] == $version) {
              echo $v['nombre_version'];
            }
          } ?>

          <?php if ($version != 1) { ?>
            <button id="change-nombre" type="button" onclick="addDescription('cambiar');">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                <path d="M13.5 6.5l4 4" />
              </svg>
            </button>
            <button id="delete-version" type="button" onclick="deleteVersion();" data-label="Elimina la versión">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                <path id="tapa" d="M4 7h16 M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                <path d="M10 11v6" />
                <path d="M14 11v6" />
                <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
              </svg>
            </button>
          <?php } ?>

        </p>


      </div>

      <div class="info">
        <ul>
          <?php if ($presupuesto['cod_cliente'] !== null && $presupuesto['cod_cliente'] !== "") {
            echo "<li><strong>Código del cliente: </strong><span>" . $presupuesto['cod_cliente'] . "</span></li>";
          } ?>
          <?php if ($presupuesto['nomb_cliente'] !== null && $presupuesto['nomb_cliente'] !== "") {
            echo "<li><strong>Nombre del cliente: </strong><span>" . $presupuesto['nomb_cliente'] . "</span></li>";
          } ?>
          <?php if ($presupuesto['referencia'] !== null && $presupuesto['referencia'] !== "") {
            echo "<li><strong>Referencia: </strong><span>" . $presupuesto['referencia'] . "</span></li>";
          } ?>
          <?php if ($presupuesto['obra'] !== null && $presupuesto['obra'] !== "") {
            echo "<li><strong>Obra: </strong><span>" . $presupuesto['obra'] . "</span></li>";
          } ?>
        </ul>
      </div>

    </section>



    <section class="sect">
      <form id="formTabla" method="POST"> <!-- n indica el numero de articulos || v indica la version -->
        <input type="hidden" name="cod_cliente" value="<?= $presupuesto['cod_cliente'] ?>">
        <input type="hidden" name="nomb_cliente" value="<?= $presupuesto['nomb_cliente'] ?>">
        <input type="hidden" name="referencia" value="<?= $presupuesto['referencia'] ?>">
        <input type="hidden" name="obra" value="<?= $presupuesto['obra'] ?>">

        <table id="main-tabla">
          <thead>
            <tr id="head1">
              <th colspan="4" class="colfija" id="top">ARTÍCULO</th>
              <th id="c2" colspan="5">VENTA</th>
              <th id="c3" colspan="7">COMPRA</th>
              <th id="c4" colspan="2">PORTE</th>
              <th id="c5" colspan="2">RAPPEL</th>
              <th id="c6" colspan="2">BSV</th>
            </tr>
            <tr id="head2">
              <th id="c0" class="colfija" style="max-width: 55px; min-width: 55px;"></th>
              <th id="c1" class="colfija" style="max-width: 75px; min-width: 75px;">Proveedor</th>
              <th id="c2" class="colfija" style="max-width: 85px; min-width: 85px;">Código</th>
              <th id="c3" class="colfija">Descripción</th>
              <th id="c4">Ud.</th>
              <th id="c5">Precio</th>
              <th id="c6">DTO</th>
              <th id="c7">Neto</th>
              <th id="c8">Importe venta</th>
              <th id="c9">Precio</th>
              <th id="c10">DTO. Fact.</th>
              <th id="c11">Palet</th>
              <th id="c12">Cantidad</th>
              <th id="c13">PLV</th>
              <th id="c14">Extra</th>
              <th id="c15">Precio factura</th>
              <th id="c16">Porte</th>
              <th id="c17">P.c/Porte</th>
              <th id="c18">Rappel</th>
              <th id="c19">P.c./rappel</th>
              <th id="c20">Factura</th>
              <th id="c21">Rappel</th>
            </tr>
          </thead>
          <tbody>
            <?php for ($i = 0; $i < $nart; $i++) {
              $row = "<tr id='f$i'";
              $ventaNeto = ($data[$i]['descuento_venta'] == 0) ? true : false;
              $bsvRappelNegativo = (calcularRappel($data[$i]) < 0) ? true : false;

              //comprueba si el precio la fila tiene alguna alerta
              if ($ventaNeto || $bsvRappelNegativo) {
                $row .= " class='fila-warning'";
              }
              $row .= ">";
              echo $row;

              //establece precio de compra en articulos manuales (cod_art empieza por 9)
              // SOLO SE COPIA SI precio_compra=0 -------------------------------------------------------
              $codigo_articulo = $data[$i]['cod_art'];
              if ($codigo_articulo[0] == '9') {
                $data[$i]['precio_compra'] = ($data[$i]['precio_compra'] == 0) ? $data[$i]['precio_venta'] : $data[$i]['precio_compra'];
              }

            ?>
              <td id="c0" class="colfija">
                <?php
                if ($ventaNeto) {
                  if (!$alertaNeto) {
                    $alertaNeto = true;
                  }
                  echo '<svg class="warn-neto" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M6.285 1.975C7.06.68 8.939.68 9.715 1.975l5.993 9.997c.799 1.333-.161 3.028-1.716 3.028H2.008C.453 15-.507 13.305.292 11.972zM8 5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 5m1 6.5a1 1 0 1 1-2 0a1 1 0 0 1 2 0" clip-rule="evenodd"/></svg>';
                }
                if ($bsvRappelNegativo) {
                  if (!$alertaRappel) {
                    $alertaRappel = true;
                  }
                  echo '<svg class="warn-bsv" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16"><path fill="currentColor" fill-rule="evenodd" d="M6.285 1.975C7.06.68 8.939.68 9.715 1.975l5.993 9.997c.799 1.333-.161 3.028-1.716 3.028H2.008C.453 15-.507 13.305.292 11.972zM8 5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 5m1 6.5a1 1 0 1 1-2 0a1 1 0 0 1 2 0" clip-rule="evenodd"/></svg>';
                }
                ?>
                <?= $i ?>
              </td>
              <td id='c1' class="colfija">
                <?= $data[$i]['proveedor'] ?>
                <input type="hidden" name="f<?= $i ?>-proveedor" value="<?= $data[$i]['proveedor'] ?>"> <!--Proveedor-->
              </td>
              <td id='c2' class="colfija">
                <?= $data[$i]['cod_art'] ?>
                <input type="hidden" name="f<?= $i ?>-cod_art" value="<?= $data[$i]['cod_art'] ?>"> <!--Código-->
              </td>
              <td id='c3' class="colfija">
                <?= $data[$i]['descripcion'] ?>
                <input type="hidden" name="f<?= $i ?>-descripcion" value="<?= $data[$i]['descripcion'] ?>"> <!--Descripción-->
              </td>
              <td id='c4'>
                <?php
                // input muestran el valor de la base de datos
                //     - name es el nombre del campo en la base de datos para procesarlo en el script save.php
                //     - value muestra el valor de la base de datos (si el valor es 0, coloca null para mostrar valor del "placeholder")
                //     - oninput llama a la función calcLine() para hacer el calculo con cada cambio
                //     - onblur llama a la función formatLine() para dar formato a los datos (eliminar letras, redondear, etc)
                ?> <!--Unidades-->
                <input type="number"
                  id="f<?= $i ?>-unidades"
                  name="f<?= $i ?>-unidades"
                  value="<?= $data[$i]['unidades'] == 0 ? null : $data[$i]['unidades'] ?>"
                  placeholder="0"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-unidades')">
              </td>
              <td id='c5'> <!--Precio-->
                <input type="text"
                  id="f<?= $i ?>-precio_venta"
                  name="f<?= $i ?>-precio_venta"
                  value="<?= $data[$i]['precio_venta'] == 0 ? null : $data[$i]['precio_venta'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-precio_venta')">
              </td>
              <td id='c6'> <!--Descuento-->
                <input type="text"
                  id="f<?= $i ?>-descuento_venta"
                  name="f<?= $i ?>-descuento_venta"
                  value="<?= $data[$i]['descuento_venta'] == 0 ? null : $data[$i]['descuento_venta'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-descuento_venta')">
              </td>
              <td id='c7' class="calculado">
                <?= $data[$i]['neto_venta'] ?> <!--Neto-->
              </td>
              <td id='c8' class="calculado">
                <?= $data[$i]['importe_venta'] ?> <!--Importe venta-->
              </td>
              <td id='c9'> <!--Precio-->
                <input type="text"
                  id="f<?= $i ?>-precio_compra"
                  name="f<?= $i ?>-precio_compra"
                  value="<?= $data[$i]['precio_compra'] == 0 ? null : $data[$i]['precio_compra'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-precio_compra')">
              </td>
              <td id='c10'> <!--Descuento factura-->
                <input type="text"
                  id="f<?= $i ?>-descuento_factura"
                  name="f<?= $i ?>-descuento_factura"
                  value="<?= $data[$i]['descuento_factura'] == 0 ? null : $data[$i]['descuento_factura'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-descuento_factura')">
              </td>
              <td id='c11'> <!--Palet-->
                <input type="text"
                  id="f<?= $i ?>-palet"
                  name="f<?= $i ?>-palet"
                  value="<?= $data[$i]['palet'] == 0 ? null : $data[$i]['palet'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-palet')">
              </td>
              <td id='c12'> <!--Cantidad-->
                <input type="text"
                  id="f<?= $i ?>-cantidad"
                  name="f<?= $i ?>-cantidad"
                  value="<?= $data[$i]['cantidad'] == 0 ? null : $data[$i]['cantidad'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-cantidad')">
              </td>
              <td id='c13'> <!--PLV-->
                <input type="text"
                  id="f<?= $i ?>-plv"
                  name="f<?= $i ?>-plv"
                  value="<?= $data[$i]['plv'] == 0 ? null : $data[$i]['plv'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-plv')">
              </td>
              <td id='c14'> <!--Extra-->
                <input type="text"
                  id="f<?= $i ?>-extra"
                  name="f<?= $i ?>-extra"
                  value="<?= $data[$i]['extra'] == 0 ? null : $data[$i]['extra'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-extra')">
              </td>
              <td id='c15' class="calculado">
                <?= $data[$i]['precio_factura'] ?> <!--Precio factura-->
              </td>
              <td id='c16'> <!--Porte-->
                <input type="text"
                  id="f<?= $i ?>-porte"
                  name="f<?= $i ?>-porte"
                  value="<?= $data[$i]['porte'] == 0 ? null : $data[$i]['porte'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-porte')">
              </td>
              <td id='c17' class="calculado">
                <?= $data[$i]['pc_porte'] ?> <!--P.c/Porte-->
              </td>
              <td id='c18'> <!--Rappel-->
                <input type="text"
                  id="f<?= $i ?>-rappel"
                  name="f<?= $i ?>-rappel"
                  value="<?= $data[$i]['rappel'] == 0 ? null : $data[$i]['rappel'] ?>"
                  placeholder="0.00"
                  oninput="calcLine( <?= $i ?> ); calcFooter(<?= $nart ?>)"
                  onblur="formatLine( <?= $i ?> )"
                  autocomplete="off"
                  onchange="checkCambios('f<?= $i ?>-rappel')">
              </td>
              <td id='c19' class="calculado"> <?= $data[$i]['pc_rappel'] ?> </td> <!--P.c./rappel-->
              <td id='c20' class="calculado"> <?= $data[$i]['bsv_porc_fact'] ?> </td> <!--Factura-->
              <td id='c21' class="calculado"> <?= $data[$i]['bsv_porc_rappel'] ?> </td> <!--Rappel-->

              </tr>

              <script>
                calcLine(<?= $i ?>);
                formatLine(<?= $i ?>);
              </script>

            <?php } ?>

          </tbody>
          <tfoot>
            <tr>
              <td id="bottom" class="colfija" colspan="4" rowspan="2">TOTAL</td>
              <td colspan="4" rowspan="2"></td>
              <td id="ventaFoot" rowspan="2"></td>
              <td colspan="6" rowspan="2"></td>
              <td id="compraFoot" rowspan="2"></td>
              <td rowspan="2"></td>
              <td id="porteFoot" rowspan="2"></td>
              <td rowspan="2"></td>
              <td id="rappelFoot" rowspan="2"></td>
              <td id="bsvFacturaFootPorcent"></td>
              <td id="bsvRappelFootPorcent"></td>
            </tr>
            <tr>
              <td id="bsvFacturaFootEur"></td>
              <td id="bsvRappelFootEur"></td>
            </tr>
          </tfoot>
        </table>
    </section>


    <section class="exportar">
      <div class="observaciones">
        <label for="obsComercial">Observaciones del comercial</label>
        <textarea id="obsComercial" name="obsComercial" rows="10" cols="50" placeholder="Escribe tus observaciones aquí..."><?php
                                                                                                                            if ($presupuesto['obs_comercial'] !== null && $presupuesto['obs_comercial'] !== "") {
                                                                                                                              echo htmlspecialchars(trim($presupuesto['obs_comercial']));
                                                                                                                            }
                                                                                                                            ?></textarea>
      </div>
      <div class="botones">
        <?php if ($version > 1) { ?>
          <button type="button" onclick="submitForm('<?= BASE_URL ?>/actions/calculadora/save.php?id=<?= $id_visual ?>&v=<?= $version ?>&n=<?= $nart ?>')" data-label="Guarda los cambios de la versión de estudio">GUARDAR CAMBIOS</button>
        <?php } ?>
        <button type="button" onclick="addDescription('crear');" data-label="Crea una nueva versión del estudio con los cambios actuales">CREAR NUEVA VERSIÓN</button>
        <div class="botones-exportar">
          <button type="button" id="descargar-button" class="excel-button" data-label="Descarga en formato Excel la versión actual del presupuesto">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-down">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M12.5 21h-7.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v7.5" />
              <path d="M3 10h18" />
              <path d="M10 3v18" />
              <path d="M19 16v6" />
              <path d="M22 19l-3 3l-3 -3" />
            </svg>
          </button>
          <button type="button" data-label="Envía el presupuesto para revisión" onclick="document.querySelector('.dialog-correo').showModal();">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M10 14l11 -11" />
              <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
            </svg>
          </button>
        </div>
      </div>
      <div class="observaciones">
        <label for="obsRevision">Observaciones de revisión</label>
        <textarea id="obsRevision" name="obsRevision" rows="10" cols="50" placeholder="Escribe tus observaciones aquí..."><?php
                                                                                                                          if ($presupuesto['obs_revision'] !== null && $presupuesto['obs_revision'] !== "") {
                                                                                                                            echo htmlspecialchars(trim($presupuesto['obs_revision']));
                                                                                                                          }
                                                                                                                          ?></textarea>
      </div>
    </section>

    </form>

  </main>

  <dialog class="dialog-warning">
    <button id="cross-button" onclick="this.parentElement.close()">
      <svg id="cross" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 15 15">
        <path fill="currentColor" d="M3.64 2.27L7.5 6.13l3.84-3.84A.92.92 0 0 1 12 2a1 1 0 0 1 1 1a.9.9 0 0 1-.27.66L8.84 7.5l3.89 3.89A.9.9 0 0 1 13 12a1 1 0 0 1-1 1a.92.92 0 0 1-.69-.27L7.5 8.87l-3.85 3.85A.92.92 0 0 1 3 13a1 1 0 0 1-1-1a.9.9 0 0 1 .27-.66L6.16 7.5L2.27 3.61A.9.9 0 0 1 2 3a1 1 0 0 1 1-1c.24.003.47.1.64.27" />
      </svg>
    </button>

    <?php
    if ($alertaNeto) { ?>
      <div class="warning">
        <div id="neto" class="title">
          <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16">
            <path fill="currentColor" fill-rule="evenodd" d="M6.285 1.975C7.06.68 8.939.68 9.715 1.975l5.993 9.997c.799 1.333-.161 3.028-1.716 3.028H2.008C.453 15-.507 13.305.292 11.972zM8 5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 5m1 6.5a1 1 0 1 1-2 0a1 1 0 0 1 2 0" clip-rule="evenodd" />
          </svg>
          <h3>PRECIO<br>NETO</h3>
        </div>
        <div class="main">
          <p>Se han importado artículos con precio neto. Revisa que no se haya alterado el precio de compra del fabricante.</p>
        </div>
        <div class="imagen">
          <img src="<?= BASE_URL ?>/img/neto.png" alt="Artículo mostrado con precio neto" width="500" height="300" title="Artículo mostrado con precio neto" loading="lazy">
        </div>
      </div>

    <?php }
    if ($alertaNeto && $alertaRappel) { ?>
      <svg id="vertical-line" viewBox="0 0 2 100" preserveAspectRatio="none" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 0V1440" stroke="currentColor" />
      </svg>
    <?php }
    if ($alertaRappel) { ?>
      <div class="warning">
        <div id="bsv" class="title">
          <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16">
            <path fill="currentColor" fill-rule="evenodd" d="M6.285 1.975C7.06.68 8.939.68 9.715 1.975l5.993 9.997c.799 1.333-.161 3.028-1.716 3.028H2.008C.453 15-.507 13.305.292 11.972zM8 5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 5m1 6.5a1 1 0 1 1-2 0a1 1 0 0 1 2 0" clip-rule="evenodd" />
          </svg>
          <h3>RAPPEL<br>NEGATIVO</h3>
        </div>
        <div class="main">
          <p>Se muestran artículos con margen BSV Rappel negativo.</p>
        </div>
        <div class="imagen">
          <img src="<?= BASE_URL ?>/img/bsv.png" alt="Artículo con margen BSV Rappel negativo" width="500" height="300" title="Artículo con margen BSV Rappel negativo" loading="lazy">
        </div>
      </div>
    <?php }
    ?>

  </dialog>

  <?php if ($alertaNeto || $alertaRappel) { ?>
    <script>
      document.querySelector('.dialog-warning').showModal(); //muestra el dialogo de alertas al cargar la página
    </script>
  <?php } ?>

  <dialog class="dialog-version">
    <button id="cross-button" onclick="this.parentElement.close()">
      <svg id="cross" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 15 15">
        <path fill="currentColor" d="M3.64 2.27L7.5 6.13l3.84-3.84A.92.92 0 0 1 12 2a1 1 0 0 1 1 1a.9.9 0 0 1-.27.66L8.84 7.5l3.89 3.89A.9.9 0 0 1 13 12a1 1 0 0 1-1 1a.92.92 0 0 1-.69-.27L7.5 8.87l-3.85 3.85A.92.92 0 0 1 3 13a1 1 0 0 1-1-1a.9.9 0 0 1 .27-.66L6.16 7.5L2.27 3.61A.9.9 0 0 1 2 3a1 1 0 0 1 1-1c.24.003.47.1.64.27" />
      </svg>
    </button>

    <h3>Introduce un nombre o descripción para la nueva versión</h3>

    <form id="formVersion" action="#">
      <div class="campo-input">
        <input id="valor-input" type="text" name="cod_presupuesto" required autocomplete="off" maxlength="40">
        <label for="valor-input">Nombre de la versión</label>
        <output id="count">0 / 40</output>
        <button id="add-button" type="button" onclick="enviarVersion();">
          <svg id="add" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
            <path fill="currentColor" d="M18 14h2v3h3v2h-3v3h-2v-3h-3v-2h3zM4 3h14a2 2 0 0 1 2 2v7.08a6 6 0 0 0-4.32.92H12v4h1.08c-.11.68-.11 1.35 0 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2m0 4v4h6V7zm8 0v4h6V7zm-8 6v4h6v-4z" />
          </svg>
        </button>
      </div>
    </form>

    <script>
      //script para actualizar el contador de catacteres y marcar en rojo al llegar a 40
      var input = document.getElementById('valor-input');
      var output = document.getElementById('count');

      input.addEventListener('input', function() {
        output.textContent = input.value.length + ' / 40';
        if (input.value.length == 40) {
          input.style.color = 'var(--warning)';
          output.style.color = 'var(--warning)';
        } else {
          input.style.color = 'var(--black)';
          output.style.color = 'var(--gris5)';
        }
      });
    </script>

  </dialog>

  <dialog class="guardar-warning">
    <button id="cross-button" onclick="this.parentElement.close()">
      <svg id="cross" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 15 15">
        <path fill="currentColor" d="M3.64 2.27L7.5 6.13l3.84-3.84A.92.92 0 0 1 12 2a1 1 0 0 1 1 1a.9.9 0 0 1-.27.66L8.84 7.5l3.89 3.89A.9.9 0 0 1 13 12a1 1 0 0 1-1 1a.92.92 0 0 1-.69-.27L7.5 8.87l-3.85 3.85A.92.92 0 0 1 3 13a1 1 0 0 1-1-1a.9.9 0 0 1 .27-.66L6.16 7.5L2.27 3.61A.9.9 0 0 1 2 3a1 1 0 0 1 1-1c.24.003.47.1.64.27" />
      </svg>
    </button>

    <div id="neto" class="title">
      <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 16 16">
        <path fill="currentColor" fill-rule="evenodd" d="M6.285 1.975C7.06.68 8.939.68 9.715 1.975l5.993 9.997c.799 1.333-.161 3.028-1.716 3.028H2.008C.453 15-.507 13.305.292 11.972zM8 5a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-1.5 0v-3A.75.75 0 0 1 8 5m1 6.5a1 1 0 1 1-2 0a1 1 0 0 1 2 0" clip-rule="evenodd" />
      </svg>
      <h3>Hay cambios<br>sin guardar</h3>
    </div>

    <div class="opciones-guardar">
      <p>Se han detectado cambios en la página que no están guardados. ¿Quieres continuar sin guardar los cambios?</p>

      <div class="opciones-guardar-botones">
        <button class="opciones-boton-guardar" type="button" onclick="cambiarVersion(true);">CONTINUAR</button>
        <button class="opciones-boton-cancelar" type="button" onclick="closeGuardarWarning();">CANCELAR</button>
      </div>
    </div>

  </dialog>

  <dialog class="dialog-correo">
    <button id="cross-button" onclick="this.parentElement.close()">
      <svg id="cross" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 15 15">
        <path fill="currentColor" d="M3.64 2.27L7.5 6.13l3.84-3.84A.92.92 0 0 1 12 2a1 1 0 0 1 1 1a.9.9 0 0 1-.27.66L8.84 7.5l3.89 3.89A.9.9 0 0 1 13 12a1 1 0 0 1-1 1a.92.92 0 0 1-.69-.27L7.5 8.87l-3.85 3.85A.92.92 0 0 1 3 13a1 1 0 0 1-1-1a.9.9 0 0 1 .27-.66L6.16 7.5L2.27 3.61A.9.9 0 0 1 2 3a1 1 0 0 1 1-1c.24.003.47.1.64.27" />
      </svg>
    </button>

    <div class="titulo-correo">
      <h3>ENVIAR CORREO</h3>
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-mail">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M22 7.535v9.465a3 3 0 0 1 -2.824 2.995l-.176 .005h-14a3 3 0 0 1 -2.995 -2.824l-.005 -.176v-9.465l9.445 6.297l.116 .066a1 1 0 0 0 .878 0l.116 -.066l9.445 -6.297z" />
        <path d="M19 4c1.08 0 2.027 .57 2.555 1.427l-9.555 6.37l-9.555 -6.37a2.999 2.999 0 0 1 2.354 -1.42l.201 -.007h14z" />
      </svg>
    </div>

    <div class="texto-correo">
      <p>Envía el presupuesto vía mail. Para hacerlo, introduce la dirección de correo a la que quieres enviar el presupuesto para revisión.</p>

      <p>¿A qué dirección quieres enviar el correo?</p>


      <form action="../scripts/envio_correo.php" method="POST" onsubmit="return validarCorreo();">
        <div class="inputs">
          <input type="hidden" name="url" value="<?= $_SERVER['REQUEST_URI'] ?>">
          <div class="input-line">
            <input type="email"
              name="correo0"
              id="email0"
              list="correos"
              placeholder="user@marginlab.com"
              required
              autocomplete="off">
          </div>
        </div>

        <button type="button" id="add-mail" onclick="addMail();">+ Añadir destinatario</button>

        <div class="botones-correo">
          <button type="submit">ENVIAR</button>
          <button type="button">CANCELAR</button>
        </div>

        <datalist id="correos">
          <?php foreach ($correos as $correo) { ?>
            <option value="<?= $correo ?>">
            <?php } ?>
        </datalist>
      </form>
      <script>
        function validarCorreo() {
          document.querySelector('.dialog-correo').close();
          document.querySelector('.dialog-simulation').showModal();
          return false;
        }
      </script>
    </div>

  </dialog>

  <dialog class="dialog-cambiar-nombre">

    <button id="cross-button" onclick="this.parentElement.close()">
      <svg id="cross" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 15 15">
        <path fill="currentColor" d="M3.64 2.27L7.5 6.13l3.84-3.84A.92.92 0 0 1 12 2a1 1 0 0 1 1 1a.9.9 0 0 1-.27.66L8.84 7.5l3.89 3.89A.9.9 0 0 1 13 12a1 1 0 0 1-1 1a.92.92 0 0 1-.69-.27L7.5 8.87l-3.85 3.85A.92.92 0 0 1 3 13a1 1 0 0 1-1-1a.9.9 0 0 1 .27-.66L6.16 7.5L2.27 3.61A.9.9 0 0 1 2 3a1 1 0 0 1 1-1c.24.003.47.1.64.27" />
      </svg>
    </button>

    <h3>Introduce una nueva descripción</h3>

    <form id="formCambiarNombre" action="<?= BASE_URL ?>/actions/calculadora/changeDescription.php" method="POST">
      <div class="campo-input">
        <input type="hidden" value="<?= $version ?>" name="version">
        <input type="hidden" value="<?= $id_visual ?>" name="id">
        <input id="valor-input-change" type="text" name="nombre_version" required autocomplete="off" maxlength="40">
        <label for="valor-input-change">Nombre de la versión</label>
        <output id="count-change">0 / 40</output>
        <button id="add-button" type="button" onclick="document.getElementById('formCambiarNombre').submit();">
          <svg id="change" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-check">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M5 12l5 5l10 -10" />
          </svg>
        </button>
      </div>
    </form>

    <script>
      //script para actualizar el contador de catacteres y marcar en rojo al llegar a 40
      var input = document.getElementById('valor-input-change');
      var output = document.getElementById('count-change');

      input.addEventListener('input', function() {
        output.textContent = input.value.length + ' / 40';
        if (input.value.length == 40) {
          input.style.color = '#e63946';
          output.style.color = '#e63946';
        } else {
          input.style.color = '#333';
          output.style.color = '#858383';
        }
      });
    </script>

  </dialog>

  <dialog class="dialog-simulation">
    <button id="cross-button" onclick="this.parentElement.close()">
      <svg id="cross" xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 15 15">
        <path fill="currentColor" d="M3.64 2.27L7.5 6.13l3.84-3.84A.92.92 0 0 1 12 2a1 1 0 0 1 1 1a.9.9 0 0 1-.27.66L8.84 7.5l3.89 3.89A.9.9 0 0 1 13 12a1 1 0 0 1-1 1a.92.92 0 0 1-.69-.27L7.5 8.87l-3.85 3.85A.92.92 0 0 1 3 13a1 1 0 0 1-1-1a.9.9 0 0 1 .27-.66L6.16 7.5L2.27 3.61A.9.9 0 0 1 2 3a1 1 0 0 1 1-1c.24.003.47.1.64.27" />
      </svg>
    </button>

    <div class="title">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-info-circle">
        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
        <path d="M12 9h.01" />
        <path d="M11 12h1v4h1" />
      </svg>
      <h3>Funcionalidad no disponible</h3>
    </div>

    <div class="main">
      <p>Al ser una simulación de la herramienta real, el envío de correos está deshabilitado.</p>
    </div>
  </dialog>

</body>

<?php require_once BASE_DIR . "/api/templates/footer.php" ?>

</html>