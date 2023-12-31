
import './bootstrap';

/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/profil.scss';
import './js/script.js';

// Initialization for ES Users
import {
    Carousel,
    initTE,
} from "tw-elements";

initTE({ Carousel });