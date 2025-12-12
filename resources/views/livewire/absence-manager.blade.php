<div>
    <!-- Debug info (optionnel, peut être retiré en production) -->
    @if(env('APP_DEBUG'))
    <div class="alert alert-info py-2">
        <small>Debug: Year: {{ $yearId }}, Class: {{ $classId }}, Types count: {{ count($types) }}</small>
    </div>
    @endif

    <div class="row mb-3">
        <!-- Année (sélection automatique) -->
        <div class="col-md-3">
            <label class="form-label">Année scolaire <span class="text-danger">*</span></label>
            <select wire:model.live="yearId" class="form-control">
                <option value="">-- Sélectionner --</option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}" {{ $year->is_current ? 'selected' : '' }}>
                        {{ $year->name }}
                        @if($year->is_current) (En cours) @endif
                    </option>
                @endforeach
            </select>
            @error('yearId') <span class="text-danger">{{ $message }}</span> @enderror
            
            @if($yearId)
                <small class="text-success">
                    <i class="fa fa-check"></i> Année sélectionnée
                </small>
            @endif
        </div>

        <!-- Classe (sélection utilisateur) -->
        <div class="col-md-3">
            <label class="form-label">Classe <span class="text-danger">*</span></label>
            <select wire:model.live="classId" class="form-control">
                <option value="">-- Sélectionner une classe --</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
            @error('classId') <span class="text-danger">{{ $message }}</span> @enderror
            
            @if($classId)
                <small class="text-success">
                    <i class="fa fa-check"></i> Classe sélectionnée
                </small>
            @endif
        </div>

        <!-- Type (dépend de la classe) -->
        @if($classId && count($types) > 0)
        <div class="col-md-3">
            <label class="form-label">Type (Trimestre/Semestre)</label>
            <select wire:model="typeId" class="form-control">
                <option value="">-- Sélectionner --</option>
                @foreach($types as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </select>
            
            <small class="text-muted">
                Période: {{ implode(', ', array_values($types)) }}
            </small>
        </div>
        @endif
    </div>

    <!-- Indicateur de chargement -->
    @if($yearId && $classId && empty($students))
    <div class="alert alert-info">
        <i class="fa fa-spinner fa-spin"></i> Chargement des étudiants...
    </div>
    @endif

    @if(count($students) > 0)
        <div class="alert alert-success">
            <i class="fa fa-users"></i> 
            {{ count($students) }} étudiant(s) trouvé(s) pour cette classe
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Nom</th>
                        <th>Genre</th>
                        <th>Absences (heures)</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($students as $index => $student)
                        <tr>
                            <td>{{ $student['name'] }}</td>
                            <td>{{ $student['gender'] }}</td>
                            <td>
                                <input type="number"
                                    wire:model="students.{{ $index }}.absences"
                                    wire:change="autoSave({{ $index }})"
                                    class="form-control"
                                    min="0" 
                                    max="40"
                                    style="width: 80px">
                            </td>
                            <td>
                                <span wire:loading wire:target="autoSave({{ $index }})" 
                                    class="text-info">
                                    <i class="fa fa-spinner fa-spin"></i>
                                </span>
                                <span wire:loading.remove 
                                    class="text-success">
                                    <i class="fa fa-check"></i>
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            <button type="button" 
                    wire:click="saveAll" 
                    wire:loading.attr="disabled"
                    class="btn btn-primary">
                <span wire:loading wire:target="saveAll" class="spinner-border spinner-border-sm"></span>
                <i class="fa fa-save"></i> Sauvegarder tout
            </button>
            
            <small class="text-muted ms-2">
                Les modifications sont sauvegardées automatiquement, ce bouton force la sauvegarde de tous les champs.
            </small>
        </div>
    @elseif($yearId && $classId)
        <div class="alert alert-info mt-3">
            <i class="fa fa-info-circle"></i>
            Aucun étudiant trouvé pour cette classe et cette année.
        </div>
    @else
        <div class="alert alert-warning mt-3">
            <i class="fa fa-exclamation-triangle"></i>
            Veuillez sélectionner une année et une classe pour voir les étudiants.
        </div>
    @endif

    @script
    <script>
        window.addEventListener('notify', (event) => {
            Swal.fire({
                icon: event.detail.type,
                title: event.detail.message,
                timer: 2000,
                showConfirmButton: false
            });
        });
        
        // Chargement automatique des étudiants quand l'année et la classe sont sélectionnées
        window.addEventListener('DOMContentLoaded', (event) => {
            @this.set('yearId', @this.get('yearId'), true);
            @this.set('classId', @this.get('classId'), true);
        });
    </script>
    @endscript
</div>