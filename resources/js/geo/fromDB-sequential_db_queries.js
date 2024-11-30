async function districtFromDB() {
    const mod_1 = document.getElementById('modal_1');
    mod_1.checked = false;

    fetch('home.geo/district/', {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(responce => responce.json())
        .then(district => {
            //console.log(district)
            let inner = '<option>Округ</option>';
            for (const key of Object.keys(district)) {
                // console.log(district[key]['id'] + ' ' + district[key]['name'])
                inner = inner + '<option value="' + district[key]['id'] + '">' + district[key]['name'] + '</option>'
            }
            let shoose_district = document.querySelector('#shoose_district');
            if (shoose_district) {
                shoose_district.innerHTML = inner;
            }
            let shoose_region = document.querySelector('#shoose_region');
            if (shoose_region) {
                shoose_region.innerHTML = '<option>Регион</option>';
            }
            let shoose_city = document.querySelector('#shoose_city');
            if (shoose_city) {
                shoose_city.innerHTML = '<option>Город</option>';
            }
        });
}

async function regionFromDB(district_id) {
    fetch('home.geo/region/' + district_id,
        {
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(responce => responce.json())
        .then(region => {
            let inner = '<option>Регион</option>';
            for (const key of Object.keys(region)) {
                inner = inner + '<option value="' + region[key]['id'] + '">' + region[key]['name'] + '</option>'
            }
            let shoose_region = document.querySelector('#shoose_region');
            if (shoose_region) {
                shoose_region.disabled = false;
                shoose_region.innerHTML = inner;
            }
            let shoose_city = document.querySelector('#shoose_city');
            if (shoose_city) {
                shoose_city.innerHTML = '<option>Город</option>';
            }
        });
}

async function cityFromDB(region_id) {
    fetch('home.geo/city/' + region_id, {
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(responce => responce.json())
        .then(city => {
            let inner = '<option>Город</option>';
            for (const key of Object.keys(city)) {
                inner = inner + '<option value="' + city[key]['id'] + '">' + city[key]['name'] + '</option>'
            }
            let shoose_city = document.querySelector('#shoose_city');
            if (shoose_city) {
                shoose_city.disabled = false;
                shoose_city.innerHTML = inner;
            }
        });
}

function saveToLocalStorage() {
    alert('save')
}

export async function fromDB() {
    let shoose_location = document.querySelector('#shoose_location');
    if (shoose_location) {
        shoose_location.addEventListener('click', districtFromDB)
    }

    let shoose_district = document.querySelector('#shoose_district');
    if (shoose_district) {
        shoose_district.addEventListener('change', function () {
            let district_id = this.value;
            regionFromDB(district_id);
        })
    }

    let shoose_region = document.querySelector('#shoose_region');
    if (shoose_region) {
        shoose_region.addEventListener('change', function () {
            let region_id = this.value;
            cityFromDB(region_id);
        })
    }

    let save_city = document.querySelector('#save_city');
    if (save_city) {
        save_city.addEventListener('click', saveToLocalStorage)
    }
};