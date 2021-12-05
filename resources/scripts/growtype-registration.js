$(document).ready(function () {
    if (window.location.search.length > 0 && window.location.search.indexOf('action') !== -1) {
        window.history.replaceState(null, null, window.location.pathname);
    } else if (window.location.search.length > 0 && window.location.search.indexOf('message') !== -1) {
        window.history.replaceState(null, null, window.location.pathname);
    }
});
