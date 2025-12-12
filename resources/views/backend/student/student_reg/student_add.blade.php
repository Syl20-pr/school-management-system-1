@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="box">
                <div class="box-header with-border">
                    <h4 class="box-title">Nouvel Élève</h4>
                </div>
                <div class="box-body">
                    <form method="POST" action="{{ route('store.student.registration') }}" enctype="multipart/form-data">
                        @include('admin.student_registration.partials._form', ['buttonText' => 'Ajouter'])
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection
