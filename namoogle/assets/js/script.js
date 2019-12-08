var timer;

$(document).ready(function() {
    $(".result").on("click", function() {
        var url = $(this).attr("href");
        var id = $(this).attr("data-linkId");   // custom attribute in HTML
        
        if (!id) {
            alert("data-linkId attribute not found");
        }        
        
        increaseLinkClicks(id, url); 
        
        // Will wait for ajax call to finish and then reroute user to the link clicked
        return false;       
    });
    
    var grid = $(".imageResults");
    
    // When the masonry layout finishes loading, set the CSS grid items to visible
    // To prevent images from bunching up in a corner on load 
    grid.on("layoutComplete", function() {
        $(".gridItem img").css("visibility", "visible");
    });
    
    grid.masonry({
        itemSelector: ".gridItem",
        columnWidth: 200,
        gutter: 5,
        isInitLayout: false
    });
    
    // Use fancybox to preview images
    $("[data-fancybox]").fancybox({
        caption: function(instance, item) {
            var caption = $(this).data('caption') || '';
            var siteUrl = $(this).data('siteurl') || '';

            if ( item.type === 'image' ) {
                caption = (caption.length ? caption + '<br />' : '')
                 + '<a href="' + item.src + '">View image</a><br>' 
                 + '<a href="' + siteUrl + '">View page</a>';
            }

            return caption;
        },
        afterShow: function(instance, item) {
            increaseImageClicks(item.src);
        }
    });
    
});

function loadImage(src, className) {
    var image = $("<img>");
    
    image.on("load", function() {
        $("." + className + " a").append(image);
        
        clearTimeout(timer);
        
        timer = setTimeout(function() {
            $(".imageResults").masonry();
        }, 500);
        
    });
    
    // if image is broken, update it on database so will not list the image again
    image.on("error", function() {
        $("." + className).remove();    // Remove the image from being listed
        console.log(src);
        $.post("ajax/setBroken.php", {src: src});
    });
    
    // Attach an attribute to the img tag
    image.attr("src", src);
}

function increaseLinkClicks(linkId, url) {
    // Post AJAX call
    $.post("ajax/updateLinkCount.php", {linkId: linkId})
    .done(function (result) {
        // result comes from the post ajax call
        if (result != "") {
            alert(result);
            return;
        }
        
        window.location.href = url;
    });
}

function increaseImageClicks(imageURL) {
    // Post AJAX call
    $.post("ajax/updateImageCount.php", {imageURL: imageURL})
    .done(function (result) {
        // result comes from the post ajax call
        if (result != "") {
            alert(result);
            return;
        }
        
    });
}