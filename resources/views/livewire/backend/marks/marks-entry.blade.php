<div>
    <div class="row">
        <div class="col-md-3">
            <label>Année Scolaire</label>
            <select wire:model="year_id" class="form-control">
                <option value="">Sélectionner</option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Classe</label>
            <select wire:model="class_id" class="form-control">
                <option value="">Sélectionner</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Matière</label>
            <select wire:model="assign_subject_id" class="form-control">
                <option value="">Sélectionner</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->subject_id }}">{{ $subject->school_subject->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label>Trimestre/Semestre</label>
            <select wire:model="term_type_id" class="form-control">
                <option value="">Sélectionner</option>
                @foreach($term_types as $term)
                    <option value="{{ $term->id }}">{{ $term->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 mt-3">
            <button wire:click="loadStudents" class="btn btn-primary">Rechercher</button>
        </div>
    </div>

    @if($students)
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    @foreach($examTypes as $examType)
                        <th>{{ $examType }}</th>
                    @endforeach
                    @if($isEPS)
                        <th>Inapte</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student->student->id_no }}</td>
                        <td>{{ $student->student->name }}</td>
                        @foreach($examTypes as $examType)
                            <td>
                                <input type="text" wire:model.lazy="marks.{{ $student->student_id }}.{{ $examType }}" class="form-control">
                            </td>
                        @endforeach
                        @if($isEPS)
                            <td>
                                <select wire:model="inapte.{{ $student->student_id }}" class="form-control">
                                    <option value="0">Apte</option>
                                    <option value="1">Inapte</option>
                                </select>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button wire:click="saveMarks" class="btn btn-success mt-3">Sauvegarder Manuellement</button>
    @endif
</div>
<!-- @push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', (message, component) => {
            if (Livewire.components.componentsByName['backend.marks.marks-entry']) {
                Livewire.emit('loadStudents');
            }
        });
    });
</script>
@endpush -->
