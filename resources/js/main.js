// import img from '.. /img/simao.jpg';
// document.body.style.background = `url(${img}) `

// import naja from 'naja';
import netteForms from 'nette-forms';
window.Nette = netteForms;
// document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));
netteForms.initOnLoad();

//script for load script into head of the page
import { loadScript } from './loadScript.js';


// for coordinates getting from html5 geolocation
import { getCoordinates } from './geo/getCoordinates.js';

// for city getting from GeoPlugin
import { geoplugin_url, city_from_geoplugin_ip } from './geo/cityFromGeoPluginIp.js';

// for city getting from Yandex Map API v2
//import { ymap_url, city_from_ymap } from './geo/cityFromYmap2.js';

//////////////////////////////////////////
// descriptive part
/////////////////////////////////////////

/* for close flash message from presenter */
function closeFlash() {
    let fbci = document.querySelector('#for_button_close_insert')
    if (fbci) {
        fbci.innerHTML = '<button id="close_flash_message">Close</button>';
        close_flash_message.onclick = function () {
            flash_message.remove();
        };
    }
}

//////////////////////////////////////////
// part to be performed
/* <!-- js for esc on modal --> */
document.onkeydown = function (event) {
    if (event.key == "Escape") {
        var mods = document.querySelectorAll('.modal > [type=checkbox]');
        [].forEach.call(mods, function (mod) { mod.checked = false; });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    closeFlash();

    let coord = getCoordinates();
    if (typeof coord === "undefined") {
        loadScript({ url: geoplugin_url, callback: city_from_geoplugin_ip });
        //loadScript({ url: ymap_url, callback: city_from_ymap });
    } else {

    }
});
