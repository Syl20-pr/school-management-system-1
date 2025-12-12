@extends('admin.admin_master')
@section('admin')

    <div class="container">
        <h1 class="mb-4">📑 Génération des Bulletins</h1>

        <x-alert type="info" message="Sélectionnez une classe, une année scolaire et une période pour générer les bulletins." />

        <form method="POST" action="{{ route('report.bulletins.class') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="year_id" class="form-label">Année scolaire</label>
                    <select class="form-select" name="year_id" id="year_id" required>
                        @foreach($years as $year)
                            <option value="{{ $year->id }}">{{ $year->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="class_id" class="form-label">Classe</label>
                    <select class="form-select" name="class_id" id="class_id" required>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="term_type_id" class="form-label">Période</label>
                    <select class="form-select" name="term_type_id" id="term_type_id" required>
                        @foreach($termTypes as $term)
                            <option value="{{ $term->id }}">{{ $term->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <x-button type="submit" style="primary">Générer les Bulletins</x-button>
        </form>
    </div>
@endsection


