function districtOut(locations) {
    let inner = '<option value="">Округ</option>';
    let districts = locations['district']
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

function regionOut(locations, district_id) {
    let inner = '<option value="">Регион</option>';
    let regions = locations['district'][district_id]['regions'];
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

function cityOut(locations, district_id, region_id) {
    let cities = locations['district'][district_id]['regions'][region_id]['cities'];
    let inner = '<option value="">Город</option>';
    for (const key of Object.keys(cities)) {
        inner = inner + '<option value="' + cities[key]['id'] + '">' + cities[key]['name'] + '</option>'
    }
    let shoose_city = document.querySelector('#shoose_city');
    if (shoose_city) {
        shoose_city.disabled = false;
        shoose_city.innerHTML = inner;
    }

}


function saveToLocalStorage() {

    alert('save')
}

function hideLocationModal() {
    const mod_1 = document.getElementById('modal_1');
    mod_1.checked = false;
}

function locationOut() {
    hideLocationModal();

    fetch('home.geo/get-all/', {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(responce => responce.json())
        .then(locations => {
            //console.log(locations);
            districtOut(locations);

            let shoose_district = document.querySelector('#shoose_district');
            if (shoose_district) {
                shoose_district.addEventListener('change', function () {
                    let district_id = this.value;
                    console.info(district_id)
                    if (district_id) {
                        regionOut(locations, district_id);
                        let shoose_region = document.querySelector('#shoose_region');
                        if (shoose_region) {
                            shoose_region.addEventListener('change', function () {
                                let region_id = this.value;
                                if (region_id) {
                                    cityOut(locations, district_id, region_id);

                                    let shoose_city = document.querySelector('#shoose_city');
                                    if (shoose_city) {
                                        shoose_city.addEventListener('change', function () {
                                            let save_city = document.querySelector('#save_city');
                                            if (save_city) {
                                                save_city.addEventListener('click', saveToLocalStorage)
                                            }
                                        });
                                    }
                                }
                            })
                        }
                    }
                })
            }
        });
}

export function fromDB() {
    let shoose_location = document.querySelector('#shoose_location');
    if (shoose_location) {
        shoose_location.addEventListener('click', locationOut)
    }
};