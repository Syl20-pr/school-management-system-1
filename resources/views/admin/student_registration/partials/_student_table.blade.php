@if($allData->count() > 0)
<table class="table table-bordered table-striped" id="studentTable">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>ID No</th>
            <th>Genre</th>
            <th>Classe</th>
            <th>Année</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allData as $key => $value)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $value->student->name }}</td>
            <td>{{ $value->student->id_no }}</td>
            <td>{{ $value->student->gender }}</td>
            <td>{{ $value->student_class->name }}</td>
            <td>{{ $value->student_year->name }}</td>
            <td>
                @if($value->student->statusclass == 'N')
                    <span class="badge badge-success">Nouveau</span>
                @else
                    <span class="badge badge-warning">Doublant</span>
                @endif
            </td>
            <td>
                <a href="{{ route('student.registration.edit', $value->student_id) }}" 
                   class="btn btn-info btn-sm">Modifier</a>
                <a href="{{ route('student.registration.promotion', $value->student_id) }}" 
                   class="btn btn-warning btn-sm">Promotion</a>
                <a href="{{ route('student.registration.details', $value->student_id) }}" 
                   class="btn btn-success btn-sm" target="_blank">Détails</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
<div class="alert alert-warning">
    Aucun étudiant trouvé pour cette classe et cette année.
</div>
@endif