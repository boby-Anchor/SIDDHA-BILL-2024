// Session Expiry Handling
function handleExpiredSession(message) {
    ErrorMessageDisplay(message);
    setTimeout(function () {
        window.open(window.location.href, "_blank");
    }, 4000);
}