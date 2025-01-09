import { Maskito, MaskitoOptions } from '@maskito/core';

import { maskitoPhoneOptionsGenerator } from '@maskito/phone';
import metadata from 'libphonenumber-js/min/metadata';

let maskitoOptions = maskitoPhoneOptionsGenerator({ countryIsoCode: 'RU', metadata });

const element = document.querySelector('#user_phone_input');
const maskedInput = new Maskito(element, maskitoOptions);

// Call this function when the element is detached from DOM
//maskedInput.destroy();