document.addEventListener('DOMContentLoaded', function() {
    console.log('Application de gestion des colis chargée');
    
    // Active tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Date range picker initialization if exists
    if(document.getElementById('date_debut') && document.getElementById('date_fin')) {
        const dateDebut = document.getElementById('date_debut');
        const dateFin = document.getElementById('date_fin');
        
        if(dateDebut && dateFin) {
            dateDebut.addEventListener('change', function() {
                dateFin.min = this.value;
            });
            
            dateFin.addEventListener('change', function() {
                dateDebut.max = this.value;
            });
        }
    }
    
    // Search functionality
    const searchForm = document.getElementById('searchForm');
    if(searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const searchInput = document.getElementById('searchInput');
            if(!searchInput.value.trim()) {
                e.preventDefault();
                alert('Veuillez saisir un terme de recherche');
            }
        });
    }
    
    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    if(forms.length > 0) {
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }
    
    // Confirm delete
    const deleteButtons = document.querySelectorAll('.btn-delete');
    if(deleteButtons.length > 0) {
        deleteButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                if(!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
                    e.preventDefault();
                }
            });
        });
    }
    
    // Print receipt
    const printButtons = document.querySelectorAll('.btn-print');
    if(printButtons.length > 0) {
        printButtons.forEach(button => {
            button.addEventListener('click', function() {
                window.print();
            });
        });
    }
});