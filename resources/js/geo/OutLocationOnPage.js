export function outLocation({ city, adress }) {
    const city_elem = document.getElementById("location");
    const clients_place_message = document.getElementById("clients_place_message");
    const button_shoose_place = document.getElementById("shoose_clients_place");
    const checkbox_modal_window = document.getElementById('modal_1');

    if (city_elem && city.length > 0) {
        city_elem.innerHTML = city + "&ensp;&#8250;";
        let adr = '';
        if (adress && adress.includes(city)) {
            adr = '<div class="my2">' + adress + '</div>';
        } else if (adress && !adress.includes(city)) {
            adr = '<div class="mt2">' + city + '</div><div class="mb2">' + adress + '</div>';
        } else {
            '<div class="my2">' + city + '.</div>'
        }

        clients_place_message.innerHTML = 'Ваше местоположение: ' + adr + ' Если нет - выберите его, нажав на кнопку "Выбрать"';
        //checkbox_modal_window.checked = true;
    }
    if (city_elem && city.length == 0) {
        if (clients_place_message) {
            clients_place_message.innerHTML = 'Ваше местоположение неизвестно. </br>Выберите его, нажав на кнопку "Выбрать"';
            checkbox_modal_window.checked = true;

            function blink_elem(elem) {
                let count = 0;
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
