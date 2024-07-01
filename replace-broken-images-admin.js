jQuery(document).ready(function ($) {
    $('#select-image').on('click', function (e) {
        e.preventDefault();
        var image_frame;
        if (image_frame) {
            image_frame.open();
        }
        image_frame = wp.media({
            title: 'Select or Upload Image',
            library: { type: 'image' },
            button: { text: 'Use this image' },
            multiple: false
        });
        image_frame.on('select', function () {
            var attachment = image_frame.state().get('selection').first().toJSON();
            $('#img_url').val(attachment.url);
            $('#image-preview').attr('src', attachment.url).show();
        });
        image_frame.open();
    });
});
