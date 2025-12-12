@extends('admin.admin_master')
@section('admin')

<style>
.timeline {
    position: relative;
    padding: 20px 0;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 50px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}
.timeline-item {
    position: relative;
    padding: 20px 0 20px 80px;
}
.timeline-icon {
    position: absolute;
    left: 38px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 12px;
}
.timeline-icon.promotion { background: #4CAF50; }
.timeline-icon.repeat { background: #FF9800; }
.timeline-icon.initial { background: #2196F3; }
.timeline-icon.cancellation { background: #9E9E9E; }
.stat-card {
    border-left: 4px solid;
    padding: 20px;
    border-radius: 8px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.stat-card.repeat { border-color: #FF9800; }
.stat-card.promotion { border-color: #4CAF50; }
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Historique des statuts</h2>
                        <p class="text-muted">{{ $student->name }} ({{ $student->id_no }})</p>
                    </div>
                    <a href="{{ route('student.promotion.view', $student->id) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left mr-2"></i> Retour
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="stat-card repeat">
                    <h3 class="mb-0">{{ $repeat_count }}</h3>
                    <p class="text-muted mb-0">Redoublement(s) total</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card promotion">
                    <h3 class="mb-0">{{ $status_history->where('change_reason', 'promotion')->count() }}</h3>
                    <p class="text-muted mb-0">Promotion(s)</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card" style="border-color: #2196F3;">
                    <h3 class="mb-0">{{ $student->statusclass === 'N' ? 'Nouveau' : 'Doublant' }}</h3>
                    <p class="text-muted mb-0">Statut actuel</p>
                </div>
            </div>
        </div>

        <!-- Timeline -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0">Historique détaillé des changements</h5>
            </div>
            <div class="card-body">
                @if($status_history->count() > 0)
                    <div class="timeline">
                        @foreach($status_history as $record)
                            <div class="timeline-item">
                                <div class="timeline-icon {{ $record->change_reason }}">
                                    @if($record->change_reason == 'promotion')
                                        <i class="fas fa-arrow-up"></i>
                                    @elseif($record->change_reason == 'repeat')
                                        <i class="fas fa-redo"></i>
                                    @elseif($record->change_reason == 'initial')
                                        <i class="fas fa-star"></i>
                                    @else
                                        <i class="fas fa-undo"></i>
                                    @endif
                                </div>
                                
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <h6 class="mb-1">{{ $record->description }}</h6>
                                                <p class="text-muted small mb-2">
                                                    {{ $record->year->name }} - {{ $record->class->name }}
                                                </p>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-light text-dark">
                                                    {{ $record->changed_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-3 mt-2">
                                            @if($record->old_status)
                                                <div>
                                                    <small class="text-muted">Ancien statut:</small>
                                                    <span class="badge {{ $record->old_status === 'N' ? 'badge-new' : 'badge-pending' }}">
                                                        {{ $record->old_status === 'N' ? 'Nouveau' : 'Doublant' }}
                                                    </span>
                                                </div>
                                            @endif
                                            
                                            <div>
                                                <small class="text-muted">Nouveau statut:</small>
                                                <span class="badge {{ $record->new_status === 'N' ? 'badge-new' : 'badge-pending' }}">
                                                    {{ $record->new_status === 'N' ? 'Nouveau' : 'Doublant' }}
                                                </span>
                                            </div>
                                        </div>

                                        @if($record->comment)
                                            <p class="mt-2 mb-0 small">
                                                <i class="fas fa-comment mr-1"></i> {{ $record->comment }}
                                            </p>
                                        @endif

                                        @if($record->changer)
                                            <p class="text-muted small mb-0 mt-2">
                                                <i class="fas fa-user mr-1"></i> Par: {{ $record->changer->name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-history" style="font-size: 3rem; color: #cbd5e1;"></i>
                        <h5 class="mt-3">Aucun historique</h5>
                        <p class="text-muted">Aucun changement de statut n'a été enregistré pour cet élève.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Analyse des redoublements -->
        @if($repeat_count > 0)
            <div class="card mt-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Analyse des redoublements
                    </h5>
                </div>
                <div class="card-body">
                    <p><strong>Nombre total de redoublements:</strong> {{ $repeat_count }}</p>
                    
                    <h6 class="mt-3">Classes redoublées:</h6>
                    <ul>
                        @foreach($status_history->where('change_reason', 'repeat') as $repeat)
                            <li>
                                {{ $repeat->class->name }} ({{ $repeat->year->name }}) 
                                - {{ $repeat->changed_at->format('d/m/Y') }}
                            </li>
                        @endforeach
                    </ul>

                    @if($repeat_count >= 2)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-info-circle mr-2"></i>
                            Cet élève a redoublé {{ $repeat_count }} fois. Un suivi particulier pourrait être nécessaire.
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@endsection