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

import 'bootstrap';

$(() => {

    const interpretationsElement = $('#inscription_interpretations');

    if (interpretationsElement.length > 0) {
        new MutationObserver(onInterpretationAdded)
            .observe(
                interpretationsElement[0],
                {
                    childList: true
                }
            );
    }

    enableBootstrapCollapse($('.eomr-embedded-form-group-label'));
});

function onInterpretationAdded(mutationsList) {

    for (let mutation of mutationsList) {

        if (mutation.type === 'childList') {

            const groupHeaders = $(mutation.addedNodes).find('.eomr-embedded-form-group-label');

            enableBootstrapCollapse(groupHeaders);
        }
    }
}

function enableBootstrapCollapse(groupHeaders) {

    groupHeaders.each((index, groupHeaderDom) => {

        const groupHeaderElement = $(groupHeaderDom);
        const groupElement = groupHeaderElement.closest('.form-group.field-form');
        const groupContentElement = groupElement.find('.eomr-embedded-form-group-content').closest('.form-widget');

        groupElement
            .addClass('eomr-embedded-form-group')
            .addClass('card')
            .removeClass('form-group');

        groupHeaderElement
            .addClass('card-header')
            .removeClass('col-form-label');

        groupContentElement
            .addClass('collapse');


        groupContentElement.collapse({
            toggle: false
        });

        groupHeaderElement.on('click', function () {
            groupContentElement.collapse('toggle');
        });
    });
}