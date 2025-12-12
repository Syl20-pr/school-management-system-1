<!-- resources/views/backend/timetable/partials/filters.blade.php -->
<div class="card filter-card mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Filtres de recherche</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('timetable.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_year">Année académique</label>
                        <select name="year_id" id="filter_year" class="form-control select2">
                            <option value="">Toutes les années</option>
                            @foreach($years as $year)
                                <option value="{{ $year->id }}" {{ request('year_id') == $year->id ? 'selected' : '' }}>
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_class">Classe</label>
                        <select name="class_id" id="filter_class" class="form-control select2">
                            <option value="">Toutes les classes</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="filter_term">Trimestre</label>
                        <select name="term" id="filter_term" class="form-control">
                            <option value="">Tous les trimestres</option>
                            <option value="first" {{ request('term') == 'first' ? 'selected' : '' }}>Premier trimestre</option>
                            <option value="second" {{ request('term') == 'second' ? 'selected' : '' }}>Deuxième trimestre</option>
                            <option value="third" {{ request('term') == 'third' ? 'selected' : '' }}>Troisième trimestre</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12 d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter mr-2"></i> Filtrer
                    </button>
                    <a href="{{ route('timetable.index') }}" class="btn btn-light">
                        <i class="fas fa-times mr-2"></i> Réinitialiser
                    </a>
                </div>
            </div>
        </form>
        
        @if(request()->hasAny(['year_id', 'class_id', 'term']))
            <div class="alert alert-light mt-3 mb-0">
                <strong>Filtres appliqués :</strong>
                @if(request('year_id'))
                    <span class="badge bg-primary mr-2">
                        Année: {{ $years->firstWhere('id', request('year_id'))->name ?? 'Inconnue' }}
                    </span>
                @endif
                @if(request('class_id'))
                    <span class="badge bg-success mr-2">
                        Classe: {{ $classes->firstWhere('id', request('class_id'))->name ?? 'Inconnue' }}
                    </span>
                @endif
                @if(request('term'))
                    <span class="badge bg-info mr-2">
                        Trimestre: {{ ucfirst(request('term')) }}
                    </span>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
.filter-card {
    border-left: 4px solid #4361ee;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les select2
    $('#filter_year, #filter_class').select2({
        width: '100%',
        placeholder: 'Sélectionnez...'
    });
});
</script>