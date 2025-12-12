@extends('admin.admin_master')
@section('admin')

@section('title', 'Statistiques par Matières')

@section('content')
<div class="container">
    <h1>📘 Statistiques par Matière - {{ $className }} ({{ $termTypeName }})</h1>

    <x-table>
        <thead>
            <tr>
                <th>Matière</th>
                <th>Professeur</th>
                <th>Moyenne Classe</th>
                <th>Meilleure Note</th>
                <th>Plus faible Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjects as $subject)
                <tr>
                    <td>{{ $subject['name'] }}</td>
                    <td>{{ $subject['teacher'] }}</td>
                    <td>{{ number_format($subject['class_mean'], 2) }}</td>
                    <td>{{ number_format($subject['highest'], 2) }}</td>
                    <td>{{ number_format($subject['lowest'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </x-table>
</div>
@endsection
