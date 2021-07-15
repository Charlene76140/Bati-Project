function help(){
    let help = document.getElementById("needHelp");
    help.classList.remove("hidden");
    help.classList.add("layer");
    hiddenLayer(help);
}

function hiddenLayer(help){
    let hidden = document.getElementById("hiddenLayer");
    hidden.addEventListener("click", function(){
        help.classList.remove("layer");
        help.classList.add("hidden");
    })
}