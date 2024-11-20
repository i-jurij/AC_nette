/**
* get city from yandex map api
*/
export let ymap_url = "https://api-maps.yandex.ru/2.1/?apikey=62e46ec4-c446-4e02-b257-50b67d420173&lang=ru_RU";
export let city_from_ymap = function () {
    let city = document.getElementById("location").innerHTML; // !!! get var from php var from @layout-header.latte from Basepresenter
    const substring = "Местоположение";
    if (city.includes(substring)) {
        ymaps.ready(init);

        function init() {
            ymaps.geolocation.get({
                provider: 'auto', // auto - либо от браузера, либо по IP
            }).then(function (result) {
                coords = result.geoObjects.get(0).geometry.getCoordinates();
                ymaps.geocode(
                    coords,
                    { results: 1 }
                ).then(function (res) {
                    var location = res.geoObjects.get(0);
                    const x = document.getElementById("location");
                    const clients_place_message = document.getElementById("clients_place_message");
                    const button_shoose_place = document.getElementById("shoose_clients_place");
                    const checkbox_modal_window = document.getElementById('modal_1');

                    city = location.getLocalities();

                    if (x && city.length > 0) {
                        x.innerHTML = city + "&ensp;&#8250;";
                        clients_place_message.innerHTML = 'Ваше местоположение: ' + city + '. Если нет - выберите его, нажав на кнопку "Выбрать"';
                        checkbox_modal_window.checked = true;
                    }
                    if (x && city.length == 0) {
                        if (clients_place_message) {
                            clients_place_message.innerHTML = 'Ваше местоположение неизвестно. Выберите его, нажав на кнопку "Выбрать"';
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

                }
                );
            });
        }
    }
};