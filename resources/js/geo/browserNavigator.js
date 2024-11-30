import { yapikey } from "../../../config/yandex_api.js"
import { locationFromYandexGeocoder } from './locationFromYandexGeocoder.js';
import { outLocation } from './OutLocationOnPage.js'

// get location from browser geolocation and yandex geocoder
// required user permission for geolocation
export function getLoc() {
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(getCoords, showError, positionOption);
        } else {
            outLocation({ city: '', adress: '' });
            alert("WARNING! Geolocation is not supported by this browser.");
        }
    }

    let positionOption = { timeout: 5000, /* maximumAge: 24 * 60 * 60, /* enableHighAccuracy: true */ };

    function getCoords(position) {
        const latitude = position.coords.latitude;
        const longitude = position.coords.longitude;
        let longLat = { long: longitude, lat: latitude };

        locationFromYandexGeocoder(yapikey, longLat);
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

    getLocation();
}