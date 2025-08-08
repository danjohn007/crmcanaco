/**
 * Main JavaScript file for CRM CANACO
 */

$(document).ready(function() {
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Auto-hide alerts after 5 seconds
    $('.alert:not(.alert-permanent)').delay(5000).fadeOut(500);

    // Form validation enhancement
    $('form').on('submit', function(e) {
        var form = this;
        if (form.checkValidity() === false) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(form).addClass('was-validated');
    });

    // Search functionality
    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        var query = $('#searchQuery').val().trim();
        if (query.length > 0) {
            performSearch(query);
        }
    });

    // Real-time search
    $('#searchQuery').on('keyup', function() {
        var query = $(this).val().trim();
        if (query.length > 2) {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(function() {
                performQuickSearch(query);
            }, 300);
        }
    });

    // Customer journey stage updates
    $('.journey-stage').on('click', function() {
        var prospectId = $(this).data('prospect-id');
        var newStage = $(this).data('stage');
        if (prospectId && newStage) {
            updateCustomerJourneyStage(prospectId, newStage);
        }
    });

    // Agenda item completion
    $('.mark-completed').on('click', function(e) {
        e.preventDefault();
        var agendaId = $(this).data('agenda-id');
        if (agendaId) {
            markAgendaItemCompleted(agendaId);
        }
    });

    // Delete confirmation
    $('.btn-delete').on('click', function(e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var itemName = $(this).data('item-name') || 'este elemento';
        
        if (confirm('¿Está seguro que desea eliminar ' + itemName + '?')) {
            window.location.href = url;
        }
    });

    // Data tables initialization
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/Spanish.json'
            },
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']]
        });
    }

    // Auto-refresh notifications
    if ($('#notificationBadge').length > 0) {
        setInterval(refreshNotifications, 30000); // Every 30 seconds
    }

    // Dashboard auto-refresh
    if ($('.dashboard-stats').length > 0) {
        setInterval(refreshDashboardStats, 60000); // Every minute
    }
});

// Search functions
function performSearch(query) {
    showLoader();
    $.ajax({
        url: '/api/search',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            displaySearchResults(response);
        },
        error: function() {
            showNotification('Error al realizar la búsqueda', 'error');
        },
        complete: function() {
            hideLoader();
        }
    });
}

function performQuickSearch(query) {
    $.ajax({
        url: '/api/quick-search',
        method: 'GET',
        data: { q: query },
        success: function(response) {
            displayQuickSearchResults(response);
        }
    });
}

function displaySearchResults(results) {
    var html = '';
    if (results.length === 0) {
        html = '<div class="alert alert-info">No se encontraron resultados</div>';
    } else {
        html = '<div class="row">';
        results.forEach(function(item) {
            html += '<div class="col-md-6 mb-3">';
            html += '<div class="card">';
            html += '<div class="card-body">';
            html += '<h5 class="card-title">' + item.title + '</h5>';
            html += '<p class="card-text">' + item.description + '</p>';
            html += '<a href="' + item.url + '" class="btn btn-canaco">Ver detalles</a>';
            html += '</div></div></div>';
        });
        html += '</div>';
    }
    $('#searchResults').html(html);
}

// Customer journey functions
function updateCustomerJourneyStage(prospectId, newStage) {
    showLoader();
    $.ajax({
        url: '/api/prospects/' + prospectId + '/journey-stage',
        method: 'POST',
        data: { stage: newStage },
        success: function(response) {
            if (response.success) {
                showNotification('Etapa actualizada correctamente', 'success');
                location.reload();
            } else {
                showNotification('Error al actualizar la etapa', 'error');
            }
        },
        error: function() {
            showNotification('Error al actualizar la etapa', 'error');
        },
        complete: function() {
            hideLoader();
        }
    });
}

// Agenda functions
function markAgendaItemCompleted(agendaId) {
    $.ajax({
        url: '/api/agenda/' + agendaId + '/complete',
        method: 'POST',
        success: function(response) {
            if (response.success) {
                showNotification('Tarea marcada como completada', 'success');
                location.reload();
            } else {
                showNotification('Error al marcar la tarea', 'error');
            }
        },
        error: function() {
            showNotification('Error al marcar la tarea', 'error');
        }
    });
}

// Notification functions
function refreshNotifications() {
    $.ajax({
        url: '/api/notifications/count',
        method: 'GET',
        success: function(response) {
            if (response.count > 0) {
                $('#notificationBadge').text(response.count).show();
            } else {
                $('#notificationBadge').hide();
            }
        }
    });
}

function showNotification(message, type) {
    var alertClass = 'alert-info';
    var icon = 'fas fa-info-circle';
    
    switch(type) {
        case 'success':
            alertClass = 'alert-success';
            icon = 'fas fa-check-circle';
            break;
        case 'error':
        case 'danger':
            alertClass = 'alert-danger';
            icon = 'fas fa-exclamation-circle';
            break;
        case 'warning':
            alertClass = 'alert-warning';
            icon = 'fas fa-exclamation-triangle';
            break;
    }
    
    var notification = $(
        '<div class="alert ' + alertClass + ' alert-dismissible fade show position-fixed" ' +
        'style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
        '<i class="' + icon + ' me-2"></i>' + message +
        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
        '</div>'
    );
    
    $('body').append(notification);
    
    setTimeout(function() {
        notification.alert('close');
    }, 5000);
}

// Dashboard functions
function refreshDashboardStats() {
    $.ajax({
        url: '/api/dashboard/stats',
        method: 'GET',
        success: function(response) {
            Object.keys(response).forEach(function(key) {
                $('#stat-' + key).text(response[key]);
            });
        }
    });
}

// Loader functions
function showLoader() {
    if ($('#globalLoader').length === 0) {
        var loader = $(
            '<div id="globalLoader" class="position-fixed d-flex justify-content-center align-items-center" ' +
            'style="top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999;">' +
            '<div class="spinner-canaco"></div>' +
            '</div>'
        );
        $('body').append(loader);
    }
    $('#globalLoader').show();
}

function hideLoader() {
    $('#globalLoader').hide();
}

// Utility functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-MX', {
        style: 'currency',
        currency: 'MXN'
    }).format(amount);
}

function formatDate(dateString) {
    var date = new Date(dateString);
    return new Intl.DateTimeFormat('es-MX', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    }).format(date);
}

function formatDateTime(dateString) {
    var date = new Date(dateString);
    return new Intl.DateTimeFormat('es-MX', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }).format(date);
}

// Form enhancement functions
function validateEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function validatePhone(phone) {
    var re = /^[\d\s\-\+\(\)]+$/;
    return re.test(phone) && phone.replace(/\D/g, '').length >= 10;
}

function validateRFC(rfc) {
    var re = /^[A-ZÑ&]{3,4}[0-9]{6}[A-Z0-9]{3}$/;
    return re.test(rfc.toUpperCase());
}

// Export functions for external use
window.CRMCanaco = {
    showNotification: showNotification,
    showLoader: showLoader,
    hideLoader: hideLoader,
    updateCustomerJourneyStage: updateCustomerJourneyStage,
    markAgendaItemCompleted: markAgendaItemCompleted,
    validateEmail: validateEmail,
    validatePhone: validatePhone,
    validateRFC: validateRFC,
    formatCurrency: formatCurrency,
    formatDate: formatDate,
    formatDateTime: formatDateTime
};