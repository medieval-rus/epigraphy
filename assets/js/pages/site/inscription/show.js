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
import 'popper.js/dist/popper.min';
import 'bootstrap';
import PhotoSwipe from 'photoswipe/dist/photoswipe';
import PhotoSwipeUI_Default from 'photoswipe/dist/photoswipe-ui-default';

$(window).on('load', () => {

    // this is an adapted copy-paste of the "Getting started" example of PhotoSwipe
    function initPhotoSwipeFromDom(gallerySelector) {

        function parseThumbnailElements(galleryElement) {

            const thumbnailElements = galleryElement.childNodes;

            const imageDataCollection = [];

            for (let i = 0; i < thumbnailElements.length; i++) {

                const figureElement = thumbnailElements[i];

                if (figureElement.nodeType !== Node.ELEMENT_NODE || figureElement.children.length < 2) {
                    continue;
                }

                const aElement = figureElement.children[0];

                if (figureElement.children.length === 0) {
                    continue;
                }

                const figcaptionElement = figureElement.children[1];
                const imgElement = aElement.children[0];

                const item = {
                    src: aElement.getAttribute('href'),
                    downloadUrl: figureElement.getAttribute('data-download-url'),
                    title: figcaptionElement.innerHTML,
                    msrc: imgElement.getAttribute('src'),
                    w: imgElement.naturalWidth,
                    h: imgElement.naturalHeight,
                    figureElement: figureElement
                };

                imageDataCollection.push(item);
            }

            return imageDataCollection;
        }

        function closest(element, callback) {
            return element && (callback(element) ? element : closest(element.parentNode, callback));
        }

        function onThumbnailsClick(event) {

            event = event || window.event;

            event.preventDefault ? event.preventDefault() : event.returnValue = false;

            const eventTarget = event.target || event.srcElement;

            const clickedListItem = closest(eventTarget, (element) => {
                return (element.tagName && element.tagName.toUpperCase() === 'FIGURE');
            });

            if (!clickedListItem) {
                return;
            }

            const clickedGallery = clickedListItem.parentNode;
            const childNodes = clickedListItem.parentNode.childNodes;
            const numChildNodes = childNodes.length;

            let index;
            let nodeIndex = 0;

            for (let i = 0; i < numChildNodes; i++) {

                if (childNodes[i].nodeType !== Node.ELEMENT_NODE) {
                    continue;
                }

                if (childNodes[i] === clickedListItem) {
                    index = nodeIndex;
                    break;
                }

                nodeIndex++;
            }

            if (index >= 0) {
                openPhotoSwipe(index, clickedGallery);
            }

            return false;
        }

        function parseGalleryDataFromUrl() {

            const hash = window.location.hash.substring(1);
            const params = {};

            if (hash.length < 5) {
                return params;
            }

            const vars = hash.split('&');

            for (let i = 0; i < vars.length; i++) {

                if (!vars[i]) {
                    continue;
                }

                const pair = vars[i].split('=');

                if (pair.length < 2) {
                    continue;
                }

                params[pair[0]] = pair[1];
            }

            if (params.gid) {
                params.gid = parseInt(params.gid, 10);
            }

            return params;
        }

        function openPhotoSwipe(index, galleryElement, disableAnimation, fromURL) {

            const pswpElement = document.querySelectorAll('.pswp')[0];

            const items = parseThumbnailElements(galleryElement);

            const options = {
                galleryUID: galleryElement.getAttribute('data-pswp-uid'),
                getThumbBoundsFn: (index) => {
                    const thumbnailElement = items[index].figureElement.getElementsByTagName('img')[0];

                    const pageYScroll = window.pageYOffset || document.documentElement.scrollTop;
                    const rect = thumbnailElement.getBoundingClientRect();

                    return {x: rect.left, y: rect.top + pageYScroll, w: rect.width};
                },
                shareButtons: [
                    {id: 'download', label: 'Download', url: '{{raw_image_url}}', download: true},
                    {
                        id: 'facebook',
                        label: 'Share on Facebook',
                        url: 'https://www.facebook.com/sharer/sharer.php?u={{url}}'
                    },
                ],
                getImageURLForShare: (shareButtonData) => {
                    return gallery.currItem.downloadUrl;
                },
                closeOnScroll: false,
                pinchToClose: false,
                tapToClose: false,
                closeElClasses: [],
                clickToCloseNonZoomable: false,
            };

            if (fromURL) {
                if (options.galleryPIDs) {
                    for (let j = 0; j < items.length; j++) {
                        if (items[j].pid == index) {
                            options.index = j;
                            break;
                        }
                    }
                } else {
                    options.index = parseInt(index, 10) - 1;
                }
            } else {
                options.index = parseInt(index, 10);
            }

            if (isNaN(options.index)) {
                return;
            }

            if (disableAnimation) {
                options.showAnimationDuration = 0;
            }

            const gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
            gallery.init();
        }

        const galleryElements = document.querySelectorAll(gallerySelector);

        for (let i = 0, l = galleryElements.length; i < l; i++) {
            galleryElements[i].setAttribute('data-pswp-uid', i + 1);
            galleryElements[i].onclick = onThumbnailsClick;
        }

        const urlData = parseGalleryDataFromUrl();
        if (urlData.pid && urlData.gid) {
            openPhotoSwipe(urlData.pid, galleryElements[urlData.gid - 1], true, true);
        }
    }

    initPhotoSwipeFromDom('[data-images-container]');
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })
    
    // TODO(epidoc): uncomment when EpiDoc viewer is ready
    // initEpidocViewer();
});

// TODO(epidoc): functions below are disabled — uncomment initEpidocViewer() call above to enable

/**
 * EpiDoc XML Viewer
 * Parses and renders EpiDoc XML with syntax highlighting and structured view
 */
function initEpidocViewer() {
    const dataScript = document.getElementById('epidoc-data');
    const stubScript = document.getElementById('epidoc-stub-data');
    const tableContainer = document.getElementById('epidoc-text-in-table');
    const tableApparatusContainer = document.getElementById('epidoc-apparatus-in-table');
    if (!dataScript) {
        // If no data but table container exists, show placeholder
        if (tableContainer) {
            tableContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">EpiDoc данные отсутствуют</span>';
        }
        if (tableApparatusContainer) {
            tableApparatusContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">EpiDoc данные отсутствуют</span>';
        }
        return;
    }
    
    const xmlString = dataScript.textContent;
    const stubString = stubScript ? stubScript.textContent : '';
    const parser = new DOMParser();
    const xmlDoc = parser.parseFromString(xmlString, 'text/xml');
    const stubDoc = stubString ? parser.parseFromString(stubString, 'text/xml') : null;
    
    // Check for parsing errors
    const parseError = xmlDoc.querySelector('parsererror');
    if (parseError) {
        console.error('EpiDoc XML parsing error:', parseError.textContent);
        if (tableContainer) {
            tableContainer.innerHTML = '<span style="color: #dc3545; font-style: italic;">Ошибка парсинга XML</span>';
        }
        if (tableApparatusContainer) {
            tableApparatusContainer.innerHTML = '<span style="color: #dc3545; font-style: italic;">Ошибка парсинга XML</span>';
        }
        return;
    }

    renderTableView(xmlDoc, stubDoc);
}

function getFirstByTagName(parent, tagName) {
    if (!parent) {
        return null;
    }
    const elements = parent.getElementsByTagName(tagName);
    return elements.length > 0 ? elements[0] : null;
}

function getTextBody(xmlDoc) {
    const textElement = getFirstByTagName(xmlDoc, 'text');
    return getFirstByTagName(textElement, 'body');
}

function getDivsByType(parent, type) {
    if (!parent) {
        return [];
    }
    const divs = parent.getElementsByTagName('div');
    const result = [];
    for (let i = 0; i < divs.length; i++) {
        if (divs[i].getAttribute('type') === type) {
            result.push(divs[i]);
        }
    }
    return result;
}

function extractAppElements(xmlDoc) {
    if (!xmlDoc) {
        return [];
    }
    const textBody = getTextBody(xmlDoc);
    const editions = getDivsByType(textBody, 'edition');
    const edition = editions.length > 0 ? editions[0] : null;
    if (!edition) {
        return [];
    }
    return Array.from(edition.getElementsByTagName('app'));
}

function extractExternalApparatusText(xmlDoc) {
    if (!xmlDoc) {
        return '';
    }
    const textBody = getTextBody(xmlDoc);
    const apparatusDivs = getDivsByType(textBody, 'apparatus');
    if (apparatusDivs.length === 0) {
        return '';
    }
    return apparatusDivs[0].textContent.trim();
}

function renderApparatusIntoContainer(container, xmlDoc, bibliographyMap, system) {
    if (!container || !xmlDoc) {
        return false;
    }
    const appElements = extractAppElements(xmlDoc);
    if (appElements.length === 0) {
        const externalText = extractExternalApparatusText(xmlDoc);
        if (!externalText) {
            return false;
        }
        container.innerHTML = `
            <table class="apparatus-table">
                <tbody>
                    <tr>
                        <td class="apparatus-lem-cell">—</td>
                        <td class="apparatus-rdg-cell">${escapeHtml(externalText)}</td>
                    </tr>
                </tbody>
            </table>`;
        return true;
    }
    const rows = buildCriticalApparatusRows(appElements, bibliographyMap, system).trim();
    if (!rows) {
        return false;
    }
    container.innerHTML = `
        <table class="apparatus-table">
            <tbody>
                ${rows}
            </tbody>
        </table>`;
    return true;
}

/**
 * Render the structured/formatted view of the EpiDoc
 */
function renderTableView(xmlDoc, stubDoc = null) {
    const tableContainer = document.getElementById('epidoc-text-in-table');
    const tableApparatusContainer = document.getElementById('epidoc-apparatus-in-table');
    const tableTranslationsContainer = document.getElementById('epidoc-translations-in-table');
    
    // Parse bibliography map
    const bibliographyMap = parseBibliography(xmlDoc);
    const stubBibliographyMap = stubDoc ? parseBibliography(stubDoc) : null;
    
    // Initial bracket system (default: Leiden = false in toggle)
    let currentSystem = getEpidocSystemPreference();

    function renderContent() {
        // Render for table widget (if exists)
        if (tableContainer || tableApparatusContainer || tableTranslationsContainer) {
            const textBody = getTextBody(xmlDoc);
            if (textBody) {
                const editions = getDivsByType(textBody, 'edition');
                const edition = editions.length > 0 ? editions[0] : null;
                if (edition) {
                    if (tableContainer) {
                        let textContent = renderEditionContent(edition, currentSystem);
                        textContent = normalizeEditionHtml(textContent);
                        tableContainer.innerHTML = textContent;
                    }
                    if (tableApparatusContainer) {
                        const rendered = renderApparatusIntoContainer(tableApparatusContainer, xmlDoc, bibliographyMap, currentSystem)
                            || renderApparatusIntoContainer(tableApparatusContainer, stubDoc, stubBibliographyMap, currentSystem);
                        if (!rendered) {
                            tableApparatusContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">Критический аппарат не найден в XML</span>';
                        }
                    }
                    if (tableTranslationsContainer) {
                        const rendered = renderTranslationsIntoContainer(tableTranslationsContainer, xmlDoc, bibliographyMap)
                            || renderTranslationsIntoContainer(tableTranslationsContainer, stubDoc, stubBibliographyMap);
                        if (!rendered) {
                            tableTranslationsContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">Переводы не найдены в XML</span>';
                        }
                    }
                } else {
                    if (tableContainer) {
                        tableContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">Секция edition не найдена в XML</span>';
                    }
                    if (tableApparatusContainer) {
                        tableApparatusContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">Секция edition не найдена в XML</span>';
                    }
                    if (tableTranslationsContainer) {
                        tableTranslationsContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">Секция edition не найдена в XML</span>';
                    }
                }
            } else {
                if (tableContainer) {
                    tableContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">Секция body не найдена в XML</span>';
                }
                if (tableApparatusContainer) {
                    tableApparatusContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">Секция body не найдена в XML</span>';
                }
                if (tableTranslationsContainer) {
                    tableTranslationsContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">Секция body не найдена в XML</span>';
                }
            }
            
            // Re-attach event listener to toggle in table
            const tableToggle = document.querySelector('.epidoc-toggle-input-table');
            if (tableToggle) {
                tableToggle.checked = currentSystem === 'zaliznyak';
                tableToggle.removeEventListener('change', handleToggleChange);
                tableToggle.addEventListener('change', handleToggleChange);
            }
        }
    }
    
    function handleToggleChange(e) {
        currentSystem = e.target.checked ? 'zaliznyak' : 'leiden';
        setEpidocSystemPreference(currentSystem);
        renderContent();
    }
    
    // Initial render
    renderContent();
}

function getEpidocSystemPreference() {
    const value = readCookie('epidoc_system');
    if (value === 'zaliznyak' || value === 'leiden') {
        return value;
    }
    return 'leiden';
}

function setEpidocSystemPreference(system) {
    setCookie('epidoc_system', system, 365);
}

function readCookie(name) {
    const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
    return match ? decodeURIComponent(match[1]) : null;
}

function setCookie(name, value, days) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${date.toUTCString()}; path=/`;
}

function extractTranslations(xmlDoc) {
    if (!xmlDoc) {
        return [];
    }
    const textBody = getTextBody(xmlDoc);
    return getDivsByType(textBody, 'translation');
}

function renderTranslationsIntoContainer(container, xmlDoc, bibliographyMap) {
    if (!container || !xmlDoc) {
        return false;
    }
    const translations = extractTranslations(xmlDoc);
    if (!translations.length) {
        return false;
    }
    const isCollapsible = translations.length > 1;
    let items = '';
    translations.forEach((trans, index) => {
        const lang = trans.getAttribute('xml:lang') || 'unknown';
        const resp = trans.getAttribute('resp') || '';
        const text = trans.textContent.trim();
        const resolvedResp = resolveResp(resp, bibliographyMap);
        const langBadge = `<span class="epidoc-lang-badge">${escapeHtml(lang)}</span>`;
        const respBadge = resolvedResp ? `<span class="epidoc-resp-badge">${escapeHtml(resolvedResp)}</span>` : '';
        const toggleBtn = isCollapsible && index === 0
            ? `<button type="button" class="epidoc-translations-toggle" aria-expanded="false" title="Показать все переводы"></button>`
            : '';
        items += `
            <div class="epidoc-translation-line">
                <span class="epidoc-translation-text">${escapeHtml(text)}</span>
                ${respBadge}${langBadge}${toggleBtn}
            </div>`;
    });
    const blockClass = isCollapsible
        ? 'epidoc-translations-block epidoc-translations-block--collapsible epidoc-translations-block--collapsed'
        : 'epidoc-translations-block';
    container.innerHTML = `<div class="${blockClass}">${items}</div>`;
    if (isCollapsible) {
        const toggle = container.querySelector('.epidoc-translations-toggle');
        const block = container.querySelector('.epidoc-translations-block');
        if (toggle && block) {
            toggle.addEventListener('click', () => {
                const isCollapsed = block.classList.contains('epidoc-translations-block--collapsed');
                block.classList.toggle('epidoc-translations-block--collapsed', !isCollapsed);
                toggle.setAttribute('aria-expanded', String(isCollapsed));
                toggle.classList.toggle('epidoc-translations-toggle--open', isCollapsed);
                toggle.title = isCollapsed ? 'Скрыть переводы' : 'Показать все переводы';
            });
        }
    }
    return true;
}

/**
 * Render header/metadata section
 */
/**
 * Bracket Systems Configuration
 */
const BRACKET_SYSTEMS = {
    leiden: {
        supplied: {
            editorial: ['⟨', '⟩'],
            lost: ['[', ']'],
            unclear: ['[', ']']
        }
    },
    zaliznyak: {
        supplied: {
            editorial: ['[', ']'],
            lost: ['(', ')'],
            unclear: ['[', ']']
        }
    }
};

/**
 * Recursively render edition content with proper markup
 */
function renderEditionContent(node, system = 'leiden') {
    let result = '';
    
    for (let index = 0; index < node.childNodes.length; index++) {
        const child = node.childNodes[index];
        if (child.nodeType === Node.TEXT_NODE) {
            // Normalize whitespace and avoid extra spaces near inline <app> inside words.
            let text = child.textContent.replace(/\s+/g, ' ');
            if (!text.trim()) {
                const prevElementForGap = getSiblingElement(node.childNodes, index, -1);
                const nextElementForGap = getSiblingElement(node.childNodes, index, 1);
                if (prevElementForGap && prevElementForGap.localName === 'supplied'
                    && nextElementForGap && nextElementForGap.localName === 'app'
                    && !/\s$/.test(result)) {
                    result += ' ';
                }
                continue;
            }
            const prevElement = getSiblingElement(node.childNodes, index, -1);
            const nextElement = getSiblingElement(node.childNodes, index, 1);
            const hadLeadingWhitespace = /^\s/.test(text);
            const hadTrailingWhitespace = /\s$/.test(text);

            if (prevElement && prevElement.localName === 'app') {
                text = text.replace(/^\s+/, '');
                if (hadLeadingWhitespace && !startsWithLowercaseLetter(text)) {
                    text = ` ${text}`;
                }
            }
            if (nextElement && nextElement.localName === 'app') {
                const textWithoutTail = text.replace(/\s+$/, '');
                const lastToken = textWithoutTail.split(/\s+/).pop() || '';
                if (!hadTrailingWhitespace || lastToken.length <= 2) {
                    text = textWithoutTail;
                } else {
                    text = `${textWithoutTail} `;
                }
            }
            result += escapeHtml(text);
        } else if (child.nodeType === Node.ELEMENT_NODE) {
            const tagName = child.localName;
            
            switch (tagName) {
                case 'supplied':
                    const reason = child.getAttribute('reason') || 'lost';
                    if (reason === 'unclear') {
                        const unclearSuppliedContent = renderEditionContent(child, system);
                        if (system === 'zaliznyak') {
                            result += `<span class="epidoc-supplied" title="Supplied: ${reason}">[${unclearSuppliedContent}]</span>`;
                        } else {
                            result += `<span class="epidoc-unclear epidoc-unclear--leiden" title="Unclear reading">${unclearSuppliedContent}</span>`;
                        }
                        break;
                    }
                    // For HTML rendering (edition text), we use data attributes and CSS/JS to handle display
                    // But brackets might be part of content for simple text extraction
                    const suppliedClass = reason === 'editorial' 
                        ? 'epidoc-supplied epidoc-supplied--editorial' 
                        : 'epidoc-supplied';
                    
                    // Get bracket chars based on system
                    const brackets = BRACKET_SYSTEMS[system].supplied[reason] || BRACKET_SYSTEMS[system].supplied.lost;
                    
                    result += `<span class="${suppliedClass}" title="Supplied: ${reason}" data-brackets-start="${brackets[0]}" data-brackets-end="${brackets[1]}">${brackets[0]}${renderEditionContent(child, system)}${brackets[1]}</span>`;
                    break;
                    
                case 'app':
                    result += renderApparatus(child, system);
                    break;
                    
                case 'ab':
                case 'p':
                    result += renderEditionContent(child, system);
                    break;
                    
                case 'lb':
                    if (!result.endsWith('<br />')) {
                        result += '<br />';
                    }
                    break;
                    
                case 'gap':
                    const extent = child.getAttribute('extent') || '?';
                    const gapMarker = system === 'zaliznyak' ? '...' : '***';
                    result += `<span class="epidoc-gap" title="Gap: ${extent}">${gapMarker}</span>`;
                    break;
                    
                case 'unclear':
                    const unclearContent = renderEditionContent(child, system);
                    if (system === 'zaliznyak') {
                        result += `[${unclearContent}]`;
                    } else {
                        result += `<span class="epidoc-unclear epidoc-unclear--leiden" title="Unclear reading">${unclearContent}</span>`;
                    }
                    break;
                    
                case 'lem':
                case 'rdg':
                    // These are handled by renderApparatus, skip here
                    result += renderEditionContent(child, system);
                    break;
                    
                default:
                    result += renderEditionContent(child, system);
            }
        }
    }
    
    return result;
}

function getSiblingElement(childNodes, startIndex, direction) {
    let index = startIndex + direction;
    while (index >= 0 && index < childNodes.length) {
        const sibling = childNodes[index];
        if (sibling.nodeType === Node.ELEMENT_NODE) {
            return sibling;
        }
        index += direction;
    }
    return null;
}

function startsWithLowercaseLetter(text) {
    return /^\p{Ll}/u.test(text);
}

function normalizeEditionHtml(html) {
    return html
        .replace(/^(?:<br\s*\/?>\s*)+/i, '')
        .trim();
}

/**
 * Render apparatus entry with lemma and readings
 */
function renderApparatus(appNode, system) {
    const lem = appNode.querySelector('lem');
    const readings = appNode.querySelectorAll('rdg');
    
    let lemContent = lem ? renderEditionContent(lem, system) : '';
    let lemResp = lem ? (lem.getAttribute('resp') || '') : '';
    
    let readingsHtml = '';
    readings.forEach(rdg => {
        const resp = rdg.getAttribute('resp') || '';
        const respLabel = resp ? `<span class="epidoc-resp">${resp}</span>` : '';
        readingsHtml += `<span class="epidoc-rdg">${renderEditionContent(rdg, system)} ${respLabel}</span>`;
    });
    
    return `<span class="epidoc-app"><span class="epidoc-lem" title="Click to see variant readings">${lemContent}</span><span class="epidoc-readings">${readingsHtml}</span></span>`;
}

function buildCriticalApparatusRows(appElements, bibliographyMap, system) {
    let rows = '';
    for (let i = 0; i < appElements.length; i++) {
        const app = appElements[i];
        const lem = app.querySelector('lem');
        const readings = app.querySelectorAll('rdg');
        
        // Get lemma text and resp
        let lemText = lem ? getPlainText(lem, system) : '';
        // let lemResp = lem ? (lem.getAttribute('resp') || '') : '';
        
        // Build alternative readings
        let alternatives = [];
        for (let j = 0; j < readings.length; j++) {
            const rdg = readings[j];
            const rdgText = getPlainText(rdg, system);
            const rdgResp = rdg.getAttribute('resp') || '';
            alternatives.push({
                text: rdgText,
                resp: rdgResp
            });
        }
        
        // Create row
        const lemDisplay = escapeHtml(lemText);
        
        const altDisplay = alternatives.map(alt => {
            const resolvedResp = resolveResp(alt.resp, bibliographyMap);
            const respTag = resolvedResp ? ` <span class="apparatus-resp">${escapeHtml(resolvedResp)}</span>` : '';
            return `${escapeHtml(alt.text)}${respTag}`;
        }).join('<br>');
        
        rows += `
            <tr>
                <td class="apparatus-lem-cell">${lemDisplay}</td>
                <td class="apparatus-rdg-cell">${altDisplay}</td>
            </tr>`;
    }
    return rows;
}

/**
 * Render critical apparatus as a table
 */
/**
 * Get plain text content from an element (recursively, handling supplied elements)
 */
function getPlainText(node, system = 'leiden') {
    let result = '';
    
    for (const child of node.childNodes) {
        if (child.nodeType === Node.TEXT_NODE) {
            result += child.textContent.replace(/\s+/g, ' ');
        } else if (child.nodeType === Node.ELEMENT_NODE) {
            const tagName = child.localName;
            
            if (tagName === 'supplied') {
                const reason = child.getAttribute('reason') || 'lost';
                if (reason === 'unclear') {
                    const unclearSuppliedText = getPlainText(child, system);
                    if (system === 'zaliznyak') {
                        result += `[${unclearSuppliedText}]`;
                    } else {
                        result += applyDotBelowMarks(unclearSuppliedText);
                    }
                    continue;
                }
                const brackets = BRACKET_SYSTEMS[system].supplied[reason] || BRACKET_SYSTEMS[system].supplied.lost;
                result += brackets[0] + getPlainText(child, system) + brackets[1];
            } else if (tagName === 'unclear') {
                const unclearText = getPlainText(child, system);
                if (system === 'zaliznyak') {
                    result += `[${unclearText}]`;
                } else {
                    result += applyDotBelowMarks(unclearText);
                }
            } else if (tagName === 'gap') {
                result += system === 'zaliznyak' ? '...' : '***';
            } else {
                result += getPlainText(child, system);
            }
        }
    }
    
    return result.trim();
}

function applyDotBelowMarks(text) {
    let result = '';
    for (const char of text) {
        if (/\s/.test(char)) {
            result += char;
        } else {
            result += `${char}\u0323`;
        }
    }
    return result;
}

/**
 * Render XML source with syntax highlighting
 */
function renderSourceView(xmlString) {
    const container = document.getElementById('epidoc-source');
    if (!container) return;
    
    // Apply syntax highlighting
    const highlighted = highlightXml(xmlString.trim());
    container.innerHTML = highlighted;
}

/**
 * Simple XML syntax highlighter
 */
function highlightXml(xml) {
    // Escape HTML first
    let result = escapeHtml(xml);
    
    // Highlight XML declarations
    result = result.replace(/(&lt;\?[\s\S]*?\?&gt;)/g, '<span class="xml-declaration">$1</span>');
    
    // Highlight comments
    result = result.replace(/(&lt;!--[\s\S]*?--&gt;)/g, '<span class="xml-comment">$1</span>');
    
    // Highlight tags with attributes
    result = result.replace(/(&lt;\/?)(\w+[\w:-]*)((?:\s+[\w:-]+\s*=\s*&quot;[^&]*&quot;)*\s*)(\/?)(&gt;)/g, 
        (match, open, tagName, attrs, selfClose, close) => {
            // Highlight attributes
            const highlightedAttrs = attrs.replace(/([\w:-]+)(\s*=\s*)(&quot;)([^&]*)(&quot;)/g, 
                '<span class="xml-attr-name">$1</span>$2<span class="xml-attr-value">$3$4$5</span>');
            return `${open}<span class="xml-tag">${tagName}</span>${highlightedAttrs}${selfClose}${close}`;
        });
    
    return result;
}

/**
 * Setup tab switching functionality
 */
function setupTabs() {
    const tabs = document.querySelectorAll('.epidoc-tab');
    const contents = document.querySelectorAll('.epidoc-content');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const targetTab = tab.getAttribute('data-tab');
            
            // Update tab states
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            
            // Update content visibility
            contents.forEach(content => {
                content.classList.toggle('active', content.getAttribute('data-content') === targetTab);
            });
        });
    });
}

/**
 * Setup copy to clipboard functionality
 */
function setupCopyButton(xmlString) {
    const copyBtn = document.querySelector('.epidoc-copy-btn');
    if (!copyBtn) return;
    
    copyBtn.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(xmlString.trim());
            copyBtn.classList.add('copied');
            copyBtn.innerHTML = '<span class="copy-icon">✓</span> Copied!';
            
            setTimeout(() => {
                copyBtn.classList.remove('copied');
                copyBtn.innerHTML = '<span class="copy-icon">📋</span> Copy';
            }, 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    });
}

/**
 * Parse bibliography for resolving references
 */
function parseBibliography(xmlDoc) {
    const map = new Map();
    // Use getElementsByTagName to avoid namespace issues that can occur with querySelector in some XML parsers
    const bibls = xmlDoc.getElementsByTagName('bibl');
    
    for (let i = 0; i < bibls.length; i++) {
        const bibl = bibls[i];
        const id = bibl.getAttribute('xml:id') || bibl.getAttribute('id');
        if (id) {
            map.set(id, bibl.textContent.trim());
        }
    }
    return map;
}

/**
 * Resolve response/responsibility ID to bibliography name if available
 */
function resolveResp(resp, bibliographyMap) {
    if (!resp) return '';
    const id = resp.replace(/^#/, '');
    if (bibliographyMap && bibliographyMap.has(id)) {
        return bibliographyMap.get(id);
    }
    return resp;
}

/**
 * Utility: Escape HTML entities
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
