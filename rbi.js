// Replace all 404 images on webpage with a placeholder.
document.addEventListener('DOMContentLoaded', function() {
    window.addEventListener('error', function(e) {
        var img_ele = e.target; // e.target is more compatible with modern browsers

        // Ensure the target element is an image
        if (img_ele.tagName !== 'IMG') {
            return;
        }

        // If this image has already been processed, skip it to prevent infinite loops.
        if (img_ele.classList.contains('4xx-image-replaced')) {
            return;
        }

        // Replace the broken image with the default image URL passed from PHP
        var defaultImageUrl = my_script_data.img_url;

        img_ele.src = defaultImageUrl;

        // Clear srcset and sizes attributes to avoid conflicts
        img_ele.removeAttribute('srcset');
        img_ele.removeAttribute('sizes');

        // Add a class to mark this image as replaced
        img_ele.classList.add('4xx-image-replaced');

        // Set max sizes for the newly loaded placeholder image
        img_ele.style.maxWidth = img_ele.getAttribute('width') + 'px';
        img_ele.style.maxHeight = img_ele.getAttribute('height') + 'px';

        console.log("Replaced Image with Placeholder.");
    }, true);
});
