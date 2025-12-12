@extends('admin.admin_master')
@section('admin')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

{{-- Font Awesome pour éviter les carrés emoji --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

{{-- Toastify --}}
<script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

<div class="content-wrapper">
  <div class="container-full">
    <section class="content">
      <div class="row">
        <div class="col-12">

          <div class="box bb-3 border-warning">
            <div class="box-header d-flex justify-content-between align-items-center">
              <h4 class="box-title mb-0"><strong>Gestion des Notes — Saisie & Modification</strong></h4>
              <small class="text-muted">
                <i class="fa-regular fa-circle-question me-1"></i>
                Utilisez la recherche pour charger les élèves puis saisissez ou modifiez les notes.
              </small>
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

              <form method="post" action="{{ route('marks.store') }}" id="marks-form">
                @csrf
                <input type="hidden" name="year_id" id="year_id" value="{{ $current_year->id }}">

                <div class="row g-3">
                  <div class="col-md-3">
                    <div class="form-group mb-2">
                      <label class="mb-1">Année Scolaire <span class="text-danger">*</span></label>
                      <input type="text" class="form-control" value="{{ $current_year->name }}" disabled>
                      <small class="text-info">Sélectionnée automatiquement</small>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group mb-2">
                      <label class="mb-1">Classe <span class="text-danger">*</span></label>
                      <select name="class_id" id="class_id" required class="form-control">
                        <option value="" selected disabled>Sélectionner la Classe</option>
                        @foreach($classes as $class)
                          <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>

                  <div class="col-md-2">
                    <div class="form-group mb-2">
                      <label class="mb-1">Matière <span class="text-danger">*</span></label>
                      <select name="assign_subject_id" id="assign_subject_id" required class="form-control" disabled>
                        <option selected disabled>Sélectionner la Matière</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-2">
                    <div class="form-group mb-2">
                      <label class="mb-1">Trimestre/Semestre <span class="text-danger">*</span></label>
                      <select name="term_type_id" id="term_type_id" required class="form-control" disabled>
                        <option selected disabled>Sélectionner</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-2 d-flex align-items-end">
                    <button id="search" type="button" class="btn btn-primary w-100">
                      <i class="fa-solid fa-magnifying-glass"></i> Rechercher
                    </button>
                  </div>
                </div>

                <div class="row d-none" id="marks-container">
                  <div class="col-md-12">
                    <div class="alert alert-info d-flex align-items-center">
                      <i class="fa-solid fa-circle-info me-2"></i>
                      <span id="period-info">Saisie des notes pour …</span>
                    </div>

                    <div class="table-responsive">
                      <table class="table table-bordered table-striped align-middle" id="marks-table">
                        <thead class="thead-light sticky-top" style="top: 0; z-index: 2;">
                          <tr>
                            <th width="5%">#</th>
                            <th width="30%">Élève</th>
                            <th width="15%"><i class="fa-regular fa-pen-to-square" data-toggle="tooltip" title="Interrogation"></i> Interro</th>
                            <th width="15%"><i class="fa-solid fa-book-open" data-toggle="tooltip" title="Devoir"></i> Devoir</th>
                            <th width="15%"><i class="fa-solid fa-file-signature" data-toggle="tooltip" title="Composition"></i> Compo</th>
                            <th width="10%" class="eps-field d-none"><i class="fa-solid fa-person-running" data-toggle="tooltip" title="Éducation Physique & Sportive"></i> Aptitude</th>
                            <th width="10%">Actions</th>
                          </tr>
                        </thead>
                        <tbody id="marks-table-body"></tbody>
                      </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                      <button type="button" id="save-all" class="btn btn-success">
                        <i class="fa-solid fa-floppy-disk"></i> Tout sauvegarder
                      </button>
                      <div class="d-flex align-items-center gap-3">
                        <div id="auto-save-status" class="text-muted" style="display:none;">
                          <i class="fa-solid fa-spinner fa-spin me-2"></i> Sauvegarde automatique…
                        </div>
                        <div id="auto-save-success" class="text-success" style="display:none;">
                          <i class="fa-regular fa-circle-check me-1"></i> Sauvegardé
                        </div>
                      </div>
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

{{-- Modal de confirmation Apte/Inapte --}}
<div class="modal fade" id="aptitudeModal" tabindex="-1" role="dialog" aria-labelledby="aptitudeModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title" id="aptitudeModalLabel"><i class="fa-solid fa-user-shield me-2"></i>Confirmation d'aptitude</h6>
        <button type="button" class="close" data-dismiss="modal" aria-label="Fermer"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body" id="aptitudeModalBody">
        <!-- message injecté en JS -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" data-dismiss="modal">Annuler</button>
        <button type="button" class="btn btn-primary" id="aptitudeConfirmBtn">
          <i class="fa-regular fa-circle-check me-1"></i> Confirmer
        </button>
      </div>
    </div>
  </div>
</div>

<style>
  .toastify-confirmation { border-radius: 8px; font-weight: 600; }
  .table-danger { background-color: #ffe6e9 !important; }
  .table-success-soft { background-color: #e6ffef !important; }
  .bg-light { background-color: #f8f9fa !important; }
  .text-muted { color: #6c757d !important; }
  .inapte-container { display:flex; align-items:center; gap:8px; }
  .inapte-label { font-size: 14px; display:flex; align-items:center; gap:6px; }
  .sticky-top th { background:#f8f9fa; }
  .marks-input.is-invalid { border-color:#dc3545; }
</style>

<script type="text/javascript">
$(function () {
  let currentClass = '';
  let currentTerm = '';
  let currentSubject = '';

  // ——— Utilitaires UI ———
  function toastOK(msg) {
    Toastify({ text: msg, duration: 2500, gravity:"top", position:"right", backgroundColor:"#198754", className:"toastify-confirmation" }).showToast();
  }
  function toastWarn(msg) {
    Toastify({ text: msg, duration: 3000, gravity:"top", position:"right", backgroundColor:"#dc3545", className:"toastify-confirmation" }).showToast();
  }

  // Tooltips (si Bootstrap en place)
  $('[data-toggle="tooltip"]').tooltip?.();

  function showAptitudeToast(studentName, isInapte) {
    const msg = isInapte
      ? `\u{f05e} ${studentName} marqué comme INAPTE (EPS).`     // fa-ban
      : `\u{f058} ${studentName} marqué comme APTE (EPS).`;      // fa-check-circle
    Toastify({
      text: msg.replace('\u{f05e}','').replace('\u{f058}',''),
      duration: 2500, gravity: "top", position: "right",
      backgroundColor: isInapte ? "#dc3545" : "#198754",
      className:"toastify-confirmation"
    }).showToast();
  }

  // ——— Gestion sélection filtres ———
  $(document).on('change', '#class_id', function () {
    var class_id = $(this).val();
    currentClass = $('#class_id option:selected').text();
    if (!class_id) {
      $('#assign_subject_id').html('<option selected disabled>Sélectionner la Matière</option>').prop('disabled', true);
      $('#term_type_id').html('<option selected disabled>Sélectionner</option>').prop('disabled', true);
      return;
    }

    $.get("{{ route('marks.getsubjects') }}", { class_id }, function(data){
      var html = '<option value="">Sélectionner la Matière</option>';
      $.each(data, function(_, v){ html += `<option value="${v.subject_id}">${v.school_subject.name}</option>`; });
      $('#assign_subject_id').html(html).prop('disabled', false);
    });

    $.get("{{ route('marks.gettermtypes') }}", { class_id }, function(data){
      var html = '<option value="" selected disabled>Sélectionner</option>';
      $.each(data, function(_, v){ html += `<option value="${v.id}">${v.name}</option>`; });
      $('#term_type_id').html(html).prop('disabled', false);
    }, 'json');
  });

  $(document).on('change', '#term_type_id', function(){ currentTerm = $('#term_type_id option:selected').text(); updatePeriodInfo(); });
  $(document).on('change', '#assign_subject_id', function(){ currentSubject = $('#assign_subject_id option:selected').text(); updatePeriodInfo(); });

  function updatePeriodInfo() {
    if (currentClass && currentTerm && currentSubject) {
      $('#period-info').text(`Saisie des notes pour ${currentClass} — ${currentSubject} — ${currentTerm}`);
    }
  }

  // ——— Rechercher ———
  $(document).on('click', '#search', function () {
    var year_id = $('#year_id').val();
    var class_id = $('#class_id').val();
    var assign_subject_id = $('#assign_subject_id').val();
    var term_type_id = $('#term_type_id').val();
    if (!year_id || !class_id || !assign_subject_id || !term_type_id) {
      toastWarn('Veuillez sélectionner toutes les options avant de rechercher.');
      return;
    }

    $.ajax({
      url: "{{ route('marks.getstudents') }}",
      type: "GET",
      data: { year_id, class_id, assign_subject_id, term_type_id },
      success: function (response) {
        $('#marks-container').removeClass('d-none');
        renderStudentsTable(response);
        setTimeout(initInapteStates, 100);
      },
      error: function(xhr) {
        console.error('Erreur chargement:', xhr.responseText);
        toastWarn('Erreur lors du chargement des données.');
      }
    });
  });

  // ——— Rendu du tableau ———
  function renderStudentsTable(response) {
    var html = '';
    var students = response.students;
    var isEPS = response.isEPS;

    if (isEPS) { $('.eps-field').removeClass('d-none'); } else { $('.eps-field').addClass('d-none'); }

    $.each(students, function (index, student) {
      html += '<tr>';
      html += `<td>${index + 1}</td>`;
      html += `<td>${student.student.name}<input type="hidden" name="student_id[]" value="${student.student_id}"></td>`;

      /*html += `<td><input type="text" class="form-control form-control-sm marks-input" name="Interro[]" value="${student.marksData['Interro'] ?? ''}" data-student-id="${student.student_id}" data-exam-type="Interro"></td>`;
      html += `<td><input type="text" class="form-control form-control-sm marks-input" name="Devoir[]"  value="${student.marksData['Devoir'] ?? ''}"  data-student-id="${student.student_id}" data-exam-type="Devoir"></td>`;
      html += `<td><input type="text" class="form-control form-control-sm marks-input" name="Compo[]"   value="${student.marksData['Compo'] ?? ''}"   data-student-id="${student.student_id}" data-exam-type="Compo"></td>`;
    */
        html += `<td><input type="text" class="form-control form-control-sm marks-input" 
    name="Interro[]" value="${student.marksData.Interro.val ?? ''}" 
    data-student-id="${student.student_id}" data-exam-type="Interro" 
    data-mark-id="${student.marksData.Interro.id ?? ''}"></td>`;

html += `<td><input type="text" class="form-control form-control-sm marks-input" 
    name="Devoir[]" value="${student.marksData.Devoir.val ?? ''}" 
    data-student-id="${student.student_id}" data-exam-type="Devoir" 
    data-mark-id="${student.marksData.Devoir.id ?? ''}"></td>`;

html += `<td><input type="text" class="form-control form-control-sm marks-input" 
    name="Compo[]" value="${student.marksData.Compo.val ?? ''}" 
    data-student-id="${student.student_id}" data-exam-type="Compo" 
    data-mark-id="${student.marksData.Compo.id ?? ''}"></td>`;


      if (isEPS) {
        let inapteValue = student.inapte || 0;
        html += '<td class="eps-field">';
        html += '  <div class="inapte-container">';
        /*html += `    <input type="checkbox" name="inapte[]" value="1" class="inapte-toggle" data-student-id="${student.student_id}" ${inapteValue == 1 ? 'checked' : ''}>`;
        */
       html += `    <input type="checkbox" name="inapte[]" value="1" class="inapte-toggle" 
        data-student-id="${student.student_id}" data-mark-id="${student.marksData.Compo.id ?? ''}" 
        ${inapteValue == 1 ? 'checked' : ''}>`;

        html += `    <span class="inapte-label">${inapteValue == 1
                ? '<i class="fa-solid fa-ban text-danger"></i> Inapte'
                : '<i class="fa-regular fa-circle-check text-success"></i> Apte'}</span>`;
        html += '  </div>';
        html += '</td>';
      } else {
        html += '<td class="eps-field d-none"><input type="hidden" name="inapte[]" value="0"></td>';
      }

      html += '<td>';
      html += `  <button type="button" class="btn btn-sm btn-outline-primary save-row" data-student-id="${student.student_id}" data-toggle="tooltip" title="Sauvegarder cette ligne">`;
      html += '    <i class="fa-solid fa-floppy-disk"></i>';
      html += '  </button>';
      html += '</td>';
      html += '</tr>';
    });

    $('#marks-table-body').html(html);
    $('[data-toggle="tooltip"]').tooltip?.();
  }

  // ——— Init état inapte sur rendu ———
  function toggleInapteState(checkbox) {
    const row = checkbox.closest('tr');
    const inputs = row.querySelectorAll('.marks-input');
    const isInapte = checkbox.checked;
    const inapteLabel = row.querySelector('.inapte-label');

    checkbox.setAttribute('data-was-inapte', isInapte);

    inapteLabel.innerHTML = isInapte
      ? '<i class="fa-solid fa-ban text-danger"></i> Inapte'
      : '<i class="fa-regular fa-circle-check text-success"></i> Apte';

    inputs.forEach(input => {
      input.disabled = isInapte;
      if (isInapte) {
        input.value = '';
        input.classList.add('bg-light','text-muted');
      } else {
        input.classList.remove('bg-light','text-muted');
      }
    });

    if (isInapte) {
      row.classList.add('table-danger');
    } else {
      row.classList.remove('table-danger');
    }
  }

  function initInapteStates() {
    document.querySelectorAll('.inapte-toggle').forEach(cb => {
      cb.setAttribute('data-was-inapte', cb.checked);
      toggleInapteState(cb);
    });
  }

  // ——— Modal Apte/Inapte (remplace confirm) ———
  let pendingCheckbox = null;
  $(document).on('change', '.inapte-toggle', function () {
    const was = $(this).attr('data-was-inapte') === 'true' || $(this).attr('data-was-inapte') === '1';
    const now = this.checked;
    const row = $(this).closest('tr');
    const studentName = row.find('td:nth-child(2)').text().trim();

    // si changement réel -> modal
    if (was !== now) {
      pendingCheckbox = this;
      const msg = now
        ? `<p><strong>Marquer ${studentName}</strong> comme <span class="text-danger">INAPTE</span> pour l’EPS ?<br/>Les notes seront effacées.</p>`
        : `<p><strong>Marquer ${studentName}</strong> comme <span class="text-success">APTE</span> pour l’EPS ?<br/>Vous pourrez saisir des notes.</p>`;
      $('#aptitudeModalBody').html(msg);
      $('#aptitudeModal').modal('show');
    } else {
      // pas de changement réel
      toggleInapteState(this);
    }
  });

  $('#aptitudeConfirmBtn').on('click', function(){
    if (!pendingCheckbox) return;
    const cb = pendingCheckbox;
    pendingCheckbox = null;
    $('#aptitudeModal').modal('hide');

    toggleInapteState(cb);
    autoSaveField($(cb)); // propage côté serveur (table globale + compat)
    const row = $(cb).closest('tr');
    const studentName = row.find('td:nth-child(2)').text().trim();
    showAptitudeToast(studentName, cb.checked);
  });

  $('#aptitudeModal').on('hidden.bs.modal', function(){
    // Si on annule, revenir à l’état précédent
    if (pendingCheckbox) {
      const was = $(pendingCheckbox).attr('data-was-inapte') === 'true' || $(pendingCheckbox).attr('data-was-inapte') === '1';
      pendingCheckbox.checked = was;
      pendingCheckbox = null;
    }
  });

  // ——— Auto-save champs ———
  $(document).on('input', '.marks-input', function () { autoSaveField($(this)); });

  /* function autoSaveField(field) {
    const student_id = field.data('student-id');
    const year_id = $('#year_id').val();
    const class_id = $('#class_id').val();
    const assign_subject_id = $('#assign_subject_id').val();
    const term_type_id = $('#term_type_id').val();
    if (!year_id || !class_id || !assign_subject_id || !term_type_id) return;

    const formData = {
      _token: "{{ csrf_token() }}",
      student_id: [student_id],
      year_id, class_id, assign_subject_id, term_type_id,
      is_auto_save: true
    };

    if (field.hasClass('marks-input')) {
      const exam_type = field.data('exam-type');
      formData[exam_type] = [field.val()];
      // cap visuel saisie
      field.closest('tr').addClass('table-success-soft');
      setTimeout(() => field.closest('tr').removeClass('table-success-soft'), 600);
    } else if (field.hasClass('inapte-toggle')) {
      formData['inapte'] = [field.is(':checked') ? 1 : 0];
    }

    $('#auto-save-status').show();
    $('#auto-save-success').hide();

    $.ajax({
      url: "{{ route('marks.auto.save') }}",
      type: "POST",
      data: formData,
      success: function (resp) {
        $('#auto-save-status').hide();
        $('#auto-save-success').show().delay(1200).fadeOut();
      },
      error: function (xhr) {
        console.error('Erreur auto-save:', xhr.responseText);
        $('#auto-save-status').hide();
        toastWarn('Erreur lors de la sauvegarde automatique.');
      }
    });
  } */

  function autoSaveField(field) {
  const markId = field.data('mark-id');
  const isInapteToggle = field.hasClass('inapte-toggle');
  const isMarkInput = field.hasClass('marks-input');

  // Si on a un mark_id → on utilise UpdateSingleMark
  if (markId) {
    const formData = {
      _token: "{{ csrf_token() }}",
      mark_id: markId
    };

    if (isMarkInput) formData.marks = field.val();
    if (isInapteToggle) formData.inapte = field.is(':checked') ? 1 : 0;

    $.ajax({
      url: "{{ route('marks.update.single') }}", // à définir dans tes routes
      type: "POST",
      data: formData,
      success: function (resp) {
        $('#auto-save-status').hide();
        $('#auto-save-success').show().delay(1200).fadeOut();
      },
      error: function (xhr) {
        console.error('Erreur auto-save (UpdateSingleMark):', xhr.responseText);
        toastWarn('Erreur lors de la sauvegarde automatique.');
      }
    });

  } else {
    // Sinon → nouvel enregistrement → MarksStore
    const student_id = field.data('student-id');
    const year_id = $('#year_id').val();
    const class_id = $('#class_id').val();
    const assign_subject_id = $('#assign_subject_id').val();
    const term_type_id = $('#term_type_id').val();

    const formData = {
      _token: "{{ csrf_token() }}",
      student_id: [student_id],
      year_id, class_id, assign_subject_id, term_type_id,
      is_auto_save: true
    };

    if (isMarkInput) formData[field.data('exam-type')] = [field.val()];
    if (isInapteToggle) formData['inapte'] = [field.is(':checked') ? 1 : 0];

    $.ajax({
      url: "{{ route('marks.auto.save') }}",
      type: "POST",
      data: formData,
      success: function (resp) {
        $('#auto-save-status').hide();
        $('#auto-save-success').show().delay(1200).fadeOut();
      },
      error: function (xhr) {
        console.error('Erreur auto-save (MarksStore):', xhr.responseText);
        toastWarn('Erreur lors de la sauvegarde automatique.');
      }
    });
  }
}


  // Bloquer saisie si inapte
  $(document).on('keydown', '.marks-input', function(e) {
    const row = $(this).closest('tr');
    const isInapte = row.find('.inapte-toggle').is(':checked');
    if (isInapte) {
      e.preventDefault();
      toastWarn('Élève inapte EPS — saisie désactivée.');
      return false;
    }
  });

  // Sauvegarde ligne
  $(document).on('click', '.save-row', function(){
    const row = $(this).closest('tr');
    const student_id = $(this).data('student-id');
    const year_id = $('#year_id').val();
    const class_id = $('#class_id').val();
    const assign_subject_id = $('#assign_subject_id').val();
    const term_type_id = $('#term_type_id').val();
    if (!year_id || !class_id || !assign_subject_id || !term_type_id) {
      toastWarn('Veuillez sélectionner tous les filtres.');
      return;
    }

    const formData = {
      _token: "{{ csrf_token() }}",
      student_id: [student_id],
      year_id, class_id, assign_subject_id, term_type_id
    };

    row.find('.marks-input').each(function(){
      const exam_type = $(this).data('exam-type');
      formData[exam_type] = [$(this).val()];
    });

    if (row.find('.inapte-toggle').length) {
      formData['inapte'] = [row.find('.inapte-toggle').is(':checked') ? 1 : 0];
    }

    const btn = $(this);
    const oldHtml = btn.html();
    btn.html('<i class="fa-solid fa-spinner fa-spin"></i>');

    $.ajax({
      url: "{{ route('marks.store') }}",
      type: "POST",
      data: formData,
      success: function (resp) {
        row.addClass('table-success');
        setTimeout(() => row.removeClass('table-success'), 800);
        toastOK('Ligne sauvegardée.');
        btn.html(oldHtml);
      },
      error: function(xhr){
        console.error('Erreur sauvegarde ligne:', xhr.responseText);
        toastWarn('Erreur lors de la sauvegarde.');
        btn.html(oldHtml);
      }
    });
  });

  // Sauvegarde globale
  $(document).on('click', '#save-all', function(){
    const year_id = $('#year_id').val();
    const class_id = $('#class_id').val();
    const assign_subject_id = $('#assign_subject_id').val();
    const term_type_id = $('#term_type_id').val();
    if (!year_id || !class_id || !assign_subject_id || !term_type_id) {
      toastWarn('Veuillez sélectionner tous les filtres.');
      return;
    }

    const btn = $(this);
    const old = btn.html();
    btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Sauvegarde…');

    $.ajax({
      url: "{{ route('marks.store') }}",
      type: "POST",
      data: $('#marks-form').serialize(),
      success: function(resp){
        $('#marks-table-body tr').addClass('table-success');
        setTimeout(()=>$('#marks-table-body tr').removeClass('table-success'), 1000);
        toastOK('Toutes les notes ont été sauvegardées.');
        btn.html(old);
      },
      error: function(xhr){
        console.error('Erreur sauvegarde:', xhr.responseText);
        toastWarn('Erreur lors de la sauvegarde.');
        btn.html(old);
      }
    });
  });

  // Validation simple [0..20]
  $(document).on('input', '.marks-input', function () {
    const max = 20, min = 0;
    const v = $(this).val().trim();
    if (v !== '' && (!$.isNumeric(v) || v < min || v > max)) {
      $(this).addClass('is-invalid');
      $(this).val('');
      if (!$(this).siblings('.error-msg').length) {
        $(this).after(`<small class="text-danger error-msg">Les notes doivent être entre ${min} et ${max}</small>`);
      }
    } else {
      $(this).removeClass('is-invalid');
      $(this).siblings('.error-msg').remove();
    }
  });
  $(document).on('focus', '.marks-input', function(){ $(this).removeClass('is-invalid'); $(this).siblings('.error-msg').remove(); });

});
</script>
@endsection
