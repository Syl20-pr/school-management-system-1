@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Modifier un Élève</h4>
                </div>
                <div class="box-body">
                    <form method="POST" action="{{ route('update.student.registration', $editData->student_id) }}" enctype="multipart/form-data">
                        @include('admin.student_registration.partials._form', ['buttonText' => 'Modifier'])
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection
