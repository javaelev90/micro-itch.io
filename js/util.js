
// Removes game iframe overlay and sets the iframe src
function runGame() {
    var iframe = document.getElementById("game-window");
    var iframeOverlay = document.getElementById("iframe-overlay");
    iframe.src = iframe.dataset.src; 
    iframeOverlay.style.display = "none";
    iframe.style.display = "flex";
}

// Loads the uploaded file image into img tag
function loadImage(input){
    if(input.files && input.files[0]){
        var reader = new FileReader();

        reader.onload = (e) => {
            var img = document.getElementById("cover-image-preview");
            img.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

// Shows an overlay
function activateDeleteOverlay(){
    setDeleteOverlay("flex");
}

// Removes an overlay 
function inactivateDeleteOverlay(){
    setDeleteOverlay("none");
}

// Sets diplay css for delete-overlay tag
function setDeleteOverlay(display) {
    var overlay = document.getElementById("delete-overlay");
    overlay.style.display = display;
}

// Sets the game-window iframe to fullscreen if allowed
function iframeFullscreen() {
    // Check if browser allows fullscreen
    if (document.fullscreenEnabled          || 
        document.webkitFullscreenEnabled    || 
        document.mozFullScreenEnabled       ||
        document.msFullscreenEnabled        ) {
        
        // Retrieve game iframe
        var iframe = document.getElementById("game-window");
        
        // Set game screen to full screen
        if (iframe.requestFullscreen) {
            iframe.requestFullscreen();
        } else if (iframe.webkitRequestFullscreen) {
            iframe.webkitRequestFullscreen();
        } else if (iframe.mozRequestFullScreen) {
            iframe.mozRequestFullScreen();
        } else if (iframe.msRequestFullscreen) {
            iframe.msRequestFullscreen();
        }
        
    }
}

// Trick to find out width of scrollbar, used to prevent horizontal scrollbar to appear in css f.e. the footer in header.css
document.addEventListener('DOMContentLoaded', () => {
    document.documentElement.style.setProperty('--scrollbar-width', (window.innerWidth - document.documentElement.clientWidth) + "px");
});
