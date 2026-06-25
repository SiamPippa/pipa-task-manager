"use strict";

document.addEventListener("DOMContentLoaded", function () {
    const titleInput = document.getElementById("title");
    const branchInput = document.getElementById("branch_name");

    if (!titleInput || !branchInput) {
        return;
    }

    function slugify(value) {
        return String(value || "")
            .toLowerCase()
            .trim()
            .replace(/[^a-z0-9\s-]/g, "")
            .replace(/[\s_]+/g, "-")
            .replace(/-+/g, "-")
            .replace(/^-+|-+$/g, "")
            .slice(0, 60);
    }

    let branchManuallyEdited =
        branchInput.value !== "" &&
        branchInput.value !== slugify(titleInput.value);

    branchInput.addEventListener("input", function () {
        branchManuallyEdited = true;
    });

    titleInput.addEventListener("input", function () {
        if (branchManuallyEdited) {
            return;
        }

        branchInput.value = slugify(titleInput.value);
    });
});
