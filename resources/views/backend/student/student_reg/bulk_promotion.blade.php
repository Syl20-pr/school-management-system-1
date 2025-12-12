@extends('admin.admin_master')
@section('admin')

<style>
:root {
    --primary: #4361ee;
    --primary-dark: #3a56d4;
    --secondary: #6c757d;
    --success: #198754;
    --danger: #dc3545;
    --warning: #ffc107;
    --info: #0dcaf0;
    --light: #f8f9fa;
    --dark: #212529;
    --background: #f5f7fb;
    --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.04);
    --transition: all 0.3s ease;
}

.content-wrapper {
    background-color: var(--background);
    padding: 20px;
}

.filter-card, .student-card {
    border-radius: 12px;
    box-shadow: var(--card-shadow);
    border: none;
    transition: var(--transition);
}

.filter-card:hover, .student-card:hover {
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.08);
}

.bulk-sticky-bar {
    position: sticky;
    top: 70px;
    z-index: 100;
    background: white;
    border-radius: 12px;
    padding: 16px 24px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.5);
    margin-bottom: 20px;
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    align-items: center;
}

.student-list-table thead th {
    background: var(--primary);
    color: white;
    border: none;
    position: sticky;
    top: 0;
    z-index: 5;
    padding: 12px 15px;
    font-weight: 600;
}

.student-list-table tbody td {
    padding: 12px 15px;
    vertical-align: middle;
}

.table-container {
    border-radius: 12px;
    overflow-x: auto; /* ✅ Ajoute le scroll horizontal */
    -webkit-overflow-scrolling: touch;
    box-shadow: var(--card-shadow);
}

.badge-promoted {
    background-color: rgba(25, 135, 84, 0.15);
    color: var(--success);
    padding: 5px 10px;
    border-radius: 50px;
    font-weight: 500;
}

.badge-repeat {
    background-color: rgba(255, 193, 7, 0.15);
    color: #b38d00;
    padding: 5px 10px;
    border-radius: 50px;
    font-weight: 500;
}

.badge-excluded {
    background-color: rgba(220, 53, 69, 0.15);
    color: var(--danger);
    padding: 5px 10px;
    border-radius: 50px;
    font-weight: 500;
}

.badge-new {
    background-color: rgba(13, 202, 240, 0.15);
    color: var(--info);
    padding: 5px 10px;
    border-radius: 50px;
    font-weight: 500;
}

.badge-pending {
    background-color: rgba(108, 117, 125, 0.15);
    color: var(--secondary);
    padding: 5px 10px;
    border-radius: 50px;
    font-weight: 500;
}

.badge-cancelled {
    background-color: rgba(108, 117, 125, 0.15);
    color: var(--secondary);
    padding: 5px 10px;
    border-radius: 50px;
    font-weight: 500;
    text-decoration: line-through;
}

.select2-container--default .select2-selection--single {
    height: 45px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 10px;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 43px;
}

.btn {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    transition: var(--transition);
}

.btn-primary {
    background-color: var(--primary);
    border-color: var(--primary);
}

.btn-primary:hover {
    background-color: var(--primary-dark);
    border-color: var(--primary-dark);
    transform: translateY(-2px);
}

.btn-success {
    border-radius: 8px;
    padding: 10px 20px;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.875rem;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--dark);
    position: relative;
    padding-left: 15px;
}

.section-title::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    height: 20px;
    width: 4px;
    background-color: var(--primary);
    border-radius: 2px;
}

.student-checkbox {
    width: 18px;
    height: 18px;
    cursor: pointer;
    border-radius: 4px;
    border: 2px solid #cbd5e0;
    transition: var(--transition);
    appearance: none;
    -webkit-appearance: none;
    position: relative;
}

.student-checkbox:checked {
    background-color: var(--primary);
    border-color: var(--primary);
}

.student-checkbox:checked::after {
    content: '✓';
    position: absolute;
    color: white;
    font-size: 12px;
    font-weight: bold;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.student-row {
    transition: var(--transition);
}

.student-row:hover {
    background-color: #f8fafc;
}

.student-row.selected {
    background-color: rgba(67, 97, 238, 0.05);
}

.action-select {
    min-width: 180px;
    border-radius: 8px;
}

.reason-input {
    border-radius: 8px;
    min-width: 250px;
}

.summary-badge {
    background: rgba(67, 97, 238, 0.1);
    color: var(--primary);
    padding: 8px 16px;
    border-radius: 50px;
    font-weight: 500;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #64748b;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #cbd5e1;
}

.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.min-w-140 {
    min-width: 140px;
}

.w-100p {
    width: 100%;
}

.decision-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.cancelled-row {
    opacity: 0.6;
    background-color: #f8f9fa !important;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
    margin-right: 6px;
}

.status-active { background-color: var(--success); }
.status-cancelled { background-color: var(--secondary); }

.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

.modal-header {
    background: var(--primary);
    color: white;
    border-radius: 12px 12px 0 0;
    border: none;
}

.modal-footer {
    border-radius: 0 0 12px 12px;
    border: none;
}

@media (min-width: 768px) {
    .w-md-auto {
        width: auto !important;
    }
}

.tooltip-wrapper {
    display: inline-block;
}

.bulk-actions-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.action-buttons {
    display: flex;
    gap: 10px;
}

.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 300px;
}

.badge-cancel-info {
    background-color: rgba(108, 117, 125, 0.1);
    color: #495057;
    font-size: 0.8rem;
    border-radius: 50px;
    padding: 4px 8px;
    margin-left: 4px;
}

@media (max-width: 768px) {
    .bulk-sticky-bar {
        flex-direction: column;
        align-items: stretch;
        gap: 12px;
    }
    .bulk-sticky-bar select,
    .bulk-sticky-bar input,
    .bulk-sticky-bar button {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .badge, .badge-new, .badge-pending, .badge-promoted, .badge-repeat, .badge-excluded {
        font-size: 0.75rem;
        padding: 4px 6px;
    }
    .btn-sm {
        padding: 6px 8px;
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    #cancelDecisionModal .modal-dialog {
        margin: 10px;
    }
    #cancelDecisionModal .modal-body textarea {
        width: 100%;
    }
}

@media (max-width: 768px) {
    .decision-actions {
        flex-direction: column;
        align-items: flex-start;
        gap: 6px;
    }
}

</style>

<div class="loading-overlay">
    <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;"></div>
</div>

<!-- Modal de confirmation d'annulation -->
<div class="modal fade" id="cancelDecisionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Annuler la décision</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="cancelModalText">Êtes-vous sûr de vouloir annuler cette décision ?</p>
                <p class="text-muted small">L'élève retournera dans la liste des élèves sans décision.</p>
                <form id="cancelDecisionForm">
                    @csrf
                    <input type="hidden" name="history_id" id="cancelHistoryId">
                    <div class="form-group">
                        <label for="cancelReason" class="form-label">Raison de l'annulation (optionnel)</label>
                        <textarea class="form-control" id="cancelReason" name="reason" rows="3" placeholder="Pourquoi annulez-vous cette décision ?"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Retour</button>
                <button type="button" class="btn btn-danger" id="confirmCancel">Confirmer l'annulation</button>
            </div>
        </div>
    </div>
</div>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h2 class="page-title">Gestion des promotions</h2>
                    <p class="page-subtitle text-muted">Gestion des promotions, passages et décisions des élèves</p>
                </div>
                
                <!-- Messages de notification -->
                @if(session('message'))
                    <div class="alert alert-{{ session('alert-type', 'info') }} alert-dismissible fade show" role="alert">
                        <i class="fas fa-{{ session('alert-type') === 'success' ? 'check-circle' : (session('alert-type') === 'warning' ? 'exclamation-triangle' : 'info-circle') }} me-2"></i>
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('bulk_errors'))
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <h6 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Erreurs rencontrées</h6>
                        <ul class="mb-0">
                            @foreach(session('bulk_errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                
                <form method="GET" action="{{ route('student.promotion.bulk') }}" id="filterForm">
                    <div class="card filter-card">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Filtres de recherche</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-row">
                                <div class="form-group col-md-4">
                                    <label class="form-label">Année scolaire</label>
                                    <select name="year_id" class="form-control select2" data-placeholder="Sélectionner une année" required>
                                        <option value=""></option>
                                        @foreach($years as $y)
                                            <option value="{{ $y->id }}" {{ request('year_id') == $y->id ? 'selected' : '' }}>{{ $y->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4">
                                    <label class="form-label">Classe</label>
                                    <select name="class_id" class="form-control select2" data-placeholder="Sélectionner une classe" required>
                                        <option value=""></option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-md-4 d-flex align-items-end">
                                    <button class="btn btn-primary mr-2" type="submit">
                                        <i class="fas fa-search mr-2"></i> Filtrer
                                    </button>
                                    <a href="{{ route('student.promotion.bulk') }}" class="btn btn-light">
                                        <i class="fas fa-redo mr-2"></i> Réinitialiser
                                    </a>
                                </div>
                            </div>

                            @if(request('year_id') && request('class_id'))
                                <div class="alert alert-light mt-3">
                                    Résultats pour <span class="fw-bold">{{ $years->firstWhere('id', request('year_id'))->name ?? '-' }}</span> 
                                    / Classe: <span class="fw-bold">{{ $classes->firstWhere('id', request('class_id'))->name ?? '-' }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @php
            $totalCount = isset($all_students) ? $all_students->count() : 0;
        @endphp

        {{-- Bulk area visible if any students exist --}}
        @if(request()->has('year_id') && request()->has('class_id'))
            @if($totalCount > 0)
                <div class="row">
                    <div class="col-12">
                        <form method="POST" action="{{ route('student.promotion.bulk.process') }}" id="bulkForm">
                            @csrf
                            <input type="hidden" name="target_class" id="target_class" value="">
                            <input type="hidden" name="year_id" value="{{ request('year_id') }}">
                            <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                            
                            <div class="bulk-sticky-bar">
                                <div class="d-flex align-items-center">
                                    <span class="summary-badge me-3">
                                        <span id="selectedCount">0</span> élève(s) sélectionné(s)
                                    </span>
                                    <span class="badge bg-light text-dark">
                                        <span id="pendingCount">{{ $students_without_decision->count() }}</span> sans décision
                                    </span>
                                </div>
                                
                                <div class="d-flex flex-grow-1 flex-wrap" style="gap:12px;">
                                    <select name="action" id="bulkAction" class="form-control action-select" required>
                                        <option value="">Choisir une action...</option>
                                        <option value="promote">Promouvoir</option>
                                        <option value="repeat">Redoubler</option>
                                        <option value="exclude">Exclure</option>
                                    </select>

                                    <div class="form-group" id="targetClassWrapper" style="display:none;">
                                        <select id="target_class_selector" class="form-control min-w-140" required>
                                            <option value="">Choisir la classe...</option>
                                        </select>
                                    </div>

                                    <div class="form-group flex-grow-1">
                                        <input type="text" name="reason" id="reason" class="form-control reason-input" placeholder="Motif (optionnel)" />
                                    </div>

                                    <button type="submit" id="applyBtn" class="btn btn-success" disabled>
                                        <i class="fas fa-check-circle mr-2"></i> Appliquer
                                    </button>
                                </div>
                                
                                <div class="w-100 mt-2 text-muted small">
                                    <i class="fas fa-info-circle mr-1"></i> 
                                    Pour le redoublement : choisissez une classe du même niveau.
                                    <br>
                                    <i class="fas fa-info-circle mr-1"></i> 
                                    Seuls les élèves sans décision peuvent être sélectionnés.
                                </div>
                            </div>

                            {{-- Students with decision --}}
                            @if($students_with_decision->count() > 0)
                                <div class="mb-4">
                                    <div class="bulk-actions-header">
                                        <h4 class="section-title">Élèves avec décision</h4>
                                        <div class="action-buttons">
                                            <span class="badge bg-light text-dark me-2" id="withDecisionCount">
                                                Total: {{ $students_with_decision->count() }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="card student-card">
                                        <div class="card-body p-0">
                                            <div class="table-container">
                                                <table class="table table-hover mb-0 student-list-table" id="withDecisionTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Nom</th>
                                                            {{-- <th>ID</th> --}}
                                                            <th>Classe</th>
                                                            <th>Décision</th>
                                                            <th>Destination</th>
                                                            <th>Date</th>
                                                            <th>Statut</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($students_with_decision as $s)
                                                            @php 
                                                                $latestDecision = $s->promotionHistory->first();
                                                                $isCancelled = $latestDecision->cancelled_at !== null;
                                                            @endphp
                                                            <tr class="{{ $isCancelled ? 'cancelled-row' : '' }}" data-history-id="{{ $latestDecision->id }}" data-student-id="{{ $s->student_id }}">
                                                                <td class="fw-medium">{{ $s->student->name }}</td>
                                                                {{-- <td class="font-monospace">{{ $s->student->id_no }}</td> --}}
                                                                <td>{{ $s->student_class->name }}</td>
                                                                <td>
                                                                    @if($latestDecision->action == 'promote') 
                                                                        <span class="badge-promoted">Promu</span>
                                                                    @elseif($latestDecision->action == 'repeat') 
                                                                        <span class="badge-repeat">Redoublant</span>
                                                                    @elseif($latestDecision->action == 'exclude') 
                                                                        <span class="badge-excluded">Exclu</span>
                                                                    @endif
                                                                </td>
                                                                <td>{{ $latestDecision && $latestDecision->action != 'exclude' && $latestDecision->toClass ? $latestDecision->toClass->name : '-' }}</td>
                                                                <td>{{ $latestDecision->decision_date ? $latestDecision->decision_date->format('d/m/Y') : '-' }}</td>
                                                                <td>
                                                                    @if($isCancelled)
                                                                        <span class="badge-cancelled">
                                                                            <span class="status-indicator status-cancelled"></span>
                                                                            Annulée
                                                                        </span>
                                                                    @else
                                                                        <span class="badge-success">
                                                                            <span class="status-indicator status-active"></span>
                                                                            Active
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="decision-actions">
                                                                        <a href="{{ route('student.promotion.view', $s->student_id) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Voir détails">
                                                                            <i class="fas fa-eye"></i>
                                                                        </a>
                                                                        @if(!$isCancelled)
                                                                            <button class="btn btn-sm btn-outline-danger cancel-decision-btn" 
                                                                                    data-history-id="{{ $latestDecision->id }}"
                                                                                    data-student-id="{{ $s->student_id }}"
                                                                                    data-student-name="{{ $s->student->name }}"
                                                                                    data-student-id-no="{{ $s->student->id_no }}"
                                                                                    data-student-class="{{ $s->student_class->name }}"
                                                                                    data-bs-toggle="tooltip" title="Annuler cette décision">
                                                                                <i class="fas fa-undo"></i>
                                                                            </button>
                                                                        @else
                                                                            <span class="btn btn-sm btn-outline-secondary disabled" data-bs-toggle="tooltip" title="Décision déjà annulée">
                                                                                <i class="fas fa-ban"></i>
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Students without decision --}}
                            @if($students_without_decision->count() > 0)
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h4 class="section-title mb-0">En attente de décision</h4>
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="selectAll">
                                            <label class="form-check-label small" for="selectAll">Tout sélectionner</label>
                                        </div>
                                    </div>
                                    
                                    <div class="card student-card">
                                        <div class="card-body p-0">
                                            <div class="table-container">
                                                <table class="table table-hover mb-0 student-list-table" id="withoutDecisionTable">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:50px;">
                                                                <input type="checkbox" id="selectAllHeader">
                                                            </th>
                                                            <th>Nom</th>
                                                            {{-- <th>ID</th> --}}
                                                            <th>Classe</th>
                                                            <th>Statut</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($students_without_decision as $s)
                                                            <tr class="student-row">
                                                                <td>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input student-checkbox" type="checkbox" 
                                                                               name="student_ids[]" value="{{ $s->student_id }}" 
                                                                               id="student-{{ $s->student_id }}">
                                                                        <label class="form-check-label" for="student-{{ $s->student_id }}"></label>
                                                                    </div>
                                                                </td>
                                                                <td class="fw-medium">{{ $s->student->name }}</td>
                                                                {{-- <td class="font-monospace">{{ $s->student->id_no }}</td> --}}
                                                                <td>{{ $s->student_class->name }}</td>
                                                                <td>
                                                                    @if($s->student->statusclass == 'N') 
                                                                        <span class="badge-new">Nouveau</span> 
                                                                    @else 
                                                                        <span class="badge-pending">Redoublant</span> 
                                                                    @endif

                                                                    @if($s->promotionHistory->isNotEmpty() && $s->promotionHistory->first()->cancelled_at)
                                                                        @php
                                                                            $cancelDate = \Carbon\Carbon::parse($s->promotionHistory->first()->cancelled_at)->format('d/m/Y');
                                                                        @endphp
                                                                        <span class="badge-cancel-info" data-bs-toggle="tooltip" title="Décision annulée le {{ $cancelDate }}">
                                                                            🕓 Annulée le {{ $cancelDate }}
                                                                        </span>
                                                                    @endif
                                                                </td>

                                                                <td>
                                                                    <a href="{{ route('student.promotion.view', $s->student_id) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Décision individuelle">
                                                                        <i class="fas fa-user-edit"></i>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="empty-state">
                                    <i class="fas fa-check-circle"></i>
                                    <h5>Tous les élèves ont une décision</h5>
                                    <p class="text-muted">Aucun élève n'est en attente de décision pour ces critères.</p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h5>Aucun élève trouvé</h5>
                    <p class="text-muted">Aucun élève ne correspond aux critères sélectionnés.</p>
                </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-filter"></i>
                <h5>Filtres requis</h5>
                <p class="text-muted">Veuillez sélectionner une année et une classe pour afficher les élèves.</p>
            </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const selectAll = document.getElementById('selectAll');
    const selectAllHeader = document.getElementById('selectAllHeader');
    const applyBtn = document.getElementById('applyBtn');
    const bulkAction = document.getElementById('bulkAction');
    const targetClassWrapper = document.getElementById('targetClassWrapper');
    const targetClassSelector = document.getElementById('target_class_selector');
    const targetClassHidden = document.getElementById('target_class');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    const studentRows = document.querySelectorAll('.student-row');
    const bulkForm = document.getElementById('bulkForm');
    const loadingOverlay = document.querySelector('.loading-overlay');
    const cancelDecisionModal = new bootstrap.Modal(document.getElementById('cancelDecisionModal'));
    const cancelDecisionForm = document.getElementById('cancelDecisionForm');
    const cancelHistoryId = document.getElementById('cancelHistoryId');
    const confirmCancelBtn = document.getElementById('confirmCancel');
    const cancelModalText = document.getElementById('cancelModalText');

    // Données des classes
    const nextClasses = @json($next_classes ?? []);
    const currentClasses = @json($current_classes ?? []);
    const currentClassId = {{ request('class_id') ?? 0 }};

    // Initialiser les tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Mise à jour du compteur et de l'apparence des lignes sélectionnées
    function updateCounts() {
        const selected = document.querySelectorAll('.student-checkbox:checked').length;
        document.getElementById('selectedCount').textContent = selected;
        
        // Mettre à jour l'état du bouton Appliquer
        const hasTargetClass = bulkAction.value === 'exclude' || targetClassSelector.value !== '';
        applyBtn.disabled = selected === 0 || !bulkAction.value || !hasTargetClass;
        
        // Mettre à jour l'apparence des lignes sélectionnées
        studentRows.forEach((row, index) => {
            if (studentCheckboxes[index] && studentCheckboxes[index].checked) {
                row.classList.add('selected');
            } else {
                row.classList.remove('selected');
            }
        });

        // Mettre à jour la case "Tout sélectionner"
        if (selectAll) {
            const allChecked = studentCheckboxes.length > 0 && 
                              document.querySelectorAll('.student-checkbox:checked').length === studentCheckboxes.length;
            selectAll.checked = allChecked;
            if (selectAllHeader) selectAllHeader.checked = allChecked;
        }
    }

    // Configuration de la sélection/désélection globale
    if (selectAll) {
        selectAll.addEventListener('change', (e) => {
            studentCheckboxes.forEach(cb => cb.checked = e.target.checked);
            updateCounts();
        });
    }

    if (selectAllHeader) {
        selectAllHeader.addEventListener('change', (e) => {
            studentCheckboxes.forEach(cb => cb.checked = e.target.checked);
            updateCounts();
        });
    }

    // Écouter les changements sur chaque case à cocher
    studentCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateCounts);
    });

    // Gérer l'affichage des sélecteurs de classe en fonction de l'action
    if (bulkAction) {
        bulkAction.addEventListener('change', () => {
            targetClassWrapper.style.display = 'none';
            targetClassSelector.innerHTML = '<option value="">Choisir la classe...</option>';
            targetClassSelector.required = false;
            
            if (bulkAction.value === 'promote') {
                // Ajouter les options de promotion
                nextClasses.forEach(cls => {
                    targetClassSelector.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
                });
                targetClassWrapper.style.display = 'block';
                targetClassSelector.required = true;
            } else if (bulkAction.value === 'repeat') {
                // Ajouter les options de redoublement (même niveau)
                currentClasses.forEach(cls => {
                    if (cls.id != currentClassId) {
                        targetClassSelector.innerHTML += `<option value="${cls.id}">${cls.name}</option>`;
                    }
                });
                // Ajouter la classe actuelle comme option
                const currentClass = currentClasses.find(cls => cls.id == currentClassId);
                if (currentClass) {
                    targetClassSelector.innerHTML += `<option value="${currentClass.id}" selected>${currentClass.name} (actuelle)</option>`;
                }
                targetClassWrapper.style.display = 'block';
                targetClassSelector.required = true;
            }
            
            updateCounts();
        });

        // Déclencher le changement initial
        bulkAction.dispatchEvent(new Event('change'));
    }

    // Écouter les changements sur le sélecteur de classe
    if (targetClassSelector) {
        targetClassSelector.addEventListener('change', updateCounts);
    }

    // Gestion de l'annulation des décisions
    document.querySelectorAll('.cancel-decision-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const historyId = this.dataset.historyId;
            const studentId = this.dataset.studentId;
            const studentName = this.dataset.studentName;
            const studentIdNo = this.dataset.studentIdNo;
            const studentClass = this.dataset.studentClass;
            
            cancelHistoryId.value = historyId;
            cancelModalText.textContent = `Êtes-vous sûr de vouloir annuler la décision pour ${studentName} ?`;
            
            // Stocker les données pour la mise à jour UI
            cancelDecisionForm.dataset.studentId = studentId;
            cancelDecisionForm.dataset.historyId = historyId;
            cancelDecisionForm.dataset.studentName = studentName;
            cancelDecisionForm.dataset.studentIdNo = studentIdNo;
            cancelDecisionForm.dataset.studentClass = studentClass;
            
            // Réinitialiser le textarea
            document.getElementById('cancelReason').value = '';
            
            cancelDecisionModal.show();
        });
    });

    // Confirmation d'annulation - Mise à jour dynamique
    confirmCancelBtn.addEventListener('click', function() {
        const formData = new FormData(cancelDecisionForm);
        const studentId = cancelDecisionForm.dataset.studentId;
        const historyId = cancelDecisionForm.dataset.historyId;
        const studentName = cancelDecisionForm.dataset.studentName;
        const studentIdNo = cancelDecisionForm.dataset.studentIdNo;
        const studentClass = cancelDecisionForm.dataset.studentClass;
        
        fetch('{{ route("student.promotion.cancel") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Mise à jour dynamique sans rechargement
                //updateUIAfterCancellation(studentId, historyId, studentName, studentIdNo, studentClass);
                updateUIAfterCancellation(studentId, historyId, studentName, studentIdNo, studentClass, data.restored_status);
                showNotification('success', data.message);
            } else {
                showNotification('error', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            const message = error.message || 'Une erreur s\'est produite';
            showNotification('error', message);
        })
        .finally(() => {
            cancelDecisionModal.hide();
        });
    });

    // Fonction pour mettre à jour l'UI après annulation
/* function updateUIAfterCancellation(studentId, historyId, studentName, studentIdNo, studentClass, statusClass = 'N') {
    // Supprimer la ligne du tableau "avec décision"
    const decisionRow = document.querySelector(`tr[data-history-id="${historyId}"]`);
    if (decisionRow) {
        decisionRow.remove();
    }

    // Mettre à jour le compteur "avec décision"
    const withDecisionRows = document.querySelectorAll('#withDecisionTable tbody tr:not(.cancelled-row)');
    document.getElementById('withDecisionCount').textContent = `Total: ${withDecisionRows.length}`;

    // Ajouter la ligne dans "En attente de décision"
    const noDecisionTable = document.querySelector('#withoutDecisionTable tbody');
    if (noDecisionTable) {
        const newRow = document.createElement('tr');
        newRow.className = 'student-row';
        newRow.innerHTML = `
            <td>
                <div class="form-check">
                    <input class="form-check-input student-checkbox" type="checkbox"
                        name="student_ids[]" value="${studentId}"
                        id="student-${studentId}">
                    <label class="form-check-label" for="student-${studentId}"></label>
                </div>
            </td>
            <td class="fw-medium">${studentName}</td>
            <td class="font-monospace">${studentIdNo}</td>
            <td>${studentClass}</td>
            <td>
                ${statusClass === 'N' 
                    ? '<span class="badge-new">Nouveau</span>'
                    : '<span class="badge-pending">Redoublant</span>'}
            </td>
            <td>
                <a href="/student/promotion/${studentId}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Décision individuelle">
                    <i class="fas fa-user-edit"></i>
                </a>
            </td>
        `;

        noDecisionTable.appendChild(newRow);

        // Réattacher les événements
        const newCheckbox = newRow.querySelector('.student-checkbox');
        newCheckbox.addEventListener('change', updateCounts);
    }

    // Mettre à jour le compteur “sans décision”
    const noDecisionCount = document.querySelectorAll('#withoutDecisionTable tbody tr').length;
    document.getElementById('pendingCount').textContent = noDecisionCount;

    updateCounts();
} */
function updateUIAfterCancellation(studentId, historyId, studentName, studentIdNo, studentClass, restoredStatus) {
    // Supprimer la ligne du tableau "avec décision"
    const decisionRow = document.querySelector(`tr[data-history-id="${historyId}"]`);
    if (decisionRow) {
        decisionRow.remove();
    }

    // Mettre à jour le compteur "avec décision"
    const withDecisionRows = document.querySelectorAll('#withDecisionTable tbody tr:not(.cancelled-row)');
    document.getElementById('withDecisionCount').textContent = `Total: ${withDecisionRows.length}`;

    // ✅ Utiliser le statut restauré depuis le backend
    const statusBadge = restoredStatus === 'N' 
        ? '<span class="badge-new">Nouveau</span>'
        : '<span class="badge-pending">Doublant</span>';

    // Ajouter la ligne dans "En attente de décision"
    const noDecisionTable = document.querySelector('#withoutDecisionTable tbody');
    if (noDecisionTable) {
        const newRow = document.createElement('tr');
        newRow.className = 'student-row';
        newRow.innerHTML = `
            <td>
                <div class="form-check">
                    <input class="form-check-input student-checkbox" type="checkbox"
                        name="student_ids[]" value="${studentId}"
                        id="student-${studentId}">
                    <label class="form-check-label" for="student-${studentId}"></label>
                </div>
            </td>
            <td class="fw-medium">${studentName}</td>
            <td>${studentClass}</td>
            <td>${statusBadge}</td>
            <td>
                <a href="/student/promotion/${studentId}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Décision individuelle">
                    <i class="fas fa-user-edit"></i>
                </a>
            </td>
        `;

        noDecisionTable.appendChild(newRow);

        // Réattacher les événements
        const newCheckbox = newRow.querySelector('.student-checkbox');
        newCheckbox.addEventListener('change', updateCounts);
    }

    // Mettre à jour le compteur "sans décision"
    const noDecisionCount = document.querySelectorAll('#withoutDecisionTable tbody tr').length;
    document.getElementById('pendingCount').textContent = noDecisionCount;

    updateCounts();
}


    // Créer une nouvelle ligne pour la table "sans décision"
    function createNoDecisionRow(studentId, studentName, studentIdNo, studentClass) {
        const row = document.createElement('tr');
        row.className = 'student-row';
        
        row.innerHTML = `
            <td>
                <div class="form-check">
                    <input class="form-check-input student-checkbox" type="checkbox" 
                           name="student_ids[]" value="${studentId}" 
                           id="student-${studentId}">
                    <label class="form-check-label" for="student-${studentId}"></label>
                </div>
            </td>
            <td class="fw-medium">${studentName}</td>
            <td class="font-monospace">${studentIdNo}</td>
            <td>${studentClass}</td>
            <td>
                <span class="badge-new">Nouveau</span>
            </td>
            <td>
                <a href="/student/promotion/${studentId}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Décision individuelle">
                    <i class="fas fa-user-edit"></i>
                </a>
            </td>
        `;

        return row;
    }

    // Fonction pour afficher les notifications
    function showNotification(type, message) {
        // Supprimer les notifications existantes
        document.querySelectorAll('.toast-notification').forEach(toast => toast.remove());
        
        // Créer une nouvelle notification
        const toast = document.createElement('div');
        toast.className = `toast-notification alert alert-${type} alert-dismissible fade show`;
        toast.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }

    // Préparer les données avant soumission du formulaire bulk
    if (bulkForm) {
        bulkForm.addEventListener('submit', (e) => {
            // Copier la valeur du sélecteur visible vers le champ caché
            if (bulkAction.value === 'promote' || bulkAction.value === 'repeat') {
                targetClassHidden.value = targetClassSelector.value;
            } else {
                targetClassHidden.value = '';
            }

            const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
            const action = bulkAction.value;
            
            let actionText = '';
            switch(action) {
                case 'promote': actionText = 'promouvoir'; break;
                case 'repeat': actionText = 'faire redoubler'; break;
                case 'exclude': actionText = 'exclure'; break;
            }
            
            if (!confirm(`Êtes-vous sûr de vouloir ${actionText} ${selectedCount} élève(s) ?`)) {
                e.preventDefault();
                return false;
            }
            
            loadingOverlay.style.display = 'flex';
        });
    }

    // Initialiser les compteurs
    updateCounts();
});
</script>

@endsection