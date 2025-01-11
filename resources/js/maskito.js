import { maskitoPhoneOptionsGenerator } from '@maskito/phone';
import metadata from 'libphonenumber-js/mobile/metadata';
// import { isValidPhoneNumber } from 'libphonenumber-js/mobile';
import { Maskito, MaskitoOptions } from '@maskito/core';

const country = 'RU';

let options = maskitoPhoneOptionsGenerator({ countryIsoCode: country, metadata });

document.addEventListener('DOMContentLoaded', function () {
    const element = window.Main.phone_elem;
    if (element) {
        const maskedInput = new Maskito(element, options);
        /*
        element.addEventListener('change', function () {
            let phone_value = element.value;
            if (phone_value) {
                console.log(isValidPhoneNumber(phone_value, country))
                if (isValidPhoneNumber(phone_value, country) != true) {
                    let et = '<small>Ошибка в номере телефона</small>';
                    window.Main.jsModaFlash(et, window.Main.elements);
                }
            }
        });
        */
    } else {
        console.warn('user_phone_input not exist on page')
    }
});

// Call this function when the element is detached from DOM
//maskedInput.destroy();
