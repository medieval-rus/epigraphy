import $ from 'jquery';
import 'jquery-ui/ui/widgets/draggable';
import Keyboard from 'simple-keyboard';

export function initializeFilters() {
    $('.vyfony-filterable-table-bundle-form-group select[multiple="multiple"]').select2({
        language: $('html').prop('lang')
    });
}

export function enableVirtualKeyboards()
{
    $('[data-virtual-keyboard]').each(initializeVirtualKeyboard);
}

export function initializeVirtualKeyboard(index, targetInputDom)
{
    const targetInputElement = $(targetInputDom);

    const virtualKeyboardWrapper = createVirtualKeyboard(index, targetInputElement);

    targetInputElement.on('focus', () => {
        const fieldWidth = targetInputElement.outerWidth() || 'auto';
        const offsetLeft = targetInputElement.position().left || 0;
        virtualKeyboardWrapper
            .css('width', fieldWidth)
            .css('margin-left', offsetLeft + 'px')
            .addClass('is-visible');
    });

    $(document).on('mousedown', function (event) {
        const clickedElement = $(event.target);

        if (0 === clickedElement.closest(virtualKeyboardWrapper).length &&
            0 === clickedElement.closest(targetInputElement).length
        ) {
            virtualKeyboardWrapper.removeClass('is-visible');
        }
    });
}

export function createVirtualKeyboard(index, targetInputElement)
{
    const keyboardElement = $('<div/>')
        .addClass('simple-keyboard-' + index)
        .addClass('hg-theme-default')
        .addClass('hg-layout-default');

    const containerParent = targetInputElement.closest('.vyfony-filterable-table-bundle-form-group, .form-group');
    const wrapper = $('<div/>')
        .addClass('virtual-keyboard-container')
        .append(
            $('<div/>')
                .addClass('virtual-keyboard-wrapper')
                .append(keyboardElement)
        )
        .insertAfter(containerParent.length ? containerParent : targetInputElement);

    const keyboard = new Keyboard(
        keyboardElement[0],
        {
            layout: {
                default: [
                    'Ѡ Ѿ Ѧ Ѩ Ѫ Ѭ Ѣ Ѯ Ꙗ Ѹ Ꙋ Ѳ І Є Ѥ Ѕ Ѵ Ѱ Ꙑ',
                    'ѡ ѿ ѧ ѩ ѫ ѭ ѣ ѯ ꙗ ѹ ꙋ ѳ і є ѥ ѕ ѵ ѱ ꙑ',
                    ['⁙', '҂', '|', '¦', '⸗', '҃', '҇', '·'].join(' '),
                ],
            },
            onChange: input => {

                insertAtCursorPosition(targetInputElement, input);

                keyboard.clearInput();
            }
        }
    );

    return wrapper;
}

export function isNumber(input)
{
    return typeof input === 'number' || Object.prototype.toString.call(input) === '[object Number]';
}

export function insertAtCursorPosition(inputElement, textToInsert)
{
    const selectionStart = inputElement.prop('selectionStart');
    const selectionEnd = inputElement.prop('selectionEnd');
    const currentValue = inputElement.val();
    const textBeforeSelection = currentValue.substring(0,  selectionStart);
    const textAfterSelection  = currentValue.substring(selectionEnd, currentValue.length);

    const newCursorPosition = selectionStart + textToInsert.length;

    const newValue = textBeforeSelection + textToInsert + textAfterSelection;

    inputElement.val(newValue);
    inputElement.prop('selectionStart', newCursorPosition);
    inputElement.prop('selectionEnd', newCursorPosition);
    setTimeout(() => inputElement.focus(), 250);
}


export function enableRowClickNavigation()
{
    const table = $('table[data-vyfony-filterable-table]');
    if (table.length === 0) {
        return;
    }

    const interactiveSelector = 'a, button, input, textarea, select, label, summary, [role="button"]';
    let isDraggingSelection = false;
    let isMouseDown = false;
    const hasTextSelection = () => {
        const selection = window.getSelection();
        return !!(selection && selection.toString().trim().length > 0);
    };

    function getRowHref($row) {
        return $row.attr('data-row-href') || null;
    }

    table.on('click', 'tbody tr[data-row-href]', function (event) {
        if ($(event.target).closest(interactiveSelector).length) {
            return;
        }
        if (hasTextSelection()) {
            return;
        }
        if (isDraggingSelection) {
            return;
        }

        const $row = $(this);
        const href = getRowHref($row);
        if (href) {
            window.location.href = href;
        }
    });

    table.on('click', 'a.table-row-link', function (event) {
        if (hasTextSelection() || isDraggingSelection) {
            event.preventDefault();
            event.stopPropagation();
        }
    });

    table.on('mousedown', 'tbody', function () {
        isMouseDown = true;
        isDraggingSelection = false;
    });

    table.on('mousemove', 'tbody', function () {
        if (isMouseDown) {
            isDraggingSelection = true;
        }
    });

    $(document).on('mouseup', function () {
        isMouseDown = false;
        setTimeout(() => {
            isDraggingSelection = false;
        }, 0);
    });

    table.find('tbody tr[data-row-href]').each(function () {
        const $row = $(this);
        const href = getRowHref($row);
        if (href) {
            $row.attr('data-row-clickable', 'true');
            $row.attr('tabindex', '0');
        }
    });

    $(document).on('keydown', 'table[data-vyfony-filterable-table] tbody tr[data-row-clickable]', function (event) {
        if (event.key !== 'Enter') {
            return;
        }
        if ($(event.target).closest(interactiveSelector).length) {
            return;
        }
        if (hasTextSelection()) {
            return;
        }
        if (isDraggingSelection) {
            return;
        }
        const href = getRowHref($(this));
        if (href) {
            window.location.href = href;
        }
    });
}
