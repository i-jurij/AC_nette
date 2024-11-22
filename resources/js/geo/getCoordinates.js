export function getCoordinates(params = null) {

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
        let latLong = { lat: latitude, long: longitude };
        return latLong;
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

    return getLocation();
}