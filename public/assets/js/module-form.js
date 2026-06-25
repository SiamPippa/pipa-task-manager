"use strict";

(function () {
    const DEFAULT_HOURS_PER_DAY = 8;
    let projectContext = null;

    function lookupBaseUrl() {
        return (
            document.querySelector('meta[name="lookup-base-url"]')?.content ||
            "/lookup"
        );
    }

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

    function getProjectId(form) {
        const select = form.querySelector('select[name="project_id"]');

        return select ? select.value : null;
    }

    function resolveHoursPerDay(form) {
        if (projectContext?.hours_per_day) {
            return Number(projectContext.hours_per_day);
        }

        const workingHours = parseWorkingHours();
        const hours = workingHours[getCompanyId(form)];

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

    function applyDateBounds(form) {
        const startInput = form.querySelector("#start_date");
        const endInput = form.querySelector("#end_date");

        if (!startInput || !endInput) {
            return;
        }

        if (projectContext?.start_date) {
            startInput.min = projectContext.start_date;
            endInput.min = projectContext.start_date;
        } else {
            startInput.removeAttribute("min");
            endInput.removeAttribute("min");
        }

        if (projectContext?.end_date) {
            startInput.max = projectContext.end_date;
            endInput.max = projectContext.end_date;
        } else {
            startInput.removeAttribute("max");
            endInput.removeAttribute("max");
        }
    }

    function updateEstimatedHours(form) {
        const display = form.querySelector("#estimated_hours_display");
        const hint = form.querySelector("#estimated_hours_hint");
        const startDate = form.querySelector("#start_date")?.value;
        const endDate = form.querySelector("#end_date")?.value;
        const hoursPerDay = resolveHoursPerDay(form);
        const workingDays = countWorkingDays(startDate, endDate);

        if (!display) {
            return;
        }

        if (workingDays === 0) {
            display.value = "";
            hint.textContent = startDate && endDate && endDate >= startDate
                ? "No working days in the selected range (Fri–Sat are weekends)."
                : projectContext?.estimated_hours != null
                  ? "Project total budget: " + projectContext.estimated_hours + "h (max per module)."
                  : "";

            return;
        }

        const estimated = workingDays * hoursPerDay;

        display.value = Number.isInteger(estimated)
            ? String(estimated)
            : estimated.toFixed(2);

        let hintText =
            workingDays +
            " working day" +
            (workingDays === 1 ? "" : "s") +
            " × " +
            hoursPerDay +
            "h/day";

        if (projectContext?.estimated_hours != null) {
            hintText += ". Project total budget: " + projectContext.estimated_hours + "h (max per module).";

            if (estimated > Number(projectContext.estimated_hours)) {
                hintText += " Exceeds project total.";
                display.classList.add("is-invalid");
            } else {
                display.classList.remove("is-invalid");
            }
        }

        hint.textContent = hintText;
    }

    function loadProjectContext(form) {
        const projectId = getProjectId(form);

        if (!projectId) {
            projectContext = null;
            applyDateBounds(form);
            updateEstimatedHours(form);

            return;
        }

        $.getJSON(lookupBaseUrl() + "/project-module-context", { project_id: projectId })
            .done(function (data) {
                projectContext = data && data.start_date ? data : null;
                applyDateBounds(form);
                updateEstimatedHours(form);
            })
            .fail(function () {
                projectContext = null;
                applyDateBounds(form);
                updateEstimatedHours(form);
            });
    }

    function bindForm(form) {
        form.querySelector("#start_date")?.addEventListener("change", function () {
            updateEstimatedHours(form);
        });
        form.querySelector("#end_date")?.addEventListener("change", function () {
            updateEstimatedHours(form);
        });

        const projectSelect = form.querySelector('select[name="project_id"]');

        if (projectSelect) {
            const onProjectChange = function () {
                loadProjectContext(form);
            };

            projectSelect.addEventListener("change", onProjectChange);
            $(projectSelect).on("change select2:select", onProjectChange);
        }

        const companySelect = form.querySelector('select[name="company_id"]');

        if (companySelect) {
            const onCompanyChange = function () {
                projectContext = null;
                applyDateBounds(form);
                updateEstimatedHours(form);
            };

            companySelect.addEventListener("change", onCompanyChange);
            $(companySelect).on("change select2:select", onCompanyChange);
        }

        loadProjectContext(form);
    }

    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector('form[action*="project-modules"]');

        if (form) {
            bindForm(form);
        }
    });
})();
