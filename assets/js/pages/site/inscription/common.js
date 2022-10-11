import $ from 'jquery';
import 'jquery-ui/ui/widgets/draggable';
import Keyboard from 'simple-keyboard';

export function initializeFilters() {
    $('.vyfony-filterable-table-bundle-form-group select[multiple="multiple"]').select2({
        language: $('html').prop('lang')
    });
}

export const virtualKeyboardCoordinates = {
    top: 30,
    right: 30,
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
        virtualKeyboardWrapper
            .css('top', virtualKeyboardCoordinates.top + 'px')
            .css('right', virtualKeyboardCoordinates.right + 'px')
            .show();
    });

    $('body').on('click', function (event) {
        let clickedElement = $(event.target);

        if (0 === clickedElement.closest(virtualKeyboardWrapper).length &&
            0 === clickedElement.closest(targetInputElement).length
        ) {
            virtualKeyboardWrapper.hide();
        }
    });
}

export function createVirtualKeyboard(index, targetInputElement)
{
    const keyboardElement = $('<div/>')
        .addClass('simple-keyboard-' + index)
        .addClass('hg-theme-default')
        .addClass('hg-layout-default');

    const wrapper = $('<div/>')
        .css('display', 'none')
        .css('position', 'fixed')
        .append(
            $('<div/>')
                .addClass('virtual-keyboard-wrapper')
                .append(keyboardElement)
        )
        .appendTo($('body'));

    wrapper.draggable({
        stop: (event, ui) => {
            const position = ui.helper.position();

            if (isNumber(position.top) && isNumber(position.right)) {
                virtualKeyboardCoordinates.top = position.top;
                virtualKeyboardCoordinates.right = position.right;
            }
        }
    });

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