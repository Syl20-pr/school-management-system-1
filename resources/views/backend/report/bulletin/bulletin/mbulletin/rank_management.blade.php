@extends('admin.admin_master')
@section('admin')

@section('title', 'Classement de la Classe')

@section('content')
<div class="container">
    <h1>🏆 Classement - {{ $className }} ({{ $termTypeName }})</h1>

    <x-table>
        <thead>
            <tr>
                <th>Rang</th>
                <th>Élève</th>
                <th>Moyenne</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentData as $student)
                <tr>
                    <td>{{ $student['term_rank'] }}</td>
                    <td>{{ $student['student_name'] }}</td>
                    <td>{{ number_format($student['term_mean'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </x-table>
</div>
@endsection
