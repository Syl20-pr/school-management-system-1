{{-- resources/views/admin/student_registration/partials/_form.blade.php --}}
@csrf
@if(isset($editData))
    <input type="hidden" name="id" value="{{ $editData->id }}">
@endif

<div class="row">
    <!-- Nom -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Nom de l'Élève <span class="text-danger">*</span></h5>
            <input type="text" name="name" class="form-control"
                   value="{{ old('name', $editData['student']['name'] ?? '') }}" required>
        </div>
        <!-- Recherche Intelligente de Nom afin d'eviter les doublons -->
        <div id="duplicateWarning" class="alert alert-warning mt-2" style="display: none;">
            <strong>⚠ Élève déjà existant :</strong>
            <div id="duplicateContent"></div>
        </div>

    </div>

    <!-- Père -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Nom du Père </h5>
            <input type="text" name="fname" class="form-control"
                   value="{{ old('fname', $editData['student']['fname'] ?? '') }}">
        </div>
    </div>

    <!-- Mère -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Nom de la Mère </h5>
            <input type="text" name="mname" class="form-control"
                   value="{{ old('mname', $editData['student']['mname'] ?? '') }}">
        </div>
    </div>
</div>

<div class="row">
    <!-- Contact -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Contact</h5>
            <input type="text" name="mobile" class="form-control"
                   value="{{ old('mobile', $editData['student']['mobile'] ?? '') }}">
        </div>
    </div>

    <!-- Adresse -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Adresse</h5>
            <input type="text" name="address" class="form-control"
                   value="{{ old('address', $editData['student']['address'] ?? '') }}">
        </div>
    </div>

    <!-- Genre -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Genre <span class="text-danger">*</span></h5>
            <select name="gender" class="form-control select2" required>
                <option value="" disabled selected>Sélectionner</option>
                <option value="Masculin" {{ old('gender', $editData['student']['gender'] ?? '') == 'Masculin' ? 'selected' : '' }}>Masculin</option>
                <option value="Féminin" {{ old('gender', $editData['student']['gender'] ?? '') == 'Féminin' ? 'selected' : '' }}>Féminin</option>
            </select>
        </div>
    </div>
</div>

<div class="row">
    <!-- Religion -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Religion</h5>
            <select name="religion" class="form-control select2">
                <option value="" disabled selected>Sélectionner</option>
                <option value="Islam" {{ old('religion', $editData['student']['religion'] ?? '') == 'Islam' ? 'selected' : '' }}>Islam</option>
                <option value="Chrétien" {{ old('religion', $editData['student']['religion'] ?? '') == 'Chrétien' ? 'selected' : '' }}>Chrétien</option>
                <option value="Hindu" {{ old('religion', $editData['student']['religion'] ?? '') == 'Hindu' ? 'selected' : '' }}>Hindou</option>
            </select>
        </div>
    </div>

    <!-- Date de naissance -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Date de Naissance</h5>
            <input type="date" name="dob" class="form-control"
                   value="{{ old('dob', $editData['student']['dob'] ?? '') }}">
        </div>
    </div>

    <!-- Lieu de naissance -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Lieu de Naissance</h5>
            <input type="text" name="lob" class="form-control"
                   value="{{ old('lob', $editData['student']['lob'] ?? '') }}">
        </div>
    </div>
</div>

<div class="row">
    <!-- Année scolaire -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Année Scolaire <span class="text-danger">*</span></h5>
            <select name="year_id" class="form-control select2" required>
                <option value="" disabled selected>Sélectionner</option>
                @foreach($years as $year)
                    <option value="{{ $year->id }}" {{ old('year_id', $editData->year_id ?? '') == $year->id ? 'selected' : '' }}>{{ $year->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Classe -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Classe <span class="text-danger">*</span></h5>
            <select name="class_id" class="form-control select2" required>
                <option value="" disabled selected>Sélectionner</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ old('class_id', $editData->class_id ?? '') == $class->id ? 'selected' : '' }}>{{ $class->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Groupe -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Groupe <span class="text-danger">*</span></h5>
            <select name="group_id" class="form-control select2" required>
                <option value="" disabled selected>Sélectionner</option>
                @foreach($groups as $group)
                    <option value="{{ $group->id }}" {{ old('group_id', $editData->group_id ?? '') == $group->id ? 'selected' : '' }}>{{ $group->name }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>

<div class="row">
    <!-- Passage -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Passage</h5>
            <select name="shift_id" class="form-control select2">
                <option value="" disabled selected>Sélectionner</option>
                @foreach($shifts as $shift)
                    <option value="{{ $shift->id }}" {{ old('shift_id', $editData->shift_id ?? '') == $shift->id ? 'selected' : '' }}>{{ $shift->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Réduction -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Réduction (%)</h5>
            <input type="text" name="discount" class="form-control"
                   value="{{ old('discount', $editData['discount']['discount'] ?? '') }}">
        </div>
    </div>

    <!-- Contact du tuteur -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Contact du Tuteur</h5>
            <input type="text" name="f_no" class="form-control"
                   value="{{ old('f_no', $editData['student']['f_no'] ?? '') }}">
        </div>
    </div>
</div>

<div class="row">
    <!-- Statut (Nouveau / Doublant) -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Statut <span class="text-danger">*</span></h5>
            <select name="statusclass" class="form-control select2" required>
                <option value="" disabled selected>Sélectionner</option>
                <option value="N" {{ old('statusclass', $editData['student']['statusclass'] ?? '') == 'N' ? 'selected' : '' }}>Nouveau(lle)</option>
                <option value="D" {{ old('statusclass', $editData['student']['statusclass'] ?? '') == 'D' ? 'selected' : '' }}>Doublant(e)</option>
            </select>
        </div>
    </div>

    <!-- Image -->
    {{-- <div class="col-md-4">
        <div class="form-group">
            <h5>Image</h5>
            <input type="file" name="image" class="filepond" id="image"
                data-max-file-size="2MB"
                data-max-files="1"
                accept="image/png, image/jpeg, image/jpg">

        </div>
    </div> --}}

    <!-- Aperçu -->
    {{-- <div class="col-md-4">
        <div class="form-group">
            <img id="showImage"
                 src="{{ !empty($editData['student']['image'] ?? null) ? url('upload/student_images/'.$editData['student']['image']) : url('upload/no_image.jpg') }}"
                 style="width: 100px; height: 100px; border: 1px solid #000;">
        </div>
    </div> --}}
    <!-- Image -->
    <div class="col-md-4">
        <div class="form-group">
            <h5>Image</h5>
            <input type="file" name="image" class="form-control" id="image"
                accept="image/png, image/jpeg, image/jpg">
            <small class="text-muted">Max: 2MB (JPEG, PNG, JPG)</small>
        </div>
    </div>

    <!-- Aperçu -->
    <div class="col-md-4">
        <div class="form-group">
            <img id="showImage"
                src="{{ !empty($editData['student']['image'] ?? null) ? url('upload/student_images/'.$editData['student']['image']) : url('upload/no_image.jpg') }}"
                style="width: 100px; height: 100px; border: 1px solid #000; object-fit: cover;">
        </div>
    </div>

    <script>
    // Script pour prévisualiser l'image
    document.getElementById('image').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('showImage').src = e.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
    </script>

</div>

<div class="text-right mt-3">
    <button type="submit" class="btn btn-rounded btn-info">
        {{ $buttonText ?? 'Enregistrer' }}
    </button>
</div>

<script>
    // Script pour prévisualiser l'existance de doublons
document.addEventListener('DOMContentLoaded', function () {
    let nameInput = document.querySelector('input[name="name"]');
    let duplicateBox = document.getElementById('duplicateWarning');
    let duplicateContent = document.getElementById('duplicateContent');
    let timeout = null;

    nameInput.addEventListener('keyup', function () {
        clearTimeout(timeout);

        let query = this.value.trim();
        if (query.length < 2) {
            duplicateBox.style.display = "none";
            return;
        }

        timeout = setTimeout(() => {
            fetch("{{ route('students.searchByName') }}?q=" + query)
                .then(res => res.json())
                .then(data => {
                    if (data.length === 0) {
                        duplicateBox.style.display = "none";
                        return;
                    }

                    duplicateContent.innerHTML = data.map(student => `
                        <div class="mt-2 p-2 border rounded d-flex align-items-center gap-3">
                            <img src="${student.image}" width="60" height="60" style="border-radius:8px;object-fit:cover;">
                            <div>
                                <strong>${student.name}</strong><br>
                                Classe : ${student.class ?? 'N/A'} <br>
                                Statut : ${student.status} <br>
                                Année : ${student.year ?? 'N/A'} <br>
                                Naissance : ${student.dob ?? 'N/A'}<br>
                                <a href="/student/registration/details/${student.id}" class="btn btn-sm btn-info mt-1" target="_blank">
                                    Voir fiche
                                </a>
                            </div>
                        </div>
                    `).join('');

                    duplicateBox.style.display = "block";
                });
        }, 300); // délai anti-spam
    });
});
</script>

