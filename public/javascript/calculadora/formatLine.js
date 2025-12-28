function formatLine(fila) {

    // funcion para seleccionar elementos del DOM
    const $ = el => document.querySelector(el);

    //UNIDADES
    let unidades = parseFloat($(`#f${fila} #c4 input`).value || 0);
    $(`#f${fila} #c4 input`).value = Math.round(unidades);  // Redondea las unidades

    //PRECIO VENTA
    let precioCompra = $(`#f${fila} #c5 input`).value || "0";
    precioCompra = precioCompra.replace(',', '.');                                                              // Cambia la coma por punto
    precioCompra = parseFloat(extractNumbers(precioCompra)).toFixed(2);                                         // Elimina caracteres no numéricos y redondea a dos decimales
    $(`#f${fila} #c5 input`).value = ((precioCompra==="0.00") || (precioCompra===null))? "" : precioCompra;     // Si el precio es 0 o null, se muestra el "placeholder" del input

    //DESCUENTO
    let descuento = $(`#f${fila} #c6 input`).value || "0";
    descuento = descuento.replace(',', '.');
    descuento = parseFloat(extractNumbers(descuento)).toFixed(2);
    $(`#f${fila} #c6 input`).value = ((descuento==="0.00") || (descuento===null))? "" : descuento;

    //PRECIO COMPRA 
    let precioComra = $(`#f${fila} #c9 input`).value || "0";
    precioComra = precioComra.replace(',', '.');
    precioComra = parseFloat(extractNumbers(precioComra)).toFixed(2);
    $(`#f${fila} #c9 input`).value = ((precioComra==="0.00") || (precioComra===null))? "" : precioComra;

    //DESCUENTO FACTURA
    let descuentoFactura = $(`#f${fila} #c10 input`).value || "0";
    descuentoFactura = descuentoFactura.replace(',', '.');
    descuentoFactura = parseFloat(extractNumbers(descuentoFactura)).toFixed(2);
    $(`#f${fila} #c10 input`).value = ((descuentoFactura==="0.00") || (descuentoFactura===null))? "" : descuentoFactura;

    //PALET
    let palet = $(`#f${fila} #c11 input`).value || "0";
    palet = palet.replace(',', '.');
    palet = parseFloat(extractNumbers(palet)).toFixed(2);
    $(`#f${fila} #c11 input`).value = ((palet==="0.00") || (palet===null))? "" : palet;

    //CANTIDAD
    let cantidad = $(`#f${fila} #c12 input`).value || "0";
    cantidad = cantidad.replace(',', '.');
    cantidad = parseFloat(extractNumbers(cantidad)).toFixed(2);
    $(`#f${fila} #c12 input`).value = ((cantidad==="0.00") || (cantidad===null))? "" : cantidad;

    //PLV
    let plv = $(`#f${fila} #c13 input`).value || "0";
    plv = plv.replace(',', '.');
    plv = parseFloat(extractNumbers(plv)).toFixed(2);
    $(`#f${fila} #c13 input`).value = ((plv==="0.00") || (plv===null))? "" : plv;

    //EXTRA
    let extra = $(`#f${fila} #c14 input`).value || "0";
    extra = extra.replace(',', '.');
    extra = parseFloat(extractNumbers(extra)).toFixed(2);
    $(`#f${fila} #c14 input`).value = ((extra==="0.00") || (extra===null))? "" : extra;

    //PORTE
    let porte = $(`#f${fila} #c16 input`).value || "0";
    porte = porte.replace(',', '.');
    porte = parseFloat(extractNumbers(porte)).toFixed(2);
    $(`#f${fila} #c16 input`).value = ((porte==="0.00") || (porte===null))? "" : porte;

    //RAPPEL
    let rappel = $(`#f${fila} #c18 input`).value || "0";
    rappel = rappel.replace(',', '.');
    rappel = parseFloat(extractNumbers(rappel)).toFixed(2);
    $(`#f${fila} #c18 input`).value = ((rappel==="0.00") || (rappel===null))? "" : rappel;

}

function extractNumbers(input) {
    // Filtra los caracteres del input y devuelve solo los números, el punto y su signo
    let result = input.split('').filter(char => {
        return (char >= '0' && char <= '9') || char === '.' || char === '-';
    }).join('');
    
    return result === "" ? "0.00" : result;
}