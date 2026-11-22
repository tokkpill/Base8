/**
 * Módulo para la gestión y búsqueda de ponentes en tiempo real.
 * Permite autocompletar ponentes desde una API y seleccionarlos.
 */
(function () {
    const ponentesInput = document.querySelector('#ponentes');

    if (ponentesInput) {
        let ponentes = [];
        let ponentesFiltrados = [];

        const listadoPonentes = document.querySelector('#listado-ponentes')
        const ponenteHidden = document.querySelector('[name="ponente_id"]')

        obtenerPonentes();
        ponentesInput.addEventListener('input', buscarPonentes)

        /**
         * Si hay un ponente previamente seleccionado (edición), lo obtiene y lo muestra en el DOM.
         */
        if (ponenteHidden.value) {
            (async () => {
                const ponente = await obtenerPonente(ponenteHidden.value)
                const { nombre, apellido } = ponente

                // Insertar en el HTML
                const ponenteDOM = document.createElement('LI');
                ponenteDOM.classList.add('listado-ponentes__ponente', 'listado-ponentes__ponente--seleccionado');
                ponenteDOM.textContent = `${nombre} ${apellido}`

                listadoPonentes.appendChild(ponenteDOM)
            })()
        }

        /**
         * Consulta la API para obtener la lista completa de ponentes.
         */
        async function obtenerPonentes() {
            const url = `/api/ponentes`;
            const respuesta = await fetch(url);
            const resultado = await respuesta.json();
            formatearPonentes(resultado)
        }

        /**
         * Consulta la API para obtener los datos de un ponente específico por su ID.
         * 
         * @param {string} id ID del ponente
         * @returns {Object} Datos del ponente
         */
        async function obtenerPonente(id) {
            const url = `/api/ponente?id=${id}`;
            const respuesta = await fetch(url)
            const resultado = await respuesta.json()
            return resultado;
        }

        /**
         * Normaliza los datos de los ponentes para facilitar la búsqueda.
         * 
         * @param {Array} arrayPonentes Lista de ponentes cruda de la API
         */
        function formatearPonentes(arrayPonentes = []) {
            ponentes = arrayPonentes.map(ponente => {
                return {
                    nombre: `${ponente.nombre.trim()} ${ponente.apellido.trim()}`,
                    id: ponente.id
                }
            })
        }

        /**
         * Filtra la lista de ponentes basándose en la entrada de texto del usuario.
         * 
         * @param {Event} e Evento de input
         */
        function buscarPonentes(e) {
            const busqueda = e.target.value;

            if (busqueda.length > 3) {
                const expresion = new RegExp(busqueda, "i");
                ponentesFiltrados = ponentes.filter(ponente => {
                    if (ponente.nombre.toLowerCase().search(expresion) != -1) {
                        return ponente
                    }
                })
            } else {
                ponentesFiltrados = []
            }

            mostrarPonentes();
        }

        /**
         * Renderiza los ponentes filtrados en el DOM.
         */
        function mostrarPonentes() {

            while (listadoPonentes.firstChild) {
                listadoPonentes.removeChild(listadoPonentes.firstChild)
            }

            if (ponentesFiltrados.length > 0) {
                ponentesFiltrados.forEach(ponente => {
                    const ponenteHTML = document.createElement('LI');
                    ponenteHTML.classList.add('listado-ponentes__ponente')
                    ponenteHTML.textContent = ponente.nombre;
                    ponenteHTML.dataset.ponenteId = ponente.id
                    ponenteHTML.onclick = seleccionarPonente

                    // Añadir al dom
                    listadoPonentes.appendChild(ponenteHTML)
                })
            } else {
                const noResultados = document.createElement('P')
                noResultados.classList.add('listado-ponentes__no-resultado')
                noResultados.textContent = 'No hay resultados para tu búsqueda'
                listadoPonentes.appendChild(noResultados)
            }
        }

        /**
         * Gestiona la selección visual de un ponente y actualiza el campo oculto.
         * 
         * @param {Event} e Evento de click en el ponente
         */
        function seleccionarPonente(e) {
            const ponente = e.target;

            // Remover la clase previa
            const ponentePrevio = document.querySelector('.listado-ponentes__ponente--seleccionado')
            if (ponentePrevio) {
                ponentePrevio.classList.remove('listado-ponentes__ponente--seleccionado')
            }
            ponente.classList.add('listado-ponentes__ponente--seleccionado')

            ponenteHidden.value = ponente.dataset.ponenteId
        }
    }
})();