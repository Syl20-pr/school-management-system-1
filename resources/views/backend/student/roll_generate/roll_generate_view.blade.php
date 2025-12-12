@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box bb-3 border-primary">
                        <div class="box-header with-border">
                            <h4 class="box-title"><strong>📚 Génération des Registres de Classe</strong></h4>
                        </div>

                        <div class="box-body">
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <h5>Année Scolaire <span class="text-danger">*</span></h5>
                                        <select name="year_id" id="year_id" required class="form-control select2">
                                            <option value="">Sélectionner l'Année</option>
                                            @foreach($years as $year)
                                                <option value="{{ $year->id }}" 
                                                    {{ $selected_year == $year->id ? 'selected' : '' }}>
                                                    {{ $year->name }}
                                                    @if($year->is_current) (En cours) @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6" style="padding-top: 25px;">
                                    <button id="searchBtn" class="btn btn-primary">
                                        <i class="fa fa-search"></i> Afficher les Classes
                                    </button>
                                </div>
                            </div>

                            <div id="classes-container" class="d-none">
                                <div class="alert alert-info">
                                    <i class="fa fa-info-circle"></i>
                                    Sélectionnez le type de registre à générer pour chaque classe
                                </div>

                                <div class="text-center mb-3">
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-outline-primary" id="btn-all-attendance">
                                            <i class="fa fa-clipboard-check"></i> Tous les registres de présence
                                        </button>
                                        <button type="button" class="btn btn-outline-success" id="btn-all-grades">
                                            <i class="fa fa-chart-line"></i> Tous les carnets de notes
                                        </button>
                                    </div>
                                </div>

                                <div class="row" id="classes-grid">
                                    <!-- Les classes seront chargées ici -->
                                </div>
                            </div>

                            <div id="no-classes" class="alert alert-warning d-none">
                                <i class="fa fa-exclamation-triangle"></i>
                                Aucune classe trouvée pour cette année scolaire.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Modals pour les téléchargements individuels -->
<div class="modal fade" id="downloadModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Type de registre</h5>
            </div>
            <div class="modal-body text-center">
                <button class="btn btn-primary btn-block mb-2" onclick="downloadPdf('attendance')">
                    <i class="fa fa-clipboard-check"></i> Registre de Présence
                </button>
                <button class="btn btn-success btn-block" onclick="downloadPdf('grades')">
                    <i class="fa fa-chart-line"></i> Carnet de Notes
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.class-card {
    transition: all 0.3s ease;
    border: 1px solid #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
}

.class-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: #3c8dbc;
}

.class-card .card-body {
    padding: 20px;
}

.class-icon {
    font-size: 3rem;
    margin-bottom: 15px;
    color: #3c8dbc;
}

.students-count {
    background: #f8f9fa;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.9em;
}

.download-buttons {
    margin-top: 15px;
}
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // Initialiser Select2
    $('#year_id').select2({
        theme: 'bootstrap',
        width: '100%'
    });

    // Charger les classes au chargement si une année est sélectionnée
    @if($selected_year)
        loadClasses({{ $selected_year }});
    @endif

    $('#searchBtn').click(function() {
        var yearId = $('#year_id').val();
        if (yearId) {
            loadClasses(yearId);
        }
    });

    $('#btn-all-attendance').click(function() {
        downloadAll('attendance');
    });

    $('#btn-all-grades').click(function() {
        downloadAll('grades');
    });
});

/* function loadClasses(yearId) {
    $.ajax({
        url: "{{ route('roll.get.classes') }}",
        type: "GET",
        data: { year_id: yearId },
        beforeSend: function() {
            $('#classes-container').addClass('d-none');
            $('#no-classes').addClass('d-none');
            $('#classes-grid').html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div><p>Chargement des classes...</p></div>');
        },
        success: function(data) {
            $('#classes-grid').empty();
            
            if (data.length > 0) {
                $.each(data, function(index, classItem) {
                    var card = `
                    <div class="col-md-4 mb-4">
                        <div class="class-card card">
                            <div class="card-body text-center">
                                <div class="class-icon">
                                    <i class="fa fa-chalkboard-teacher"></i>
                                </div>
                                <h4 class="card-title">${classItem.name}</h4>
                                <span class="badge badge-primary students-count">
                                    <i class="fa fa-users"></i> ${classItem.students_count} élève(s)
                                </span>
                                
                                <div class="download-buttons">
                                    <button class="btn btn-outline-primary btn-sm mr-2" 
                                            onclick="showDownloadModal(${classItem.id}, '${classItem.name}')">
                                        <i class="fa fa-download"></i> Télécharger
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    $('#classes-grid').append(card);
                });
                
                $('#classes-container').removeClass('d-none');
                $('#no-classes').addClass('d-none');
            } else {
                $('#classes-container').addClass('d-none');
                $('#no-classes').removeClass('d-none');
            }
        }
    });
} */
function loadClasses(yearId) {
    $.ajax({
        url: "{{ route('roll.get.classes') }}",
        type: "GET",
        data: { year_id: yearId },
        beforeSend: function() {
            $('#classes-container').addClass('d-none');
            $('#no-classes').addClass('d-none');
            $('#classes-grid').html('<div class="col-12 text-center"><div class="spinner-border text-primary"></div><p>Chargement des classes...</p></div>');
        },
        success: function(data) {
            $('#classes-grid').empty();
            
            if (data.length > 0) {
                $.each(data, function(index, classItem) {
                    var card = `
                    <div class="col-md-4 mb-4">
                        <div class="class-card card">
                            <div class="card-body text-center">
                                <div class="class-icon">
                                    <i class="fa fa-chalkboard-teacher"></i>
                                </div>
                                <h4 class="card-title">${classItem.name}</h4>
                                <span class="badge badge-primary students-count">
                                    <i class="fa fa-users"></i> ${classItem.students_count} élève(s)
                                </span>
                                
                                <div class="download-buttons">
                                    <button class="btn btn-outline-primary btn-sm mr-2" 
                                            onclick="showDownloadModal(${classItem.id}, '${classItem.name}')">
                                        <i class="fa fa-download"></i> Télécharger
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>`;
                    $('#classes-grid').append(card);
                });
                
                $('#classes-container').removeClass('d-none');
                $('#no-classes').addClass('d-none');
            } else {
                $('#classes-container').addClass('d-none');
                $('#no-classes').removeClass('d-none');
            }
        },
        error: function(xhr) {
            console.error('Erreur lors du chargement des classes:', xhr);
            $('#classes-container').addClass('d-none');
            $('#no-classes').removeClass('d-none');
            $('#no-classes').html('<i class="fa fa-exclamation-triangle"></i> Erreur lors du chargement des classes.');
        }
    });
}

function showDownloadModal(classId, className) {
    $('#downloadModal').data('classId', classId);
    $('#downloadModal').data('className', className);
    $('#downloadModal').modal('show');
}

function downloadPdf(type) {
    var yearId = $('#year_id').val();
    var classId = $('#downloadModal').data('classId');
    
    $('#downloadModal').modal('hide');
    
    // Afficher une notification de téléchargement
    Swal.fire({
        title: 'Génération en cours',
        text: 'Préparation du fichier...',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    
    // Rediriger vers le téléchargement
    window.location.href = `/students/roll/generate/pdf?year_id=${yearId}&class_id=${classId}&type=${type}`;
}

function downloadAll(type) {
    var yearId = $('#year_id').val();
    
    if (!yearId) {
        Swal.fire('Erreur', 'Veuillez sélectionner une année scolaire', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Génération en cours',
        text: 'Préparation de tous les fichiers...',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false
    });
    
    window.location.href = `/students/roll/generate/all?year_id=${yearId}&type=${type}`;
} 
/* function downloadPdf(type) {
    var yearId = $('#year_id').val();
    var classId = $('#downloadModal').data('classId');
    
    $('#downloadModal').modal('hide');
    
    // Fermer SweetAlert s'il est ouvert
    if (Swal.isVisible()) {
        Swal.close();
    }
    
    // Rediriger vers le téléchargement sans afficher de message
    window.location.href = `/students/roll/generate/pdf?year_id=${yearId}&class_id=${classId}&type=${type}`;
}

function downloadAll(type) {
    var yearId = $('#year_id').val();
    
    if (!yearId) {
        Swal.fire('Erreur', 'Veuillez sélectionner une année scolaire', 'error');
        return;
    }
    
    // Fermer SweetAlert s'il est ouvert
    if (Swal.isVisible()) {
        Swal.close();
    }
    
    // Rediriger directement sans afficher de message
    window.location.href = `/students/roll/generate/all?year_id=${yearId}&type=${type}`;
} */
</script>

@endsection