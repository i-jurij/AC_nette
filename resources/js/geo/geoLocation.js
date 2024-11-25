import { outLocation } from './OutLocationOnPage.js'
// for city getting from Yandex Map API
import { yapikey } from "../../../config/yandex_api.js"
import { locationFromYandexGeocoder } from './locationFromYandexGeocoder.js';

//-------- part to be performed on the page ---------------------
export function geoLocation() {
    document.addEventListener('DOMContentLoaded', () => {
        // если на странице есть эемент с id="location" и 
        // если его содержимое равно "Местоположение" (значит на сервере положение не было определено),
        // определим местоположение
        let city_elem = document.getElementById("location");
        if (city_elem) {
            let city = city_elem.innerHTML;
            const substring = "Местоположение";

            if (city && city.includes(substring)) {

                function getLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(getCoords, showError, positionOption);
                    } else {
                        console.warning("WARNING! Geolocation is not supported by this browser.");
                    }
                }

                let positionOption = { timeout: 3000, /* enableHighAccuracy: true */ };

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
        }
    });
}