<!-- resources/views/backend/timetable/edit.blade.php -->
@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h2 class="page-title">Modifier l'Emploi du Temps</h2>
                    <p class="page-subtitle text-muted">Modifiez l'emploi du temps de {{ $timetable->class->name }}</p>
                </div>
                
                <form action="{{ route('timetable.update', $timetable->id) }}" method="POST" id="timetableForm">
                    @csrf
                    @method('PUT')
                    
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
                                               value="{{ old('name', $timetable->name) }}" required>
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
                                                <option value="{{ $year->id }}" {{ old('academic_year_id', $timetable->academic_year_id) == $year->id ? 'selected' : '' }}>
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
                                                <option value="{{ $class->id }}" {{ old('class_id', $timetable->class_id) == $class->id ? 'selected' : '' }}>
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
                                            <option value="first" {{ old('term', $timetable->term) == 'first' ? 'selected' : '' }}>Premier trimestre</option>
                                            <option value="second" {{ old('term', $timetable->term) == 'second' ? 'selected' : '' }}>Deuxième trimestre</option>
                                            <option value="third" {{ old('term', $timetable->term) == 'third' ? 'selected' : '' }}>Troisième trimestre</option>
                                        </select>
                                        @error('term')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-check mt-3">
                                <input type="checkbox" name="is_active" id="is_active" 
                                       class="form-check-input" {{ old('is_active', $timetable->is_active) ? 'checked' : '' }}>
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
                            <i class="fas fa-save mr-2"></i> Mettre à jour l'emploi du temps
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    let slotIndex = 0;
    const slotsContainer = document.getElementById('slots-container');
    const slotTemplate = document.getElementById('slot-template');
    const addSlotButton = document.getElementById('addSlot');
    
    // Fonction pour ajouter un créneau avec des valeurs spécifiques
    /* function addSlot(slotData = {}) {
        const newSlot = slotTemplate.content.cloneNode(true);
        const slotHtml = newSlot.querySelector('.slot-item').outerHTML;
        let newSlotHtml = slotHtml.replace(/INDEX/g, slotIndex);
        
        // Pré-remplir les valeurs si fournies
        if (slotData.day_of_week) {
            newSlotHtml = newSlotHtml.replace(
                `value="${slotData.day_of_week}"`, 
                `value="${slotData.day_of_week}" selected`
            );
        }
        if (slotData.period_id) {
            newSlotHtml = newSlotHtml.replace(
                `value="${slotData.period_id}"`, 
                `value="${slotData.period_id}" selected`
            );
        }
        if (slotData.subject_id) {
            newSlotHtml = newSlotHtml.replace(
                `value="${slotData.subject_id}"`, 
                `value="${slotData.subject_id}" selected`
            );
        }
        if (slotData.teacher_id) {
            newSlotHtml = newSlotHtml.replace(
                `value="${slotData.teacher_id}"`, 
                `value="${slotData.teacher_id}" selected`
            );
        }
        if (slotData.classroom_id) {
            newSlotHtml = newSlotHtml.replace(
                `value="${slotData.classroom_id}"`, 
                `value="${slotData.classroom_id}" selected`
            );
        }
        
        slotsContainer.insertAdjacentHTML('beforeend', newSlotHtml);
        slotIndex++;
        
        // Initialiser les select2 pour les nouveaux selects
        setTimeout(() => {
            $('.day-select:last, .period-select:last, .subject-select:last, .teacher-select:last, .classroom-select:last').select2({
                width: '100%'
            });
        }, 100);
    } */

    function addSlot(slotData = {}) {
        const newSlot = slotTemplate.content.cloneNode(true);
        
        // Remplace INDEX par slotIndex
        const slotHtml = newSlot.querySelector('.slot-item').outerHTML.replace(/INDEX/g, slotIndex);
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = slotHtml;

        const slotElement = tempDiv.querySelector('.slot-item');

        // Assigne les valeurs du slot si disponibles
        if (slotData.day_of_week) {
            slotElement.querySelector('[name^="slots"][name$="[day_of_week]"]').value = slotData.day_of_week;
        }
        if (slotData.period_id) {
            slotElement.querySelector('[name^="slots"][name$="[period_id]"]').value = slotData.period_id;
        }
        if (slotData.subject_id) {
            slotElement.querySelector('[name^="slots"][name$="[subject_id]"]').value = slotData.subject_id;
        }
        if (slotData.teacher_id) {
            slotElement.querySelector('[name^="slots"][name$="[teacher_id]"]').value = slotData.teacher_id;
        }
        if (slotData.classroom_id) {
            slotElement.querySelector('[name^="slots"][name$="[classroom_id]"]').value = slotData.classroom_id;
        }

        // Ajoute le slot dans le DOM
        slotsContainer.appendChild(slotElement);
        slotIndex++;

        // Active select2 pour ce slot
        setTimeout(() => {
            $(slotElement).find('.day-select, .period-select, .subject-select, .teacher-select, .classroom-select').select2({
                width: '100%'
            });
        }, 100);
    }

    
    // Ajouter un créneau vide
    addSlotButton.addEventListener('click', function() {
        addSlot();
    });
    
    // Supprimer un créneau
    slotsContainer.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-slot') || e.target.closest('.remove-slot')) {
            const slotItem = e.target.closest('.slot-item');
            slotItem.remove();
        }
    });
    
    // Charger les créneaux existants
    @if(old('slots'))
        @foreach(old('slots') as $index => $slot)
            addSlot({!! json_encode($slot) !!});
        @endforeach
    @else
        @foreach($timetable->slots as $slot)
            addSlot({!! json_encode($slot) !!});
        @endforeach
    @endif
    
    // Initialiser select2 pour tous les selects
    $('.select2').select2();
});
</script>

@endsection