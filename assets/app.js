import './bootstrap.js';

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// start the Stimulus application
import './bootstrap';

// enable the interactive UI components from Flowbite
import 'flowbite';

import { createIcons, icons } from 'lucide';

// Initialize Lucide
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});


console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');