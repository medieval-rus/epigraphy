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
    
    initEpidocViewer();
});


/**
 * EpiDoc XML Viewer
 * Parses and renders EpiDoc XML with syntax highlighting and structured view
 */
function initEpidocViewer() {
    const dataScript = document.getElementById('epidoc-data');
    const stubScript = document.getElementById('epidoc-stub-data');
    const tableContainer = document.getElementById('epidoc-text-in-table');
    const tableApparatusContainer = document.getElementById('epidoc-apparatus-in-table');
    const fullReadingsContainer = document.getElementById('epidoc-full-readings-in-text');
    const fullReadingsToggle = document.getElementById('epidoc-full-readings-toggle');

    if (!dataScript) {
        // If no data but table container exists, show placeholder
        if (tableContainer) {
            tableContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">EpiDoc данные отсутствуют</span>';
        }
        if (tableApparatusContainer) {
            tableApparatusContainer.innerHTML = '<span style="color: #6c757d; font-style: italic;">EpiDoc данные отсутствуют</span>';
        }
        if (fullReadingsContainer) {
            fullReadingsContainer.innerHTML = '';
        }
        if (fullReadingsToggle) {
            fullReadingsToggle.hidden = true;
            fullReadingsToggle.setAttribute('aria-expanded', 'false');
            fullReadingsToggle.classList.remove('epidoc-translations-toggle--open');
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
        if (fullReadingsContainer) {
            fullReadingsContainer.innerHTML = '';
        }
        if (fullReadingsToggle) {
            fullReadingsToggle.hidden = true;
            fullReadingsToggle.setAttribute('aria-expanded', 'false');
            fullReadingsToggle.classList.remove('epidoc-translations-toggle--open');
        }
        return;
    }

    renderTableView(xmlDoc, stubDoc);
}

function applyBracketSystemToServerRenderedEdition(container, system) {
    if (!container) {
        return;
    }

    const suppliedNodes = container.querySelectorAll('[data-epidoc-hook="supplied"]');
    suppliedNodes.forEach(node => {
        if (!(node instanceof HTMLElement)) {
            return;
        }
        const originalText = node.dataset.originalText ?? stripCombiningDotBelowMarks(node.textContent ?? '');
        node.dataset.originalText = originalText;

        const reason = node.dataset.suppliedReason || (node.classList.contains('epidoc-supplied--editorial') ? 'editorial' : 'lost');
        if (reason === 'unclear') {
            if (system === 'zaliznyak') {
                node.textContent = `[${originalText}]`;
            } else {
                node.innerHTML = renderUnderdottedHtml(originalText);
            }
            return;
        }
        const brackets = (BRACKET_SYSTEMS[system] && BRACKET_SYSTEMS[system].supplied[reason])
            || BRACKET_SYSTEMS[system].supplied.lost;
        node.textContent = `${brackets[0]}${originalText}${brackets[1]}`;
    });

    const unclearNodes = container.querySelectorAll('[data-epidoc-hook="unclear"]');
    unclearNodes.forEach(node => {
        if (!(node instanceof HTMLElement)) {
            return;
        }
        const originalText = node.dataset.originalText ?? stripCombiningDotBelowMarks(node.textContent ?? '');
        node.dataset.originalText = originalText;

        if (system === 'zaliznyak') {
            node.textContent = `[${originalText}]`;
            node.classList.remove('epidoc-unclear--leiden');
        } else {
            node.innerHTML = renderUnderdottedHtml(originalText);
            node.classList.add('epidoc-unclear--leiden');
        }
    });

    const gapNodes = container.querySelectorAll('[data-epidoc-hook="gap"]');
    gapNodes.forEach(node => {
        if (!(node instanceof HTMLElement)) {
            return;
        }
        const gapMeta = getGapMetadataFromDataset(node);
        const originalText = node.dataset.originalText ?? node.textContent ?? '';
        node.dataset.originalText = originalText;
        node.textContent = getGapDisplayText(system, gapMeta);
    });

    container.dataset.epidocSystem = system;
}

function stripCombiningDotBelowMarks(text) {
    return text.replace(/\u0323/g, '');
}

function renderUnderdottedHtml(text) {
    let result = '';
    for (const char of text) {
        if (/\s/.test(char)) {
            result += char;
            continue;
        }
        result += `<span class="epidoc-underdot-char">${escapeHtml(char)}</span>`;
    }
    return result;
}

function getGapMetadataFromElement(gapElement) {
    if (!(gapElement instanceof Element)) {
        return {
            quantity: null,
            unit: '',
            extent: '',
            reason: ''
        };
    }

    const quantityRaw = gapElement.getAttribute('quantity');
    const quantity = quantityRaw !== null ? Number.parseInt(quantityRaw, 10) : null;

    return {
        quantity: Number.isInteger(quantity) && quantity > 0 ? quantity : null,
        unit: (gapElement.getAttribute('unit') || '').trim().toLowerCase(),
        extent: (gapElement.getAttribute('extent') || '').trim().toLowerCase(),
        reason: (gapElement.getAttribute('reason') || '').trim().toLowerCase()
    };
}

function getGapMetadataFromDataset(gapNode) {
    if (!(gapNode instanceof HTMLElement)) {
        return {
            quantity: null,
            unit: '',
            extent: '',
            reason: ''
        };
    }

    const quantityRaw = gapNode.dataset.gapQuantity;
    const quantity = quantityRaw !== undefined ? Number.parseInt(quantityRaw, 10) : null;

    return {
        quantity: Number.isInteger(quantity) && quantity > 0 ? quantity : null,
        unit: (gapNode.dataset.gapUnit || '').trim().toLowerCase(),
        extent: (gapNode.dataset.gapExtent || '').trim().toLowerCase(),
        reason: (gapNode.dataset.gapReason || '').trim().toLowerCase()
    };
}

function getGapDisplayText(system, gapMeta = {}) {
    const quantity = Number.isInteger(gapMeta.quantity) && gapMeta.quantity > 0 ? gapMeta.quantity : null;
    const unit = (gapMeta.unit || '').toLowerCase();
    const extent = (gapMeta.extent || '').toLowerCase();
    const reason = (gapMeta.reason || '').toLowerCase();

    if (quantity !== null && unit === 'character') {
        if (system === 'zaliznyak') {
            return '-'.repeat(quantity);
        }
        return '*'.repeat(quantity);
    }

    if (system !== 'zaliznyak' && reason === 'lost' && extent === 'unknown') {
        return '[---]';
    }

    return system === 'zaliznyak' ? '...' : '***';
}

function getGapTitle(gapMeta = {}) {
    if (Number.isInteger(gapMeta.quantity) && gapMeta.quantity > 0 && gapMeta.unit === 'character') {
        return `Gap: ${gapMeta.quantity} character${gapMeta.quantity === 1 ? '' : 's'}`;
    }

    const extent = gapMeta.extent || '?';
    return `Gap: ${extent}`;
}

function trimRenderedEditionEdges(container) {
    if (!(container instanceof HTMLElement)) {
        return;
    }

    const root = container.querySelector('[data-epidoc-role="edition-root"], #edition') || container;
    trimLeadingBoundary(root);
    trimTrailingBoundary(root);
}

function trimLeadingBoundary(node) {
    let current = node;

    while (current && current.firstChild) {
        const first = current.firstChild;

        if (first.nodeType === Node.TEXT_NODE) {
            const value = first.textContent || '';
            const trimmed = value.replace(/^\s+/, '');
            if (trimmed === '') {
                current.removeChild(first);
                continue;
            }
            if (trimmed !== value) {
                first.textContent = trimmed;
            }
            break;
        }

        if (first.nodeType === Node.ELEMENT_NODE && first.nodeName === 'BR') {
            current.removeChild(first);
            continue;
        }

        if (first.nodeType === Node.ELEMENT_NODE) {
            current = first;
            continue;
        }

        break;
    }
}

function trimTrailingBoundary(node) {
    let current = node;

    while (current && current.lastChild) {
        const last = current.lastChild;

        if (last.nodeType === Node.TEXT_NODE) {
            const value = last.textContent || '';
            const trimmed = value.replace(/\s+$/, '');
            if (trimmed === '') {
                current.removeChild(last);
                continue;
            }
            if (trimmed !== value) {
                last.textContent = trimmed;
            }
            break;
        }

        if (last.nodeType === Node.ELEMENT_NODE && last.nodeName === 'BR') {
            current.removeChild(last);
            continue;
        }

        if (last.nodeType === Node.ELEMENT_NODE) {
            current = last;
            continue;
        }

        break;
    }
}

function restoreMissingSpacingBeforeVariantApps(container) {
    if (!(container instanceof HTMLElement)) {
        return;
    }

    const appNodes = container.querySelectorAll('[data-epidoc-role="app"], .epidoc-app, .app');
    appNodes.forEach(appNode => {
        const previous = appNode.previousSibling;
        if (!previous || previous.nodeType !== Node.TEXT_NODE) {
            return;
        }

        const value = previous.textContent || '';
        if (value === '' || /\s$/.test(value)) {
            return;
        }

        if (/[,:;.!?]$/u.test(value)) {
            previous.textContent = `${value} `;
        }
    });
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

function extractEditionDiv(xmlDoc) {
    if (!xmlDoc) {
        return null;
    }
    const textBody = getTextBody(xmlDoc);
    const editions = getDivsByType(textBody, 'edition');
    return editions.length > 0 ? editions[0] : null;
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
    const fullReadingsContainer = document.getElementById('epidoc-full-readings-in-text');
    const fullReadingsToggle = document.getElementById('epidoc-full-readings-toggle');
    const serverEditionContainer = document.querySelector('[data-epidoc-render-source="xslt"]');
    const systemToggle = document.querySelector('.epidoc-system-toggle-btn');
    
    // Parse bibliography map
    const bibliographyMap = parseBibliography(xmlDoc);
    const stubBibliographyMap = stubDoc ? parseBibliography(stubDoc) : null;
    
    // Initial bracket system (default: Leiden = false in toggle)
    let currentSystem = getEpidocSystemPreference();
    let readingsToggleBound = false;
    let systemToggleBound = false;

    function setupReadingsToggle() {
        if (!fullReadingsToggle || !fullReadingsContainer || readingsToggleBound) {
            return;
        }
        readingsToggleBound = true;

        fullReadingsToggle.addEventListener('click', () => {
            const isExpanded = fullReadingsToggle.getAttribute('aria-expanded') === 'true';
            const nextExpanded = !isExpanded;
            const showTitle = fullReadingsContainer.dataset.showReadingsTitle || 'Показать все прочтения';
            const hideTitle = fullReadingsContainer.dataset.hideReadingsTitle || 'Скрыть прочтения';

            fullReadingsToggle.setAttribute('aria-expanded', String(nextExpanded));
            fullReadingsToggle.classList.toggle('epidoc-translations-toggle--open', nextExpanded);
            fullReadingsToggle.title = nextExpanded ? hideTitle : showTitle;

            const block = fullReadingsContainer.querySelector('.epidoc-alt-readings-block');
            if (block) {
                block.classList.toggle('epidoc-alt-readings-block--collapsed', !nextExpanded);
            }
        });
    }

    function renderContent() {
        if (serverEditionContainer) {
            applyBracketSystemToServerRenderedEdition(serverEditionContainer, currentSystem);
            trimRenderedEditionEdges(serverEditionContainer);
            restoreMissingSpacingBeforeVariantApps(serverEditionContainer);
        }

        // Render for table widget (if exists)
        if (tableContainer || tableApparatusContainer || tableTranslationsContainer || fullReadingsContainer) {
            const textBody = getTextBody(xmlDoc);
            if (textBody) {
                const editions = getDivsByType(textBody, 'edition');
                const edition = editions.length > 0 ? editions[0] : null;
                if (edition) {
                    if (tableContainer) {
                        let textContent = renderEditionContent(edition, currentSystem);
                        textContent = normalizeEditionHtml(textContent);
                        tableContainer.innerHTML = textContent;
                        trimRenderedEditionEdges(tableContainer);
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
                    if (fullReadingsContainer) {
                        const rendered = renderReadingsIntoContainer(
                            fullReadingsContainer,
                            fullReadingsToggle,
                            xmlDoc,
                            bibliographyMap,
                            currentSystem
                        ) || renderReadingsIntoContainer(
                            fullReadingsContainer,
                            fullReadingsToggle,
                            stubDoc,
                            stubBibliographyMap,
                            currentSystem
                        );
                        if (!rendered) {
                            fullReadingsContainer.innerHTML = '';
                            if (fullReadingsToggle) {
                                fullReadingsToggle.hidden = true;
                                fullReadingsToggle.setAttribute('aria-expanded', 'false');
                                fullReadingsToggle.classList.remove('epidoc-translations-toggle--open');
                            }
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
                    if (fullReadingsContainer) {
                        fullReadingsContainer.innerHTML = '';
                        if (fullReadingsToggle) {
                            fullReadingsToggle.hidden = true;
                            fullReadingsToggle.setAttribute('aria-expanded', 'false');
                            fullReadingsToggle.classList.remove('epidoc-translations-toggle--open');
                        }
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
                if (fullReadingsContainer) {
                    fullReadingsContainer.innerHTML = '';
                    if (fullReadingsToggle) {
                        fullReadingsToggle.hidden = true;
                        fullReadingsToggle.setAttribute('aria-expanded', 'false');
                        fullReadingsToggle.classList.remove('epidoc-translations-toggle--open');
                    }
                }
            }
            
        }

        updateSystemToggle(systemToggle, currentSystem);
    }
    
    function handleToggleClick() {
        currentSystem = currentSystem === 'zaliznyak' ? 'leiden' : 'zaliznyak';
        setEpidocSystemPreference(currentSystem);
        renderContent();
    }

    function setupSystemToggle() {
        if (!systemToggle || systemToggleBound) {
            return;
        }
        systemToggleBound = true;
        systemToggle.addEventListener('click', handleToggleClick);
    }
    
    setupReadingsToggle();
    setupSystemToggle();

    // Initial render
    renderContent();
}

function getEpidocSystemPreference() {
    const value = readCookie('epidoc_system');
    if (value === 'zaliznyak' || value === 'leiden') {
        return value;
    }
    return 'zaliznyak';
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

function updateSystemToggle(toggle, system) {
    if (!toggle) {
        return;
    }

    const nextSystemLabel = system === 'zaliznyak' ? 'LEID' : 'ZAL';
    toggle.textContent = nextSystemLabel;
    toggle.setAttribute('aria-label', `Переключить на ${nextSystemLabel}`);
    toggle.title = `Переключить на ${nextSystemLabel}`;
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
            let text = normalizeInWordBreakMarkers(child.textContent.replace(/\s+/g, ' '), system);
            if (!text.trim()) {
                const prevElementForGap = getSiblingElement(node.childNodes, index, -1);
                const nextElementForGap = getSiblingElement(node.childNodes, index, 1);
                if (prevElementForGap && prevElementForGap.localName === 'supplied'
                    && nextElementForGap && nextElementForGap.localName === 'app'
                    && !/\s$/.test(result)) {
                    result += ' ';
                }
                if (prevElementForGap && prevElementForGap.localName === 'app'
                    && nextElementForGap && nextElementForGap.localName === 'app'
                    && !/\s$/.test(result)) {
                    result += ' ';
                }
                if (prevElementForGap && prevElementForGap.localName === 'app'
                    && !/[\r\n]/.test(child.textContent || '')
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
                if (hadLeadingWhitespace) {
                    text = ` ${text}`;
                }
            }
            if (nextElement && nextElement.localName === 'app') {
                const textWithoutTail = text.replace(/\s+$/, '');
                const lastToken = textWithoutTail.split(/\s+/).pop() || '';
                const endsWithPunctuation = /[,:;.!?]$/u.test(textWithoutTail);
                if (!hadTrailingWhitespace || (lastToken.length <= 2 && !endsWithPunctuation)) {
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
                        const unclearSuppliedText = stripCombiningDotBelowMarks(child.textContent || '');
                        if (system === 'zaliznyak') {
                            result += `<span class="epidoc-supplied" title="Supplied: ${reason}">[${escapeHtml(unclearSuppliedText)}]</span>`;
                        } else {
                            result += `<span class="epidoc-unclear epidoc-unclear--leiden" title="Unclear reading">${renderUnderdottedHtml(unclearSuppliedText)}</span>`;
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

                case 'hi':
                    const rend = (child.getAttribute('rend') || '').trim().toLowerCase();
                    if (rend === 'superscript') {
                        result += `<sup class="epidoc-hi-superscript">${renderEditionContent(child, system)}</sup>`;
                        break;
                    }
                    if (rend === 'subscript') {
                        result += `<sub class="epidoc-hi-subscript">${renderEditionContent(child, system)}</sub>`;
                        break;
                    }
                    result += renderEditionContent(child, system);
                    break;
                    
                case 'lb':
                    result = result.replace(/\s+$/, '');
                    if (isInWordLineBreak(child)) {
                        result += getInWordLineBreakMarker(system);
                    }
                    if (!result.endsWith('<br />')) {
                        result += '<br />';
                    }
                    break;
                    
                case 'gap':
                    const gapMeta = getGapMetadataFromElement(child);
                    const gapMarker = getGapDisplayText(system, gapMeta);
                    result += `<span class="epidoc-gap" title="${escapeHtml(getGapTitle(gapMeta))}" data-gap-quantity="${gapMeta.quantity ?? ''}" data-gap-unit="${escapeHtml(gapMeta.unit)}" data-gap-extent="${escapeHtml(gapMeta.extent)}" data-gap-reason="${escapeHtml(gapMeta.reason)}">${gapMarker}</span>`;
                    break;
                    
                case 'unclear':
                    const unclearText = stripCombiningDotBelowMarks(child.textContent || '');
                    if (system === 'zaliznyak') {
                        result += `[${escapeHtml(unclearText)}]`;
                    } else {
                        result += `<span class="epidoc-unclear epidoc-unclear--leiden" title="Unclear reading">${renderUnderdottedHtml(unclearText)}</span>`;
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
        
        // Keep inline markup (e.g. hi[@rend='superscript']) in apparatus cells.
        let lemHtml = lem ? renderEditionContent(lem, system) : '';
        
        // Build alternative readings
        let alternatives = [];
        for (let j = 0; j < readings.length; j++) {
            const rdg = readings[j];
            const rdgHtml = renderEditionContent(rdg, system);
            const rdgResp = rdg.getAttribute('resp') || '';
            alternatives.push({
                html: rdgHtml,
                resp: rdgResp
            });
        }
        
        // Create row
        const lemDisplay = lemHtml;
        
        const altDisplay = alternatives.map(alt => {
            const resolvedResp = resolveResp(alt.resp, bibliographyMap);
            const respTag = resolvedResp ? ` <span class="apparatus-resp">${escapeHtml(resolvedResp)}</span>` : '';
            return `${alt.html}${respTag}`;
        }).join('<br>');
        
        rows += `
            <tr>
                <td class="apparatus-lem-cell">${lemDisplay}</td>
                <td class="apparatus-rdg-cell">${altDisplay}</td>
            </tr>`;
    }
    return rows;
}

function splitRespValues(resp) {
    if (!resp) {
        return [];
    }
    return resp.trim().split(/\s+/).filter(Boolean);
}

function collectWitnessRespValues(editionDiv) {
    const values = new Set();
    if (!editionDiv) {
        return [];
    }

    const appElements = editionDiv.getElementsByTagName('app');
    for (let i = 0; i < appElements.length; i++) {
        const readings = appElements[i].getElementsByTagName('rdg');
        for (let j = 0; j < readings.length; j++) {
            const respValues = splitRespValues(readings[j].getAttribute('resp') || '');
            respValues.forEach(resp => values.add(resp));
        }
    }

    return Array.from(values);
}

function pickWitnessReading(appNode, witnessResp) {
    const lem = appNode.querySelector('lem');
    const readings = appNode.querySelectorAll('rdg');

    if (!witnessResp) {
        return lem || (readings.length > 0 ? readings[0] : null);
    }

    for (let i = 0; i < readings.length; i++) {
        const rdgRespValues = splitRespValues(readings[i].getAttribute('resp') || '');
        if (rdgRespValues.includes(witnessResp)) {
            return readings[i];
        }
    }

    return lem || (readings.length > 0 ? readings[0] : null);
}

function buildWitnessReadingText(node, system, witnessResp) {
    let result = '';

    for (const child of node.childNodes) {
        if (child.nodeType === Node.TEXT_NODE) {
            result += normalizeInWordBreakMarkers(child.textContent.replace(/\s+/g, ' '), system);
            continue;
        }

        if (child.nodeType !== Node.ELEMENT_NODE) {
            continue;
        }

        const tagName = child.localName;
        if (tagName === 'app') {
            const selectedReading = pickWitnessReading(child, witnessResp);
            if (selectedReading) {
                result += buildWitnessReadingText(selectedReading, system, witnessResp);
            }
            continue;
        }

        if (tagName === 'supplied') {
            const reason = child.getAttribute('reason') || 'lost';
            if (reason === 'unclear') {
                const unclearSuppliedText = buildWitnessReadingText(child, system, witnessResp);
                if (system === 'zaliznyak') {
                    result += `[${unclearSuppliedText}]`;
                } else {
                    result += applyDotBelowMarks(unclearSuppliedText);
                }
                continue;
            }

            const brackets = BRACKET_SYSTEMS[system].supplied[reason] || BRACKET_SYSTEMS[system].supplied.lost;
            result += `${brackets[0]}${buildWitnessReadingText(child, system, witnessResp)}${brackets[1]}`;
            continue;
        }

        if (tagName === 'unclear') {
            const unclearText = buildWitnessReadingText(child, system, witnessResp);
            if (system === 'zaliznyak') {
                result += `[${unclearText}]`;
            } else {
                result += applyDotBelowMarks(unclearText);
            }
            continue;
        }

        if (tagName === 'gap') {
            result += getGapDisplayText(system, getGapMetadataFromElement(child));
            continue;
        }

        if (tagName === 'lb') {
            result = result.replace(/\s+$/, '');
            if (isInWordLineBreak(child)) {
                result += getInWordLineBreakMarker(system);
            }
            result += '\n';
            continue;
        }

        result += buildWitnessReadingText(child, system, witnessResp);
    }

    return result;
}

function normalizeWitnessReadingText(text) {
    return text
        .replace(/[ \t]+\n/g, '\n')
        .replace(/\n[ \t]+/g, '\n')
        .replace(/[ \t]{2,}/g, ' ')
        .trim();
}

function formatReadingTextForHtml(text) {
    return escapeHtml(text).replace(/\n/g, '<br>');
}

function buildFullReadingsEntries(editionDiv, bibliographyMap, system) {
    const entries = [];
    const witnesses = collectWitnessRespValues(editionDiv);

    for (let i = 0; i < witnesses.length; i++) {
        const witnessResp = witnesses[i];
        const reader = resolveResp(witnessResp, bibliographyMap) || witnessResp;
        const readingText = normalizeWitnessReadingText(buildWitnessReadingText(editionDiv, system, witnessResp));
        if (!readingText) {
            continue;
        }

        entries.push({ reader, text: readingText });
    }

    return entries;
}

function renderReadingsIntoContainer(container, toggle, xmlDoc, bibliographyMap, system) {
    if (!container || !xmlDoc) {
        return false;
    }

    const editionDiv = extractEditionDiv(xmlDoc);
    if (!editionDiv) {
        return false;
    }

    if (editionDiv.getElementsByTagName('app').length === 0) {
        return false;
    }

    const entries = buildFullReadingsEntries(editionDiv, bibliographyMap, system);
    if (!entries.length) {
        return false;
    }

    const isExpanded = !!(toggle && toggle.getAttribute('aria-expanded') === 'true');

    let items = '';
    for (let i = 0; i < entries.length; i++) {
        const entry = entries[i];
        items += `
            <div class="epidoc-reading-line">
                <span class="epidoc-resp-badge">${escapeHtml(entry.reader)}</span>
                <div class="epidoc-alt-reading-text">${formatReadingTextForHtml(entry.text)}</div>
            </div>`;
    }

    const blockClass = isExpanded
        ? 'epidoc-alt-readings-block'
        : 'epidoc-alt-readings-block epidoc-alt-readings-block--collapsed';

    container.innerHTML = `<div class="${blockClass}">${items}</div>`;
    if (toggle) {
        const showTitle = container.dataset.showReadingsTitle || 'Показать все прочтения';
        const hideTitle = container.dataset.hideReadingsTitle || 'Скрыть прочтения';
        toggle.hidden = false;
        toggle.classList.toggle('epidoc-translations-toggle--open', isExpanded);
        toggle.title = isExpanded ? hideTitle : showTitle;
    }

    return true;
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
            result += normalizeInWordBreakMarkers(child.textContent.replace(/\s+/g, ' '), system);
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
                result += getGapDisplayText(system, getGapMetadataFromElement(child));
            } else if (tagName === 'lb') {
                result = result.replace(/\s+$/, '');
                if (isInWordLineBreak(child)) {
                    result += getInWordLineBreakMarker(system);
                }
                result += '\n';
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

function isInWordLineBreak(node) {
    if (!node || node.nodeType !== Node.ELEMENT_NODE) {
        return false;
    }

    return node.getAttribute('break') === 'no' || node.getAttribute('type') === 'inWord';
}

function getInWordLineBreakMarker(system) {
    return system === 'zaliznyak' ? '⸗' : '-';
}

function normalizeInWordBreakMarkers(text, system) {
    return system === 'zaliznyak' ? text : text.replace(/⸗/g, '-');
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
