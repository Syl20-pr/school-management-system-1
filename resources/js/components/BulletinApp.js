// resources/js/components/BulletinApp.js
import { ref, reactive } from 'vue';

export default {
    name: 'BulletinApp',
    setup() {
        const form = reactive({
            year_id: '',
            class_id: '', 
            term_type_id: ''
        });

        const loading = ref(false);
        const error = ref(null);
        const pdfUrl = ref(null);
        const previewData = ref(null);
        const showPreview = ref(false);
        const showStatistics = ref(false);
        const statistics = reactive({
            highest_term_mean: 0,
            lowest_term_mean: 0,
            class_term_mean: 0,
            number_of_students: 0,
            percentage_above_10: 0
        });

        const generateBulletin = async () => {
            loading.value = true;
            error.value = null;
            pdfUrl.value = null;
            previewData.value = null;
            showStatistics.value = false;

            try {
                const response = await fetch('/admin/reports/bulletin/api/generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(form)
                });

                const data = await response.json();

                if (data.success) {
                    previewData.value = data.data;

                    Object.assign(statistics, {
                        highest_term_mean: parseFloat(data.data.statistics.highest_term_mean || 0),
                        lowest_term_mean: parseFloat(data.data.statistics.lowest_term_mean || 0),
                        class_term_mean: parseFloat(data.data.statistics.class_term_mean || 0),
                        number_of_students: parseInt(data.data.statistics.number_of_students || 0),
                        percentage_above_10: parseFloat(data.data.statistics.percentage_above_10 || 0)
                    });

                    showStatistics.value = true;
                    //await downloadPdf();
                    showPreview.value = true;  // ouvre uniquement le modal
                } else {
                    error.value = data.message;
                }
            } catch (err) {
                error.value = 'Erreur de connexion au serveur';
                console.error('Erreur:', err);
            } finally {
                loading.value = false;
            }
        };

        const downloadPdf = async () => {
            try {
                const pdfForm = document.createElement('form');
                pdfForm.method = 'POST';
                pdfForm.action = '/admin/reports/bulletin/generate';
                pdfForm.target = '_blank';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
                pdfForm.appendChild(csrfToken);

                for (const key in form) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = key;
                    input.value = form[key];
                    pdfForm.appendChild(input);
                }

                document.body.appendChild(pdfForm);
                pdfForm.submit();
                document.body.removeChild(pdfForm);

            } catch (err) {
                console.error('Erreur lors du téléchargement:', err);
                error.value = 'Erreur lors du téléchargement du PDF';
            }
        };

        const getRankSuffix = (rank, gender) => {
            if (rank === 1) {
                return gender === 'Féminin' ? 'ère' : 'er';
            }
            return 'ème';
        };

        return {
            form,
            loading,
            error,
            pdfUrl,
            previewData,
            showPreview,
            showStatistics,
            statistics,
            generateBulletin,
            downloadPdf,
            getRankSuffix,
            years: window.years,
            classes: window.classes,
            term_types: window.term_types
        };
        
    }
};
