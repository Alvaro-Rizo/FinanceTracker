document.addEventListener("DOMContentLoaded", function() {
    // Inicialización de Select2 para tags (si no está ya en tu blade)
    if ($().select2) {
        $('#tags').select2({
            placeholder: 'Seleccione etiquetas',
            width: '100%',
            theme: 'bootstrap-5',
            closeOnSelect: false
        });
    }

    // Manejo del formulario de filtros
    const filterForm = document.getElementById("main-filter-form");
    
    if (filterForm) {
        // Prevenir envío tradicional si es AJAX
        filterForm.addEventListener("submit", function(e) {
            if (window.location.search === '?' + new URLSearchParams(new FormData(filterForm)).toString()) {
                e.preventDefault();
                return;
            }
        });

        // Validación de fechas y montos
        filterForm.addEventListener("submit", function(e) {
            const startDate = document.getElementById("start_date").value;
            const endDate = document.getElementById("end_date").value;
            const minAmount = parseFloat(document.getElementById("min_amount").value) || 0;
            const maxAmount = parseFloat(document.getElementById("max_amount").value) || 0;

            if (startDate && endDate && new Date(startDate) > new Date(endDate)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error en fechas',
                    text: 'La fecha de inicio no puede ser mayor a la fecha final'
                });
                return false;
            }

            if (minAmount > 0 && maxAmount > 0 && minAmount > maxAmount) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Error en montos',
                    text: 'El monto mínimo no puede ser mayor al monto máximo'
                });
                return false;
            }
        });

        // Opcional: Auto-enviar al cambiar ciertos campos (sin bucle)
        const autoSubmitFields = ['type', 'account_id', 'category_id', 'tags'];
        autoSubmitFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.addEventListener("change", function() {
                    // Solo enviar si hay un cambio real
                    if (this.value !== this.dataset.lastValue) {
                        this.dataset.lastValue = this.value;
                        filterForm.submit();
                    }
                });
            }
        });
    }

    // Manejo del estado del colapso de filtros
    const filtersCollapse = document.getElementById("filtersCollapse");
    if (filtersCollapse) {
        const bsCollapse = new bootstrap.Collapse(filtersCollapse, {
            toggle: localStorage.getItem('filtersCollapseState') !== 'hidden'
        });

        filtersCollapse.addEventListener('hidden.bs.collapse', function() {
            localStorage.setItem('filtersCollapseState', 'hidden');
        });

        filtersCollapse.addEventListener('shown.bs.collapse', function() {
            localStorage.setItem('filtersCollapseState', 'shown');
        });
    }
});