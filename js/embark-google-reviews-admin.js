jQuery(document).ready(function ($) {
    $('textarea[data-editor]').each(function () {
        var textarea = $(this);
        var mode = textarea.data('editor');
        var editDiv = $('<div>', {
            position: 'absolute',
            width: textarea.width(),
            height: textarea.height(),
            'class': textarea.attr('class')
        }).insertBefore(textarea);
        textarea.css('display', 'none');
        window.editor = ace.edit(editDiv[0]);
        editor.renderer.setShowGutter(textarea.data('gutter'));
        editor.getSession().setValue(textarea.val());
        editor.getSession().setMode("ace/mode/" + mode);
        editor.setTheme("ace/theme/github");

        // copy back to textarea on form submit...
        textarea.closest('form').submit(function () {
            textarea.val(editor.getSession().getValue());
        })
    });
})

const resetTemplate = function () {
    const template = document.querySelector("#embark-reviews-html-template");
    const textarea = document.querySelector("#template_0");
    editor.setValue(template.innerHTML);
}