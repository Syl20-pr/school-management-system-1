import './bootstrap';
import { createApp } from 'vue';
import BulletinApp from './components/BulletinApp.js';

//createApp(BulletinApp).mount('#bulletin-app');

const app = createApp({
    setup() {
        //return BulletinApp.setup(); // <-- on injecte seulement la logique
        console.log("Vue est bien initialisé ✅");
        return {
            ...BulletinApp.setup()
        };

    }
});

// Données envoyées par le Blade
app.provide('years', window.years);
app.provide('classes', window.classes);
app.provide('term_types', window.term_types);

app.mount('#bulletin-app');
