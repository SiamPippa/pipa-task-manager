"use strict";

$(function () {
    const lookupBaseUrl =
        document.querySelector('meta[name="lookup-base-url"]')?.content ||
        "/lookup";

    function parseNames(value) {
        return String(value || "")
            .split(",")
            .map(function (name) {
                return name.trim();
            })
            .filter(Boolean);
    }

    function getFormScope($element) {
        const $form = $element.closest("form");

        return $form.length ? $form : $(document);
    }

    function getFieldValue(name, $scope) {
        const $context = $scope && $scope.length ? $scope : $(document);
        const $select = $context.find('select[name="' + name + '"]');

        if ($select.length) {
            return $select.first().val();
        }

        const $input = $context.find('input[name="' + name + '"]');

        if ($input.length) {
            return $input.first().val();
        }

        return null;
    }

    function getLookupParams($select) {
        const $scope = getFormScope($select);
        const dependsOn = parseNames($select.data("depends-on"));
        const filterOn = parseNames($select.data("filter-on"));
        const values = {};
        let ready = true;

        dependsOn.forEach(function (name) {
            const value = getFieldValue(name, $scope);

            if (!value) {
                ready = false;
            } else {
                values[name] = value;
            }
        });

        filterOn.forEach(function (name) {
            const value = getFieldValue(name, $scope);

            if (value) {
                values[name] = value;
            }
        });

        return { values, ready };
    }

    function buildLookupUrl(lookup, values) {
        if (lookup === "users") {
            const excludeId = document.querySelector(
                'meta[name="lookup-exclude-user-id"]',
            )?.content;

            if (excludeId) {
                values.exclude_id = excludeId;
            }
        }

        const params = new URLSearchParams(values);

        return lookupBaseUrl + "/" + lookup + "?" + params.toString();
    }

    function updateSelectOptions($select, items, selectedValue) {
        const emptyOption = $select.data("empty-option");
        const hasEmptyOption =
            emptyOption !== false && emptyOption !== undefined;
        const placeholder =
            $select.data("placeholder") || "Search and select...";

        $select.empty();

        if (hasEmptyOption) {
            $select.append(
                new Option(
                    emptyOption || placeholder,
                    "",
                    false,
                    !selectedValue,
                ),
            );
        }

        items.forEach(function (item) {
            const isSelected = String(item.id) === String(selectedValue);
            $select.append(new Option(item.label, item.id, false, isSelected));
        });

        if ($select.hasClass("select2-hidden-accessible")) {
            $select.trigger("change.select2");
        } else {
            $select.trigger("change");
        }
    }

    function refreshDependentSelect($select, preserveValue) {
        const lookup = $select.data("lookup");
        const dependsOn = parseNames($select.data("depends-on"));

        if (!lookup) {
            return;
        }

        const currentValue = preserveValue ? $select.val() : "";
        const paramState = getLookupParams($select);

        function setDisabledState(isDisabled) {
            $select.prop("disabled", isDisabled);

            if ($select.hasClass("select2-hidden-accessible")) {
                $select.trigger("change.select2");
            }
        }

        if (dependsOn.length && !paramState.ready) {
            updateSelectOptions($select, [], "");
            setDisabledState(true);

            return;
        }

        setDisabledState(true);

        $.getJSON(buildLookupUrl(lookup, paramState.values))
            .done(function (items) {
                const selectedValue =
                    preserveValue &&
                    currentValue &&
                    items.some(function (item) {
                        return String(item.id) === String(currentValue);
                    })
                        ? currentValue
                        : "";

                updateSelectOptions($select, items, selectedValue);
                setDisabledState(false);
            })
            .fail(function () {
                updateSelectOptions($select, [], "");
                setDisabledState(true);
            });
    }

    function getWatchedParentNames($select) {
        return Array.from(
            new Set(
                parseNames($select.data("depends-on")).concat(
                    parseNames($select.data("filter-on")),
                ),
            ),
        );
    }

    function getDependentSelects(parentName, $scope) {
        const $context = $scope && $scope.length ? $scope : $(document);

        return $context.find(".searchable-select[data-lookup]").filter(function () {
            return getWatchedParentNames($(this)).includes(parentName);
        });
    }

    function onParentChange(parentName, $scope) {
        getDependentSelects(parentName, $scope).each(function () {
            const $child = $(this);
            const dependsOn = parseNames($child.data("depends-on"));
            const shouldPreserve = !dependsOn.includes(parentName);

            refreshDependentSelect($child, shouldPreserve);

            const childName = $child.attr("name");

            if (childName) {
                getDependentSelects(childName, $scope).each(function () {
                    refreshDependentSelect($(this), false);
                });
            }
        });
    }

    const boundParents = new Set();

    function parentBindingKey(parentName, $scope) {
        const scopeId =
            $scope.attr("id") ||
            $scope.data("dependent-scope") ||
            String($("form").index($scope));

        return parentName + "::" + scopeId;
    }

    function bindParentField(parentName, $scope) {
        const key = parentBindingKey(parentName, $scope);

        if (boundParents.has(key)) {
            return;
        }

        boundParents.add(key);

        $scope
            .find(
                'select[name="' +
                    parentName +
                    '"], input[name="' +
                    parentName +
                    '"]',
            )
            .on("change", function () {
                onParentChange(parentName, $scope);
            });
    }

    function bootstrapLockedCompanyFields() {
        $("form").each(function () {
            const $form = $(this);
            const companyId = getFieldValue("company_id", $form);

            if (!companyId || !getDependentSelects("company_id", $form).length) {
                return;
            }

            const $companySelect = $form.find('select[name="company_id"]');
            const hasLockedCompany =
                $companySelect.length === 0 ||
                $companySelect.is(":disabled") ||
                $companySelect.is("[data-fixed-company]");

            if (hasLockedCompany) {
                onParentChange("company_id", $form);
            }
        });
    }

    $("form").on("submit", function () {
        $(this)
            .find("select.searchable-select:disabled")
            .prop("disabled", false);
    });

    $(".searchable-select[data-lookup]").each(function () {
        const $select = $(this);
        const $scope = getFormScope($select);
        const dependsOn = parseNames($select.data("depends-on"));

        getWatchedParentNames($select).forEach(function (parentName) {
            bindParentField(parentName, $scope);
        });

        if (dependsOn.length) {
            const paramState = getLookupParams($select);

            if (!paramState.ready) {
                $select.prop("disabled", true);

                if ($select.hasClass("select2-hidden-accessible")) {
                    $select.trigger("change.select2");
                }
            } else {
                refreshDependentSelect($select, true);
            }
        }
    });

    bootstrapLockedCompanyFields();
});
