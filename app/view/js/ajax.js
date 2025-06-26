const formulario_ajax = document.querySelectorAll(".FormularioAjax"); //selecciona todos los formularios con la clase FormularioAjax

// cuando se envia el formulario se ejecuta la funcion 
formulario_ajax.forEach(formularios => {
    formularios.addEventListener('submit', function(evento){
        evento.preventDefault(); //hace que se envia el formulario sin redireccionar
        // alerta que pregunta si se quiere enviar el formulario
        Swal.fire({
            title: "Estas seguro?",
            text: "Quieres enviar el formulario",
            icon: "question",
            theme: "dark",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "enviar",
            cancelButtonText: "cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                // si se le da aceptar a la alerta 
                let data = new FormData(this); // crea un array en base a la informacion del formulario (this hace referencia al formulario que se esta enviando)
                let method = this.getAttribute("method"); //obtine el method del formulario
                let action = this.getAttribute("action"); //obtine el action del formulario

                let encabezados = new Headers(); //crea un nuevo encabezado es obligatorio para el fetch

                let config = {
                    method: method,
                    headers: encabezados,
                    mode: 'cors',
                    cache: 'no-cache',
                    body: data
                };

                fetch(action, config) // el primer parametro es la url del formulario(a donde se envia el formulario) y el segundo parametro es la configuracion del fetch
                .then(respuesta => respuesta.json()) // la respuesta del archivo Carga la convierte en json
                .then(respuesta => {  
                    return alertas_ajax(respuesta); // llama a la funcion alertas_ajax que se encarga de mostrar la alerta
                });
            }
        });
    });
});

function alertas_ajax(alerta){

}