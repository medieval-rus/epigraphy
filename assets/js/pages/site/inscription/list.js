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
import * as common from './common';

window.superFilters = { // mapping of parent and child categories
    "super-carrier-category": {"subfilter": "carrier-category", "cache": []},
    "super-writing-method": {"subfilter": "writing-method", "cache": []},
    "super-content-category": {"subfilter": "content-category", "cache": []},
    "super-material": {"subfilter": "material", "cache": []}
}

$(window).on('load', () => {
    common.initializeFilters();
    setUpdateListeners();
    common.enableVirtualKeyboards();
});

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
        window.superFilters[parent_id].cache = Array.from(child.children)

        parent.addEventListener("change", updateSubfilters);
    }
}

