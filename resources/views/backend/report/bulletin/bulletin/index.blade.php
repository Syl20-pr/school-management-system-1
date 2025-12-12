<!DOCTYPE html>
<!-- resources/views/backend/report/bulletin/bulletin/index.blade.php -->
@extends('admin.admin_master')
@section('admin')

<div class="content-wrapper">
    <div class="container-full">
        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box bb-3 border-warning">
                        <div class="box-header">
                            <h4 class="box-title">Session De <strong>Gestion Des Bulletins</strong></h4>
                        </div>

                        <div class="box-body">
                            <form method="POST" action="{{ route('reports.bulletins.generate') }}" id="bulletin-form">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Année Scolaire</label>
                                        <select name="year_id" id="year_id" required class="form-control rounded-md shadow-sm">
                                            <option value="" selected disabled>Sélectionner l'Année Scolaire</option>
                                            @foreach($years as $year)
                                                <option value="{{ $year->id }}">{{ $year->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Classe</label>
                                        <select name="class_id" id="class_id" required class="form-control rounded-md shadow-sm">
                                            <option value="" selected disabled>Sélectionner La Classe</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Type de Bulletin</label>
                                        <select name="term_type_id" id="term_type_id" required class="form-control rounded-md shadow-sm">
                                            <option value="" selected disabled>Sélectionner le Type de Bulletin</option>
                                            @foreach($term_types as $term)
                                                <option value="{{ $term->id }}">{{ $term->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group flex items-end">
                                        <button type="submit" class="btn btn-primary rounded-md px-4 py-2 w-full">
                                            <i class="fa fa-search mr-2"></i> Générer
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div id="preview-container" class="hidden mt-6">
                                <div class="bg-white rounded-lg shadow-md p-6">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-xl font-semibold">Aperçu du Bulletin</h3>
                                        <div class="flex space-x-2">
                                            <button id="download-pdf" class="btn btn-success">
                                                <i class="fa fa-download mr-2"></i> Télécharger PDF
                                            </button>
                                            <button id="download-statistics" class="btn btn-info">
                                                <i class="fa fa-chart-bar mr-2"></i> Statistiques
                                            </button>
                                        </div>
                                    </div>
                                    <div id="preview-content"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bulletin-form');
    const previewContainer = document.getElementById('preview-container');
    const previewContent = document.getElementById('preview-content');
    const downloadPdfBtn = document.getElementById('download-pdf');
    const downloadStatsBtn = document.getElementById('download-statistics');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Afficher l'indicateur de chargement
        previewContent.innerHTML = '<div class="text-center py-8"><i class="fa fa-spinner fa-spin fa-2x"></i><p class="mt-2">Génération en cours...</p></div>';
        previewContainer.classList.remove('hidden');
        
        // Soumettre le formulaire via AJAX
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            previewContent.innerHTML = html;
            
            // Mettre à jour les URLs de téléchargement
            const formData = new FormData(form);
            const params = new URLSearchParams();
            for (let [key, value] of formData) {
                params.append(key, value);
            }
            
            downloadPdfBtn.onclick = () => {
                window.location.href = '{{ route("reports.bulletins.download") }}?' + params.toString();
            };
        })
        .catch(error => {
            previewContent.innerHTML = '<div class="alert alert-danger">Erreur lors de la génération: ' + error + '</div>';
        });
    });
});
</script>

@endsection