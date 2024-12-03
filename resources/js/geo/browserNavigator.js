// for city getting from Yandex Geocoder from browser navigator geolocation
import { yapikey } from "../../../config/yandex_api.js"
import { locationFromYandexGeocoder } from './locationFromYandexGeocoder.js';

import { outLocation } from './OutLocationOnPage.js'
import { setLocality } from './localStorage.js'

// get location from browser geolocation and yandex geocoder
// required user permission for geolocation
export async function getLoc() {
    async function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(getCoords, showError, positionOption);
        } else {
            outLocation({ city: '', adress: '' });
            console.warn("WARNING! Geolocation is not supported by this browser.");
        }
    }

    let positionOption = { timeout: 5000, /* maximumAge: 24 * 60 * 60, /* enableHighAccuracy: true */ };

    function getCoords(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;

        let coord = { long: longitude, lat: latitude };

        fetch('home.geo/location-from-coord/' + coord.long + '_' + coord.lat, {
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(response => checkResponce(response) ? outSave(response) : locationFromYandexGeocoder(yapikey, coord))
            .catch(error => console.error(error));
    }

    function showError(error) {
        outLocation({ city: '', adress: '' });

        switch (error.code) {
            case error.PERMISSION_DENIED:
                console.error("ERROR! User denied the request for Geolocation.")
                break;
            case error.POSITION_UNAVAILABLE:
                console.error("ERROR! Location information is unavailable.")
                break;
            case error.TIMEOUT:
                console.error("ERROR! The request to get user location timed out.")
                break;
            case error.UNKNOWN_ERROR:
                console.error("ERROR! An unknown error occurred.")
                break;
        }
    }

    function outSave({ city, adress, id }) {
        outLocation({ city, adress });
        setLocality({ city, adress, id });
    }

    function checkResponce(obj) {
        if (typeof obj === 'object' && 'city' in obj && obj.city != '' && obj.city != 'undefined' && typeof obj.city == 'string') {
            return true;
        } else {
            return false;
        }
    }
    getLocation();
}