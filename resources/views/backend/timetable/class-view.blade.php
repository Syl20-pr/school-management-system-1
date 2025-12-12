<!-- resources/views/backend/timetable/class-view.blade.php -->
@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h2 class="page-title">Emploi du Temps - {{ $class->name }}</h2>
                    <p class="page-subtitle text-muted">
                        Année: {{ $timetable->academicYear->name }} - 
                        Trimestre: {{ ucfirst($timetable->term) }} - 
                        Statut: <span class="badge {{ $timetable->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $timetable->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </p>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Emploi du temps de la classe</h5>
                        <div class="btn-group">
                            <a href="javascript:window.print()" class="btn btn-info btn-sm">
                                <i class="fas fa-print mr-1"></i> Imprimer
                            </a>
                            <a href="{{ route('timetable.show', $timetable->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit mr-1"></i> Modifier
                            </a>
                            <a href="{{ route('timetable.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Retour
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @include('backend.timetable.partials.timetable-grid', [
                            'slots' => $timetable->slots,
                            'showActions' => true//true
                        ])
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Enseignants de la classe</h5>
                            </div>
                            <div class="card-body">
                                <div class="list-group">
                                    @foreach($timetable->slots->groupBy('teacher_id') as $teacherSlots)
                                        @php $teacher = $teacherSlots->first()->teacher; @endphp
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="mb-1">{{ $teacher->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $teacher->specialty ?? 'Enseignant' }}
                                                    </small>
                                                </div>
                                                <span class="badge bg-primary rounded-pill">
                                                    {{ $teacherSlots->count() }}h
                                                </span>
                                            </div>
                                            <div class="mt-2">
                                                <small>
                                                    <strong>Matières:</strong> 
                                                    {{ $teacherSlots->groupBy('subject.name')->keys()->join(', ') }}
                                                </small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">Statistiques hebdomadaires</h5>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <h3 class="text-primary">{{ $timetable->slots->count() }}</h3>
                                            <small>Heures totales</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <h3 class="text-success">{{ $timetable->slots->groupBy('subject_id')->count() }}</h3>
                                            <small>Matières</small>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="stat-item">
                                            <h3 class="text-info">{{ $timetable->slots->groupBy('teacher_id')->count() }}</h3>
                                            <small>Enseignants</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mt-4">
                                    <h6>Répartition par jour</h6>
                                    <div class="progress-stacked">
                                        @php
                                            $dailySlots = [
                                                'monday' => $timetable->slots->where('day_of_week', 'monday')->count(),
                                                'tuesday' => $timetable->slots->where('day_of_week', 'tuesday')->count(),
                                                'wednesday' => $timetable->slots->where('day_of_week', 'wednesday')->count(),
                                                'thursday' => $timetable->slots->where('day_of_week', 'thursday')->count(),
                                                'friday' => $timetable->slots->where('day_of_week', 'friday')->count(),
                                                'saturday' => $timetable->slots->where('day_of_week', 'saturday')->count()
                                            ];
                                            $totalSlots = array_sum($dailySlots);
                                        @endphp
                                        
                                        @foreach($dailySlots as $day => $count)
                                            @if($count > 0)
                                                <div class="progress" role="progressbar" 
                                                     style="width: {{ ($count / $totalSlots) * 100 }}%"
                                                     aria-valuenow="{{ $count }}" aria-valuemin="0" 
                                                     aria-valuemax="{{ $totalSlots }}">
                                                    <div class="progress-bar" 
                                                         style="background-color: {{ getDayColor($day) }}">
                                                        {{ $count }}h
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                    
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            @foreach($dailySlots as $day => $count)
                                                @if($count > 0)
                                                    <span class="me-3">
                                                        <span class="color-dot" style="background-color: {{ getDayColor($day) }}"></span>
                                                        {{ ucfirst($day) }}: {{ $count }}h
                                                    </span>
                                                @endif
                                            @endforeach
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-item {
    padding: 15px;
}

.stat-item h3 {
    margin: 0;
    font-weight: bold;
}

.progress-stacked {
    height: 25px;
    border-radius: 5px;
    overflow: hidden;
}

.progress-stacked .progress {
    border-radius: 0;
}

.color-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    margin-right: 5px;
}

/* Styles pour l'impression */
@media print {
    .btn-group, .page-subtitle .badge {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .timetable-grid {
        font-size: 10pt !important;
    }
    
    .list-group-item {
        border: 1px solid #dee2e6 !important;
    }
}
</style>

@php
function getDayColor($day) {
    $colors = [
        'monday' => '#4361ee',
        'tuesday' => '#3a0ca3',
        'wednesday' => '#7209b7',
        'thursday' => '#f72585',
        'friday' => '#4cc9f0',
        'saturday' => '#4895ef'
    ];
    return $colors[$day] ?? '#6c757d';
}
@endphp

@endsection