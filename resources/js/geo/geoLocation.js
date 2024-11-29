import { outLocation } from './OutLocationOnPage.js'
// for city getting from Yandex Map API
import { yapikey } from "../../../config/yandex_api.js"
import { locationFromYandexGeocoder } from './locationFromYandexGeocoder.js';

//-------- part to be performed on the page ---------------------

function getLoc() {
    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(getCoords, showError, positionOption);
        } else {
            console.warning("WARNING! Geolocation is not supported by this browser.");
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

export function geoLocation() {
    document.addEventListener('DOMContentLoaded', () => {
        // search in localstorage keeped data with user location
        let locality = JSON.parse(localStorage.getItem('locality'));

        // если на странице есть эемент с id="location" и 
        // если его содержимое равно "Местоположение" (значит на сервере положение не было определено),
        // определим местоположение
        let city_elem = document.getElementById("location");
        if (city_elem) {
            let city = city_elem.innerHTML;

            const substring = "Местоположение";

            if (locality) {
                outLocation({ city: locality.city, adress: locality.adress });
            } else {
                if (city) {
                    if (city.includes(substring)) {
                        getLoc();
                    } else {
                        let region = (document.getElementById('p_region')) ? document.getElementById('p_region').innerHTML : '';
                        outLocation({ city: city, adress: region });
                        localStorage.setItem('locality', JSON.stringify({ city: city, adress: region }));
                    }
                } else {
                    console.error('ERROR! Element with id "location" is empty.')
                }
            }
        } else {
            console.error('ERROR! Element with id "location" not exist.')
        }
    });
}