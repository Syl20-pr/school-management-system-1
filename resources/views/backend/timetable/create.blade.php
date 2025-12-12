<!-- resources/views/backend/timetable/create.blade.php -->
@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h2 class="page-title">Créer un Emploi du Temps</h2>
                    <p class="page-subtitle text-muted">Configurez un nouvel emploi du temps pour une classe</p>
                </div>
                
                <form action="{{ route('timetable.store') }}" method="POST" id="timetableForm">
                    @csrf
                    
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="card-title mb-0">Informations de base</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nom de l'emploi du temps *</label>
                                        <input type="text" name="name" id="name" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="academic_year_id">Année académique *</label>
                                        <select name="academic_year_id" id="academic_year_id" 
                                                class="form-control select2 @error('academic_year_id') is-invalid @enderror" required>
                                            <option value="">Sélectionnez une année</option>
                                            @foreach($years as $year)
                                                <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                    {{ $year->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('academic_year_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="class_id">Classe *</label>
                                        <select name="class_id" id="class_id" 
                                                class="form-control select2 @error('class_id') is-invalid @enderror" required>
                                            <option value="">Sélectionnez une classe</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                    {{ $class->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('class_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="term">Trimestre *</label>
                                        <select name="term" id="term" 
                                                class="form-control @error('term') is-invalid @enderror" required>
                                            <option value="first" {{ old('term') == 'first' ? 'selected' : '' }}>Premier trimestre</option>
                                            <option value="second" {{ old('term') == 'second' ? 'selected' : '' }}>Deuxième trimestre</option>
                                            <option value="third" {{ old('term') == 'third' ? 'selected' : '' }}>Troisième trimestre</option>
                                        </select>
                                        @error('term')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mt-3">
                                <input type="checkbox" name="is_active" id="is_active" 
                                       class="form-check-input" {{ old('is_active') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Rendre cet emploi du temps actif</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Créneaux horaires</h5>
                            <button type="button" class="btn btn-sm btn-primary" id="addSlot">
                                <i class="fas fa-plus mr-1"></i> Ajouter un créneau
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="slots-container">
                                <!-- Les créneaux seront ajoutés ici dynamiquement -->
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('timetable.index') }}" class="btn btn-light">
                            <i class="fas fa-arrow-left mr-2"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save mr-2"></i> Enregistrer l'emploi du temps
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Template pour un créneau (caché) -->
<template id="slot-template">
    <div class="slot-item card mb-3">
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Jour *</label>
                        <select name="slots[INDEX][day_of_week]" class="form-control day-select" required>
                            <option value="monday">Lundi</option>
                            <option value="tuesday">Mardi</option>
                            <option value="wednesday">Mercredi</option>
                            <option value="thursday">Jeudi</option>
                            <option value="friday">Vendredi</option>
                            <option value="saturday">Samedi</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Période *</label>
                        <select name="slots[INDEX][period_id]" class="form-control period-select" required>
                            @foreach($periods as $period)
                                <option value="{{ $period->id }}">{{ $period->name }} ({{ $period->start_time }} - {{ $period->end_time }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Matière *</label>
                        <select name="slots[INDEX][subject_id]" class="form-control subject-select" required>
                            <option value="">Sélectionnez</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Enseignant *</label>
                        <select name="slots[INDEX][teacher_id]" class="form-control teacher-select" required>
                            <option value="">Sélectionnez</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Salle *</label>
                        <select name="slots[INDEX][classroom_id]" class="form-control classroom-select" required>
                            <option value="">Sélectionnez</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">{{ $classroom->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-slot">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.slot-item {
    border-left: 4px solid #4361ee;
}
.remove-slot {
    margin-bottom: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let slotIndex = 0;
    const slotsContainer = document.getElementById('slots-container');
    const slotTemplate = document.getElementById('slot-template');
    const addSlotButton = document.getElementById('addSlot');
    
    // Ajouter un créneau
    addSlotButton.addEventListener('click', function() {
        const newSlot = slotTemplate.content.cloneNode(true);
        const slotHtml = newSlot.querySelector('.slot-item').outerHTML;
        const newSlotHtml = slotHtml.replace(/INDEX/g, slotIndex);
        
        slotsContainer.insertAdjacentHTML('beforeend', newSlotHtml);
        slotIndex++;
        
        // Initialiser les select2 pour les nouveaux selects
        $('.day-select:last, .period-select:last, .subject-select:last, .teacher-select:last, .classroom-select:last').select2({
            width: '100%'
        });
    });
    
    // Supprimer un créneau
    slotsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-slot') || e.target.closest('.remove-slot')) {
            const slotItem = e.target.closest('.slot-item');
            slotItem.remove();
        }
    });
    
    // Ajouter un créneau initial si validation échouée
    @if(old('slots'))
        @foreach(old('slots') as $index => $slot)
            const newSlot = slotTemplate.content.cloneNode(true);
            const slotHtml = newSlot.querySelector('.slot-item').outerHTML;
            let newSlotHtml = slotHtml.replace(/INDEX/g, {{ $index }});
            
            // Pré-remplir les valeurs
            newSlotHtml = newSlotHtml.replace(
                'value="' + '{{ $slot['day_of_week'] }}' + '"', 
                'value="{{ $slot['day_of_week'] }}" selected'
            );
            
            slotsContainer.insertAdjacentHTML('beforeend', newSlotHtml);
            slotIndex = {{ $index }} + 1;
        @endforeach
        
        // Initialiser select2 pour les slots existants
        $('.day-select, .period-select, .subject-select, .teacher-select, .classroom-select').select2({
            width: '100%'
        });
    @else
        // Ajouter un créneau vide au chargement
        addSlotButton.click();
    @endif
});
</script>

@endsection