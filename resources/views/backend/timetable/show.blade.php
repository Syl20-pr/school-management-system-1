<!-- resources/views/backend/timetable/show.blade.php -->
@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h2 class="page-title">Emploi du Temps: {{ $timetable->name }}</h2>
                    <p class="page-subtitle text-muted">
                        {{ $timetable->class->name }} - {{ $timetable->academicYear->name }} - 
                        <span class="text-capitalize">{{ $timetable->term }}</span>
                    </p>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Détails de l'emploi du temps</h5>
                        <div class="btn-group">
                            <a href="{{ route('timetable.edit', $timetable->id) }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit mr-1"></i> Modifier
                            </a>
                            <form action="{{ route('timetable.toggle-active', $timetable->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm {{ $timetable->is_active ? 'btn-secondary' : 'btn-success' }}">
                                    <i class="fas {{ $timetable->is_active ? 'fa-times' : 'fa-check' }} mr-1"></i>
                                    {{ $timetable->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                            <a href="{{ route('timetable.class-view', $timetable->class_id) }}" class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-print mr-1"></i> Imprimer
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <strong>Statut:</strong>
                                @if($timetable->is_active)
                                    <span class="badge bg-success">Actif</span>
                                @else
                                    <span class="badge bg-secondary">Inactif</span>
                                @endif
                            </div>
                            <div class="col-md-3">
                                <strong>Nombre de créneaux:</strong> {{ $timetable->slots->count() }}
                            </div>
                            <div class="col-md-3">
                                <strong>Créé le:</strong> {{ $timetable->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="col-md-3">
                                <strong>Modifié le:</strong> {{ $timetable->updated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered timetable-grid">
                                <thead>
                                    <tr>
                                        <th>Période</th>
                                        <th>Lundi</th>
                                        <th>Mardi</th>
                                        <th>Mercredi</th>
                                        <th>Jeudi</th>
                                        <th>Vendredi</th>
                                        <th>Samedi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $periods = \App\Models\Period::orderBy('order')->get();
                                        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                    @endphp
                                    
                                    {{-- @foreach($periods as $period)
                                        <tr>
                                            <td class="period-cell">
                                                <strong>{{ $period->name }}</strong><br>
                                                <small>{{ $period->start_time }} - {{ $period->end_time }}</small>
                                            </td>
                                            @foreach($days as $day)
                                                @php
                                                    $slot = $timetable->slots
                                                        ->where('day_of_week', $day)
                                                        ->where('period_id', $period->id)
                                                        ->first();
                                                @endphp
                                                <td class="day-cell {{ $slot ? 'has-slot' : 'empty-slot' }}">
                                                    @if($slot)
                                                        <div class="slot-content">
                                                            <div class="subject-name">
                                                                <strong>{{ $slot->subject->name }}</strong>
                                                            </div>
                                                            <div class="teacher-name">
                                                                {{ $slot->teacher->name }}
                                                            </div>
                                                            <div class="classroom">
                                                                <i class="fas fa-door-open"></i> {{ $slot->classroom->name }}
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="text-center text-muted">
                                                            <i class="fas fa-times"></i><br>
                                                            <small>Libre</small>
                                                        </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach --}}
                                    @foreach($periods as $period)

                                        @if($period->is_break)
                                            {{-- Ligne spéciale pour la pause --}}
                                            <tr class="break-row">
                                                <td colspan="7" class="text-center break-cell bg-warning text-dark font-italic">
                                                    <i class="fas fa-coffee mr-2"></i>
                                                    {{ $period->name }} ({{ $period->start_time }} - {{ $period->end_time }})
                                                </td>
                                            </tr>
                                        @else
                                            {{-- Ligne normale pour les cours --}}
                                            <tr>
                                                <td class="period-cell">
                                                    <strong>{{ $period->name }}</strong><br>
                                                    <small>{{ $period->start_time }} - {{ $period->end_time }}</small>
                                                </td>

                                                @foreach($days as $day)
                                                    @php
                                                        $slot = $timetable->slots
                                                            ->where('day_of_week', $day)
                                                            ->where('period_id', $period->id)
                                                            ->first();
                                                    @endphp

                                                    <td class="day-cell {{ $slot ? 'has-slot' : 'empty-slot' }}">
                                                        @if($slot)
                                                            <div class="slot-content">
                                                                <div class="subject-name">
                                                                    <strong>{{ $slot->subject->name }}</strong>
                                                                </div>
                                                                <div class="teacher-name">
                                                                    {{ $slot->teacher->name }}
                                                                </div>
                                                                <div class="classroom">
                                                                    <i class="fas fa-door-open"></i> {{ $slot->classroom->name }}
                                                                </div>
                                                            </div>
                                                        @else
                                                            <div class="text-center text-muted">
                                                                <i class="fas fa-times"></i><br>
                                                                <small>Libre</small>
                                                            </div>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endif

                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Statistiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Répartition par matière</h6>
                                <canvas id="subjectChart" height="200"></canvas>
                            </div>
                            <div class="col-md-6">
                                <h6>Charge des enseignants</h6>
                                <canvas id="teacherChart" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timetable-grid {
    font-size: 0.9rem;
}
.period-cell {
    background-color: #f8f9fa;
    text-align: center;
    width: 120px;
}
.day-cell {
    vertical-align: middle;
    min-height: 80px;
}
.has-slot {
    background-color: #e8f5e8;
    border: 1px solid #c3e6c3;
}
.empty-slot {
    background-color: #f8f9fa;
}
.slot-content {
    padding: 5px;
}
.subject-name {
    font-weight: 500;
    margin-bottom: 3px;
}
.teacher-name {
    font-size: 0.85em;
    color: #6c757d;
    margin-bottom: 3px;
}
.classroom {
    font-size: 0.8em;
    color: #28a745;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Données pour le graphique des matières
    const subjectData = {!! json_encode($timetable->slots->groupBy('subject_id')->map->count()) !!};
    const subjectLabels = {!! json_encode($timetable->slots->groupBy('subject.name')->map->count()->keys()) !!};
    
    new Chart(document.getElementById('subjectChart'), {
        type: 'pie',
        data: {
            labels: subjectLabels,
            datasets: [{
                data: Object.values(subjectData),
                backgroundColor: [
                    '#4361ee', '#3a0ca3', '#7209b7', '#f72585', 
                    '#4cc9f0', '#4895ef', '#560bad', '#b5179e'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
    
    // Données pour le graphique des enseignants
    const teacherData = {!! json_encode($timetable->slots->groupBy('teacher.name')->map->count()) !!};
    
    new Chart(document.getElementById('teacherChart'), {
        type: 'bar',
        data: {
            labels: Object.keys(teacherData),
            datasets: [{
                label: 'Heures par semaine',
                data: Object.values(teacherData),
                backgroundColor: '#4361ee'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Nombre d\'heures'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Enseignants'
                    }
                }
            }
        }
    });
});
</script>

@endsection