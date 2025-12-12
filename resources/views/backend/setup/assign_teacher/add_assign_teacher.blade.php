@extends('admin.admin_master')
@section('admin')

<!-- Include jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<div class="content-wrapper">
  <div class="container-full">
    <section class="content">
      <!-- Basic Forms -->
      <div class="box">
        <div class="box-header with-border">
          <h4 class="box-title">Assigner Une Matière à Un(e) Prof.</h4>
        </div>

        <div class="box-body">
          <div class="row">
            <div class="col">
              <form method="post" action="{{ route('store.assign.teachers') }}">
                @csrf
                <div class="row">
                  <div class="col-12">
                    <div class="add_item">
                      <div class="row">
                        <!-- Year Dropdown -->
                        <div class="col-4">
                          <div class="form-group">
                            <h5>Année Scolaire <span class="text-danger">*</span></h5>
                            <div class="controls">
                              <select name="year_id" required="" class="form-control">
                                <option value="" selected="" disabled="">Sélectionner l'Année Scolaire</option>
                                @foreach($years as $year)
                                  <option value="{{ $year->id }}">{{ $year->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        </div>

                        <!-- Class Dropdown -->
                        <div class="col-4">
                          <div class="form-group">
                            <h5>Classe <span class="text-danger">*</span></h5>
                            <div class="controls">
                              <select name="class_id" id="class_id" required="" class="class_id form-control">
                                <option value="" selected="" disabled="">Sélectionner Une Classe</option>
                                @foreach($classes as $class)
                                  <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        </div>

                        <!-- Designation (Type de Prof.) Dropdown -->
                        <div class="col-4">
                          <div class="form-group">
                            <h5>Type de Prof. <span class="text-danger">*</span></h5>
                            <div class="controls">
                              <select name="designation_id" class="designation_id form-control" required="">
                                <option value="" selected="" disabled="">Sélectionner le Type de Prof.</option>
                                @foreach($designations as $designation)
                                  <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        </div>

                        <!-- Teacher (Nom du Prof.) Dropdown -->
                        <div class="col-4">
                          <div class="form-group">
                            <h5>Nom du Prof <span class="text-danger">*</span></h5>
                            <div class="controls">
                              <select name="teacher_id" class="teacher_id form-control" required="">
                                <option value="" selected="" disabled="">Sélectionner le Nom du Prof.</option>
                              </select>
                            </div>
                          </div>
                        </div>

                        <!-- Subject Dropdown -->
                        <div class="col-4">
                          <div class="form-group">
                            <h5>Matière <span class="text-danger">*</span></h5>
                            <div class="controls">
                              <select name="subject_id" class="subject_id form-control" required="">
                                <option value="" selected="" disabled="">Sélectionner Une Matière</option>
                              </select>
                            </div>
                          </div>
                        </div>

                        <div class="col-md-4">
                          <div class="form-group">
                            <h5>Quota Horaire par Semaine <span class="text-danger">*</span></h5>
                            <div class="controls">
                              <input type="text" name="total_hours[]" class="form-control"> 
                            </div>     
                          </div>
                        </div>

                        <div class="col-md-4">
                          <div class="form-group">
                            <h5>Nombre de Séances par Semaine <span class="text-danger">*</span></h5>
                            <div class="controls">
                              <input type="text" name="total_sessions[]" class="form-control"> 
                            </div>     
                          </div>
                        </div>

                        <div class="col-md-4">
                          <div class="form-group">
                            <h5>Commentaires <span class="text-danger"></span></h5>
                            <div class="controls">
                              <input type="text" name="comments[]" class="form-control"> 
                            </div>     
                          </div>
                        </div>
                      </div> <!-- End Row -->
                      
                      <div class="col-md-2" style="padding-top: 25px;">
                        <span class="btn btn-success addeventmore"><i class="fa fa-plus-circle"></i></span>
                      </div>
                    </div> <!-- End add_item -->
                    
                    <div class="text-xs-right">
                      <input type="submit" class="btn btn-rounded btn-info mb-5" value="Affecter">
                    </div>
                    
                  </div> <!-- End Col -->
                </div> <!-- End Row -->
              </form>
            </div> <!-- End Col -->
          </div> <!-- End Row -->
        </div> <!-- End box-body -->
      </div> <!-- End box -->
    </section>
  </div> <!-- End container-full -->
</div> <!-- End content-wrapper -->

<div style="visibility: hidden;">
  <div class="whole_extra_item_add" id="whole_extra_item_add">
    <div class="delete_whole_extra_item_add" id="delete_whole_extra_item_add">
      <div class="row">
        <!-- Year Dropdown -->
        <div class="col-4">
          <div class="form-group">
            <h5>Année Scolaire <span class="text-danger">*</span></h5>
            <div class="controls">
              <select name="year_id" required="" class="form-control">
                <option value="" selected="" disabled="">Sélectionner l'Année Scolaire</option>
                @foreach($years as $year)
                  <option value="{{ $year->id }}">{{ $year->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <!-- Class Dropdown -->
        <div class="col-4">
          <div class="form-group">
            <h5>Classe <span class="text-danger">*</span></h5>
            <div class="controls">
              <select name="class_id" class="class_id form-control" required="">
                <option value="" selected="" disabled="">Sélectionner Une Classe</option>
                @foreach($classes as $class)
                  <option value="{{ $class->id }}">{{ $class->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <!-- Designation Dropdown -->
        <div class="col-4">
          <div class="form-group">
            <h5>Type de Prof. <span class="text-danger">*</span></h5>
            <div class="controls">
              <select name="designation_id" class="designation_id form-control" required="">
                <option value="" selected="" disabled="">Sélectionner le Type de Prof.</option>
                @foreach($designations as $designation)
                  <option value="{{ $designation->id }}">{{ $designation->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        <!-- Teacher Dropdown -->
        <div class="col-4">
          <div class="form-group">
            <h5>Nom du Prof <span class="text-danger">*</span></h5>
            <div class="controls">
              <select name="teacher_id" class="teacher_id form-control" required="">
                <option value="" selected="" disabled="">Sélectionner le Nom du Prof.</option>
              </select>
            </div>
          </div>
        </div>

        <!-- Subject Dropdown -->
        <div class="col-4">
          <div class="form-group">
            <h5>Matière <span class="text-danger">*</span></h5>
            <div class="controls">
              <select name="subject_id" class="subject_id form-control" required="">
                <option value="" selected="" disabled="">Sélectionner Une Matière</option>
              </select>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <h5>Quota Horaire par Semaine <span class="text-danger">*</span></h5>
            <div class="controls">
              <input type="text" name="total_hours[]" class="form-control"> 
            </div>     
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <h5>Nombre de Séances par Semaine <span class="text-danger">*</span></h5>
            <div class="controls">
              <input type="text" name="total_sessions[]" class="form-control"> 
            </div>     
          </div>
        </div>

        <div class="col-md-4">
          <div class="form-group">
            <h5>Commentaires <span class="text-danger"></span></h5>
            <div class="controls">
              <input type="text" name="comments[]" class="form-control"> 
            </div>     
          </div>
        </div>
      </div> <!-- End Row -->
      
      <div class="col-md-2" style="padding-top: 25px;">
        <span class="btn btn-danger removeeventmore"><i class="fa fa-minus-circle"></i></span>
      </div>
    </div> <!-- End delete_whole_extra_item_add -->
  </div> <!-- End whole_extra_item_add -->
</div>

<script type="text/javascript">
  $(document).ready(function() {
    var counter = 0;

    // Add new entry
    $(document).on("click", ".addeventmore", function() {
      var whole_extra_item_add = $('#whole_extra_item_add').html();
      $(this).closest(".add_item").append(whole_extra_item_add);
      counter++;
    });

    // Remove entry
    $(document).on("click", '.removeeventmore', function(event) {
      $(this).closest(".delete_whole_extra_item_add").remove();
      counter -= 1;
    });

    // Fetch teachers based on the selected designation
    $(document).on('change', '.designation_id', function() {
      var designationId = $(this).val();
      var teacherDropdown = $(this).closest('.row').find('.teacher_id');

      teacherDropdown.html('<option value="">Chargement...</option>');

      if (designationId) {
        $.ajax({
          url: "{{ route('get.assign.teachers') }}",
          type: "GET",
          data: { designation_id: designationId },
          success: function(data) {
            teacherDropdown.html('<option value="">Sélectionner le Nom du Prof.</option>');
            $.each(data, function(key, teacher) {
              teacherDropdown.append('<option value="' + teacher.id + '">' + teacher.name + '</option>');
            });
          },
          error: function() {
            teacherDropdown.html('<option value="">Aucun Prof. trouvé</option>');
          }
        });
      } else {
        teacherDropdown.html('<option value="">Sélectionner le Nom du Prof.</option>');
      }
    });

    // Fetch subjects based on the selected class
    $(document).on('change', '.class_id', function() {
      var classId = $(this).val();
      var subjectDropdown = $(this).closest('.row').find('.subject_id');

      subjectDropdown.html('<option value="">Chargement...</option>');

      if (classId) {
        $.ajax({
          url: "{{ route('get.subject.byclass') }}",
          type: "GET",
          data: { class_id: classId },
          success: function(data) {
            subjectDropdown.html('<option value="">Sélectionner Une Matière</option>');
            $.each(data, function(key, subject) {
              subjectDropdown.append('<option value="' + subject.id + '">' + subject.name + '</option>');
            });
          },
          error: function() {
            subjectDropdown.html('<option value="">Aucune Matiere trouvée</option>');
          }
        });
      } else {
        subjectDropdown.html('<option value="">Sélectionner Une Matière</option>');
      }
    });
  });
</script>

@endsection
