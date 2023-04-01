CKEDITOR.dialog.add( 'quoteDialog', function ( editor ) {
        return {
            title: 'Цитировать исследование',
            minWidth: 400,
            minHeight: 200,
            contents: [
                {
                    id: 'tab-basic',
                    label: 'Исследование',
                    elements: [
                        {
                            type: 'select',
                            id: 'shortname',
                            label: 'Выбрать публикацию',
                            items: bibliography.map(item => [item.shortName]),
                            validate: CKEDITOR.dialog.validate.notEmpty( 'Обязательное поле' )
                        },
                        {
                            type: 'text',
                            id: 'pages',
                            label: 'Страницы',
                            validate: CKEDITOR.dialog.validate.regex( /^\d*$/, 'Ошибка' )
                        }
                    ]
                },
            ],
            onOk: function() {
                var dialog = CKEDITOR.dialog.getCurrent();
                let link = editor.document.createElement( 'a' );
                let shortName = dialog.getValueOf( 'tab-basic', 'shortname' );
                let pages = dialog.getValueOf( "tab-basic", "pages" );
                let truePages = pages ? ', ' + pages : "";
                let hrefValue = `/epigraphy/bibliography/record/list#${shortName}`;
                let textValue = `${shortName}${truePages}`;
                link.setAttribute( 'href', hrefValue );
                link.setText( textValue );
                editor.insertText('(');
                editor.insertElement( link );
                editor.insertText(')');
            }
        };
    }
)        