import { outLocation } from './OutLocationOnPage.js'

import { getLoc } from './browserNavigator.js'

import { setLocality } from './localStorage.js'
import { getLocality } from './localStorage.js'

export function geoLocation() {
    document.addEventListener('DOMContentLoaded', () => {
        // search in localstorage keeped data with user location
        //let locality = JSON.parse(localStorage.getItem('locality'));
        let locality = getLocality();

        const substring = "Местоположение";

        if (locality) {
            outLocation({ city: locality.city, adress: locality.adress });
        } else {
            if (city_from_back) {
                if (city_from_back.includes(substring)) {
                    getLoc();
                    //outLocation({ city: '', adress: '' });
                } else {
                    let region = region_from_back ?? '';
                    outLocation({ city: city_from_back, adress: region });
                    setLocality({ city: city_from_back, adress: region });
                }
            } else {
                console.error('ERROR! Element with id "location" is empty.')
            }
        }
    });
}