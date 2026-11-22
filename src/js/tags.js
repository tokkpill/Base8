/**
 * Módulo para la gestión de campos de entrada tipo tags/etiquetas.
 * Permite añadir, mostrar y eliminar etiquetas dinámicamente.
 */
(function () {

    const tagsInput = document.querySelector('#tags_input')

    if (tagsInput) {

        const tagsDiv = document.querySelector('#tags');
        const tagsInputHidden = document.querySelector('[name="tags"]');

        let tags = [];

        // Recuperar del input oculto
        if (tagsInputHidden.value !== '') {
            tags = tagsInputHidden.value.split(',');
            mostrarTags();
        }

        // Escuchar los cambios en el input
        tagsInput.addEventListener('keypress', guardarTag)

        /**
         * Guarda una etiqueta cuando se presiona la tecla coma (",").
         * 
         * @param {Event} e Evento de teclado
         */
        function guardarTag(e) {
            if (e.keyCode === 44) {
                if (e.target.value.trim() === '' || e.target.value < 1) {
                    return
                }
                e.preventDefault();
                tags = [...tags, e.target.value.trim()];
                tagsInput.value = '';
                mostrarTags();
            }
        }

        /**
         * Renderiza las etiquetas actuales en el DOM.
         */
        function mostrarTags() {
            tagsDiv.textContent = '';
            tags.forEach(tag => {
                const etiqueta = document.createElement('LI');
                etiqueta.classList.add('formulario__tag')
                etiqueta.textContent = tag;
                etiqueta.ondblclick = eliminarTag
                tagsDiv.appendChild(etiqueta)
            })
            actualizarInputHidden();
        }

        /**
         * Elimina una etiqueta del listado y del DOM.
         * 
         * @param {Event} e Evento de doble click en la etiqueta
         */
        function eliminarTag(e) {
            e.target.remove()
            tags = tags.filter(tag => tag !== e.target.textContent)
            actualizarInputHidden();
        }

        /**
         * Sincroniza el listado de etiquetas con el campo oculto del formulario.
         */
        function actualizarInputHidden() {
            tagsInputHidden.value = tags.toString();
        }
    }

})();