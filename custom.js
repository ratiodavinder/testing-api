document.addEventListener('DOMContentLoaded', function() {
    var primaryElement = document.getElementById('primary');
    
    if (primaryElement) {
        primaryElement.addEventListener('click', function() {
            window.location.href = custom_array.url;
        });
    }
});



