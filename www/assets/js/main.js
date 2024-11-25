/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 53:
/***/ (function(module) {

/*!
 * NetteForms - simple form validation.
 *
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
(function (global, factory) {
	 true ? module.exports = factory() :
	0;
})(this, (function () { 'use strict';

	class Validators {
		filled(elem, arg, val) {
			return val !== '' && val !== false && val !== null
				&& (!Array.isArray(val) || val.length > 0)
				&& (!(val instanceof FileList) || val.length > 0);
		}
		blank(elem, arg, val) {
			return !this.filled(elem, arg, val);
		}
		valid(elem, arg) {
			return arg.validateControl(elem, undefined, true);
		}
		equal(elem, arg, val) {
			if (arg === undefined) {
				return null;
			}
			let toString = (val) => {
				if (typeof val === 'number' || typeof val === 'string') {
					return '' + val;
				}
				else {
					return val === true ? '1' : '';
				}
			};
			let vals = Array.isArray(val) ? val : [val];
			let args = Array.isArray(arg) ? arg : [arg];
			loop: for (let a of vals) {
				for (let b of args) {
					if (toString(a) === toString(b)) {
						continue loop;
					}
				}
				return false;
			}
			return vals.length > 0;
		}
		notEqual(elem, arg, val) {
			return arg === undefined ? null : !this.equal(elem, arg, val);
		}
		minLength(elem, arg, val) {
			val = typeof val === 'number' ? val.toString() : val;
			return val.length >= arg;
		}
		maxLength(elem, arg, val) {
			val = typeof val === 'number' ? val.toString() : val;
			return val.length <= arg;
		}
		length(elem, arg, val) {
			val = typeof val === 'number' ? val.toString() : val;
			arg = Array.isArray(arg) ? arg : [arg, arg];
			return ((arg[0] === null || val.length >= arg[0])
				&& (arg[1] === null || val.length <= arg[1]));
		}
		email(elem, arg, val) {
			return (/^("([ !#-[\]-~]|\\[ -~])+"|[-a-z0-9!#$%&'*+/=?^_`{|}~]+(\.[-a-z0-9!#$%&'*+/=?^_`{|}~]+)*)@([0-9a-z\u00C0-\u02FF\u0370-\u1EFF]([-0-9a-z\u00C0-\u02FF\u0370-\u1EFF]{0,61}[0-9a-z\u00C0-\u02FF\u0370-\u1EFF])?\.)+[a-z\u00C0-\u02FF\u0370-\u1EFF]([-0-9a-z\u00C0-\u02FF\u0370-\u1EFF]{0,17}[a-z\u00C0-\u02FF\u0370-\u1EFF])?$/i).test(val);
		}
		url(elem, arg, val, newValue) {
			if (!(/^[a-z\d+.-]+:/).test(val)) {
				val = 'https://' + val;
			}
			if ((/^https?:\/\/((([-_0-9a-z\u00C0-\u02FF\u0370-\u1EFF]+\.)*[0-9a-z\u00C0-\u02FF\u0370-\u1EFF]([-0-9a-z\u00C0-\u02FF\u0370-\u1EFF]{0,61}[0-9a-z\u00C0-\u02FF\u0370-\u1EFF])?\.)?[a-z\u00C0-\u02FF\u0370-\u1EFF]([-0-9a-z\u00C0-\u02FF\u0370-\u1EFF]{0,17}[a-z\u00C0-\u02FF\u0370-\u1EFF])?|\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|\[[0-9a-f:]{3,39}\])(:\d{1,5})?(\/\S*)?$/i).test(val)) {
				newValue.value = val;
				return true;
			}
			return false;
		}
		regexp(elem, arg, val) {
			let parts = typeof arg === 'string' ? arg.match(/^\/(.*)\/([imu]*)$/) : false;
			try {
				return parts && (new RegExp(parts[1], parts[2].replace('u', ''))).test(val);
			}
			catch {
				return null;
			}
		}
		pattern(elem, arg, val, newValue, caseInsensitive) {
			if (typeof arg !== 'string') {
				return null;
			}
			try {
				let regExp;
				try {
					regExp = new RegExp('^(?:' + arg + ')$', caseInsensitive ? 'ui' : 'u');
				}
				catch {
					regExp = new RegExp('^(?:' + arg + ')$', caseInsensitive ? 'i' : '');
				}
				return val instanceof FileList
					? Array.from(val).every((file) => regExp.test(file.name))
					: regExp.test(val);
			}
			catch {
				return null;
			}
		}
		patternCaseInsensitive(elem, arg, val) {
			return this.pattern(elem, arg, val, null, true);
		}
		numeric(elem, arg, val) {
			return (/^[0-9]+$/).test(val);
		}
		integer(elem, arg, val, newValue) {
			if ((/^-?[0-9]+$/).test(val)) {
				newValue.value = parseFloat(val);
				return true;
			}
			return false;
		}
		float(elem, arg, val, newValue) {
			val = val.replace(/ +/g, '').replace(/,/g, '.');
			if ((/^-?[0-9]*\.?[0-9]+$/).test(val)) {
				newValue.value = parseFloat(val);
				return true;
			}
			return false;
		}
		min(elem, arg, val) {
			if (Number.isFinite(arg)) {
				val = parseFloat(val);
			}
			return val >= arg;
		}
		max(elem, arg, val) {
			if (Number.isFinite(arg)) {
				val = parseFloat(val);
			}
			return val <= arg;
		}
		range(elem, arg, val) {
			if (!Array.isArray(arg)) {
				return null;
			}
			else if (elem.type === 'time' && arg[0] > arg[1]) {
				return val >= arg[0] || val <= arg[1];
			}
			return (arg[0] === null || this.min(elem, arg[0], val))
				&& (arg[1] === null || this.max(elem, arg[1], val));
		}
		submitted(elem) {
			return elem.form['nette-submittedBy'] === elem;
		}
		fileSize(elem, arg, val) {
			return Array.from(val).every((file) => file.size <= arg);
		}
		mimeType(elem, args, val) {
			let parts = [];
			args = Array.isArray(args) ? args : [args];
			args.forEach((arg) => parts.push('^' + arg.replace(/([^\w])/g, '\\$1').replace('\\*', '.*') + '$'));
			let re = new RegExp(parts.join('|'));
			return Array.from(val).every((file) => !file.type || re.test(file.type));
		}
		image(elem, arg, val) {
			return this.mimeType(elem, arg ?? ['image/gif', 'image/png', 'image/jpeg', 'image/webp'], val);
		}
		static(elem, arg) {
			return arg;
		}
	}

	class FormValidator {
		formErrors = [];
		validators = new Validators;
		#preventFiltering = {};
		#formToggles = {};
		#toggleListeners = new WeakMap;
		#getFormElement(form, name) {
			let res = form.elements.namedItem(name);
			return (res instanceof RadioNodeList ? res[0] : res);
		}
		#expandRadioElement(elem) {
			let res = elem.form.elements.namedItem(elem.name);
			return (res instanceof RadioNodeList ? Array.from(res) : [res]);
		}
		/**
		 * Function to execute when the DOM is fully loaded.
		 */
		#onDocumentReady(callback) {
			if (document.readyState !== 'loading') {
				callback.call(this);
			}
			else {
				document.addEventListener('DOMContentLoaded', callback);
			}
		}
		/**
		 * Returns the value of form element.
		 */
		getValue(elem) {
			if (elem instanceof HTMLInputElement) {
				if (elem.type === 'radio') {
					return this.#expandRadioElement(elem)
						.find((input) => input.checked)
						?.value ?? null;
				}
				else if (elem.type === 'file') {
					return elem.files;
				}
				else if (elem.type === 'checkbox') {
					return elem.name.endsWith('[]') // checkbox list
						? this.#expandRadioElement(elem)
							.filter((input) => input.checked)
							.map((input) => input.value)
						: elem.checked;
				}
				else {
					return elem.value.trim();
				}
			}
			else if (elem instanceof HTMLSelectElement) {
				return elem.multiple
					? Array.from(elem.selectedOptions, (option) => option.value)
					: elem.selectedOptions[0]?.value ?? null;
			}
			else if (elem instanceof HTMLTextAreaElement) {
				return elem.value;
			}
			else if (elem instanceof RadioNodeList) {
				return this.getValue(elem[0]);
			}
			else {
				return null;
			}
		}
		/**
		 * Returns the effective value of form element.
		 */
		getEffectiveValue(elem, filter = false) {
			let val = this.getValue(elem);
			if (val === elem.getAttribute('data-nette-empty-value')) {
				val = '';
			}
			if (filter && this.#preventFiltering[elem.name] === undefined) {
				this.#preventFiltering[elem.name] = true;
				let ref = { value: val };
				this.validateControl(elem, undefined, true, ref);
				val = ref.value;
				delete this.#preventFiltering[elem.name];
			}
			return val;
		}
		/**
		 * Validates form element against given rules.
		 */
		validateControl(elem, rules, onlyCheck = false, value, emptyOptional) {
			rules ??= JSON.parse(elem.getAttribute('data-nette-rules') ?? '[]');
			value ??= { value: this.getEffectiveValue(elem) };
			emptyOptional ??= !this.validateRule(elem, ':filled', null, value);
			for (let rule of rules) {
				let op = rule.op.match(/(~)?([^?]+)/), curElem = rule.control ? this.#getFormElement(elem.form, rule.control) : elem;
				rule.neg = !!op[1];
				rule.op = op[2];
				rule.condition = !!rule.rules;
				if (!curElem) {
					continue;
				}
				else if (emptyOptional && !rule.condition && rule.op !== ':filled') {
					continue;
				}
				let success = this.validateRule(curElem, rule.op, rule.arg, elem === curElem ? value : undefined);
				if (success === null) {
					continue;
				}
				else if (rule.neg) {
					success = !success;
				}
				if (rule.condition && success) {
					if (!this.validateControl(elem, rule.rules, onlyCheck, value, rule.op === ':blank' ? false : emptyOptional)) {
						return false;
					}
				}
				else if (!rule.condition && !success) {
					if (this.isDisabled(curElem)) {
						continue;
					}
					if (!onlyCheck) {
						let arr = Array.isArray(rule.arg) ? rule.arg : [rule.arg], message = rule.msg.replace(/%(value|\d+)/g, (foo, m) => this.getValue(m === 'value' ? curElem : elem.form.elements.namedItem(arr[m].control)));
						this.addError(curElem, message);
					}
					return false;
				}
			}
			return true;
		}
		/**
		 * Validates whole form.
		 */
		validateForm(sender, onlyCheck = false) {
			let form = sender.form ?? sender, scope;
			this.formErrors = [];
			if (sender.getAttribute('formnovalidate') !== null) {
				let scopeArr = JSON.parse(sender.getAttribute('data-nette-validation-scope') ?? '[]');
				if (scopeArr.length) {
					scope = new RegExp('^(' + scopeArr.join('-|') + '-)');
				}
				else {
					this.showFormErrors(form, []);
					return true;
				}
			}
			for (let elem of form.elements) {
				if (elem.willValidate && elem.validity.badInput) {
					elem.reportValidity();
					return false;
				}
			}
			for (let elem of form.elements) {
				if (elem.getAttribute('data-nette-rules')
					&& (!scope || elem.name.replace(/]\[|\[|]|$/g, '-').match(scope))
					&& !this.isDisabled(elem)
					&& !this.validateControl(elem, undefined, onlyCheck)
					&& !this.formErrors.length) {
					return false;
				}
			}
			let success = !this.formErrors.length;
			this.showFormErrors(form, this.formErrors);
			return success;
		}
		/**
		 * Check if input is disabled.
		 */
		isDisabled(elem) {
			if (elem.type === 'radio') {
				return this.#expandRadioElement(elem)
					.every((input) => input.disabled);
			}
			return elem.disabled;
		}
		/**
		 * Adds error message to the queue.
		 */
		addError(elem, message) {
			this.formErrors.push({
				element: elem,
				message: message,
			});
		}
		/**
		 * Display error messages.
		 */
		showFormErrors(form, errors) {
			let messages = [], focusElem;
			for (let error of errors) {
				if (messages.indexOf(error.message) < 0) {
					messages.push(error.message);
					focusElem ??= error.element;
				}
			}
			if (messages.length) {
				this.showModal(messages.join('\n'), () => {
					focusElem?.focus();
				});
			}
		}
		/**
		 * Display modal window.
		 */
		showModal(message, onclose) {
			let dialog = document.createElement('dialog');
			if (!dialog.showModal) {
				alert(message);
				onclose();
				return;
			}
			let style = document.createElement('style');
			style.innerText = '.netteFormsModal { text-align: center; margin: auto; border: 2px solid black; padding: 1rem } .netteFormsModal button { padding: .1em 2em }';
			let button = document.createElement('button');
			button.innerText = 'OK';
			button.onclick = () => {
				dialog.remove();
				onclose();
			};
			dialog.setAttribute('class', 'netteFormsModal');
			dialog.innerText = message + '\n\n';
			dialog.append(style, button);
			document.body.append(dialog);
			dialog.showModal();
		}
		/**
		 * Validates single rule.
		 */
		validateRule(elem, op, arg, value) {
			if (elem.validity.badInput) {
				return op === ':filled';
			}
			value ??= { value: this.getEffectiveValue(elem, true) };
			let method = op.charAt(0) === ':' ? op.substring(1) : op;
			method = method.replace('::', '_').replaceAll('\\', '');
			let args = Array.isArray(arg) ? arg : [arg];
			args = args.map((arg) => {
				if (arg?.control) {
					let control = this.#getFormElement(elem.form, arg.control);
					return control === elem ? value.value : this.getEffectiveValue(control, true);
				}
				return arg;
			});
			if (method === 'valid') {
				args[0] = this; // todo
			}
			return this.validators[method]
				? this.validators[method](elem, Array.isArray(arg) ? args : args[0], value.value, value)
				: null;
		}
		/**
		 * Process all toggles in form.
		 */
		toggleForm(form, event) {
			this.#formToggles = {};
			for (let elem of Array.from(form.elements)) {
				if (elem.getAttribute('data-nette-rules')) {
					this.toggleControl(elem, undefined, null, !event);
				}
			}
			for (let i in this.#formToggles) {
				this.toggle(i, this.#formToggles[i].state, this.#formToggles[i].elem, event);
			}
		}
		/**
		 * Process toggles on form element.
		 */
		toggleControl(elem, rules, success = null, firsttime = false, value, emptyOptional) {
			rules ??= JSON.parse(elem.getAttribute('data-nette-rules') ?? '[]');
			value ??= { value: this.getEffectiveValue(elem) };
			emptyOptional ??= !this.validateRule(elem, ':filled', null, value);
			let has = false, curSuccess;
			for (let rule of rules) {
				let op = rule.op.match(/(~)?([^?]+)/), curElem = rule.control ? this.#getFormElement(elem.form, rule.control) : elem;
				rule.neg = !!op[1];
				rule.op = op[2];
				rule.condition = !!rule.rules;
				if (!curElem) {
					continue;
				}
				else if (emptyOptional && !rule.condition && rule.op !== ':filled') {
					continue;
				}
				curSuccess = success;
				if (success !== false) {
					curSuccess = this.validateRule(curElem, rule.op, rule.arg, elem === curElem ? value : undefined);
					if (curSuccess === null) {
						continue;
					}
					else if (rule.neg) {
						curSuccess = !curSuccess;
					}
					if (!rule.condition) {
						success = curSuccess;
					}
				}
				if ((rule.condition && this.toggleControl(elem, rule.rules, curSuccess, firsttime, value, rule.op === ':blank' ? false : emptyOptional)) || rule.toggle) {
					has = true;
					if (firsttime) {
						this.#expandRadioElement(curElem)
							.filter((el) => !this.#toggleListeners.has(el))
							.forEach((el) => {
							el.addEventListener('change', (e) => this.toggleForm(elem.form, e));
							this.#toggleListeners.set(el, null);
						});
					}
					for (let id in rule.toggle ?? {}) {
						this.#formToggles[id] ??= { elem: elem, state: false };
						this.#formToggles[id].state ||= rule.toggle[id] ? !!curSuccess : !curSuccess;
					}
				}
			}
			return has;
		}
		/**
		 * Displays or hides HTML element.
		 */
		toggle(selector, visible, srcElement, event) {
			if (/^\w[\w.:-]*$/.test(selector)) { // id
				selector = '#' + selector;
			}
			Array.from(document.querySelectorAll(selector))
				.forEach((elem) => elem.hidden = !visible);
		}
		/**
		 * Compact checkboxes
		 */
		compactCheckboxes(form, formData) {
			let values = {};
			for (let elem of form.elements) {
				if (elem instanceof HTMLInputElement && elem.type === 'checkbox' && elem.name.endsWith('[]') && elem.checked && !elem.disabled) {
					formData.delete(elem.name);
					values[elem.name] ??= [];
					values[elem.name].push(elem.value);
				}
			}
			for (let name in values) {
				formData.set(name.substring(0, name.length - 2), values[name].join(','));
			}
		}
		/**
		 * Setup handlers.
		 */
		initForm(form) {
			if (form.method === 'get' && form.hasAttribute('data-nette-compact')) {
				form.addEventListener('formdata', (e) => this.compactCheckboxes(form, e.formData));
			}
			if (!Array.from(form.elements).some((elem) => elem.getAttribute('data-nette-rules'))) {
				return;
			}
			this.toggleForm(form);
			if (form.noValidate) {
				return;
			}
			form.noValidate = true;
			form.addEventListener('submit', (e) => {
				if (!this.validateForm((e.submitter || form))) {
					e.stopPropagation();
					e.preventDefault();
				}
			});
			form.addEventListener('reset', () => {
				setTimeout(() => this.toggleForm(form));
			});
		}
		initOnLoad() {
			this.#onDocumentReady(() => {
				Array.from(document.forms)
					.forEach((form) => this.initForm(form));
			});
		}
	}

	let webalizeTable = { \u00e1: 'a', \u00e4: 'a', \u010d: 'c', \u010f: 'd', \u00e9: 'e', \u011b: 'e', \u00ed: 'i', \u013e: 'l', \u0148: 'n', \u00f3: 'o', \u00f4: 'o', \u0159: 'r', \u0161: 's', \u0165: 't', \u00fa: 'u', \u016f: 'u', \u00fd: 'y', \u017e: 'z' };
	/**
	 * Converts string to web safe characters [a-z0-9-] text.
	 * @param {string} s
	 * @return {string}
	 */
	function webalize(s) {
		s = s.toLowerCase();
		let res = '';
		for (let i = 0; i < s.length; i++) {
			let ch = webalizeTable[s.charAt(i)];
			res += ch ? ch : s.charAt(i);
		}
		return res.replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
	}

	var version = "3.5.2";

	let nette = new FormValidator;
	nette.version = version;
	nette.webalize = webalize;

	return nette;

}));


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/************************************************************************/
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";

// EXTERNAL MODULE: ./node_modules/nette-forms/src/assets/netteForms.js
var netteForms = __webpack_require__(53);
var netteForms_default = /*#__PURE__*/__webpack_require__.n(netteForms);
;// ./resources/js/geo/OutLocationOnPage.js
function outLocation({ city, adress }) {
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

;// ./config/yandex_api.js
const yapikey = '62e46ec4-c446-4e02-b257-50b67d420173';
;// ./resources/js/geo/locationFromYandexGeocoder.js


async function locationFromYandexGeocoder(yapikey, { long, lat }, format = 'json', kind = 'locality', results = 1) {
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
;// ./resources/js/geo/geoLocation.js

// for city getting from Yandex Map API



//-------- part to be performed on the page ---------------------
function geoLocation() {
    document.addEventListener('DOMContentLoaded', () => {
        // если на странице есть эемент с id="location" и 
        // если его содержимое равно "Местоположение" (значит на сервере положение не было определено),
        // определим местоположение
        let city_elem = document.getElementById("location");
        if (city_elem) {
            let city = city_elem.innerHTML;
            const substring = "Местоположение";

            if (city && city.includes(substring)) {

                function getLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(getCoords, showError);
                    } else {
                        console.warning("WARNING! Geolocation is not supported by this browser.");
                    }
                }

                function getCoords(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    let longLat = { long: longitude, lat: latitude };
                    if (typeof longLat === "undefined") {
                        outLocation({ city: '', adress: '' });
                    } else {
                        locationFromYandexGeocoder(yapikey, longLat);
                    }
                }

                function showError(error) {
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            console.error("ERROR! User denied the request for Geolocation.")
                            break;
                        case error.POSITION_UNAVAILABLE:
                            console.error("ERROR! Location information is unavailable.")
                            break;
                        case error.TIMEOUT:
                            console.error("ERROR! The request to get user location timed out.")
                            break;
                        case error.UNKNOWN_ERROR:
                            console.error("ERROR! An unknown error occurred.")
                            break;
                    }
                }

                getLocation();

            }
        }
    });
}
;// ./resources/js/main.js

window.Nette = (netteForms_default());
netteForms_default().initOnLoad();



geoLocation();


/* <!-- js for esc on modal (in Home part of site that based on PicnicCSS) --> */
document.onkeydown = function (event) {
    if (event.key == "Escape") {
        var mods = document.querySelectorAll('.modal > [type=checkbox]');
        [].forEach.call(mods, function (mod) { mod.checked = false; });
    }
}
})();

// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
// extracted by mini-css-extract-plugin

})();

/******/ })()
;