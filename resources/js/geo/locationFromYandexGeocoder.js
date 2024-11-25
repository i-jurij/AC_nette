import { outLocation } from './OutLocationOnPage.js'

export async function locationFromYandexGeocoder(yapikey, { long, lat }, format = 'json', kind = 'locality', results = 1) {
    const url = "https://geocode-maps.yandex.ru/1.x/?apikey=" + yapikey + "&geocode=" + long + "," + lat + "&format=" + format + "&results=" + results + "&kind=" + kind;
    try {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        });
        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }

        const json = await response.json();

        let name = json.response.GeoObjectCollection.featureMember[0].GeoObject.name;
        let description = json.response.GeoObjectCollection.featureMember[0].GeoObject.description;

        outLocation({ city: name, adress: description });

    } catch (error) {
        console.error(error.message);
    }
}