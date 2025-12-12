@extends('admin.admin_master')
@section('admin')


@section('title', 'Statistiques de Classe')

@section('content')
<div class="container">
    <h1>📊 Statistiques - {{ $className }} ({{ $termTypeName }})</h1>

    <div class="row mt-4">
        <div class="col-md-3"><x-stats-box titre="Meilleure Moyenne" :valeur="$highestTermMean" /></div>
        <div class="col-md-3"><x-stats-box titre="Plus faible Moyenne" :valeur="$lowestTermMean" /></div>
        <div class="col-md-3"><x-stats-box titre="Moyenne de Classe" :valeur="$classTermMean" /></div>
        <div class="col-md-3"><x-stats-box titre="% Réussite" :valeur="$percentageAbove10 . '%'" /></div>
    </div>

    <x-table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Moyenne</th>
                <th>Rang</th>
                <th>Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentData as $student)
                <x-student-row :student="$student" />
            @endforeach
        </tbody>
    </x-table>
</div>
@endsection
