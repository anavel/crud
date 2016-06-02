$(function () {
    // Replace the <textarea class="ckeditor"> with a CKEditor
    // instance, using default configuration.
    CKEDITOR.replaceClass = 'ckeditor';

    //bootstrap WYSIHTML5 - text editor
    $(".bootstrap-wysihtml5").wysihtml5();
    $('select').selectize();
});