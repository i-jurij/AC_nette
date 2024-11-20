export let geoplugin_url = "http://www.geoplugin.net/javascript.gp";
export function city_from_geoplugin() {
    let city = document.getElementById("location").innerHTML; // !!! get var from php var from @layout-header.latte from Basepresenter
    const substring = "Местоположение";
    if (city.includes(substring)) {
        const x = document.getElementById("location");
        city = geoplugin_city();
        const clients_place_message = document.getElementById("clients_place_message");
        const button_shoose_place = document.getElementById("shoose_clients_place");
        const checkbox_modal_window = document.getElementById('modal_1');

        if (x && city.length > 0) {
            x.innerHTML = city + "&ensp;&#8250;";
            clients_place_message.innerHTML = 'Ваше местоположение: ' + city + '. </br>Если нет - выберите его, нажав на кнопку "Выбрать"';
            checkbox_modal_window.checked = true;
        }
        if (x && city.length == 0) {
            if (clients_place_message) {
                clients_place_message.innerHTML = 'Ваше местоположение неизвестно. </br>Выберите его, нажав на кнопку "Выбрать"';
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
}