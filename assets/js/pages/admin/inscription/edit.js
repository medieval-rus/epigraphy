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
import 'jquery-ui/ui/widgets/draggable';
import Keyboard from 'simple-keyboard';

const virtualKeyboardCoordinates = {
    top: 150,
    left: 0,
}
$(() => {
    createCheckboxes();
    copyValuesFromZeroRowToInterpretation();
    forwardChangesFromInterpretationToZeroRow();
    forwardChangesFromZeroRowToInterpretation();
    enableVirtualKeyboards();
});

function createCheckboxes() {

    $('body').find('[data-zero-row-part]').each((index, dom) => {

        const element = $(dom);

        const zeroRowFieldName = element.attr('data-zero-row-part');

        const checkboxElement = $('<div/>')
            .append(
                $('<div/>')
                    .addClass('form-group')
                    .append(
                        $('<div/>')
                            .append(
                                $('<div/>')
                                    .addClass('checkbox')
                                    .append(
                                        $('<label/>')
                                            .append(
                                                $(`<input type="checkbox" data-zero-row-part-checkbox="${zeroRowFieldName}"/>`),
                                                $('<span class="control-label__text">Часть нулевой строки</span>')
                                            )
                                    )
                            )
                    )
            );

        checkboxElement.insertAfter(element.parent().parent().parent());
    });
}

function copyValuesFromZeroRowToInterpretation()
{
    $('body').find('[data-zero-row-references]').each((index, dom) => {
        copySingleValueFromZeroRowToInterpretation(dom);
    });
}

function forwardChangesFromInterpretationToZeroRow()
{
    $('body').on('change', '[data-zero-row-part-checkbox]', (event) => {

        const checkboxElement = $(event.target);

        const checkboxRowElement = checkboxElement.parent().parent().parent().parent().parent();

        const interpretationElement = checkboxRowElement.parent().parent().parent().parent().parent().parent();

        const interpretationId = parseInt(
            interpretationElement.find('[data-interpretation-id]').attr('data-interpretation-id')
        );

        // update zero row on the client if interpretation already exists
        // (for newly created interpretations these checkboxes are handled by server)
        if (!Number.isNaN(interpretationId)) {
            const isChecked = event.target.checked;

            const zeroRowFieldName = checkboxElement.attr('data-zero-row-part-checkbox');

            const inscriptionFormElement = interpretationElement.closest('form');

            const zeroRowFieldElement = inscriptionFormElement
                .find('select[data-zero-row-references="' + zeroRowFieldName+'"]');

            const optionToChange = zeroRowFieldElement.find('option[value="' + interpretationId + '"]');

            optionToChange[0].selected = isChecked;
        }
    });
}

function forwardChangesFromZeroRowToInterpretation()
{
    $('[data-zero-row-references]').on('change', (event) => {
        copySingleValueFromZeroRowToInterpretation(event.target)
    });
}

function copySingleValueFromZeroRowToInterpretation(selectDom)
{
    const selectElement = $(selectDom);
    const zeroRowFieldName = selectElement.attr('data-zero-row-references');

    const inscriptionFormElement = selectElement.closest('form');

    const interpretationsOptionsDom = selectDom.options;

    for (let index = 0; index < interpretationsOptionsDom.length; index++) {
        const interpretationOption = interpretationsOptionsDom[index];

        const interpretationId = interpretationOption.value;
        const isInterpretationSelected = interpretationOption.selected;

        const interpretationElement = inscriptionFormElement
            .find('[data-interpretation-id="' + interpretationId + '"]')
            .parent().parent().parent().parent().parent().parent().parent();

        const interpretationCheckboxElement = interpretationElement
            .find('[data-zero-row-part-checkbox="' + zeroRowFieldName + '"]');

        interpretationCheckboxElement[0].checked = isInterpretationSelected;
    }
}

function enableVirtualKeyboards()
{
    $('[data-virtual-keyboard]').each(initializeVirtualKeyboard);
}

function initializeVirtualKeyboard(index, targetInputDom)
{
    const targetInputElement = $(targetInputDom);

    const virtualKeyboardWrapper = createVirtualKeyboard(index, targetInputElement);

    targetInputElement.on('focus', () => {
        virtualKeyboardWrapper
            .css('top', virtualKeyboardCoordinates.top + 'px')
            .css('left', virtualKeyboardCoordinates.left + 'px')
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

function createVirtualKeyboard(index, targetInputElement)
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

            if (isNumber(position.top) && isNumber(position.left)) {
                virtualKeyboardCoordinates.top = position.top;
                virtualKeyboardCoordinates.left = position.left;
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

function isNumber(input)
{
    return typeof input === 'number' || Object.prototype.toString.call(input) === '[object Number]';
}

function insertAtCursorPosition(inputElement, textToInsert)
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