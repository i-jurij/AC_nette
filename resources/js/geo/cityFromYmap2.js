/**
* get city from yandex map api
*/
export let ymap_url = 'https://api-maps.yandex.ru/2.1/?apikey={YANDEX_API_KEY}&lang=ru_RU';
export let city_from_ymap = function () {
    let city_elem = document.getElementById("location");
    if (city_elem) {
        let city = city_elem.innerHTML; // !!! get var from php var from @layout-header.latte from Basepresenter

        ymaps.ready(init);

        function init() {
            try {
                ymaps.geolocation.get({
                    // Зададим способ определения геолокации на основе ip пользователя.
                    // provider: 'yandex',
                    // Зададим способ определения геолокации на основе данных браузера.
                    //provider: 'browser',
                    // пусть яндекс сам выберет лучший способ определения геолокации.
                    provider: 'auto', // auto - либо от браузера, либо по IP
                }).then(function (result) {
                    const x = document.getElementById("location");
                    const clients_place_message = document.getElementById("clients_place_message");
                    const button_shoose_place = document.getElementById("shoose_clients_place");
                    const checkbox_modal_window = document.getElementById('modal_1');

                    //var location = result.geoObjects.get(0).properties.get('name');
                    //var userAddress = result.geoObjects.get(0).properties.get('text');

                    let location = result.geoObjects.get(0);
                    //let adress = location.getAddressLine();
                    let adress = location.properties.get('balloonContent');
                    city = location.getLocalities();

                    if (x && city.length > 0) {
                        x.innerHTML = city + "&ensp;&#8250;";
                        let adr = adress ? adress : '<p>' + city + '.</p>';
                        clients_place_message.innerHTML = 'Ваше местоположение:</br> ' + adr + ' Если нет - выберите его, нажав на кнопку "Выбрать"';
                        checkbox_modal_window.checked = true;
                    }
                    if (x && city.length == 0) {
                        if (clients_place_message) {
                            clients_place_message.innerHTML = 'Ваше местоположение неизвестно.</br> Выберите его, нажав на кнопку "Выбрать"';
                            checkbox_modal_window.checked = true;

                            function blink_elem(elem) {
                                count = 0;
                                var x = setInterval(function () {
                                    elem.style.visibility = (elem.style.visibility == 'visible' ? 'hidden' : 'visible');
                                    if (count >= 5) {
                                        clearInterval(x);
                                        elem.style.visibility == 'visible';
                                        elem.focus();
                                    }
                                    count++;
                                }, 500);
                            }

                            blink_elem(button_shoose_place);
                        }

                    }
                });
            } catch (error) {
                console.log(error);
            }

        }
    }
};