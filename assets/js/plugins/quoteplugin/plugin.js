CKEDITOR.plugins.add( 'quoteplugin', {
    icons: 'quote',
    init: function( editor ) {
        editor.addCommand( 'quoteplugin', new CKEDITOR.dialogCommand( 'quoteDialog' ) );
        editor.ui.addButton( 'Quoteplugin', {
            label: 'Цитировать исследование',
            command: 'quoteplugin',
            icon: this.path+'icons/quote.png',
            toolbar: 'insert'
        } );
        CKEDITOR.dialog.add( 'quoteDialog', this.path + 'dialogs/quote.js' );
    }
})