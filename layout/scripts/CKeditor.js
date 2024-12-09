let editor;
const {
    ClassicEditor,
    Essentials,
    Bold,
    Italic,
    Font,
    Paragraph
} = CKEDITOR;

ClassicEditor
    .create( document.querySelector( '#editor' ), {
        plugins: [ Essentials, Bold, Italic, Font, Paragraph ],
        toolbar: [
            'undo', 'redo', '|', 'bold', 'italic', '|',
            'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor'
        ]
    } )
    .then( newEditor => {
        editor = newEditor;
    } )
    .catch( error => {
        console.error( error );
    } );