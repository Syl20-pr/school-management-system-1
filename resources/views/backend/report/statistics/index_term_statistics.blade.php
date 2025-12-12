@extends('admin.admin_master')
@section('admin')
<style>
    [v-cloak] { display: none !important; }
    .progress { height: 25px; border-radius: 8px; overflow: hidden; }
    .progress-bar {
        font-size: 14px;
        line-height: 25px;
        transition: width 0.4s ease-in-out;
    }
</style>

<div class="content-wrapper" id="stats-app" v-cloak>
    <div class="container-full">
        <div class="box bb-3 border-info">
            <div class="box-header">
                <h4 class="box-title">Session de <strong>Statistiques</strong></h4>
            </div>

            <div class="box-body">
                <!-- FORMULAIRE -->
                <form @submit.prevent="generateAll" class="statistics-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <label>Année Scolaire <span class="text-danger">*</span></label>
                            <select v-model="form.year_id" class="form-control" required>
                                <option value="" disabled selected>Choisir une année</option>
                                <option v-for="year in years" :value="year.id">@{{ year.name }}</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Classe <span class="text-danger">*</span></label>
                            <select v-model="form.class_id" class="form-control" required>
                                <option value="" disabled selected>Choisir une classe</option>
                                <option v-for="classe in classes" :value="classe.id">@{{ classe.name }}</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Type de Bulletin <span class="text-danger">*</span></label>
                            <select v-model="form.term_type_id" class="form-control" required>
                                <option value="" disabled selected>Choisir le type</option>
                                <option v-for="term in term_types" :value="term.id">@{{ term.name }}</option>
                            </select>
                        </div>

                        <div class="col-md-3 align-self-end">
                            <button type="button" class="btn btn-primary btn-block mb-2" @click="download('class')">
                                <i class="fa fa-file-pdf"></i> Classement PDF
                            </button>
                            <button type="button" class="btn btn-secondary btn-block mb-2" @click="download('subject')">
                                <i class="fa fa-chart-bar"></i> Statistiques Matières
                            </button>
                            <button type="button" class="btn btn-success btn-block" @click="generateAll">
                                <i class="fa fa-archive"></i> Télécharger Tout (ZIP)
                            </button>
                        </div>
                    </div>
                </form>

                <!-- SECTION PROGRESSION -->
                <div v-if="progress.status" class="mt-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Progression de la génération</h5>
                        </div>

                        <div class="card-body text-center">
                            <!-- En cours -->
                            <div v-if="progress.status === 'processing'">
                                <div class="progress mb-3">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-info" 
                                         :style="{ width: progress.progress + '%' }">
                                        @{{ progress.progress }}%
                                    </div>
                                </div>
                                <strong>@{{ progress.message }}</strong>
                                <p class="text-muted mb-0">Vous pouvez continuer à utiliser l’application</p>
                            </div>

                            <!-- Terminé : barre devient bouton -->
                            <div v-else-if="progress.status === 'completed'" class="text-success">
                                <div class="progress mb-3">
                                    <div class="progress-bar bg-success" style="width: 100%">
                                        100% - Terminé
                                    </div>
                                </div>
                                <i class="fa fa-check-circle fa-3x mb-2"></i>
                                <h5>Génération terminée !</h5>
                                <button @click="downloadNow" class="btn btn-success mt-3">
                                    <i class="fa fa-download"></i> Télécharger maintenant
                                </button>
                            </div>

                            <!-- Erreur -->
                            <div v-else-if="progress.status === 'error'" class="text-danger">
                                <i class="fa fa-exclamation-triangle fa-3x mb-2"></i>
                                <h5>Erreur</h5>
                                <p>@{{ progress.error }}</p>
                                <button class="btn btn-warning mt-2" @click="resetProgress">
                                    <i class="fa fa-refresh"></i> Réessayer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>  
</div>

<script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
new Vue({
    el: '#stats-app',
    data: {
        form: { year_id: '', class_id: '', term_type_id: '' },
        years: @json($years),
        classes: @json($classes),
        term_types: @json($term_types),
        progress: {},
        jobId: null,
        checkInterval: null
    },
    methods: {
        async download(type) {
            if (!this.form.year_id || !this.form.class_id || !this.form.term_type_id) {
                alert('Veuillez remplir tous les champs');
                return;
            }

            // Chercher les noms réels depuis les sélections
            const selectedClass = this.classes.find(c => c.id === this.form.class_id)?.name || 'Classe';
            const selectedTerm = this.term_types.find(t => t.id === this.form.term_type_id)?.name || 'Trimestre';
            const selectedYear = this.years.find(y => y.id === this.form.year_id)?.name || 'Année';

            const clean = str => str.replace(/\s+/g, '_');

            let filename = '';
            if (type === 'class') {
                filename = `${clean(selectedClass)}_Classement_${clean(selectedTerm)}_${clean(selectedYear)}.pdf`;
            } else {
                filename = `${clean(selectedClass)}_Statistiques_Matieres_${clean(selectedTerm)}_${clean(selectedYear)}.pdf`;
            }

            const routes = {
                class: '{{ route("admin.reports.statistics.generate.class") }}',
                subject: '{{ route("admin.reports.statistics.generate.subject") }}'
            };

            try {
                const res = await axios.post(routes[type], this.form, { responseType: 'blob' });
                const url = window.URL.createObjectURL(new Blob([res.data]));
                const a = document.createElement('a');
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
            } catch (e) {
                alert('Erreur de génération');
                console.error(e);
            }
        },

        async generateAll() {
            if (!this.form.year_id || !this.form.class_id || !this.form.term_type_id) {
                alert('Veuillez remplir tous les champs');
                return;
            }
            this.resetProgress();
            this.progress = { status: 'processing', progress: 0, message: 'Préparation en cours...' };
            try {
                const res = await axios.post('{{ route("admin.reports.statistics.generate.async") }}', this.form);
                if (res.data.job_id) {
                    this.jobId = res.data.job_id;
                    this.startProgressChecking(res.data.job_id);
                } else {
                    this.progress = { status: 'error', error: 'Job non démarré' };
                }
            } catch (e) {
                this.progress = { status: 'error', error: 'Erreur serveur' };
            }
        },

        startProgressChecking(jobId) {
            this.checkInterval = setInterval(async () => {
                const res = await axios.get('{{ route("admin.reports.statistics.progress", "") }}/' + jobId);
                this.progress = res.data;

                if (['completed', 'error'].includes(res.data.status)) {
                    clearInterval(this.checkInterval);
                }
            }, 3000);
        },

        downloadNow() {
            if (!this.jobId) return;
            window.location.href = '{{ route("admin.reports.statistics.download", "") }}/' + this.jobId;
        },

        resetProgress() {
            this.progress = {};
            this.jobId = null;
            if (this.checkInterval) clearInterval(this.checkInterval);
        }
    }
});
</script>
@endsection
