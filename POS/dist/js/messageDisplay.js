function ErrorMessageDisplay(message) {
    MessageDisplay("error", "Error: ", message);
}

function SuccessMessageDisplay(message) {
    MessageDisplay("success", "Success: ", message);
}

function InfoMessageDisplay(message) {
    MessageDisplay("info", "Info: ", message);
}

// ==============================================================================

function MessageDisplay(icon, status, message) {
    $("#updatePriceBtn").removeAttr("data-toggle data-target");

    Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    }).fire({
        icon: icon,
        title: status + ": " + message,
    });
}

// ==============================================================================