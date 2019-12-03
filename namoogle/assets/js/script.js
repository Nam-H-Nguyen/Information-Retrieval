$(document).ready(function() {
    $(".result").on("click", function() {
        var url = $(this).attr("href");
        var id = $(this).attr("data-linkId");
        
        if (!id) {
            alert("data-linkId attribute not found");
        }        
        
        increaseLinkClicks(id, url);        
    });
});

function increaseLinkClicks(linkId, url) {
    
}