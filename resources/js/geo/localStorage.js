export function getLocality() {
    return JSON.parse(localStorage.getItem('locality'));
}

export function setLocality({ city, adress = '', id = '' }) {
    let data_object = { city, adress, id };
    localStorage.setItem('locality', JSON.stringify(data_object));
}

export function removeLocality() {
    localStorage.removeItem("locality");
}