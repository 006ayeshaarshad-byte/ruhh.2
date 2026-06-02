const music = document.getElementById("background-music");
const toggle = document.getElementById("music-toggle");

toggle.addEventListener("change", function () {
    if (this.checked) {
        music.play();
    } else {
        music.pause();
    }
});
 

