jQuery(document).ready(function ($) {
    $('.upload-image-button').click(function (e) {
        e.preventDefault();

        const button = $(this);
        const fieldId = button.data('field-id');

        const customUploader = wp.media({
            title: 'Select Image',
            button: {
                text: 'Use this image'
            },
            multiple: false
        });

        customUploader.on('select', function () {
            const attachment = customUploader.state().get('selection').first().toJSON();
            $('#' + fieldId).val(attachment.url);
            $('#' + fieldId + '_preview').html('<img src="' + attachment.url + '" style="max-width:100px;">');
        });

        customUploader.open();
    });
});
