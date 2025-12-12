@extends('admin.admin_master')
@section('admin')


@section('title', "Bulletin de {{ $student->name }}")

@section('content')
<div class="container">
    <h2 class="mb-3">Bulletin de <strong>{{ $student->name }}</strong> ({{ $className }} - {{ $termTypeName }})</h2>

    <x-table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Notes</th>
                <th>Moyenne</th>
                <th>Coeff.</th>
                <th>Moyenne Pondérée</th>
                <th>Rang</th>
                <th>Appréciation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentData['subjects'] as $subject)
                <x-subject-row :subject="$subject" />
            @endforeach
        </tbody>
    </x-table>

    <x-stats-box titre="Moyenne générale" :valeur="number_format($studentData['term_mean'], 2)" />
    <x-stats-box titre="Appréciation" :valeur="$studentData['appreciation_term']" />

    <a href="{{ route('report.bulletins.download', $student->id) }}" class="btn btn-success mt-3">📥 Télécharger en PDF</a>
</div>
@endsection
