@extends('admin.admin_master')
@section('admin')

<style>
/* copie les styles du fichier que tu m'as donné (omitted here for brevity) */
</style>

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card promotion-card">
                    <div class="card-header py-4">
                        <h4 class="card-title mb-0">Décision de fin d'année – {{ $student->student->name }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="student-info-card mb-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ID:</strong> {{ $student->student->id_no }}</p>
                                    <p><strong>Classe actuelle:</strong> {{ $student->student_class->name }}</p>
                                    <p><strong>Année scolaire:</strong> {{ $student->student_year->name }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Genre:</strong> {{ $student->student->gender }}</p>
                                    <p><strong>Statut:</strong> <span class="badge">{{ $student->student->statusclass }}</span></p>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('student.promotion.process', $student->student_id) }}" method="POST" id="promotionForm">
                            @csrf

                            <h5 class="mb-3 text-light">Choisir une action :</h5>
                            <div class="row">
                                <div class="col-md-3 mb-3"><button type="button" class="action-btn action-btn-promote" data-action="promote">Promouvoir</button></div>
                                <div class="col-md-3 mb-3"><button type="button" class="action-btn action-btn-repeat" data-action="repeat">Redoubler</button></div>
                                <div class="col-md-3 mb-3"><button type="button" class="action-btn action-btn-exclude" data-action="exclude">Exclure</button></div>
                                <div class="col-md-3 mb-3"><button type="button" class="action-btn action-btn-custom" data-action="custom">Promotion spéciale</button></div>
                            </div>

                            <input type="hidden" name="action" id="selectedAction">

                            <div id="normalPromotionSection" class="mt-3" style="display:none;">
                                @if($promotionOptions['can_promote'])
                                    <div class="next-classes-list">
                                        <h6 class="text-light">Passage en {{ $promotionOptions['current_class']->getNextLevelName() }}</h6>
                                        @if($promotionOptions['next_classes']->count() > 0)
                                            @foreach($promotionOptions['next_classes'] as $nextClass)
                                                <div class="form-check text-light">
                                                    <input class="form-check-input" type="radio" name="target_class" id="class_{{ $nextClass->id }}" value="{{ $nextClass->id }}" {{ $loop->first ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="class_{{ $nextClass->id }}">{{ $nextClass->name }}</label>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="alert alert-warning">Aucune classe disponible pour ce niveau.</div>
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-info">Cet élève est au niveau maximum. Utilisez la promotion spéciale si nécessaire.</div>
                                @endif
                            </div>

                            <div id="customPromotionSection" class="mt-3" style="display:none;">
                                <div class="form-group">
                                    <label for="target_class" class="text-light">Choisir la classe de destination :</label>
                                    <select name="target_class" id="target_class" class="form-control custom-class-select">
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group mt-3">
                                <label for="reason" class="text-light">Motif (optionnel):</label>
                                <textarea name="reason" id="reason" class="form-control reason-textarea" placeholder="Ex: Résultats excellents..."></textarea>
                            </div>

                            <div class="text-right mt-4">
                                <button type="submit" class="btn btn-success btn-lg px-5">Confirmer</button>
                                <a href="{{ route('student.registration.view') }}" class="btn btn-secondary btn-lg px-5 ml-2">Annuler</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if($history->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card promotion-card">
                        <div class="card-header py-4"><h5 class="card-title mb-0">Historique des décisions</h5></div>
                        <div class="card-body">
                            <div class="history-timeline">
                                @foreach($history as $record)
                                    <div class="timeline-item">
                                        <div class="d-flex justify-content-between">
                                            <h6>
                                                @if($record->action == 'promote') <span class="decision-badge badge-promote">Promotion</span>
                                                @elseif($record->action == 'repeat') <span class="decision-badge badge-repeat">Redoublement</span>
                                                @else <span class="decision-badge badge-exclude">Exclusion</span> @endif
                                                de {{ $record->fromClass->name }} ({{ $record->fromYear->name }}) 
                                                @if($record->action != 'exclude') → {{ $record->toClass->name }} ({{ $record->toYear->name }}) @endif
                                            </h6>
                                            <small class="text-muted">{{ $record->decision_date->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <p class="mb-1">Décidé par: {{ $record->decisionMaker->name }}</p>
                                        @if($record->reason) <p class="text-muted">Motif: {{ $record->reason }}</p> @endif

                                        @if(!$record->cancelled_at)
                                            <form action="{{ route('student.promotion.cancel', $record->id) }}" method="POST" onsubmit="return confirm('Confirmer annulation ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-danger">Annuler cette décision</button>
                                            </form>
                                        @else
                                            <div class="text-muted">Annulé le {{ $record->cancelled_at->format('d/m/Y H:i') }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const actionButtons = document.querySelectorAll('[data-action]');
    const selectedActionInput = document.getElementById('selectedAction');
    const normalPromotionSection = document.getElementById('normalPromotionSection');
    const customPromotionSection = document.getElementById('customPromotionSection');
    const form = document.getElementById('promotionForm');

    actionButtons.forEach(button => {
        button.addEventListener('click', function() {
            const action = this.getAttribute('data-action');
            selectedActionInput.value = action;

            actionButtons.forEach(btn => btn.style.opacity = 0.6);
            this.style.opacity = 1;

            if (action === 'promote') {
                normalPromotionSection.style.display = 'block';
                customPromotionSection.style.display = 'none';
            } else if (action === 'custom') {
                normalPromotionSection.style.display = 'none';
                customPromotionSection.style.display = 'block';
            } else {
                normalPromotionSection.style.display = 'none';
                customPromotionSection.style.display = 'none';
            }
        });
    });

    form.addEventListener('submit', function(e) {
        if (!selectedActionInput.value) {
            e.preventDefault();
            alert('Veuillez sélectionner une action.');
            return;
        }
    });
});
</script>

@endsection
