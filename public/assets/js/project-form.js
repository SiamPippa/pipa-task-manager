"use strict";

(function () {
    const DEFAULT_HOURS_PER_DAY = 8;

    function parseWorkingHours() {
        const element = document.getElementById("company-working-hours");

        if (!element) {
            return {};
        }

        try {
            return JSON.parse(element.textContent || "{}");
        } catch (error) {
            return {};
        }
    }

    function getCompanyId(form) {
        const hidden = form.querySelector('input[name="company_id"]');

        if (hidden) {
            return hidden.value;
        }

        const select = form.querySelector('select[name="company_id"]');

        return select ? select.value : null;
    }

    function getHoursPerDay(companyId, workingHours) {
        if (!companyId) {
            return DEFAULT_HOURS_PER_DAY;
        }

        const hours = workingHours[companyId];

        return hours ? Number(hours) : DEFAULT_HOURS_PER_DAY;
    }

    function isWorkingDay(date) {
        const day = date.getDay();

        return day !== 5 && day !== 6;
    }

    function countWorkingDays(fromValue, toValue) {
        if (!fromValue || !toValue) {
            return 0;
        }

        const from = new Date(fromValue + "T00:00:00");
        const to = new Date(toValue + "T00:00:00");

        if (Number.isNaN(from.getTime()) || Number.isNaN(to.getTime()) || to < from) {
            return 0;
        }

        let count = 0;
        const cursor = new Date(from);

        while (cursor <= to) {
            if (isWorkingDay(cursor)) {
                count += 1;
            }

            cursor.setDate(cursor.getDate() + 1);
        }

        return count;
    }

    function updateEstimatedHours() {
        const form = document.querySelector('form[action*="projects"]');

        if (!form) {
            return;
        }

        const display = form.querySelector("#estimated_hours_display");
        const hint = form.querySelector("#estimated_hours_hint");
        const startDate = form.querySelector("#start_date")?.value;
        const endDate = form.querySelector("#end_date")?.value;
        const workingHours = parseWorkingHours();
        const hoursPerDay = getHoursPerDay(getCompanyId(form), workingHours);
        const workingDays = countWorkingDays(startDate, endDate);

        if (!display) {
            return;
        }

        if (workingDays === 0) {
            display.value = "";
            hint.textContent = startDate && endDate && endDate >= startDate
                ? "No working days in the selected range (Fri–Sat are weekends)."
                : "";

            return;
        }

        const estimated = workingDays * hoursPerDay;

        display.value = Number.isInteger(estimated)
            ? String(estimated)
            : estimated.toFixed(2);
        hint.textContent =
            workingDays +
            " working day" +
            (workingDays === 1 ? "" : "s") +
            " × " +
            hoursPerDay +
            "h/day";
    }

    function bindForm(form) {
        form.querySelector("#start_date")?.addEventListener("change", updateEstimatedHours);
        form.querySelector("#end_date")?.addEventListener("change", updateEstimatedHours);

        const companySelect = form.querySelector('select[name="company_id"]');

        if (companySelect) {
            companySelect.addEventListener("change", updateEstimatedHours);
            $(companySelect).on("change select2:select", updateEstimatedHours);
        }

        updateEstimatedHours();
    }

    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector('form[action*="projects"]');

        if (form) {
            bindForm(form);
        }
    });
})();
