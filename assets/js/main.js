window.addEventListener("load", function () {

    // INTRO
    const intro = document.getElementById("intro");
    const video = document.getElementById("introVideo");

    if (intro && video) {

        if (!sessionStorage.getItem("introPlayed")) {

            video.play().catch(() => {});

            video.onended = function () {

                intro.classList.add("hide");

                setTimeout(() => {
                    intro.remove();
                }, 800);

                sessionStorage.setItem("introPlayed", "true");
            };

        } else {
            intro.remove();
        }

    }

    // SEARCH
    const searchInput = document.getElementById("searchInput");

    if (searchInput) {

        searchInput.addEventListener("keyup", function () {

            let keyword = this.value;

            fetch("search.php?q=" + encodeURIComponent(keyword))
                .then(res => res.text())
                .then(data => {
                    document.getElementById("searchResult").innerHTML = data;
                });

        });

    }

});
const input = document.getElementById("searchInput");

if(input){

    input.addEventListener("keyup",function(){

        fetch("search.php?q="+this.value)

        .then(r=>r.text())

        .then(html=>{

            document.getElementById("searchResult").innerHTML=html;

        });

    });

}
const modal = document.getElementById("searchModal");

if(modal){

    modal.addEventListener("shown.bs.modal", function(){

        fetch("search.php")
        .then(r=>r.text())
        .then(html=>{

            document.getElementById("searchResult").innerHTML = html;

        });

    });

}
document.addEventListener("DOMContentLoaded", function () {

    document.querySelectorAll("input, textarea, select").forEach(function(el){

        el.setAttribute("autocomplete","off");
        el.setAttribute("spellcheck","false");
        el.setAttribute("autocapitalize","off");

    });

});