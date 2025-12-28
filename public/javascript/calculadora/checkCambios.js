//Script que activa/desactiva flag que indica si hay cambios sin guardar

const valoresIniciales = [];    //guarda los valores iniciales de los campos

let hayUnsaved = false; //flag que indica si hay cambios sin guardar

function checkCambios(inputId){

    const fila = inputId.charAt(1);
    formatLine(fila);

    //almacenamos array con los valores actuales de los input
    const inputsActuales = document.querySelectorAll('#formTabla input:not([type="hidden"]');
    
    //recorremos TODOS los input y vemos si hay alguno que es distinto del valor inicial
    for (let i = 0; i < inputsActuales.length; i++) {
        const input = inputsActuales[i];
        if (input.value !== valoresIniciales[input.name]) {
            //console.log('Cambio en ' + input.name + ': '+valoresIniciales[input.name]+'------>' + input.value);
            hayUnsaved = true;
            break;
        } else {
            hayUnsaved = false;
        }
    }
    
    // console.log('Cambios no guardados: '+hayUnsaved);
    // console.log('-----------------------------------');

    // mostramos/ocultamos el icono de alerta
    const svgAlerta = document.getElementById('svgAlerta');
    if (hayUnsaved) {
        svgAlerta.style.display = 'block';
    } else {
        svgAlerta.style.display = 'none';
    }

    
}


//inicia los listener de los input y guarda sus valores al cargar la página
window.addEventListener('DOMContentLoaded', () => {
    //selecciona los input (los type hidden no hace falta)
    const inputs = document.querySelectorAll('#formTabla input:not([type="hidden"]');

    //guarda los valores iniciales de los input
    inputs.forEach(input => {
        valoresIniciales[input.name] = input.value || '';
        //console.log('Valor inicial de ' + input.name + ': ' + input.value);
    });

});