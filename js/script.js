// Función para mostrar menú del Header.
document.addEventListener("DOMContentLoaded", function () {
    const iconMenu = document.getElementById("iconmenu");
    const menu = document.querySelector("nav.menu");

    iconMenu.addEventListener("click", function () {
        menu.classList.toggle("show-menu");
    });

    // Ocultar el menú si se hace clic fuera de él en dispositivos móviles
    document.addEventListener("click", function (event) {
        const target = event.target;
        const isClickInsideMenu = menu.contains(target);
        const isClickOnIcon = target === iconMenu;

        if (!isClickInsideMenu && !isClickOnIcon && menu.classList.contains("show-menu")) {
            menu.classList.remove("show-menu");
        }
    });
});

//Función para procesar un préstamo.
document.getElementById("prestamoForm").addEventListener("submit", function (e) {
    e.preventDefault();

    const copiaId = document.querySelector("input[name='copia_id']").value;
    const cedula = document.querySelector("input[name='cedula']").value;

    fetch('php/procesar_prestamoPaquete.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `copia_id=${copiaId}&cedula=${cedula}`
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(`Error: ${data.error}`);
            } else {
                const facturaHTML = `
            <div id="factura">
                <div class="encabezado">Biblioteca Nocturna de las Nubes</div>
                <div class="detalle">Referencia de préstamo: ${data.referencia_prestamo}</div>
                <div class="detalle">Fecha de préstamo: ${data.fecha_prestamo}</div>
                <div class="detalle">Fecha de entrega: ${data.fecha_entrega}</div>
                <div class="detalle">Libro prestado: ${data.libro_prestado}</div>
                <div class="detalle">Autor del libro: ${data.autor_libro}</div>
                <div class="detalle">Nombre del lector: ${data.nombre_lector}</div>
                <div class="detalle">Cédula del lector: ${data.cedula_lector}</div>
                <div class="pie-pagina">Gracias por su préstamo</div>
                <br>
                <button id="boton-imprimir"  onclick="imprimirRecibo()" >Imprimir factura</button>
                <br>
            </div>`;
                document.getElementById("resultado").innerHTML = facturaHTML;
            }
            console.log('Data:', data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error en la solicitud: ' + error.message);
        });
});


