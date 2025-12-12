<!-- resources/views/backend/timetable/teacher-view.blade.php -->
@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h2 class="page-title">Emploi du Temps - {{ $teacher->name }}</h2>
                    <p class="page-subtitle text-muted">
                        {{ $teacher->specialty ?? 'Enseignant' }} - 
                        {{ $teacher->email }}
                    </p>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Emploi du temps hebdomadaire</h5>
                        <div class="btn-group">
                            <a href="javascript:window.print()" class="btn btn-info btn-sm">
                                <i class="fas fa-print mr-1"></i> Imprimer
                            </a>
                            <a href="{{ route('timetable.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left mr-1"></i> Retour
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($slots->flatten()->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-bordered timetable-grid">
                                    <thead>
                                        <tr>
                                            <th class="period-header">Période</th>
                                            @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'] as $day)
                                                <th class="day-header">{{ $day }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $periods = \App\Models\Period::orderBy('order')->get();
                                            $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                        @endphp
                                        
                                        @foreach($periods as $period)
                                            @if($period->is_break)
                                                {{-- Ligne pause (colspan) --}}
                                                <tr class="break-row">
                                                    <td colspan="7" class="text-center break-cell">
                                                        <i class="fas fa-coffee mr-2"></i> {{ $period->name }}
                                                        ({{ $period->start_time }} - {{ $period->end_time }})
                                                    </td>
                                                </tr>
                                            @else
                                                <tr class="period-row">
                                                    <td class="period-cell">
                                                        <div class="period-info">
                                                            <strong>{{ $period->name }}</strong>
                                                            <br>
                                                            <small class="text-muted">{{ $period->start_time }} - {{ $period->end_time }}</small>
                                                        </div>
                                                    </td>
                                                    
                                                    @foreach($days as $dayKey)
                                                        @php
                                                            $daySlots = $slots[$dayKey] ?? collect();
                                                            $slot = $daySlots->firstWhere('period_id', $period->id);
                                                        @endphp
                                                        
                                                        <td class="day-cell {{ $slot ? 'has-slot' : 'empty-slot' }}">
                                                            @if($slot)
                                                                <div class="slot-content">
                                                                    <div class="subject-name">
                                                                        <strong>{{ $slot->subject->name }}</strong>
                                                                    </div>
                                                                    <div class="class-info">
                                                                        <i class="fas fa-users"></i> {{ $slot->timetable->class->name }}
                                                                    </div>
                                                                    <div class="classroom">
                                                                        <i class="fas fa-door-open"></i> {{ $slot->classroom->name }}
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="empty-slot-content text-center text-muted">
                                                                    <i class="fas fa-times"></i>
                                                                    <br>
                                                                    <small>Libre</small>
                                                                </div>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endif


                                            
                                            {{-- @if($period->is_break)
                                                <tr class="break-row">
                                                    <td colspan="7" class="text-center break-cell">
                                                        <i class="fas fa-coffee mr-2"></i> {{ $period->name }}
                                                        ({{ $period->start_time }} - {{ $period->end_time }})
                                                    </td>
                                                </tr>
                                            @endif --}}
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Aucun cours programmé pour cet enseignant.
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">Statistiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-icon bg-primary">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3>{{ $slots->flatten()->count() }}</h3>
                                        <p>Heures de cours par semaine</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-card">
                                    <div class="stat-icon bg-success">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="stat-content">
                                        <h3>{{ $slots->flatten()->groupBy('timetable.class_id')->count() }}</h3>
                                        <p>Classes différentes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6>Répartition par matière</h6>
                                <div class="list-group">
                                    @foreach($slots->flatten()->groupBy('subject.name') as $subjectName => $subjectSlots)
                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $subjectName }}
                                            <span class="badge bg-primary rounded-pill">{{ $subjectSlots->count() }}h</span>
                                        </div>
                                    @endforeach
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
.stat-card {
    display: flex;
    align-items: center;
    padding: 20px;
    background-color: #f8f9fa;
    border-radius: 8px;
    margin-bottom: 20px;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 20px;
    color: white;
    font-size: 1.5rem;
}

.stat-content h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: bold;
    color: #4361ee;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
}

/* Styles pour l'impression */
@media print {
    .btn-group, .page-subtitle {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .timetable-grid {
        font-size: 10pt !important;
    }
    
    .stat-card {
        page-break-inside: avoid;
    }
}
</style>

@endsection