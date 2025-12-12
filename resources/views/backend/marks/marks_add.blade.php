@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box bb-3 border-warning">
                        <div class="box-header">
                            <h4 class="box-title"><strong>Saisir/Modifier des Notes</strong></h4>
                        </div>

                        <div class="box-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button> 
                                </div>
                            @endif
                            <form method="post" action="{{ route('marks.entry.store') }}" id="marks-form">
                                @csrf
                                <div class="row"> 
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Année Scolaire <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="year_id" id="year_id" required class="form-control">
                                                    <option value="" selected disabled>Sélectionner l'Année</option>
                                                    @foreach($years as $year)
                                                    <option value="{{ $year->id }}">{{ $year->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Classe <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="class_id" id="class_id" required class="form-control">
                                                    <option value="" selected disabled>Sélectionner la Classe</option>
                                                    @foreach($classes as $class)
                                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Matière <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="assign_subject_id" id="assign_subject_id" required class="form-control">
                                                    <option selected disabled>Sélectionner la Matière</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <h5>Trimestre/Semestre <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="term_type_id" id="term_type_id" required class="form-control">
                                                    <option selected disabled>Sélectionner Trim ou Sem</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <a id="search" class="btn btn-primary" name="search">Rechercher</a>
                                    </div>
                                </div>

                                <div class="row d-none" id="marks-entry">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>No ID</th>
                                                    <th>Nom</th>
                                                    <th>Interro</th>
                                                    <th>Devoir</th>
                                                    <th>Compo</th>
                                                    <th class="eps-field d-none">Inapte</th>
                                                </tr>
                                            </thead>
                                            <tbody id="marks-entry-tr"></tbody>
                                        </table>
                                        <input type="submit" class="btn btn-rounded btn-primary" value="Sauvegarder" id="saveBtn">
                                        <span id="loading" style="display: none;">Enregistrement en cours...</span>
                                        <div id="auto-save-status" class="mt-2" style="display: none;">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                            <span class="ml-2">Sauvegarde automatique en cours...</span>
                                        </div>
                                        <div id="auto-save-success" class="mt-2 text-success" style="display: none;">
                                            <i class="fas fa-check-circle"></i>
                                            <span class="ml-2">Sauvegardé automatiquement</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        // Fetch subjects and term types based on selected class
        $(document).on('change', '#class_id', function () {
            var class_id = $(this).val();
            if (class_id) {
                $.ajax({
                    url: "{{ route('marks.getsubjects') }}",
                    type: "GET",
                    data: { class_id: class_id },
                    success: function (data) {
                        var html = '<option value="">Sélectionner la Matière</option>';
                        $.each(data, function (key, v) {
                            html += '<option value="' + v.subject_id + '">' + v.school_subject.name + '</option>';
                        });
                        $('#assign_subject_id').html(html);
                    }
                });

                $.ajax({
                    url: "{{ route('marks.gettermtypes') }}",
                    type: "GET",
                    data: { class_id: class_id },
                    dataType: "json",
                    success: function (data) {
                        var html = '<option value="" selected disabled>Sélectionner le Trim/Sem</option>';
                        $.each(data, function (key, v) {
                            html += '<option value="' + v.id + '">' + v.name + '</option>';
                        });
                        $('#term_type_id').html(html);
                    }
                });
            } else {
                $('#assign_subject_id').html('<option selected disabled>Sélectionner la Matière</option>');
                $('#term_type_id').html('<option selected disabled>Sélectionner le Trimestre/Semestre</option>');
            }
        });

        // Show students based on selected filters
        $(document).on('click', '#search', function () {
            var year_id = $('#year_id').val();
            var class_id = $('#class_id').val();
            var assign_subject_id = $('#assign_subject_id').val();
            var term_type_id = $('#term_type_id').val();

            if (!year_id || !class_id || !assign_subject_id || !term_type_id) {
                alert('Veuillez sélectionner toutes les options nécessaires avant de rechercher.');
                return;
            }

            $.ajax({
                url: "{{ route('student.marks.getstudent') }}",
                type: "GET",
                data: {
                    'year_id': year_id,
                    'class_id': class_id,
                    'assign_subject_id': assign_subject_id,
                    'term_type_id': term_type_id
                },
                success: function (response) {
                    $('#marks-entry').removeClass('d-none');

                    var students = response.students;
                    var examTypes = response.examTypes;
                    var isEPS = response.isEPS;

                    if (isEPS) {
                        $('.eps-field').removeClass('d-none');
                    } else {
                        $('.eps-field').addClass('d-none');
                    }

                    var html = '';
                    $.each(students, function (key, student) {
                        html += '<tr>';
                        html += '<td>' + student.student.id_no + '<input type="hidden" name="student_id[]" value="' + student.student_id + '"><input type="hidden" name="id_no[]" value="' + student.student.id_no + '"></td>';
                        html += '<td>' + student.student.name + '</td>';

                        $.each(examTypes, function (index, examType) {
                            html += '<td><input type="text" class="form-control form-control-sm marks-input" name="' + examType.name + '[]" value="' + (student.marksData[examType.name] || '') + '" data-student-id="' + student.student_id + '" data-exam-type="' + examType.name + '"></td>';
                        });

                        if (isEPS) {
                            let inapteValue = student.inapte || 0;
                            html += '<td class="eps-field">';
                            html += '<select name="inapte[]" class="form-control form-control-sm inapte-select" data-student-id="' + student.student_id + '">';
                            html += '<option value="0" ' + (inapteValue == 0 ? 'selected' : '') + '>Apte</option>';
                            html += '<option value="1" ' + (inapteValue == 1 ? 'selected' : '') + '>Inapte</option>';
                            html += '</select>';
                            html += '</td>';
                        } else {
                            html += '<td class="eps-field d-none"><input type="hidden" name="inapte[]" value="0"></td>';
                        }

                        html += '</tr>';
                    });

                    $('#marks-entry-tr').html(html);
                }
            });
        });

        // Automatically save marks when the user types
        $(document).on('input', '.marks-input', function () {
            var student_id = $(this).data('student-id');
            var exam_type = $(this).data('exam-type');
            var marks = $(this).val();
            var year_id = $('#year_id').val();
            var class_id = $('#class_id').val();
            var assign_subject_id = $('#assign_subject_id').val();
            var term_type_id = $('#term_type_id').val();

            if (!year_id || !class_id || !assign_subject_id || !term_type_id) {
                return;
            }

            // Show auto-save status
            $('#auto-save-status').show();
            $('#auto-save-success').hide();

            $.ajax({
                url: "{{ route('marks.entry.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    student_id: [student_id],
                    [exam_type]: [marks],
                    year_id: year_id,
                    class_id: class_id,
                    assign_subject_id: assign_subject_id,
                    term_type_id: term_type_id,
                    is_auto_save: true // Flag for auto-saving
                },
                success: function (response) {
                    if (response.success) {
                        $('#auto-save-status').hide();
                        $('#auto-save-success').show().delay(2000).fadeOut();
                    }
                },
                error: function (xhr) {
                    console.error('Error during auto-saving:', xhr.responseText);
                }
            });
        });

        // Automatically save "inapte" value when changed
        $(document).on('change', '.inapte-select', function () {
            var student_id = $(this).data('student-id');
            var inapte = $(this).val();
            var year_id = $('#year_id').val();
            var class_id = $('#class_id').val();
            var assign_subject_id = $('#assign_subject_id').val();
            var term_type_id = $('#term_type_id').val();

            if (!year_id || !class_id || !assign_subject_id || !term_type_id) {
                return;
            }

            // Show auto-save status
            $('#auto-save-status').show();
            $('#auto-save-success').hide();

            $.ajax({
                url: "{{ route('marks.entry.store') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    student_id: student_id,
                    inapte: inapte,
                    year_id: year_id,
                    class_id: class_id,
                    assign_subject_id: assign_subject_id,
                    term_type_id: term_type_id,
                    is_auto_save: true // Flag for auto-saving
                },
                success: function (response) {
                    if (response.success) {
                        $('#auto-save-status').hide();
                        $('#auto-save-success').show().delay(2000).fadeOut();
                    }
                }
            });
        });

        // Disable save button and show loading on form submit
        $(document).ready(function() {
            $("form").on("submit", function() {
                $("#saveBtn").prop("disabled", true);
                $("#loading").show();
            });
        });

        $(document).on('input', 'input[name$="[]"]', function () {
            var maxMarks = 20; // Maximum allowed marks
            var minMarks = 0;  // Minimum allowed marks
            var value = $(this).val();

            // Ensure only numeric values and within the range
            if (value !== '' && (!$.isNumeric(value) || value < minMarks || value > maxMarks)) {
                $(this).addClass('is-invalid'); // Highlight the field
                $(this).val(''); // Clear invalid input
                $(this).after('<small class="text-danger error-msg">Les notes doivent être entre ' + minMarks + ' et ' + maxMarks + '</small>');
            } else {
                $(this).removeClass('is-invalid'); // Remove highlight if valid
                $(this).siblings('.error-msg').remove(); // Remove error message
            }
        });

        // Remove error message on focus
        $(document).on('focus', 'input[name$="[]"]', function () {
            $(this).removeClass('is-invalid');
            $(this).siblings('.error-msg').remove();
        });

    });
</script>
@endsection