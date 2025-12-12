<!-- resources/views/backend/timetable/index.blade.php -->
@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h2 class="page-title">Gestion des Emplois du Temps</h2>
                    <p class="page-subtitle text-muted">Créez et gérez les emplois du temps des classes</p>
                </div>
                
                @include('backend.timetable.partials.filters')
                
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Liste des Emplois du Temps</h5>
                        <a href="{{ route('timetable.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus mr-2"></i> Nouvel Emploi du Temps
                        </a>
                    </div>
                    <div class="card-body">
                        @if($timetables->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Année</th>
                                            <th>Classe</th>
                                            <th>Trimestre</th>
                                            <th>Statut</th>
                                            <th>Créneaux</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($timetables as $timetable)
                                            <tr>
                                                <td>{{ $timetable->name }}</td>
                                                <td>{{ $timetable->academicYear->name }}</td>
                                                <td>{{ $timetable->class->name }}</td>
                                                <td class="text-capitalize">{{ $timetable->term }}</td>
                                                <td>
                                                    @if($timetable->is_active)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-secondary">Inactif</span>
                                                    @endif
                                                </td>
                                                <td>{{ $timetable->slots_count }} créneaux</td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('timetable.show', $timetable->id) }}" 
                                                           class="btn btn-sm btn-info" title="Voir">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('timetable.edit', $timetable->id) }}" 
                                                           class="btn btn-sm btn-warning" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('timetable.toggle-active', $timetable->id) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm {{ $timetable->is_active ? 'btn-secondary' : 'btn-success' }}" 
                                                                    title="{{ $timetable->is_active ? 'Désactiver' : 'Activer' }}">
                                                                <i class="fas {{ $timetable->is_active ? 'fa-times' : 'fa-check' }}"></i>
                                                            </button>
                                                        </form>
                                                        <form action="{{ route('timetable.destroy', $timetable->id) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet emploi du temps ?')"
                                                                    title="Supprimer">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $timetables->links() }}
                        @else
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                Aucun emploi du temps trouvé. 
                                <a href="{{ route('timetable.create') }}" class="alert-link">Créez-en un maintenant</a>.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection