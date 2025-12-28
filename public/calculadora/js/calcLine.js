function calcLine(fila) {

    /* CAMPOS DE VENTA */
    let uds = parseFloat(document.querySelector(`#f${fila} #c4 input`).value) || 0;
    let precio_v = parseFloat(document.querySelector(`#f${fila} #c5 input`).value) || 0;
    let desc = parseFloat(document.querySelector(`#f${fila} #c6 input`).value) || 0;
    //calcular neto
    let neto = precio_v - (precio_v * (desc/100));
    document.querySelector(`#f${fila} #c7`).innerHTML = neto.toFixed(2);
    //calcular importe venta
    let importe = neto * uds;
    document.querySelector(`#f${fila} #c8`).innerHTML = importe.toFixed(2);

    

    /* CAMPOS DE COMPRA */
    let precio_c = parseFloat(document.querySelector(`#f${fila} #c9 input`).value) || 0;    //m
    let dto_fact = parseFloat(document.querySelector(`#f${fila} #c10 input`).value) || 0;   //n
    let palet = parseFloat(document.querySelector(`#f${fila} #c11 input`).value) || 0;    //p
    let cantidad = parseFloat(document.querySelector(`#f${fila} #c12 input`).value) || 0;   //q
    let plv = parseFloat(document.querySelector(`#f${fila} #c13 input`).value) || 0; //r
    let extra = parseFloat(document.querySelector(`#f${fila} #c14 input`).value) || 0;       //s

    //calcular factura
    let precio_fact_sinExtra = precio_c - (precio_c * ((dto_fact+palet+cantidad+plv)/100));
    //aplicar "especial" > multiplicar por unidades
    let precio_fact = precio_fact_sinExtra - ( precio_fact_sinExtra * (extra/100) );
    precio_fact = precio_fact * uds;
    document.querySelector(`#f${fila} #c15`).innerHTML = precio_fact.toFixed(2);  //t



    /* CAMPOS DE PORTE */
    let porte = parseFloat(document.querySelector(`#f${fila} #c16 input`).value) || 0;  //u
    let p_cporte = precio_fact + (porte * uds);
    document.querySelector(`#f${fila} #c17`).innerHTML = p_cporte.toFixed(2);  //v



  /* CAMPOS DE RAPPEL */
  let rappel = parseFloat(document.querySelector(`#f${fila} #c18 input`).value) || 0;  //y
  let pc_rappel = (precio_fact - (precio_fact * ((rappel) / 100))) + (porte * uds);
  document.querySelector(`#f${fila} #c19`).innerHTML = pc_rappel.toFixed(2);  //aa

  /* CAMPOS DE BSV */
  if (importe == 0) {
    document.querySelector(`#f${fila} #c20`).innerHTML = "0 %";
    document.querySelector(`#f${fila} #c21`).innerHTML = "0 %";
  } else {
    let bsv_factura = ((importe - p_cporte) / importe) * 100;
    document.querySelector(`#f${fila} #c20`).innerHTML = `${bsv_factura.toFixed(2)} %`;  //ab
    let bsv_rappel = ((importe - pc_rappel) / importe) * 100;
    document.querySelector(`#f${fila} #c21`).innerHTML = `${bsv_rappel.toFixed(2)} %`;  //ac
  }

}