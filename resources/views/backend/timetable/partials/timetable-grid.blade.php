<!-- resources/views/backend/timetable/partials/timetable-grid.blade.php -->
@php
    $days = ['monday' => 'Lundi', 'tuesday' => 'Mardi', 'wednesday' => 'Mercredi', 
             'thursday' => 'Jeudi', 'friday' => 'Vendredi', 'saturday' => 'Samedi'];
    $periods = \App\Models\Period::orderBy('order')->get();
@endphp

<div class="table-responsive">
    <table class="table table-bordered timetable-grid">
        <thead>
            <tr>
                <th class="period-header">Période</th>
                @foreach($days as $dayName => $dayLabel)
                    <th class="day-header">{{ $dayLabel }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            {{-- @foreach($periods as $period)
                <tr class="period-row">
                    <td class="period-cell">
                        <div class="period-info">
                            <strong>{{ $period->name }}</strong>
                            <br>
                            <small class="text-muted">{{ $period->start_time }} - {{ $period->end_time }}</small>
                        </div>
                    </td>
                    
                    @foreach($days as $dayKey => $dayLabel)
                        @php
                            $slot = $slots->firstWhere('day_of_week', $dayKey)
                                ?->firstWhere('period_id', $period->id);
                        @endphp
                        
                        <td class="day-cell {{ $slot ? 'has-slot' : 'empty-slot' }}" 
                            data-day="{{ $dayKey }}" 
                            data-period="{{ $period->id }}">
                            
                            @if($slot)
                                <div class="slot-content">
                                    <div class="subject-name">
                                        <strong>{{ $slot->subject->name }}</strong>
                                    </div>
                                    <div class="teacher-name">
                                        <i class="fas fa-user"></i> {{ $slot->teacher->name }}
                                    </div>
                                    <div class="classroom">
                                        <i class="fas fa-door-open"></i> {{ $slot->classroom->name }}
                                    </div>
                                    @if(isset($showActions) && $showActions)
                                        <div class="slot-actions mt-2">
                                            <button class="btn btn-sm btn-warning edit-slot" 
                                                    data-slot-id="{{ $slot->id }}"
                                                    data-toggle="tooltip" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-slot" 
                                                    data-slot-id="{{ $slot->id }}"
                                                    data-toggle="tooltip" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="empty-slot-content text-center text-muted">
                                    <i class="fas fa-times fa-lg mb-2"></i>
                                    <br>
                                    <small>Libre</small>
                                    @if(isset($showActions) && $showActions)
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-primary add-slot" 
                                                    data-day="{{ $dayKey }}" 
                                                    data-period="{{ $period->id }}"
                                                    data-toggle="tooltip" title="Ajouter un cours">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </td>
                    @endforeach
                </tr>
                
                @if($period->is_break)
                    <tr class="break-row">
                        <td colspan="7" class="text-center break-cell">
                            <i class="fas fa-coffee mr-2"></i> {{ $period->name }}
                            ({{ $period->start_time }} - {{ $period->end_time }})
                        </td>
                    </tr>
                @endif
            @endforeach --}}

            @foreach($periods as $period)

    @if($period->is_break)
        {{-- Ligne de pause : pas de slots, affichage spécial --}}
        <tr class="break-row">
            <td colspan="{{ count($days) + 1 }}" class="text-center break-cell bg-warning text-dark font-italic">
                <i class="fas fa-coffee mr-2"></i> {{ $period->name }}
                ({{ $period->start_time }} - {{ $period->end_time }})
            </td>
        </tr>
    @else
        {{-- Ligne normale : créneaux par jour --}}
        <tr class="period-row">
            <td class="period-cell">
                <div class="period-info">
                    <strong>{{ $period->name }}</strong><br>
                    <small class="text-muted">{{ $period->start_time }} - {{ $period->end_time }}</small>
                </div>
            </td>

            @foreach($days as $dayKey => $dayLabel)
                @php
                    $slot = $slots->firstWhere('day_of_week', $dayKey)
                        ?->firstWhere('period_id', $period->id);
                @endphp

                <td class="day-cell {{ $slot ? 'has-slot' : 'empty-slot' }}" 
                    data-day="{{ $dayKey }}" 
                    data-period="{{ $period->id }}">
                    
                    @if($slot)
                        <div class="slot-content">
                            <div class="subject-name">
                                <strong>{{ $slot->subject->name }}</strong>
                            </div>
                            <div class="teacher-name">
                                <i class="fas fa-user"></i> {{ $slot->teacher->name }}
                            </div>
                            <div class="classroom">
                                <i class="fas fa-door-open"></i> {{ $slot->classroom->name }}
                            </div>
                            @if(isset($showActions) && $showActions)
                                <div class="slot-actions mt-2">
                                    <button class="btn btn-sm btn-warning edit-slot" 
                                            data-slot-id="{{ $slot->id }}"
                                            data-toggle="tooltip" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-slot" 
                                            data-slot-id="{{ $slot->id }}"
                                            data-toggle="tooltip" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="empty-slot-content text-center text-muted">
                            <i class="fas fa-times fa-lg mb-2"></i>
                            <br>
                            <small>Libre</small>
                            @if(isset($showActions) && $showActions)
                                <div class="mt-2">
                                    <button class="btn btn-sm btn-primary add-slot" 
                                            data-day="{{ $dayKey }}" 
                                            data-period="{{ $period->id }}"
                                            data-toggle="tooltip" title="Ajouter un cours">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            @endif
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

<style>
.timetable-grid {
    font-size: 0.9rem;
    margin-bottom: 0;
}

.period-header, .day-header {
    background-color: #4361ee;
    color: white;
    text-align: center;
    font-weight: bold;
    vertical-align: middle;
}

.period-header {
    width: 120px;
}

.day-header {
    min-width: 150px;
}

.period-cell {
    background-color: #f8f9fa;
    text-align: center;
    vertical-align: middle;
}

.period-info {
    padding: 5px;
}

.day-cell {
    vertical-align: top;
    min-height: 100px;
    position: relative;
}

.has-slot {
    background-color: #e8f5e8;
    border: 2px solid #c3e6c3;
    cursor: pointer;
}

.has-slot:hover {
    background-color: #d4edda;
    border-color: #b8dfc1;
}

.empty-slot {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.empty-slot:hover {
    background-color: #e9ecef;
}

.slot-content {
    padding: 8px;
}

.subject-name {
    font-weight: 500;
    margin-bottom: 3px;
    color: #155724;
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

.empty-slot-content {
    padding: 15px 5px;
}

.slot-actions {
    display: flex;
    gap: 5px;
    justify-content: center;
}

.slot-actions .btn {
    padding: 0.15rem 0.5rem;
    font-size: 0.7rem;
}

.break-row {
    background-color: #fff3cd;
}

.break-cell {
    padding: 8px;
    font-weight: 500;
    color: #856404;
    font-style: italic;
}

/* Responsive design */
@media (max-width: 768px) {
    .timetable-grid {
        font-size: 0.8rem;
    }
    
    .day-cell {
        min-height: 80px;
        padding: 3px;
    }
    
    .slot-content {
        padding: 3px;
    }
    
    .subject-name {
        font-size: 0.85em;
    }
    
    .teacher-name, .classroom {
        font-size: 0.75em;
    }
    
    .empty-slot-content {
        padding: 5px 2px;
    }
    
    .empty-slot-content i {
        font-size: 0.9em;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Gestion des clics sur les cases vides (ajout)
    $('.add-slot').on('click', function() {
        const day = $(this).data('day');
        const periodId = $(this).data('period');
        
        // Ouvrir le modal d'ajout de créneau
        openSlotModal(null, day, periodId);
    });
    
    // Gestion des clics sur les cases pleines (édition)
    $('.edit-slot').on('click', function(e) {
        e.stopPropagation();
        const slotId = $(this).data('slot-id');
        
        // Ouvrir le modal d'édition de créneau
        openSlotModal(slotId);
    });
    
    // Gestion des clics sur les cases pleines (suppression)
    $('.delete-slot').on('click', function(e) {
        e.stopPropagation();
        const slotId = $(this).data('slot-id');
        
        if (confirm('Êtes-vous sûr de vouloir supprimer ce créneau ?')) {
            deleteSlot(slotId);
        }
    });
    
    // Clic sur toute la case pour l'édition
    $('.has-slot').on('click', function() {
        const slotId = $(this).find('.edit-slot').data('slot-id');
        if (slotId) {
            openSlotModal(slotId);
        }
    });
    
    function openSlotModal(slotId, day, periodId) {
        // Implémentez l'ouverture du modal ici
        console.log('Ouvrir modal pour:', { slotId, day, periodId });
        // Vous devrez implémenter cette fonction selon votre structure modale
    }
    
    function deleteSlot(slotId) {
        // Implémentez la suppression AJAX ici
        console.log('Supprimer le créneau:', slotId);
        // Vous devrez implémenter cette fonction avec une requête AJAX
    }
});
</script>