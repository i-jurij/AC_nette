import { outLocation } from './OutLocationOnPage.js'
import { setLocality } from './localStorage.js'

function districtOut(districts) {
    let inner = '<option value="" id="empty_district">Округ</option>';

    for (const key of Object.keys(districts)) {
        // console.log(district[key]['id'] + ' ' + district[key]['name'])
        inner = inner + '<option value="' + districts[key]['id'] + '">' + districts[key]['name'] + '</option>'
    }
    let shoose_district = document.querySelector('#shoose_district');
    if (shoose_district) {
        shoose_district.innerHTML = inner;
    }
    let shoose_region = document.querySelector('#shoose_region');
    if (shoose_region) {
        shoose_region.innerHTML = '<option value="">Регион</option>';
    }
    let shoose_city = document.querySelector('#shoose_city');
    if (shoose_city) {
        shoose_city.innerHTML = '<option value="">Город</option>';
    }
}

function regionOut(regions) {
    let inner = '<option value="" id="empty_region">Регион</option>';
    for (const key of Object.keys(regions)) {
        inner = inner + '<option value="' + regions[key]['id'] + '">' + regions[key]['name'] + '</option>'
    }
    let shoose_region = document.querySelector('#shoose_region');
    if (shoose_region) {
        shoose_region.disabled = false;
        shoose_region.innerHTML = inner;
    }
    let shoose_city = document.querySelector('#shoose_city');
    if (shoose_city) {
        shoose_city.innerHTML = '<option value="">Город</option>';
    }
}

function cityOut(cities) {
    let inner = '<option value="" id="empty_city">Город</option>';
    for (const key of Object.keys(cities)) {
        inner = inner + '<option value="' + cities[key]['id'] + '">' + cities[key]['name'] + '</option>'
    }
    let shoose_city = document.querySelector('#shoose_city');
    if (shoose_city) {
        shoose_city.disabled = false;
        shoose_city.innerHTML = inner;
    }

}

function hideLocationModal() {
    const mod_1 = document.getElementById('modal_1');
    mod_1.checked = false;
}

function regionOutAndCityOutAndSave(districts) {
    let shoose_district = document.querySelector('#shoose_district');
    if (shoose_district) {
        shoose_district.addEventListener('change', function () {
            let options_empty_district = document.querySelector('#empty_district');
            if (options_empty_district) {
                options_empty_district.remove();
            }

            let district_id = this.value;
            let district_text = this.options[this.selectedIndex].text;

            if (district_id) {
                let regions0 = districts[district_id];
                if (regions0) {
                    let regions = regions0['regions'];
                    regionOut(regions);
                    cityOutAndSave(regions);
                }
            }
        })
    }
}

function cityOutAndSave(regions) {
    let shoose_region = document.querySelector('#shoose_region');
    if (shoose_region) {
        shoose_region.addEventListener('change', function () {
            let options_empty_region = document.querySelector('#empty_region');
            if (options_empty_region) {
                options_empty_region.remove();
            }
            let region_id = this.value;
            let region_text = this.options[this.selectedIndex].text;
            if (region_id) {
                let cities0 = regions[region_id];
                if (cities0) {
                    let cities = cities0['cities'];
                    if (cities) {
                        cityOut(cities);
                    }
                }

                let shoose_city = document.querySelector('#shoose_city');
                if (shoose_city) {
                    shoose_city.addEventListener('change', function () {
                        let options_empty_city = document.querySelector('#empty_city');
                        if (options_empty_city) {
                            options_empty_city.remove();
                        }
                        let city_id = this.value;
                        let city_text = this.options[this.selectedIndex].text;

                        saveCity(city_text, region_text, city_id);
                    });
                }
            }
        })
    }
}

function saveCity(city_text, region_text, city_id) {
    let save_city = document.querySelector('#save_city');
    if (save_city) {
        save_city.addEventListener('click', function () {
            //let opt_adress = region_text + ' ' + district_text;
            let opt_adress = region_text;
            setLocality({ city: city_text, adress: opt_adress, id: city_id });
            outLocation({ city: city_text, adress: opt_adress });
            const show_city_select = document.getElementById('show_city_select');
            if (show_city_select) {
                show_city_select.checked = false;
            }
        });
    }
}

export function fromDB() {
    let shoose_location = document.querySelector('#shoose_location');
    if (shoose_location) {
        shoose_location.addEventListener('click', function () {
            hideLocationModal();

            fetch('home.geo/get-all/', {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then(responce => responce.json())
                .then(locations => {
                    let districts = locations['district'];
                    districtOut(districts);
                    regionOutAndCityOutAndSave(districts);
                });
        }, false);

    }
};