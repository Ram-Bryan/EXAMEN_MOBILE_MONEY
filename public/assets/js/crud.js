// Generic CRUD operations
class CRUDManager {
    constructor(endpoint, tableId) {
        this.endpoint = endpoint;
        this.tableId = tableId;
        this.dataTable = null;
    }
    
    // Initialize DataTable
    initTable(columns) {
        if ($.fn.DataTable) {
            this.dataTable = $(`#${this.tableId}`).DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
                },
                processing: true,
                serverSide: true,
                ajax: `${this.endpoint}/list`,
                columns: columns
            });
        }
    }
    
    // Load data
    loadData() {
        if (this.dataTable) {
            this.dataTable.ajax.reload();
        } else {
            $.ajax({
                url: `${this.endpoint}/list`,
                method: 'GET',
                success: function(data) {
                    $(`#${this.tableId}`).html(data);
                }.bind(this),
                error: function(xhr) {
                    notify.show('Erreur lors du chargement des données', 'error');
                }
            });
        }
    }
    
    // Create
    create(data, callback) {
        showLoader();
        $.ajax({
            url: `${this.endpoint}/create`,
            method: 'POST',
            data: data,
            success: function(response) {
                hideLoader();
                if (response.success) {
                    notify.show('Enregistrement créé avec succès', 'success');
                    this.loadData();
                    if (callback) callback(response);
                } else {
                    notify.show(response.message || 'Erreur lors de la création', 'error');
                }
            }.bind(this),
            error: function() {
                hideLoader();
                notify.show('Erreur lors de la création', 'error');
            }
        });
    }
    
    // Update
    update(id, data, callback) {
        showLoader();
        $.ajax({
            url: `${this.endpoint}/update/${id}`,
            method: 'PUT',
            data: data,
            success: function(response) {
                hideLoader();
                if (response.success) {
                    notify.show('Enregistrement mis à jour avec succès', 'success');
                    this.loadData();
                    if (callback) callback(response);
                } else {
                    notify.show(response.message || 'Erreur lors de la mise à jour', 'error');
                }
            }.bind(this),
            error: function() {
                hideLoader();
                notify.show('Erreur lors de la mise à jour', 'error');
            }
        });
    }
    
    // Delete
    delete(id, callback) {
        confirmAction('Êtes-vous sûr de vouloir supprimer cet enregistrement ?', function() {
            showLoader();
            $.ajax({
                url: `${this.endpoint}/delete/${id}`,
                method: 'DELETE',
                success: function(response) {
                    hideLoader();
                    if (response.success) {
                        notify.show('Enregistrement supprimé avec succès', 'success');
                        this.loadData();
                        if (callback) callback(response);
                    } else {
                        notify.show(response.message || 'Erreur lors de la suppression', 'error');
                    }
                }.bind(this),
                error: function() {
                    hideLoader();
                    notify.show('Erreur lors de la suppression', 'error');
                }
            });
        }.bind(this));
    }
    
    // Get single record
    get(id, callback) {
        $.ajax({
            url: `${this.endpoint}/get/${id}`,
            method: 'GET',
            success: function(data) {
                if (callback) callback(data);
            },
            error: function() {
                notify.show('Erreur lors du chargement des données', 'error');
            }
        });
    }
}

// Helper functions
function openModal(modalId, title, content) {
    $(`#${modalId}`).modal('show');
    $(`#${modalId} .modal-title`).html(title);
    $(`#${modalId} .modal-body`).html(content);
}

function closeModal(modalId) {
    $(`#${modalId}`).modal('hide');
}

// Form submission with AJAX
function submitForm(formId, callback) {
    const form = $(`#${formId}`);
    if (!validateForm(formId)) {
        notify.show('Veuillez remplir tous les champs obligatoires', 'warning');
        return;
    }
    
    const formData = new FormData(form[0]);
    const action = form.attr('action');
    
    showLoader();
    $.ajax({
        url: action,
        method: form.attr('method') || 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            hideLoader();
            if (response.success) {
                notify.show(response.message || 'Opération réussie', 'success');
                if (callback) callback(response);
            } else {
                notify.show(response.message || 'Erreur lors de l\'opération', 'error');
            }
        },
        error: function() {
            hideLoader();
            notify.show('Erreur lors de l\'opération', 'error');
        }
    });
}