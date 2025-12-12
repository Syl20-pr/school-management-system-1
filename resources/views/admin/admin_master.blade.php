<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="{{asset('backend/images/grapmult_logo.png')}}">

    <title>Grapmult - Système de Gestion Scolaire - Tableau de Bord</title>
    
    <!-- Vendors Style-->
    <link rel="stylesheet" href="{{asset('backend/css/vendors_css.css')}}">
    <!-- Style-->  
    <link rel="stylesheet" href="{{asset('backend/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('backend/css/skin_color.css')}}">
    <!-- Font Awesome 6 (pour les icônes modernes) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">


    <!-- Toastr -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" >

    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond@^4/dist/filepond.css" rel="stylesheet" />
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet" />

    @if (!request()->is('students/reg*'))
      @livewireStyles
    @endif
  <!-- CSS Pour la page des ABSCENCES-->
  <style>
    /* Dans admin_master.blade.php ou votre fichier CSS principal */
    .absence-manager .form-control:focus {
        border-color: #3c8dbc;
        box-shadow: 0 0 0 0.2rem rgba(60, 141, 188, 0.25);
    }

    .absence-manager .table th {
        background-color: #3c8dbc;
        color: white;
    }

    .absence-manager .btn-primary {
        background-color: #3c8dbc;
        border-color: #367fa9;
    }

    .absence-manager .btn-primary:hover {
        background-color: #367fa9;
        border-color: #2d708c;
    }
  </style>
  </head>

<body class="hold-transition dark-skin sidebar-mini theme-primary fixed">
  
<div class="wrapper">

  @include('admin.body.header')
  @include('admin.body.sidebar')

  @yield('admin')

  @include('admin.body.footer')
  
  <div class="control-sidebar-bg"></div>
</div>

<!-- Vendor JS -->
<script src="{{asset('backend/js/vendors.min.js')}}"></script>
<script src="{{asset('../assets/icons/feather-icons/feather.min.js')}}"></script> 
<script src="{{asset('../assets/vendor_components/easypiechart/dist/jquery.easypiechart.js')}}"></script>
<script src="{{asset('../assets/vendor_components/apexcharts-bundle/irregular-data-series.js')}}"></script>
<script src="{{asset('../assets/vendor_components/apexcharts-bundle/dist/apexcharts.js')}}"></script>
<script src="{{asset('../assets/vendor_components/datatable/datatables.min.js')}}"></script>
<script src="{{asset('backend/js/pages/data-table.js')}}"></script>
<script src="{{asset('backend/js/template.js')}}"></script>
<script src="{{asset('backend/js/pages/dashboard.js')}}"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- FilePond -->
<script src="https://unpkg.com/filepond@^4/dist/filepond.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
<script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
<script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.js"></script>

<!-- Toastr -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

{{-- <script>
    // Toastr notifications
    @if(Session::has('message'))
      var type = "{{ Session::get('alert-type','info') }}";
      switch(type){
          case 'info': toastr.info("{{ Session::get('message') }}"); break;
          case 'success': toastr.success("{{ Session::get('message') }}"); break;
          case 'warning': toastr.warning("{{ Session::get('message') }}"); break;
          case 'error': toastr.error("{{ Session::get('message') }}"); break;
      }
    @endif
</script>
 --}}
 <script>
    @if(Session::has('message'))
        var type = "{{ Session::get('alert-type','info') }}";
        var Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500,
            timerProgressBar: true,
            background: '#1e1e2f',
            color: '#fff',
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        switch(type){
            case 'info':
                Toast.fire({ icon: 'info', title: "{{ Session::get('message') }}" });
                break;
            case 'success':
                Toast.fire({ icon: 'success', title: "{{ Session::get('message') }}" });
                break;
            case 'warning':
                Toast.fire({ icon: 'warning', title: "{{ Session::get('message') }}" });
                break;
            case 'error':
                Toast.fire({ icon: 'error', title: "{{ Session::get('message') }}" });
                break;
        }
    @endif
</script>

<script>
    // SweetAlert2 flash messages
    @if(Session::has('success'))
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: "{{ Session::get('success') }}",
            showConfirmButton: false,
            timer: 2500
        });
    @endif

    @if(Session::has('error'))
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: "{{ Session::get('error') }}",
            showConfirmButton: true
        });
    @endif
</script>

<script>
    // FilePond setup
    FilePond.registerPlugin(
        FilePondPluginFileValidateType,
        FilePondPluginFileValidateSize,
        FilePondPluginImagePreview,
        FilePondPluginImageExifOrientation
    );

    FilePond.create(document.querySelector('.filepond'), {
        labelIdle: 'Glissez-déposez votre image ou <span class="filepond--label-action">Parcourir</span>',
        acceptedFileTypes: ['image/png', 'image/jpeg', 'image/jpg'],
        maxFileSize: '2MB',
        allowMultiple: false,
        instantUpload: false
    });
</script>

<!-- Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
      $('select[name="year_id"], select[name="class_id"], select[name="group_id"], select[name="shift_id"]').select2({
          theme: 'classic',
          width: '100%',
          placeholder: 'Sélectionner...',
          allowClear: true
      });

      $('select[name="gender"], select[name="religion"], select[name="statusclass"]').select2({
          theme: 'classic',
          width: '100%',
          minimumResultsForSearch: Infinity
      });
  });
</script>

@if (!request()->is('students/reg*')) 
  @livewireScripts 
@endif

</body>
</html>
