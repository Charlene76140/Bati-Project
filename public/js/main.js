//function launched by the "help" button
function help(){
    let help = document.getElementById("needHelp");
    // I remove the "hidden" class so that the container is visible and I add the "layer" class
    help.classList.remove("hidden");
    help.classList.add("layer");
    hiddenLayer(help);
}

function hiddenLayer(help){
    let hidden = document.getElementById("hiddenLayer");
    //when clicking on the "closed" button, I remove the layer and the container containing the questions / answers disappears
    hidden.addEventListener("click", function(){
        help.classList.remove("layer");
        help.classList.add("hidden");
    })
}

//function allowing to create the cards visible to the user
function makeCard(data){
    let card = document.createElement("div");
    card.classList.add("card", "my-4")
    card.innerHTML = 
    `
        <div class="card-header text-colorPrim">
            ${data.Question}
        </div>
        <div class="card-body text-dark">
            ${data.Réponse}
        </div>    
    `
    ;
    return card;
}

//display of cards in the "questionsContainer" container
function showCard(datas){
    let questionsContainer = document.getElementById("questionsContainer");

    for(data of datas) {
        let card = makeCard(data);
        questionsContainer.appendChild(card);
    }
}

let httpRequest = new XMLHttpRequest();
let slotErreur = document.getElementById("erreur");
httpRequest.onreadystatechange = function() {
    if (httpRequest.readyState === XMLHttpRequest.DONE){
        if(httpRequest.status === 200) {
            let datas = JSON.parse(httpRequest.responseText); 
            showCard(datas);
        } 
        else{
            slotErreur.innerText = "Nous n'avons pas réussi à récupérer la FAQ";
        }
    }
    else {
        slotErreur.innerText = "Requête en cours";
    }
};
httpRequest.open('GET', 'FAQ.json', true);
httpRequest.send();

