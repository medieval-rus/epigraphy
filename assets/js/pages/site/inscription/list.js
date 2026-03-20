/*
 * This file is part of «Epigraphy of Medieval Rus» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus» database is distributed
 * in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus» database,
 * see <http://www.gnu.org/licenses/>.
 */

import $ from 'jquery';
import 'bootstrap';
import 'select2';
import 'jquery-ui/ui/widgets/slider';
import * as common from './common';

window.superFilters = { // mapping of parent and child categories
    "city": {"subfilter": "discovery-site", "cache": []}
}

const hierarchicalFilterIds = ['carrier-category', 'material', 'writing-method', 'content-category'];
const previousSelections = {};
const selectionSyncLocks = {};

$(window).on('load', () => {
    common.initializeFilters();
    initializeHierarchicalFilters();
    setUpdateListeners();
    common.enableVirtualKeyboards();
    common.enableRowClickNavigation();

    // Delay the date slider initialization to ensure DOM is fully ready
    setTimeout(() => {
        initializeDateSlider();
    }, 100);
});

function initializeHierarchicalFilters() {
    for (const filterId of hierarchicalFilterIds) {
        const selectElement = document.getElementById(filterId);

        if (!selectElement) {
            continue;
        }

        previousSelections[filterId] = new Set($(selectElement).val() || []);
        selectionSyncLocks[filterId] = false;

        $(selectElement).on('change', function () {
            syncHierarchicalSelection(this);
        });
    }
}

function syncHierarchicalSelection(selectElement) {
    const filterId = selectElement.id;

    if (selectionSyncLocks[filterId]) {
        return;
    }

    const previous = previousSelections[filterId] || new Set();
    const currentValues = $(selectElement).val() || [];
    const current = new Set(currentValues);
    const next = new Set(currentValues);

    for (const value of current) {
        if (!previous.has(value) && isRootOption(selectElement, value)) {
            for (const childValue of getChildValues(selectElement, value)) {
                next.add(childValue);
            }
        }
    }

    for (const value of previous) {
        if (!current.has(value) && isRootOption(selectElement, value)) {
            for (const childValue of getChildValues(selectElement, value)) {
                next.delete(childValue);
            }
        }
    }

    const normalizedNext = Array.from(next);
    if (!areValueArraysEqual(normalizedNext, currentValues)) {
        selectionSyncLocks[filterId] = true;
        $(selectElement).val(normalizedNext).trigger('change.select2');
        selectionSyncLocks[filterId] = false;
        previousSelections[filterId] = new Set(normalizedNext);

        return;
    }

    previousSelections[filterId] = new Set(currentValues);
}

function isRootOption(selectElement, value) {
    const optionElement = findOptionByValue(selectElement, value);

    if (!optionElement) {
        return false;
    }

    return optionElement.dataset.parentId === '';
}

function getChildValues(selectElement, parentId) {
    const childValues = [];

    for (const optionElement of Array.from(selectElement.options)) {
        if (optionElement.dataset.parentId === parentId) {
            childValues.push(optionElement.value);
        }
    }

    return childValues;
}

function findOptionByValue(selectElement, value) {
    for (const optionElement of Array.from(selectElement.options)) {
        if (optionElement.value === value) {
            return optionElement;
        }
    }

    return null;
}

function areValueArraysEqual(left, right) {
    if (left.length !== right.length) {
        return false;
    }

    const leftSorted = [...left].sort();
    const rightSorted = [...right].sort();

    for (let index = 0; index < leftSorted.length; index += 1) {
        if (leftSorted[index] !== rightSorted[index]) {
            return false;
        }
    }

    return true;
}

function setUpdateListeners() {
    function updateSubfilters(event) { // update child categories on parent category choice
        let value = event.target.value;
        let child_id = window.superFilters[event.target.id].subfilter;
        let cache = window.superFilters[event.target.id].cache;
        let result;

        if (value === "") {
            result = cache;
        } else {
            result = cache.filter(item => item.dataset.super === value);
        }
        document.getElementById(child_id).replaceChildren(...result);
    }

    for (let parent_id in window.superFilters) { // assign listeners to elements
        let child_id = window.superFilters[parent_id].subfilter;
        let parent = document.getElementById(parent_id);
        let child = document.getElementById(child_id);
        if (!parent || !child) {
            continue;
        }
        window.superFilters[parent_id].cache = Array.from(child.children)

        parent.addEventListener("change", updateSubfilters);
    }
}

function initializeDateSlider() {
    const sliderElement = $('#conventional-date-range-slider');
    const initialInput = $('#conventional-date-initial-input');
    const finalInput = $('#conventional-date-final-input');
    const hiddenInitialInput = $('#conventionalDateInitialYear');
    const hiddenFinalInput = $('#conventionalDateFinalYear');

    // Check if all required elements exist
    if (sliderElement.length === 0) {
        return;
    }

    if (initialInput.length === 0 || finalInput.length === 0) {
        return;
    }

    const minDate = parseInt(sliderElement.attr('data-minimal-date')) || 862;
    const maxDate = parseInt(sliderElement.attr('data-maximal-date')) || 1700;
    const step = 1;

    // Keep slider visual display limited to 862-1700 but allow typing extended range
    const extendedMinDate = 1;  // Allow typing dates from year 1
    const extendedMaxDate = 2025; // Allow typing dates up to current year

    const urlParams = new URLSearchParams(window.location.search);
    const urlInitialValue = parseInt(urlParams.get('conventionalDateInitialYear'), 10);
    const urlFinalValue = parseInt(urlParams.get('conventionalDateFinalYear'), 10);

    const hiddenInitialValue = parseInt(hiddenInitialInput.val(), 10);
    const hiddenFinalValue = parseInt(hiddenFinalInput.val(), 10);

    const hasInitialValue = Number.isFinite(urlInitialValue) || Number.isFinite(hiddenInitialValue);
    const hasFinalValue = Number.isFinite(urlFinalValue) || Number.isFinite(hiddenFinalValue);

    let initialValue = hasInitialValue
        ? (Number.isFinite(urlInitialValue) ? urlInitialValue : hiddenInitialValue)
        : minDate;
    let finalValue = hasFinalValue
        ? (Number.isFinite(urlFinalValue) ? urlFinalValue : hiddenFinalValue)
        : maxDate;

    if (initialValue < extendedMinDate) {
        initialValue = extendedMinDate;
    } else if (initialValue > extendedMaxDate) {
        initialValue = extendedMaxDate;
    }

    if (finalValue < extendedMinDate) {
        finalValue = extendedMinDate;
    } else if (finalValue > extendedMaxDate) {
        finalValue = extendedMaxDate;
    }

    if (initialValue > finalValue) {
        initialValue = minDate;
        finalValue = maxDate;
    }

    const sliderInitialValue = Math.min(Math.max(initialValue, minDate), maxDate);
    const sliderFinalValue = Math.min(Math.max(finalValue, minDate), maxDate);

    // Initialize slider with visual range limited to 862-1700
    sliderElement.slider({
        range: true,
        step: step,
        min: minDate,
        max: maxDate,
        values: [sliderInitialValue, sliderFinalValue],
        slide: function(event, ui) {
            updateInputs(ui.values);
            // write to hidden fields if present so backend can use them
            const $initial = $('#conventionalDateInitialYear');
            const $final = $('#conventionalDateFinalYear');
            if ($initial.length) { $initial.val(ui.values[0]); }
            if ($final.length) { $final.val(ui.values[1]); }
        }
    });

    // Force slider handles to the restored range in case another init path overwrote defaults.
    sliderElement.slider('values', [sliderInitialValue, sliderFinalValue]);

    // Track which input is currently being edited
    let editingInput = null;
    let editingInputType = null; // 'initial' or 'final'

    // Handle input field focus - detach from slider
    initialInput.on('focus', function() {
        editingInput = initialInput;
        editingInputType = 'initial';
        initialInput.removeClass('is-valid is-invalid');
        initialInput.addClass('editing');
    });

    finalInput.on('focus', function() {
        editingInput = finalInput;
        editingInputType = 'final';
        finalInput.removeClass('is-valid is-invalid');
        finalInput.addClass('editing');
    });

    // Handle input field blur - validate and reattach to slider
    initialInput.on('blur', function() {
        validateCrossFieldAndUpdateVisuals();
        validateAndUpdateInput('initial');
    });

    finalInput.on('blur', function() {
        validateCrossFieldAndUpdateVisuals();
        validateAndUpdateInput('final');
    });

    // Handle Enter key press - validate and update
    initialInput.on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            validateCrossFieldAndUpdateVisuals();
            validateAndUpdateInput('initial');
            $(this).blur(); // Trigger blur to reattach
        }
    });

    finalInput.on('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            validateCrossFieldAndUpdateVisuals();
            validateAndUpdateInput('final');
            $(this).blur(); // Trigger blur to reattach
        }
    });

    // Handle input changes - show visual feedback but don't validate yet
    initialInput.on('input', function() {
        validateInputVisualFeedback('initial');
    });

    finalInput.on('input', function() {
        validateInputVisualFeedback('final');
    });

    // Initial setup
    updateInputs([initialValue, finalValue]);
    updateHiddenFields(initialValue, finalValue);

    // Set initial visual feedback
    validateCrossFieldAndUpdateVisuals();

    function validateInputVisualFeedback(inputType) {
        const input = inputType === 'initial' ? initialInput : finalInput;
        const value = parseInt(input.val());

        // Check if input is valid number and within extended range
        let isValid = false;
        if (!isNaN(value) && value >= extendedMinDate && value <= extendedMaxDate) {
            isValid = true;
        }

        // Apply cross-field validation during typing
        const initialValue = parseInt(initialInput.val());
        const finalValue = parseInt(finalInput.val());
        const initialIsValid = !isNaN(initialValue) && initialValue >= extendedMinDate && initialValue <= extendedMaxDate;
        const finalIsValid = !isNaN(finalValue) && finalValue >= extendedMinDate && finalValue <= extendedMaxDate;

        let hasCrossFieldError = false;
        if (initialIsValid && finalIsValid) {
            if (inputType === 'initial' && initialValue > finalValue) {
                hasCrossFieldError = true;
            } else if (inputType === 'final' && finalValue < initialValue) {
                hasCrossFieldError = true;
            }
        }

        // Update visual state
        if (hasCrossFieldError) {
            input.removeClass('is-valid').addClass('is-invalid');
            // Also mark the other input as invalid
            if (inputType === 'initial') {
                finalInput.removeClass('is-valid').addClass('is-invalid');
            } else {
                initialInput.removeClass('is-valid').addClass('is-invalid');
            }
        } else if (isValid) {
            input.removeClass('is-invalid').addClass('is-valid');
        } else {
            input.removeClass('is-valid is-invalid');
        }
    }

    function validateCrossFieldAndUpdateVisuals() {
        // Apply cross-field validation and update both fields
        const initialValue = parseInt(initialInput.val());
        const finalValue = parseInt(finalInput.val());
        const initialIsValid = !isNaN(initialValue) && initialValue >= extendedMinDate && initialValue <= extendedMaxDate;
        const finalIsValid = !isNaN(finalValue) && finalValue >= extendedMinDate && finalValue <= extendedMaxDate;

        if (initialIsValid && finalIsValid) {
            if (initialValue <= finalValue) {
                // Valid combination - make both green
                initialInput.removeClass('is-invalid').addClass('is-valid');
                finalInput.removeClass('is-invalid').addClass('is-valid');
            } else {
                // Invalid order - make both red
                initialInput.removeClass('is-valid').addClass('is-invalid');
                finalInput.removeClass('is-valid').addClass('is-invalid');
            }
        } else {
            // At least one field is invalid or empty - update visual state for valid fields only
            if (initialIsValid) {
                initialInput.removeClass('is-invalid').addClass('is-valid');
            } else if (initialInput.val() !== '') {
                initialInput.removeClass('is-valid').addClass('is-invalid');
            }

            if (finalIsValid) {
                finalInput.removeClass('is-invalid').addClass('is-valid');
            } else if (finalInput.val() !== '') {
                finalInput.removeClass('is-valid').addClass('is-invalid');
            }
        }
    }

    function validateAndUpdateInput(inputType) {
        const input = inputType === 'initial' ? initialInput : finalInput;
        const value = parseInt(input.val());

        input.removeClass('editing');

        if (isNaN(value)) {
            // Invalid input - reset to previous valid value
            const currentValues = sliderElement.slider('values');
            const newValue = inputType === 'initial' ? currentValues[0] : currentValues[1];
            input.val(newValue);
            input.removeClass('is-valid is-invalid');
            // Re-validate visual feedback after reset
            validateCrossFieldAndUpdateVisuals();
            return;
        }

        // Clamp value to valid range (use extended range for validation)
        let clampedValue = value;
        if (value < extendedMinDate) {
            clampedValue = extendedMinDate;
        } else if (value > extendedMaxDate) {
            clampedValue = extendedMaxDate;
        }

        if (clampedValue !== value) {
            // Value was outside range, clamp it
            input.val(clampedValue);
        }

        // Apply cross-field validation for final submission
        const initialValue = parseInt(initialInput.val());
        const finalValue = parseInt(finalInput.val());

        const initialIsValid = !isNaN(initialValue) && initialValue >= extendedMinDate && initialValue <= extendedMaxDate;
        const finalIsValid = !isNaN(finalValue) && finalValue >= extendedMinDate && finalValue <= extendedMaxDate;
        const datesInOrder = initialIsValid && finalIsValid && initialValue <= finalValue;

        if (initialIsValid && finalIsValid && datesInOrder) {
            // Both dates are valid and in correct order - update slider and hidden fields
            sliderElement.slider('values', [initialValue, finalValue]);
            updateHiddenFields(initialValue, finalValue);
            // Visual feedback is handled by validateCrossFieldAndUpdateVisuals()
        } else {
            // Either invalid or out of order - don't update slider, just update visual feedback
            // Visual feedback is handled by validateCrossFieldAndUpdateVisuals()
        }

        editingInput = null;
        editingInputType = null;
    }

    function updateInputs(values) {
        initialInput.val(values[0]);
        finalInput.val(values[1]);
        initialInput.removeClass('is-valid is-invalid editing');
        finalInput.removeClass('is-valid is-invalid editing');

        // Update visual feedback after slider changes
        validateCrossFieldAndUpdateVisuals();
    }

    function updateHiddenFields(initial, final) {
        const $initial = $('#conventionalDateInitialYear');
        const $final = $('#conventionalDateFinalYear');
        if ($initial.length) { $initial.val(initial); }
        if ($final.length) { $final.val(final); }
    }
}
