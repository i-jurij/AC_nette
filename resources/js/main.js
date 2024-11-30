import netteForms from 'nette-forms';
window.Nette = netteForms;
netteForms.initOnLoad();


import { geoLocation } from './geo/geoLocation.js';
geoLocation();

import { fromDB } from './geo/fromDB.js';
fromDB();

/* <!-- js for esc on modal (in Home part of site that based on PicnicCSS) --> */
document.onkeydown = function (event) {
    if (event.key == "Escape") {
        var mods = document.querySelectorAll('.modal > [type=checkbox]');
        [].forEach.call(mods, function (mod) { mod.checked = false; });
    }
}