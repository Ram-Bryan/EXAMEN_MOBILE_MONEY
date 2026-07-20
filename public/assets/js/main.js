// Initialize tooltips and popovers
$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
    
    // Auto-hide alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

// Format number as currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-MG', {
        style: 'currency',
        currency: 'MGA',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(amount);
}

// Validation helper
function validateForm(formId) {
    let isValid = true;
    $(`#${formId} .required`).each(function() {
        if ($(this).val().trim() === '') {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });
    return isValid;
}

// Confirm dialog
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Loader
function showLoader() {
    $('#loader').show();
}

function hideLoader() {
    $('#loader').hide();
}