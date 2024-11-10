// import img from '.. /img/simao.jpg';
// document.body.style.background = `url(${img}) `

// import naja from 'naja';
import netteForms from 'nette-forms';
window.Nette = netteForms;
// document.addEventListener('DOMContentLoaded', naja.initialize.bind(naja));
netteForms.initOnLoad();

function closeFlash() {
    let fbci = document.querySelector('#for_button_close_insert')
    if (fbci) {
        fbci.innerHTML = '<button id="close_flash_message">Close</button>';
        close_flash_message.onclick = function () {
            flash_message.remove();
        };
    }
}
document.addEventListener('DOMContentLoaded', closeFlash());

/* geolocation */
const gloc = document.getElementById("geoLocationDiv");
function showError(error) {
    switch (error.code) {
        case error.PERMISSION_DENIED:
            gloc.innerHTML = "User denied the request for Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            gloc.innerHTML = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            gloc.innerHTML = "The request to get user location timed out."
            break;
        case error.UNKNOWN_ERROR:
            gloc.innerHTML = "An unknown error occurred."
            break;
    }
}
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        showError(error);
    }
}

function showPosition(position) {
    gloc.innerHTML = "Latitude: " + position.coords.latitude +
        "<br>Longitude: " + position.coords.longitude;
}