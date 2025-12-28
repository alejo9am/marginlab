//CALCULO DE LOS TOTALES

function calcFooter(nart) {

    //venta
    let total_venta = 0;
    for(let i=0; i<nart ; i++) {
        let importe = parseFloat(document.querySelector(`#f${i} #c8`).innerHTML) || 0;
        total_venta += importe;
    }
    document.querySelector("#ventaFoot").innerHTML = `${total_venta.toFixed(2)} €`;

    //compra
    let total_compra = 0;
    for(let i=0; i<nart ; i++) {
        let importe = parseFloat(document.querySelector(`#f${i} #c15`).innerHTML) || 0;
        total_compra += importe;
    }
    document.querySelector("#compraFoot").innerHTML = `${total_compra.toFixed(2)} €`;

    //porte
    let total_porte = 0;
    for(let i=0; i<nart ; i++) {
        let importe = parseFloat(document.querySelector(`#f${i} #c17`).innerHTML) || 0;
        total_porte += importe;
    }
    document.querySelector("#porteFoot").innerHTML = `${total_porte.toFixed(2)} €`;

    //rappel
    let total_rappel = 0;
    for(let i=0; i<nart ; i++) {
        let importe = parseFloat(document.querySelector(`#f${i} #c19`).innerHTML) || 0;
        total_rappel += importe;
    }
    document.querySelector("#rappelFoot").innerHTML = `${total_rappel.toFixed(2)} €`;

    //bsv
    let bsvFacturaEur = total_venta - total_compra;
    document.querySelector("#bsvFacturaFootEur").innerHTML = `${bsvFacturaEur.toFixed(2)} €`;
    let bsvRappelEur = total_venta - total_rappel;
    document.querySelector("#bsvRappelFootEur").innerHTML = `${bsvRappelEur.toFixed(2)} €`;

    if (total_venta == 0) {
        document.querySelector("#bsvFacturaFootPorcent").innerHTML = "0 %";
        document.querySelector("#bsvRappelFootPorcent").innerHTML = "0 %";
    } else {
        let bsvFactPorcent = ((total_venta - total_porte) / total_venta) * 100;
        document.querySelector("#bsvFacturaFootPorcent").innerHTML = `${bsvFactPorcent.toFixed(2)} %`;
        let bsvRappelPorcent = ((total_venta - total_rappel) / total_venta) * 100;
        document.querySelector("#bsvRappelFootPorcent").innerHTML = `${bsvRappelPorcent.toFixed(2)} %`;
    }

}