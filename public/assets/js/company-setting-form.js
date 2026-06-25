"use strict";

document.addEventListener("DOMContentLoaded", function () {
    const startInput = document.getElementById("office_start_time");
    const endInput = document.getElementById("office_end_time");
    const form = startInput?.closest("form");

    if (!startInput || !endInput || !form) {
        return;
    }

    const errorMessage =
        "Office end time must be greater than office start time.";

    function validateOfficeHours() {
        const start = startInput.value;
        const end = endInput.value;

        if (!start || !end) {
            endInput.setCustomValidity("");

            return true;
        }

        if (end <= start) {
            endInput.setCustomValidity(errorMessage);

            return false;
        }

        endInput.setCustomValidity("");

        return true;
    }

    startInput.addEventListener("input", validateOfficeHours);
    startInput.addEventListener("change", validateOfficeHours);
    endInput.addEventListener("input", validateOfficeHours);
    endInput.addEventListener("change", validateOfficeHours);

    form.addEventListener("submit", function (event) {
        if (!validateOfficeHours()) {
            event.preventDefault();
            endInput.reportValidity();
        }
    });
});
