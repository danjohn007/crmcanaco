<?php
$title = 'Dashboard - CRM CANACO';
ob_start();
?>

<div class="container-fluid">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 text-canaco mb-1">¡Bienvenido, <?php echo htmlspecialchars($_SESSION['full_name']); ?>!</h1>
                    <p class="text-muted mb-0">
                        <?php echo date('l, j \d\e F \d\e Y'); ?> • 
                        <?php echo $this->userModel->getRoleName($currentUser['role']); ?>
                    </p>
                </div>
                <div>
                    <button class="btn btn-outline-canaco me-2" data-bs-toggle="modal" data-bs-target="#quickAddModal">
                        <i class="fas fa-plus me-1"></i>Agregar Rápido
                    </button>
                    <a href="/search" class="btn btn-canaco">
                        <i class="fas fa-search me-1"></i>Búsqueda Inteligente
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card dashboard-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-number" id="stat-total-prospects"><?php echo number_format($stats['total_prospects']); ?></div>
                        <div class="stat-label">Total Prospectos</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x text-canaco"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card dashboard-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-number" id="stat-new-prospects"><?php echo number_format($stats['new_prospects_month']); ?></div>
                        <div class="stat-label">Nuevos Este Mes</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-plus fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card dashboard-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-number" id="stat-active-members"><?php echo number_format($stats['active_members']); ?></div>
                        <div class="stat-label">Miembros Activos</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-certificate fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stat-card dashboard-card">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="stat-number" id="stat-pending-tasks"><?php echo number_format($stats['pending_tasks']); ?></div>
                        <div class="stat-label">Tareas Pendientes</div>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-tasks fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Journey Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-route me-2"></i>Customer Journey - Cámara de Comercio
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php 
                        $journeyStages = [
                            'prospectacion' => ['icon' => 'fa-search', 'name' => 'Prospectación'],
                            'atencion' => ['icon' => 'fa-handshake', 'name' => 'Atención'],
                            'facturacion' => ['icon' => 'fa-file-invoice-dollar', 'name' => 'Facturación'],
                            'postventa' => ['icon' => 'fa-headset', 'name' => 'Post-venta']
                        ];
                        
                        $stageIndex = 0;
                        foreach ($journeyStages as $stage => $info): 
                            $count = $journeyStats[$stage] ?? 0;
                        ?>
                        <div class="col-md-3">
                            <div class="journey-stage <?php echo $count > 0 ? 'active' : ''; ?>" 
                                 data-stage="<?php echo $stage; ?>">
                                <i class="fas <?php echo $info['icon']; ?> fa-2x mb-2"></i>
                                <h6><?php echo $info['name']; ?></h6>
                                <div class="badge badge-canaco"><?php echo $count; ?> prospectos</div>
                            </div>
                            <?php if ($stageIndex < count($journeyStages) - 1): ?>
                                <div class="text-center">
                                    <i class="fas fa-arrow-right journey-arrow"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php 
                            $stageIndex++;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Today's Agenda -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day me-2"></i>Agenda de Hoy
                    </h5>
                    <a href="/agenda" class="btn btn-sm btn-outline-canaco">Ver Todo</a>
                </div>
                <div class="card-body">
                    <?php if (empty($todayAgenda)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-calendar-check fa-3x mb-3"></i>
                            <p>No hay tareas programadas para hoy</p>
                            <a href="/agenda?action=create" class="btn btn-canaco">
                                <i class="fas fa-plus me-1"></i>Agregar Tarea
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="agenda-list">
                            <?php foreach ($todayAgenda as $task): ?>
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="me-3">
                                    <?php
                                    $actionIcons = [
                                        'llamada' => 'fa-phone',
                                        'whatsapp' => 'fa-whatsapp',
                                        'email' => 'fa-envelope',
                                        'visita' => 'fa-map-marker-alt',
                                        'seguimiento' => 'fa-eye',
                                        'otro' => 'fa-tasks'
                                    ];
                                    $icon = $actionIcons[$task['action_type']] ?? 'fa-tasks';
                                    ?>
                                    <i class="fas <?php echo $icon; ?> text-canaco"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($task['title']); ?></h6>
                                    <small class="text-muted">
                                        <?php echo date('H:i', strtotime($task['scheduled_date'])); ?> • 
                                        <?php echo htmlspecialchars($task['commercial_name'] ?? 'Sin prospecto'); ?>
                                    </small>
                                </div>
                                <div>
                                    <?php if ($task['status'] === 'pendiente'): ?>
                                        <button class="btn btn-sm btn-success mark-completed" 
                                                data-agenda-id="<?php echo $task['id']; ?>">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="badge bg-success">Completada</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Prospects -->
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-friends me-2"></i>Prospectos Recientes
                    </h5>
                    <a href="/prospects" class="btn btn-sm btn-outline-canaco">Ver Todos</a>
                </div>
                <div class="card-body">
                    <?php if (empty($recentProspects)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <p>No hay prospectos recientes</p>
                            <a href="/prospects?action=create" class="btn btn-canaco">
                                <i class="fas fa-plus me-1"></i>Agregar Prospecto
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="prospects-list">
                            <?php foreach ($recentProspects as $prospect): ?>
                            <div class="d-flex align-items-center mb-3 p-2 border rounded">
                                <div class="me-3">
                                    <div class="bg-canaco text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        <?php echo strtoupper(substr($prospect['commercial_name'], 0, 1)); ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="/prospects?action=view&id=<?php echo $prospect['id']; ?>" 
                                           class="text-decoration-none">
                                            <?php echo htmlspecialchars($prospect['commercial_name']); ?>
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($prospect['contact_name'] ?? ''); ?> • 
                                        Asignado a: <?php echo htmlspecialchars($prospect['assigned_name'] ?? 'Sin asignar'); ?>
                                    </small>
                                </div>
                                <div>
                                    <?php
                                    $stageColors = [
                                        'prospectacion' => 'bg-info',
                                        'atencion' => 'bg-warning',
                                        'facturacion' => 'bg-primary',
                                        'postventa' => 'bg-success'
                                    ];
                                    $badgeClass = $stageColors[$prospect['customer_journey_stage']] ?? 'bg-secondary';
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>">
                                        <?php echo ucfirst($prospect['customer_journey_stage']); ?>
                                    </span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Events and Notifications -->
    <div class="row">
        <!-- Upcoming Events -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Próximos Eventos
                    </h5>
                    <a href="/events" class="btn btn-sm btn-outline-canaco">Ver Todos</a>
                </div>
                <div class="card-body">
                    <?php if (empty($upcomingEvents)): ?>
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                            <p>No hay eventos próximos</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Evento</th>
                                        <th>Fecha</th>
                                        <th>Tipo</th>
                                        <th>Ubicación</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcomingEvents as $event): ?>
                                    <tr>
                                        <td>
                                            <a href="/events?action=view&id=<?php echo $event['id']; ?>" 
                                               class="text-decoration-none fw-bold">
                                                <?php echo htmlspecialchars($event['title']); ?>
                                            </a>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($event['start_date'])); ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo ucfirst($event['event_type']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars($event['location'] ?? 'Por definir'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bell me-2"></i>Notificaciones
                        <?php if (!empty($notifications)): ?>
                            <span class="badge bg-danger ms-2"><?php echo count($notifications); ?></span>
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (empty($notifications)): ?>
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <p>No hay notificaciones</p>
                        </div>
                    <?php else: ?>
                        <div class="notifications-list">
                            <?php foreach ($notifications as $notification): ?>
                            <div class="notification-item <?php echo $notification['is_read'] ? '' : 'unread'; ?>">
                                <h6 class="mb-1"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                <p class="mb-1 small"><?php echo htmlspecialchars($notification['message']); ?></p>
                                <small class="text-muted">
                                    <?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?>
                                </small>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Modal -->
<div class="modal fade" id="quickAddModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Agregar Rápido</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-grid gap-2">
                    <a href="/prospects?action=create" class="btn btn-outline-canaco">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Prospecto
                    </a>
                    <a href="/events?action=create" class="btn btn-outline-canaco">
                        <i class="fas fa-calendar-plus me-2"></i>Nuevo Evento
                    </a>
                    <a href="/agenda?action=create" class="btn btn-outline-canaco">
                        <i class="fas fa-tasks me-2"></i>Nueva Tarea
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../layouts/main.php';
?>