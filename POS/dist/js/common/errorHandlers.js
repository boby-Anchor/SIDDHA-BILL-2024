// Session Expiry Handling
function handleExpiredSession(message) {
    ErrorMessageDisplay(message);
    setTimeout(function () {
        window.open("/POS/login.php", "_blank");
    }, 4000);
}