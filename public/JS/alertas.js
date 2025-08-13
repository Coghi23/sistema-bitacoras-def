    window.envioExito = function() {
        // Aquí puedes procesar el envío del formulario
        console.log('Creación en curso...');
        // Mostrar mensaje de éxito con SweetAlert
        Swal.fire({
            title: '¡Éxito!',
            text: 'Creación completada con éxito',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    };


        window.envioModificado = function() {
        // Aquí puedes procesar el envío del formulario
        console.log('Modificando datos...');
        // Mostrar mensaje de éxito con SweetAlert
        Swal.fire({
            title: '¡Éxito!',
            text: 'Modificación completada con éxito',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    };


        window.envioEliminado = function() {
        // Aquí puedes procesar el envío del formulario
        console.log('Eliminando datos...');
        // Mostrar mensaje de éxito con SweetAlert
        Swal.fire({
            title: '¡Éxito!',
            text: 'Eliminación completada con éxito',
            icon: 'success',
            confirmButtonText: 'OK'
        });
    };
