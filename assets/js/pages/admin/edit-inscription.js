/*
 * This file is part of «Epigraphy of Medieval Rus'» database.
 *
 * Copyright (c) National Research University Higher School of Economics
 *
 * «Epigraphy of Medieval Rus'» database is free software:
 * you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, version 3.
 *
 * «Epigraphy of Medieval Rus'» database is distributed
 * in the hope  that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code. If you have not received
 * a copy of the GNU General Public License along with
 * «Epigraphy of Medieval Rus'» database,
 * see <http://www.gnu.org/licenses/>.
 */

import $ from 'jquery';
import '../../src/common'

$(() => {
    forwardChangesFromInterpretationToZeroRow();

    forwardChangesFromZeroRowToInterpretation();
});

function forwardChangesFromInterpretationToZeroRow()
{
    $('body').on('change', '[data-zero-row-part]', (event) => {

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

            const zeroRowFieldName = checkboxElement.attr('data-zero-row-part');

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

        const selectDom = event.target;

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
                .find('[data-zero-row-part="' + zeroRowFieldName + '"]');

            interpretationCheckboxElement[0].checked = isInterpretationSelected;
        }
    });
}