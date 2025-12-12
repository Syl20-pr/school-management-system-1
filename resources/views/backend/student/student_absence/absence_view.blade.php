@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box bb-3 border-warning">
                        <div class="box-header">
                            <h4 class="box-title"><strong>Gestion des Absences</strong></h4>
                        </div>
                        <div class="box-body">
                            @livewire('absence-manager')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

@endsection