document.getElementById("descargar-button").addEventListener("click", function () {
    console.log("Descargando Excel");

    // Crear un nuevo libro de Excel
    var libro = new ExcelJS.Workbook();
    var nombreHoja = document.getElementById("nombre-version").innerText;
    var hoja = libro.addWorksheet(nombreHoja);

    // Obtener la cabecera de la tabla
    var filasCabecera = document.querySelectorAll("thead tr");

    // Crear estilos para la cabecera principal
    var estiloCabecera = {
        font: { bold: true, color: { argb: "FF333333" } }, // Negrita y color de texto negro
        fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: "FF6AB9FF" } }, // Color de fondo #6AB9FF
        alignment: { 
            horizontal: 'center', // Centrar el texto horizontalmente
            vertical: 'middle'    // Centrar el texto verticalmente
        }
    };

    // Crear estilo para la subcabecera
    var estiloSubcabecera = {
        font: { bold: true, color: { argb: "FF333333" } }, // Negrita y color de texto negro
        fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: "FFA3D1FF" } }, // Color de fondo #A3D1FF
        alignment: { 
            horizontal: 'center', // Centrar el texto horizontalmente
            vertical: 'middle'    // Centrar el texto verticalmente
        }
    };

    // Crear estilo para las filas impares
    var estiloFilaImpar = {
        fill: { type: 'pattern', pattern: 'solid', fgColor: { argb: "FFE6F0FF" } }, // Color de fondo #E6F0FF
        alignment: { 
            horizontal: 'center', // Centrar el texto horizontalmente
            vertical: 'middle'    // Centrar el texto verticalmente
        }
    };

    // Crear estilo para las celdas normales
    var estiloCelda = {
        alignment: { 
            horizontal: 'center', // Centrar el texto horizontalmente
            vertical: 'middle'    // Centrar el texto verticalmente
        }
    };

    // Crear estilo para bordes
    var estiloBordeDerecho = {
        border: {
            right: { style: 'thin', color: { argb: 'FF000000' } } // Borde derecho delgado y color negro
        }
    };

    // Agregar filas de cabecera con estilos
    filasCabecera.forEach(function (row, rowIndex) {
        var cols = row.querySelectorAll("th");
        var startCol = 1; // Comenzar desde la columna 1

        // Agregar celdas de cabecera con colspan
        cols.forEach(function (col) {
            var colspan = parseInt(col.getAttribute('colspan') || 1, 10);
            var cellValue = col.innerText;

            // Fusionar celdas si el colspan es mayor a 1
            if (colspan > 1) {
                hoja.mergeCells(rowIndex + 1, startCol, rowIndex + 1, startCol + colspan - 1);
            }

            // Aplicar valores y estilos a las celdas
            var cell = hoja.getCell(rowIndex + 1, startCol);
            cell.value = cellValue;
            cell.font = estiloCabecera.font;
            // Agrega fill según sea la primera o la segunda fila
            if (rowIndex === 0) {
                cell.fill = estiloCabecera.fill;
            } else {
                cell.fill = estiloSubcabecera.fill;
            }
            cell.alignment = estiloCabecera.alignment;

            startCol += colspan; // Avanzar la columna de inicio
        });
    });

    // Obtener los datos de la tabla
    var filas = document.querySelectorAll("tbody tr");
    filas.forEach(function (fila, rowIndex) {
        var datosFila = [];
        fila.querySelectorAll("td").forEach(function (celda) {
            if (celda.querySelector("input")) {
                datosFila.push(celda.querySelector("input").value || "0.00"); // Agrega el valor de input
            } else {
                datosFila.push(celda.innerText || "0.00"); // Agrega el texto de la celda
            }
        });
        var newRow = hoja.addRow(datosFila); // Agrega la fila de datos a la hoja

        // Aplicar estilos de fondo a las filas impares
        if (rowIndex % 2 === 0) { // Filas pares en el cuerpo de la tabla
            newRow.eachCell(function (cell) {
                cell.alignment = estiloCelda.alignment; // Centrar el texto horizontal y verticalmente
            });
        } else { // Filas impares
            newRow.eachCell(function (cell) {
                cell.fill = estiloFilaImpar.fill; // Color de fondo para filas impares
                cell.alignment = estiloFilaImpar.alignment; // Centrar el texto horizontal y verticalmente
            });
        }
    });

    //comprobar si la url actual es de compras
    if (window.location.href.includes("compras")) {
        // Agregar borde derecho a columnas específicas
        var columnasConBordeDerecho = [4, 9, 16, 18, 20, 22]; // Índices de las columnas (1-based)
    } else {
        // Agregar borde derecho a columnas específicas
        var columnasConBordeDerecho = [4, 9, 16, 18, 20]; // Índices de las columnas (1-based)
    }

    columnasConBordeDerecho.forEach(function (colIndex) {
        hoja.eachRow({ includeEmpty: true }, function (row) {
            var cell = row.getCell(colIndex);
            cell.border = estiloBordeDerecho.border; // Aplicar el borde derecho
        });
    });

    // Agregar el pie de página (tfoot)
    var filaFooter = document.querySelector("tfoot tr");
    var startColFooter = 1; // Comenzar desde la columna 1

    filaFooter.querySelectorAll("td").forEach(function (col) {
        var colspan = parseInt(col.getAttribute('colspan') || 1, 10);
        var cellValue = col.innerText;

        // Agregar celdas de pie de página con colspan
        if (colspan > 1) {
            hoja.mergeCells(filaFooter.rowIndex + 1, startColFooter, filaFooter.rowIndex + 1, startColFooter + colspan - 1);
        }

        var cell = hoja.getCell(filaFooter.rowIndex + 1, startColFooter);
        cell.value = cellValue;
        cell.font = { bold: true, color: { argb: "FF333333" } }; // Estilo de fuente para el pie de página
        cell.fill = estiloCabecera.fill; // Usar el mismo color de fondo que la cabecera
        cell.alignment = estiloCabecera.alignment; // Centrar texto

        startColFooter += colspan; // Avanzar la columna de inicio
    });

    // Ajustar el ancho de las columnas según el contenido
    hoja.columns.forEach(function (column) {
        var maxLength = 0;
        column.eachCell({ includeEmpty: true }, function (cell) {
            var cellLength = cell.value ? cell.value.toString().length : 10;
            if (cellLength > maxLength) {
                maxLength = cellLength;
            }
        });
        column.width = maxLength + 5; // Ajustar el ancho de la columna
    });

    // Guardar el archivo
    var nombreLibro = document.getElementById("cod-presupuesto").innerText.replace(/\s/g, '');
    nombreLibro = nombreLibro + "_" + nombreHoja.replace(/\s/g, ''); // Elimina los espacios en blanco

    // Generar el archivo Excel y descargarlo
    libro.xlsx.writeBuffer().then(function (data) {
        var blob = new Blob([data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
        var url = window.URL.createObjectURL(blob);
        var a = document.createElement("a");
        a.href = url;
        a.download = nombreLibro + ".xlsx";
        a.click();
        window.URL.revokeObjectURL(url);
    });
});
