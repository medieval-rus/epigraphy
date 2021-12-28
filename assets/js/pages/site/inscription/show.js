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
});