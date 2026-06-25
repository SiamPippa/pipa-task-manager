"use strict";

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll("[data-rich-text]").forEach(function (wrapper) {
        const textarea = wrapper.querySelector("textarea");
        const editorEl = wrapper.querySelector(".rich-text-editor");

        if (!textarea || !editorEl || editorEl.dataset.quillReady === "1") {
            return;
        }

        editorEl.dataset.quillReady = "1";

        const quill = new Quill(editorEl, {
            theme: "snow",
            modules: {
                toolbar: [
                    ["bold", "italic", "underline"],
                    [{ list: "ordered" }, { list: "bullet" }],
                    ["link"],
                    ["clean"],
                ],
            },
        });

        if (textarea.value) {
            quill.root.innerHTML = textarea.value;
        }

        const syncContent = function () {
            textarea.value = quill.root.innerHTML;
        };

        quill.on("text-change", syncContent);

        const form = wrapper.closest("form");

        if (form) {
            form.addEventListener("submit", syncContent);
        }
    });
});
