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
                            <h4 class="box-title"><strong>Modifier les Notes</strong></h4>
                        </div>

                        <div class="box-body">
                            <form method="post" action="{{ route('marks.entry.update') }}">
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
                                            <h5>Type d'Examen <span class="text-danger">*</span></h5>
                                            <div class="controls">
                                                <select name="exam_type_id" id="exam_type_id" required class="form-control">
                                                    <option value="" selected disabled>Sélectionner le Type d'Examen</option>
                                                    @foreach($exam_types as $exam)
                                                    <option value="{{ $exam->id }}">{{ $exam->name }}</option>
                                                    @endforeach
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
                                                    <th>Date de Naissance</th>
                                                    <th>Genre</th>
                                                    <th>Notes</th>
                                                    @if ($isEPS) <!-- Only show "inapte" column for E.P.S. -->
                                                    <th>Inapte</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody id="marks-entry-tr"></tbody>
                                        </table>
                                        <input type="submit" class="btn btn-rounded btn-primary" value="Mettre à Jour">
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
    $(function() {
        // Fetch subjects based on selected class
        $(document).on('change', '#class_id', function() {
            var class_id = $(this).val();
            if (class_id) {
                $.ajax({
                    url: "{{ route('marks.getsubjects') }}",
                    type: "GET",
                    data: { class_id: class_id },
                    success: function(data) {
                        var html = '<option value="">Sélectionner la Matière</option>';
                        $.each(data, function(key, v) {
                            html += '<option value="' + v.subject_id + '">' + v.school_subject.name + '</option>';
                        });
                        $('#assign_subject_id').html(html);
                    }
                });
            }
        });

        // Fetch students and their marks based on filters
        $(document).on('click', '#search', function() {
            var year_id = $('#year_id').val();
            var class_id = $('#class_id').val();
            var assign_subject_id = $('#assign_subject_id').val();
            var exam_type_id = $('#exam_type_id').val();

            if (!year_id || !class_id || !assign_subject_id || !exam_type_id) {
                alert('Veuillez sélectionner toutes les options nécessaires avant de rechercher.');
                return;
            }

            $.ajax({
                url: "{{ route('student.edit.getstudents') }}",
                type: "GET",
                data: {
                    year_id: year_id,
                    class_id: class_id,
                    assign_subject_id: assign_subject_id,
                    exam_type_id: exam_type_id
                },
                success: function(data) {
                    $('#marks-entry').removeClass('d-none');

                    // Check if the selected subject is "E.P.S."
                    var selectedSubject = $('#assign_subject_id option:selected').text();
                    var isEPS = selectedSubject === 'E.P.S';

                    var html = '';
                    $.each(data, function(key, v) {
                        html += '<tr>';
                        html += '<td>' + v.student.id_no + '<input type="hidden" name="student_id[]" value="' + v.student_id + '"><input type="hidden" name="id_no[]" value="' + v.student.id_no + '"></td>';
                        html += '<td>' + v.student.name + '</td>';
                        html += '<td>' + v.student.dob + '</td>';
                        html += '<td>' + v.student.gender + '</td>';
                        html += '<td><input type="text" class="form-control form-control-sm" name="marks[]" value="' + v.marks + '"></td>';

                        if (isEPS) {
                            var inapte = v.inapte || 0; // Default to 0 (Apte) if "inapte" is not set
                            html += '<td>';
                            html += '<select name="inapte[]" class="form-control form-control-sm">';
                            html += '<option value="0" ' + (inapte == 0 ? 'selected' : '') + '>Apte</option>';
                            html += '<option value="1" ' + (inapte == 1 ? 'selected' : '') + '>Inapte</option>';
                            html += '</select>';
                            html += '</td>';
                        }

                        html += '</tr>';
                    });
                    $('#marks-entry-tr').html(html);
                }
            });
        });

        // Real-time validation for marks input
        $(document).on('input', 'input[name="marks[]"]', function () {
            var value = $(this).val();
            if (value < 0 || value > 20) {
                $(this).addClass('is-invalid');
                alert('Les Notes Doivent varier de 0 à 20');
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        // Auto-save marks and "inapte" status
        $(document).on('change', 'input[name="marks[]"], select[name="inapte[]"]', function() {
            var student_id = $(this).closest('tr').find('input[name="student_id[]"]').val();
            var marks = $(this).closest('tr').find('input[name="marks[]"]').val();
            var inapte = $(this).closest('tr').find('select[name="inapte[]"]').val();
            var year_id = $('#year_id').val();
            var class_id = $('#class_id').val();
            var assign_subject_id = $('#assign_subject_id').val();
            var exam_type_id = $('#exam_type_id').val();

            $.ajax({
                url: "{{ route('marks.autoupdate.save') }}",
                type: "POST",
                data: {
                    '_token': '{{ csrf_token() }}',
                    'student_id': student_id,
                    'marks': marks,
                    'inapte': inapte,
                    'year_id': year_id,
                    'class_id': class_id,
                    'assign_subject_id': assign_subject_id,
                    'exam_type_id': exam_type_id
                },
                success: function(response) {
                    console.log('Notes Sauvegardées Automatiquement');
                }
            });
        });
    });
</script>
@endsection
