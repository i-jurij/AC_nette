import netteForms from 'nette-forms';
window.Nette = netteForms;
netteForms.initOnLoad();

import { geoLocation } from '../../vendor/i-jurij/geolocation2/src/js/geolocation2.js';

// reread data from db with regions or city data of executors or customers from city of localstorage

/* <!-- js for esc on modal (in Home part of site that based on PicnicCSS) --> */
document.onkeydown = function (event) {
    if (event.key == "Escape") {
        var mods = document.querySelectorAll('.modal > [type=checkbox]');
        [].forEach.call(mods, function (mod) { mod.checked = false; });
    }
}