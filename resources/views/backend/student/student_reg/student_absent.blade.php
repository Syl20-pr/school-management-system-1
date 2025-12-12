@extends('admin.admin_master')

@section('admin')
<div class="content-wrapper">
    <div class="container-full">
        <h3>Gestion des Absences (Livewire)</h3>
        @livewire('absence-manager')
    </div>
</div>
@endsection
