// Replace all 404 images on webpage with a placeholder.
window.addEventListener('error', function(e) {
    var img_ele = e.srcElement; 
    console.log(img_ele.src); // The 404 url

    // if not image, ignore.
    if( img_ele.tagName != 'IMG'){
        return;
    }
    // if the 404 image is from a 3rd party (like tracking pixels). Ignore.
    if( ! img_ele.src.includes(window.location.hostname) ){
        return;
    }
    // if this code already processed this image skip it. Prevents infinite loops.
    if( img_ele.classList.contains('4xx-image-replaced')){
        return;
    }

    // IMPORTANT FOR DEVs:
    // If the placeholder is also 404 the browser will go in infinite loop and crash !
    img_ele.src = "http://meissa.test/wp-content/uploads/Logo_White.png";
    img_ele.removeAttribute('srcset');
    img_ele.removeAttribute('sizes');
    img_ele.classList.add('4xx-image-replaced');
    // Set the max sizes for the newly loaded placeholder.
    img_ele.style.maxWidth = img_ele.getAttribute('width') + 'px';
    img_ele.style.maxHeight = img_ele.getAttribute('height') + 'px';
    
    console.log("Replaced Image with Placeholder. "); 
}, true);