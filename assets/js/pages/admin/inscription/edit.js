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

$(() => {
    createCheckboxes();
    copyValuesFromZeroRowToInterpretation();
    forwardChangesFromInterpretationToZeroRow();
    forwardChangesFromZeroRowToInterpretation();
    initializeAutoTranslateButtons();
    initializeTranslateAllAction();
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
                                                $('<span class="control-label__text">Часть актуальной информации</span>')
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

function initializeAutoTranslateButtons()
{
    $('[data-auto-translate-source-suffix]').each((index, dom) => {
        const targetElement = $(dom);

        if (targetElement.data('auto-translate-bound') === true) {
            return;
        }

        targetElement.data('auto-translate-bound', true);

        const targetLang = targetElement.attr('data-auto-translate-target-lang') || 'en';
        const sourceLang = targetElement.attr('data-auto-translate-source-lang') || 'ru';

        const button = $('<button type="button" class="btn btn-default btn-sm"/>')
            .text('Автоперевод в ' + targetLang.toUpperCase())
            .attr('data-auto-translate-button', 'true');

        const aiCheckboxFieldName = buildAiFlagFieldName(targetElement.attr('name') || '');
        const aiCheckboxLabelText = targetElement.attr('data-auto-translate-ai-label') || 'Переведено ИИ (черновик)';
        const aiHiddenValue = $('<input type="hidden" value="0" />')
            .attr('name', aiCheckboxFieldName);
        const aiCheckbox = $('<input type="checkbox" value="1" />')
            .prop('checked', targetElement.attr('data-auto-translate-ai-generated') === '1');
        aiHiddenValue.val(aiCheckbox.prop('checked') ? '1' : '0');
        aiCheckbox.on('change', () => {
            aiHiddenValue.val(aiCheckbox.prop('checked') ? '1' : '0');
        });
        const aiCheckboxLabel = $('<label style="font-weight: normal; margin-bottom: 6px; display: block;"></label>')
            .append(aiCheckbox, ' ' + aiCheckboxLabelText);

        const status = $('<span class="help-block" style="margin-top: 6px;"></span>');

        const fieldContainer = targetElement.closest('.form-group');
        fieldContainer.append(aiHiddenValue);
        fieldContainer.append(aiCheckboxLabel);
        fieldContainer.append(button);
        fieldContainer.append(status);
        targetElement.data('auto-translate-ai-checkbox', aiCheckbox);

        bindManualEditReset(targetElement, aiCheckbox);

        button.on('click', () => {
            const targetMeta = getTranslationTargetMeta(targetElement);
            if (null === targetMeta) {
                status.text('Не удалось определить целевое поле для перевода.');
                return;
            }

            button.prop('disabled', true);
            status.text('Переводим...');

            requestTranslatedText(targetMeta, sourceLang, targetLang)
                .then((translatedText) => {
                    return writeTranslatedValue(targetElement, translatedText, aiCheckbox)
                        .then(() => persistTranslatedField(targetElement, translatedText, true))
                        .then(() => {
                            status.text('Перевод сохранен как AI-черновик.');
                        });
                })
                .catch((error) => {
                    status.text((error && error.message) ? error.message : 'Ошибка перевода.');
                })
                .finally(() => {
                    button.prop('disabled', false);
                });
        });
    });
}

function initializeTranslateAllAction()
{
    const actionLink = $('[data-auto-translate-all-inscription]');
    if (actionLink.length === 0 || actionLink.data('auto-translate-all-bound') === true) {
        return;
    }

    actionLink.data('auto-translate-all-bound', true);

    actionLink.on('click', async (event) => {
        event.preventDefault();

        if (actionLink.data('auto-translate-all-running') === true) {
            return;
        }

        actionLink.data('auto-translate-all-running', true);
        actionLink.parent().addClass('disabled');

        const summary = {
            translated: 0,
            skipped: 0,
            errors: 0,
        };
        const excludedFields = new Set(['text', 'reconstruction', 'transliteration', 'normalization']);

        const targets = $('[data-auto-translate-source-suffix]');

        for (let index = 0; index < targets.length; index++) {
            const targetElement = $(targets[index]);
            const targetField = targetElement.attr('data-auto-translate-target-field');
            if (targetField && excludedFields.has(targetField)) {
                summary.skipped++;
                continue;
            }

            const targetMeta = getTranslationTargetMeta(targetElement);
            if (null === targetMeta) {
                summary.skipped++;
                continue;
            }

            const targetLang = targetElement.attr('data-auto-translate-target-lang') || 'en';
            const sourceLang = targetElement.attr('data-auto-translate-source-lang') || 'ru';
            const aiCheckbox = targetElement.data('auto-translate-ai-checkbox');

            try {
                const translatedText = await requestTranslatedText(targetMeta, sourceLang, targetLang);
                await writeTranslatedValue(targetElement, translatedText, aiCheckbox);
                await persistTranslatedField(targetElement, translatedText, true);
                summary.translated++;
            } catch (error) {
                summary.errors++;
            }
        }

        actionLink.parent().removeClass('disabled');
        actionLink.data('auto-translate-all-running', false);

        const message = [
            'Автоперевод завершен.',
            'Переведено: ' + summary.translated + '.',
            'Пропущено: ' + summary.skipped + '.',
            'Ошибок: ' + summary.errors + '.',
            'Нажмите "Сохранить", чтобы записать изменения.',
        ].join('\n');

        window.alert(message);
    });
}

function getTranslationTargetMeta(targetElement)
{
    const targetType = targetElement.attr('data-auto-translate-target-type') || '';
    const targetField = targetElement.attr('data-auto-translate-target-field') || '';
    const targetId = parseInt(targetElement.attr('data-auto-translate-target-id') || '', 10);

    if (!targetType || !targetField || Number.isNaN(targetId) || targetId <= 0) {
        return null;
    }

    return {
        targetType: targetType,
        targetId: targetId,
        targetField: targetField,
    };
}

function writeFieldValue(element, value)
{
    const dom = element.get(0);
    if (!dom) {
        return Promise.resolve();
    }

    if (window.CKEDITOR && dom.id && window.CKEDITOR.instances[dom.id]) {
        const editor = window.CKEDITOR.instances[dom.id];
        if (editor.status === 'ready') {
            return new Promise((resolve) => {
                let resolved = false;
                const done = () => {
                    if (resolved) {
                        return;
                    }

                    resolved = true;
                    resolve();
                };

                editor.setData(value, {callback: done});
                window.setTimeout(done, 500);
            });
        }

        element.val(value);
        if (typeof editor.once === 'function') {
            return new Promise((resolve) => {
                editor.once('instanceReady', () => {
                    let resolved = false;
                    const done = () => {
                        if (resolved) {
                            return;
                        }

                        resolved = true;
                        resolve();
                    };

                    editor.setData(value, {callback: done});
                    window.setTimeout(done, 500);
                });
            });
        }
    }

    element.val(value);
    return Promise.resolve();
}

function writeTranslatedValue(targetElement, translatedText, aiCheckbox)
{
    targetElement.data('auto-translate-programmatic-write', true);

    return writeFieldValue(targetElement, translatedText)
        .finally(() => {
            if (aiCheckbox && aiCheckbox.length > 0) {
                aiCheckbox.prop('checked', true);
                aiCheckbox.trigger('change');
            }

            // Delay unlock to the next task tick to absorb trailing synthetic events.
            window.setTimeout(() => {
                targetElement.data('auto-translate-programmatic-write', false);
            }, 0);
        });
}

function bindManualEditReset(targetElement, aiCheckbox)
{
    const resetAiCheckbox = () => {
        if (targetElement.data('auto-translate-programmatic-write') === true) {
            return;
        }

        aiCheckbox.prop('checked', false);
        aiCheckbox.trigger('change');
    };

    targetElement.on('input change', resetAiCheckbox);
    bindCkEditorChangeListener(targetElement, resetAiCheckbox);
}

function bindCkEditorChangeListener(targetElement, onChange)
{
    const dom = targetElement.get(0);
    if (!dom || !dom.id) {
        return;
    }

    let attempts = 0;
    const maxAttempts = 20;

    const tryBind = () => {
        if (window.CKEDITOR && window.CKEDITOR.instances[dom.id]) {
            const editor = window.CKEDITOR.instances[dom.id];
            if (editor.status === 'ready') {
                editor.on('change', onChange);
            } else if (typeof editor.once === 'function') {
                editor.once('instanceReady', () => {
                    editor.on('change', onChange);
                });
            }
            return;
        }

        attempts += 1;
        if (attempts < maxAttempts) {
            window.setTimeout(tryBind, 200);
        }
    };

    tryBind();
}

function buildAiFlagFieldName(targetFieldName)
{
    if (!targetFieldName) {
        return 'localized_ai_flags';
    }

    const firstBracket = targetFieldName.indexOf('[');
    if (firstBracket < 0) {
        return 'localized_ai_flags[' + targetFieldName + ']';
    }

    return 'localized_ai_flags' + targetFieldName.substring(firstBracket);
}

function requestTranslatedText(targetMeta, sourceLang, targetLang)
{
    return fetch('/admin/translation/preview', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({
            targetType: targetMeta.targetType,
            targetId: targetMeta.targetId,
            field: targetMeta.targetField,
            sourceLang: sourceLang,
            targetLang: targetLang,
        }),
    })
        .then((response) => response.json().then((body) => ({ok: response.ok, body: body})))
        .then(({ok, body}) => {
            if (!ok || !body.translatedText) {
                const message = body && body.error ? body.error : 'Ошибка перевода.';
                throw new Error(message);
            }

            return body.translatedText;
        });
}

function persistTranslatedField(targetElement, translatedText, isAiGenerated)
{
    const targetType = targetElement.attr('data-auto-translate-target-type') || '';
    const targetId = parseInt(targetElement.attr('data-auto-translate-target-id') || '', 10);
    const targetField = targetElement.attr('data-auto-translate-target-field') || '';
    const targetLocale = targetElement.attr('data-auto-translate-target-locale') || 'en';
    const csrfToken = getTranslationStoreCsrfToken();

    if (!targetType || !targetField || Number.isNaN(targetId) || targetId <= 0) {
        return Promise.resolve();
    }
    if (!csrfToken) {
        return Promise.reject(new Error('Ошибка безопасности: отсутствует CSRF token.'));
    }

    return fetch('/admin/translation/store', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-Token': csrfToken,
        },
        body: JSON.stringify({
            targetType: targetType,
            targetId: targetId,
            field: targetField,
            locale: targetLocale,
            value: translatedText,
            isAiGenerated: isAiGenerated === true ? '1' : '0',
            _token: csrfToken,
        }),
    })
        .then((response) => response.json().then((body) => ({ok: response.ok, body: body})))
        .then(({ok, body}) => {
            if (!ok) {
                const message = body && body.error ? body.error : 'Ошибка сохранения перевода.';
                throw new Error(message);
            }
        });
}

function getTranslationStoreCsrfToken()
{
    const tokenFromWindow = window.EOMR_TRANSLATION_STORE_CSRF_TOKEN;
    if (typeof tokenFromWindow === 'string' && tokenFromWindow.trim() !== '') {
        return tokenFromWindow;
    }

    return '';
}
