import { outLocation } from './OutLocationOnPage.js'
// for city getting from Yandex Geocoder from browser navigator geolocation
// import { getLoc } from './browserNavigator.js'

export function geoLocation() {
    document.addEventListener('DOMContentLoaded', () => {
        // search in localstorage keeped data with user location
        let locality = JSON.parse(localStorage.getItem('locality'));

        const substring = "Местоположение";

        if (locality) {
            outLocation({ city: locality.city, adress: locality.adress });
        } else {
            if (city_from_back) {
                if (city_from_back.includes(substring)) {
                    // getLoc();
                    outLocation({ city: '', adress: '' });
                } else {
                    let region = region_from_back ?? '';
                    outLocation({ city: city_from_back, adress: region });
                    localStorage.setItem('locality', JSON.stringify({ city: city_from_back, adress: region }));
                }
            } else {
                console.error('ERROR! Element with id "location" is empty.')
            }
        }
    });
}