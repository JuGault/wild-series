
// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.scss';
const $ = require('jquery');
require('bootstrap');
// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
// import $ from 'jquery';
const select = document.getElementById('category');
select.addEventListener('change', function () {
    var valeur = select.options[select.selectedIndex].value;
    changeURL(valeur);
});

function changeURL(url) {
    window.location.href = url;
}


console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
