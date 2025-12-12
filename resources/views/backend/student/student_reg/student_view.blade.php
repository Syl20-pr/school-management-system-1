@extends('admin.admin_master')
@section('admin')

<style>
    /* Table hover */
    #studentTable tbody tr {
        transition: all 0.3s ease;
    }
    #studentTable tbody tr:hover {
        background-color: #f1f3f9;
    }

    /* Badges statut */
    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
        border-radius: 6px;
    }

    /* Boutons d’action */
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .btn-sm {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
        border-radius: 6px;
    }

    /* --- Ajustements responsive --- */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    @media (max-width: 768px) {
        .action-buttons {
            flex-direction: column;
            align-items: stretch;
        }
        .action-buttons .btn-sm {
            width: 100%;
            justify-content: center;
        }

        form .form-group {
            margin-bottom: 1rem;
        }

        .box-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
        }

        .box-header .d-flex.gap-2 {
            width: 100%;
            flex-wrap: wrap;
        }

        .box-header .btn {
            flex: 1 1 48%;
        }
    }

    @media (max-width: 576px) {
        .badge {
            font-size: 0.7rem;
            padding: 0.4em 0.7em;
        }
        .btn-sm {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }
    }





</style>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box bb-3 border-warning">
                        <div class="box-header d-flex justify-content-between align-items-center">
                            <h4 class="box-title">
                                <i class="fa-solid fa-users-rectangle text-primary"></i>
                                Gestion des <strong>Inscriptions des Élèves</strong>
                            </h4>
                            <div class="d-flex gap-2">
                                <a href="{{ route('student.registration.add') }}" class="btn btn-success btn-rounded">
                                    <i class="fa-solid fa-user-plus"></i> Ajouter
                                </a>
                                <a href="{{ route('student.promotion.bulk') }}" class="btn btn-primary btn-rounded">
                                    <i class="fa-solid fa-users"></i> Promotion groupée
                                </a>
                                <a href="{{ route('student.promotion.statistics') }}" class="btn btn-info btn-rounded">
                                    <i class="fa-solid fa-chart-column"></i> Statistiques
                                </a>
                            </div>
                        </div>

                        <div class="box-body">
                            <!-- 🔍 Formulaire de recherche -->
                            <form method="GET" action="{{ route('student.registration.search') }}" id="searchForm">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Année scolaire <span class="text-danger">*</span></h5>
                                            <select name="year_id" id="year_id" required class="form-control">
                                                <option value="" {{ !$year_id ? 'selected disabled' : '' }}>Sélectionner</option>
                                                @foreach($years as $year)
                                                    <option value="{{ $year->id }}" {{ $year_id == $year->id ? 'selected' : '' }}>
                                                        {{ $year->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <h5>Classe <span class="text-danger">*</span></h5>
                                            <select name="class_id" id="class_id" required class="form-control">
                                                <option value="" {{ !$class_id ? 'selected disabled' : '' }}>Sélectionner</option>
                                                @foreach($classes as $class)
                                                    <option value="{{ $class->id }}" {{ $class_id == $class->id ? 'selected' : '' }}>
                                                        {{ $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary me-2">
                                            <i class="fa-solid fa-magnifying-glass"></i> Rechercher
                                        </button>
                                        <a href="{{ route('student.registration.view') }}" class="btn btn-secondary">
                                            <i class="fa-solid fa-rotate-right"></i> Réinitialiser
                                        </a>
                                    </div>
                                </div>
                            </form>

                            <!-- 🔎 Recherche par nom -->
                            @if(isset($allData) && $allData->count() > 0)
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <h5>Rechercher par nom</h5>
                                            <input type="text" id="searchByName" class="form-control"
                                                placeholder="Entrez le nom de l'élève...">
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- 📋 Tableau des élèves -->
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div id="DocumentResults">
                                        @if(isset($allData) && $allData->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped align-middle" id="studentTable">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th>Nom</th>
                                                            <th>Genre</th>
                                                            <th>Classe</th>
                                                            <th>Statut</th>
                                                            <th>Actions</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($allData as $value)
                                                            <tr>
                                                                <td>{{ $value->student->name }}</td>
                                                                <td>{{ $value->student->gender == 'Masculin' ? 'M' : 'F' }}</td>
                                                                <td>{{ $value->student_class->name }}</td>
                                                                <td>
                                                                    @if($value->student->statusclass == 'N')
                                                                        <span class="badge bg-success">Nouveau</span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark">Doublant</span>
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    <div class="action-buttons">
                                                                        <a href="{{ route('student.registration.edit', $value->student_id) }}" 
                                                                            class="btn btn-info btn-sm">
                                                                            <i class="fa-solid fa-pen"></i> Modifier
                                                                        </a>
                                                                        <a href="{{ route('student.promotion.view', $value->student_id) }}" 
                                                                            class="btn btn-warning btn-sm">
                                                                            <i class="fa-solid fa-graduation-cap"></i> Promotion
                                                                        </a>
                                                                        <a href="{{ route('student.registration.details', $value->student_id) }}" 
                                                                            class="btn btn-success btn-sm" target="_blank">
                                                                            <i class="fa-solid fa-eye"></i> Détails
                                                                        </a>
                                                                        <button type="button" 
                                                                                class="btn btn-danger btn-sm delete-btn"
                                                                                data-student-id="{{ $value->student_id }}"
                                                                                data-student-name="{{ $value->student->name }}">
                                                                            <i class="fa-solid fa-trash"></i> Supprimer
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @elseif(isset($allData) && $allData->count() == 0)
                                            <div class="alert alert-warning">⚠️ Aucun étudiant trouvé pour cette classe et cette année.</div>
                                        @else
                                            <div class="alert alert-info">ℹ️ Veuillez sélectionner une année et une classe pour voir les étudiants.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div> <!-- box-body -->
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Formulaire suppression -->
<form id="delete-form" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🔍 Recherche par nom
    const searchInput = document.getElementById('searchByName');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const value = this.value.toLowerCase();
            const rows = document.querySelectorAll('#studentTable tbody tr');
            rows.forEach(row => {
                const studentName = row.querySelector('td:first-child').textContent.toLowerCase();
                row.style.display = studentName.includes(value) ? '' : 'none';
            });
        });
    }

    // 🗑️ Suppression avec SweetAlert2
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const studentId = this.dataset.studentId;
            const studentName = this.dataset.studentName;
            const deleteUrl = "{{ route('student.registration.delete', ':id') }}".replace(':id', studentId);

            Swal.fire({
                title: 'Confirmation',
                html: `Voulez-vous vraiment supprimer <strong>${studentName}</strong> ?<br><span class="text-danger">Cette action est irréversible.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<i class="fa-solid fa-trash"></i> Oui, supprimer',
                cancelButtonText: '<i class="fa-solid fa-xmark"></i> Annuler',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.getElementById('delete-form');
                    form.action = deleteUrl;
                    form.submit();
                }
            });
        });
    });
});
</script>

@endsection
