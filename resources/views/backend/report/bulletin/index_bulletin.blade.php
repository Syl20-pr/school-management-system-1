@extends('admin.admin_master')
@section('admin')
<style>
    [v-cloak] {
        display: none !important;
    }
    .progress {
        height: 25px;
    }
    .progress-bar {
        font-size: 14px;
        line-height: 25px;
    }
</style>

<div class="content-wrapper" id="bulletin-app" v-cloak>
    <div class="container-full">
        <div class="box bb-3 border-warning">
            <div class="box-header">
                <h4 class="box-title">Session De <strong>Gestion Des Bulletins</strong></h4>
            </div>

            <div class="box-body">
                <!-- FORMULAIRE -->
                <form @submit.prevent="generateBulletin" class="bulletin-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Année Scolaire <span class="text-danger">*</span></label>
                                <select v-model="form.year_id" required class="form-control">
                                    <option value="" selected disabled>Sélectionner l'Année Scolaire</option>
                                    <option v-for="year in years" :value="year.id">@{{ year.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Classe <span class="text-danger">*</span></label>
                                <select v-model="form.class_id" required class="form-control">
                                    <option value="" selected disabled>Sélectionner La Classe</option>
                                    <option v-for="classe in classes" :value="classe.id">@{{ classe.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Type de Bulletin <span class="text-danger">*</span></label>
                                <select v-model="form.term_type_id" required class="form-control">
                                    <option value="" selected disabled>Sélectionner le Type de Bulletin</option>
                                    <option v-for="term in term_types" :value="term.id">@{{ term.name }}</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <button type="submit" :disabled="loading" class="btn btn-primary btn-block" style="margin-top: 28px;">
                                <span v-if="loading">
                                    <i class="fa fa-spinner fa-spin"></i> Lancement...
                                </span>
                                <span v-else>
                                    <i class="fa fa-file-pdf"></i> Générer (Async)
                                </span>
                            </button>
                        </div>
                    </div>
                </form>

                <!-- SECTION PROGRESSION -->
                <div v-if="progress.status" class="mt-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Progression de la génération</h5>
                        </div>
                        <div class="card-body">
                            <!-- EN COURS -->
                            <div v-if="progress.status === 'processing'" class="text-center">
                                <div class="progress mb-3">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         :style="{ width: progress.progress + '%' }">
                                        <strong>@{{ progress.progress }}%</strong>
                                    </div>
                                </div>
                                <p class="mb-1"><strong>@{{ progress.message }}</strong></p>
                                <small class="text-muted">
                                    Vous pouvez continuer à utiliser l'application pendant la génération
                                </small>
                            </div>
                            
                            <!-- TERMINÉ -->
                            <div v-else-if="progress.status === 'completed'" class="text-center text-success">
                                <i class="fa fa-check-circle fa-3x mb-3"></i>
                                <h5>Génération terminée avec succès!</h5>
                                <p class="text-muted">Vos bulletins sont prêts au téléchargement</p>
                                <a :href="progress.download_url" class="btn btn-success btn-lg mt-3">
                                    <i class="fa fa-download"></i> Télécharger les bulletins
                                </a>
                                <button @click="resetProgress" class="btn btn-secondary btn-lg mt-3 ml-2">
                                    <i class="fa fa-refresh"></i> Nouvelle Génération
                                </button>
                            </div>
                            
                            <!-- ERREUR -->
                            <div v-else-if="progress.status === 'error'" class="text-center text-danger">
                                <i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
                                <h5>Erreur lors de la génération</h5>
                                <div class="alert alert-danger">
                                    <strong>@{{ progress.error }}</strong>
                                </div>
                                <button @click="resetProgress" class="btn btn-warning mt-3">
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
    el: '#bulletin-app',
    data: {
        form: {
            year_id: '',
            class_id: '',
            term_type_id: ''
        },
        years: @json($years),
        classes: @json($classes),
        term_types: @json($term_types),
        loading: false,
        progress: {},
        checkInterval: null
    },
    methods: {
        async generateBulletin() {
    // 🔥 VALIDATION RENFORCÉE
    if (!this.form.year_id || !this.form.class_id || !this.form.term_type_id) {
        alert('Veuillez remplir tous les champs obligatoires');
        return;
    }

    // 🔥 ARRÊTER tout suivi précédent
    this.resetProgress();
    this.loading = true;
    
    try {
        console.log('🎯 Envoi de la requête async...', {
            year_id: this.form.year_id,
            class_id: this.form.class_id,
            term_type_id: this.form.term_type_id
        });
        
        const response = await axios.post(
            '{{ route("admin.reports.bulletin.generate.async") }}', 
            this.form,
            {
                timeout: 10000, // 10 secondes timeout
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }
        );
        
        console.log('📦 Réponse complète:', response);
        console.log('📦 Données reçues:', response.data);
        
        // 🔥 VALIDATION DE LA RÉPONSE
        if (!response.data) {
            throw new Error('Réponse vide du serveur');
        }
        
        if (response.data.success && response.data.job_id) {
            const newJobId = response.data.job_id;
            console.log('✅ NOUVEAU Job ID reçu:', newJobId);
            
            // Initialiser l'état de progression
            this.progress = {
                status: 'processing',
                progress: 0,
                message: 'Lancement en cours...'
            };
            
            this.startProgressChecking(newJobId);
        } else {
            const errorMsg = response.data.message || 'Erreur inconnue du serveur';
            throw new Error(errorMsg);
        }
        
    } catch (error) {
        console.error('❌ Erreur détaillée:', error);
        
        let errorMessage = 'Erreur lors du lancement';
        
        if (error.response && error.response.data) {
            // Erreur HTTP avec réponse
            errorMessage = error.response.data.message || 
                          `Erreur ${error.response.status}: ${error.response.statusText}`;
        } else if (error.request) {
            // Pas de réponse du serveur
            errorMessage = 'Pas de réponse du serveur. Vérifiez votre connexion.';
        } else {
            // Erreur JavaScript
            errorMessage = error.message;
        }
        
        alert('❌ ' + errorMessage);
        this.resetProgress();
    } finally {
        this.loading = false;
    }
},
        
        startProgressChecking(jobId) {
    console.log('🔍 Démarrage du suivi pour NOUVEAU job:', jobId);
    
    // S'assurer qu'aucun intervalle précédent ne tourne
    if (this.checkInterval) {
        clearInterval(this.checkInterval);
        this.checkInterval = null;
    }
    
    // Premier check immédiat
    this.checkProgress(jobId);
    
    // Lancer l'intervalle
    this.checkInterval = setInterval(() => {
        this.checkProgress(jobId);
    }, 3000);
},
        
async checkProgress(jobId) {
    try {
        console.log('🔄 Vérification progression pour:', jobId);
        const response = await axios.get('{{ route("admin.reports.bulletin.progress", "") }}/' + jobId);
        
        console.log('📊 Progression reçue:', response.data);
        this.progress = response.data;
        
        // 🔥 VÉRIFIER SI C'EST LE BON JOB
        if (this.progress.status === 'processing' && this.progress.progress === 0) {
            const currentTime = new Date().getTime();
            if (!this.lastProgressTime) this.lastProgressTime = currentTime;
            
            // Si bloqué plus de 45 secondes, vérifier si le job existe
            if (currentTime - this.lastProgressTime > 45000) {
                console.warn('⚠️ Job bloqué depuis 45s, vérification...');
                
                // Tester si le job existe vraiment
                try {
                    const testResponse = await axios.get('/test-cache/' + jobId);
                    console.log('🔍 Test cache:', testResponse.data);
                    
                    if (testResponse.data.file_cache && testResponse.data.file_cache.progress === 0) {
                        console.warn('❌ Job probablement mort, reset...');
                        this.resetProgress();
                        return;
                    }
                } catch (testError) {
                    console.error('Erreur test cache:', testError);
                }
            }
        } else {
            this.lastProgressTime = new Date().getTime();
        }
        
        if (['completed', 'error'].includes(this.progress.status)) {
            console.log('🏁 Génération terminée - Statut:', this.progress.status);
            clearInterval(this.checkInterval);
            this.checkInterval = null;
            this.lastProgressTime = null;
        }
    } catch (error) {
        console.error('❌ Erreur vérification:', error);
        
        if (error.response && error.response.status === 404) {
            console.warn('⚠️ Job non trouvé, probablement expiré');
            this.resetProgress();
        }
    }
},
        
        resetProgress() {
    console.log('🔄 Réinitialisation complète de la progression');
    this.progress = {};
    if (this.checkInterval) {
        clearInterval(this.checkInterval);
        this.checkInterval = null;
    }
    this.lastProgressTime = null;
    
    // Forcer le re-rendu
    this.$forceUpdate();
}
    },
    
    beforeDestroy() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
    }
});
</script>

@endsection