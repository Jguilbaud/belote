const BASE_URL = 'http://localhost/belote';
const MERCURE_BASE_URL = 'http://localhost:3000/.well-known/mercure';
function sendPostToBack(uri,data,callbackSuccess){
	$.post(BASE_URL+uri,data,callbackSuccess);	   
}
function redirectToUri(uri){
	window.location.href = BASE_URL+uri;
}


function getPlayerName(playerPosition){
	return $("#playerName_"+playerPosition).text()
}



function setMercureEventHandler(){
	const url = new URL(MERCURE_BASE_URL);
    url.searchParams.append('topic', BASE_URL+'/game/'+$("#hashGame").val());
    
    if($("#playerPosition")){
    	url.searchParams.append('topic', BASE_URL+'/game/'+$("#hashGame").val()+'/'+$("#playerPosition").val());
    }
    
    
    const eventSource = new EventSource(url, { withCredentials: true });
    eventSource.onmessage = e => {
   	 $("#mercure_messages_debug").append(e.data+'<br />');
   	 	var response = $.parseJSON(e.data);
   	 	switch(response.action){
   	 		case 'playerjoin' :
   	 			$('#name_'+response.data.newPlayerPosition).html(response.data.newPlayerName)
   	 			$('#joinas_'+response.data.newPlayerPosition).attr('disabled','disabled');
   	 			break;
   	 		case 'launchgame' :
   	 			redirectToUri('/play/'+$("#hashGame").val());       	 			
   	 			break;
   	 		case 'showproposedtrump' :   	 			
	 			showProposedTrump(response.data);       	 			
	 			break;

   	 		case 'chooseTrumpNextPlayer' :   	 			
   	 			chooseTrumpNextPlayer(response.data);       	 			
	 			break;
	 			
	 		case 'startfirstturn' :   	 			
	 			startFirstTurn(response.data);       	 			
	 			break;	
	 			
   	 	}
   	 
    };
	
	
}


function addCardInHand(cardCode){	
	$("#myCards .cards").append('<img src="'+BASE_URL+'/img/cards/'+cardCode+'.png" id="mycard_'+cardCode+'" />');
}


function setChooseTrumpEvents(){
	$("#chooseTrump img.trump:not(.disabled)").on('click', function(event){
		 
		 $("#chooseTrump img.trump").removeClass("selected");
		 $(this).addClass("selected");
		 $("#trump_selected").val($(this).attr("id"));
		 
	 });
	 // valider l'atout choisi
	 $("#chooseTrump #btnChooseTrump").on('click', function(event){
		 event.preventDefault();
		 if($("#trump_selected").val() == ""){
			 alert("Vous devez choisir un atout");
			 return;
		 }

		 	sendPostToBack('/play/'+$("#hashGame").val()+'/choosetrump', {choice: $("#trump_selected").val().replace("chooseTrump_","")},function(){
				 $("#btnPassTrump").attr('disabled','disabled');
				 $("#btnChooseTrump").attr('disabled','disabled');
		 });
		 
	 });
	 // passer
	 $("#chooseTrump #btnPassTrump").on('click', function(event){
		 event.preventDefault();
		 
		 sendPostToBack('/play/'+$("#hashGame").val()+'/choosetrump', {choice:"pass"},function(){
			 $("#btnPassTrump").attr('disabled','disabled');
			 $("#btnChooseTrump").attr('disabled','disabled');
		 });
		 
	 });
}


function setJoinGameEvents(){
	$(".btnJoinGame").on('click', function(event){
		 event.preventDefault();
		 
		 if($.trim($("#pseudo").val()) == ''){
			 alert("Erreur, vous devez indiquer votre pseudo !");
			 return;
		 }
		 
		 // alert($(this).attr('id'));
		 var position = 'guest';
		 switch($(this).attr('id')){
			 case 'joinas_n': 
				 position = 'n';
				 break;
			 case 'joinas_e': 
				 position = 'e';
				 break;
			 case 'joinas_s': 
				 position = 's';
				 break;
			 case 'joinas_w': 
				 position = 'w';
				 break;
		 
		 }
		 
		 sendPostToBack('/join/'+$("#hashGame").val(),
				    {
			 			pseudo: $("#pseudo").val(),
			 			playerPosition: position
				    }, function(data) {
				    	data = $.parseJSON(data);
				    	if(data.response == 'ok'){
				    		// On désactive les boutons et le champ de saisie
				    		$("#pseudo").attr('disabled','disabled');
				    		$(".btnJoinGame").attr('disabled','disabled');
				    		
				    		// On met le nom en face du bon emplacement
				    		$("#name_"+position).html($("#pseudo").val());
				    		
				    	}else{
				    		alert(data.error_msg);
			    		}
				    	
				});
		 
		 
	 }); // fin onclick rejoindre partie
}


function setCutDeckEvents(){
	$("#cutDeck #btnCutDeck").on('click', function(event){
		 event.preventDefault();
		 
		 sendPostToBack('/play/'+$("#hashGame").val()+'/cutdeck', {value:$('#cutDeckValue').val()},function(){
			 $("#chooseTrump").css('display','none');
		 });
		 
	 });
}



function showProposedTrump(data){
	
	// Joueur actif : currentPlayerToPlay
	$("#currentPlayerToPlay").html(getPlayerName(data.firstPlayer));
	
	// Numéro de manche
	$("#round_id").html(data.numRound);
	
	// proposedTrumpCard
	//TODO
	
	// Cartes
	jQuery.each(data.cards, function(index, value) {
		addCardInHand(value)
	});
	
}


function chooseTrumpNextPlayer(data){
	// Joueur actif : currentPlayerToPlay
	$("#currentPlayerToPlay").html(getPlayerName(data.newPlayer));
	
	// Si le joueur devient le joueur actif, on active les boutons
	if($("#playerPosition").val() == data.newPlayer){
		$("#btnPassTrump").removeAttr("disabled");
		$("#btnChooseTrump").removeAttr("disabled");
	}else{
		// Sinon on les désactive
		$("#btnPassTrump").attr("disabled","disabled");
		$("#btnChooseTrump").attr("disabled","disabled");
	}
	
	// Si on passe au deuxieme tour de choix
	if(!data.isFirstChoiceTurn){
		proposedTurn = $(".trump:not(.disabled)");
		$(".trump.disabled").removeClass("disabled");
		$(proposedTurn).addClass("disabled");
		$(proposedTurn).unbind('click');
		setChooseTrumpEvents();
		
	}
	
	// On logs dans les messages l'evenement
	$("#mercure_messages p").append('[Choix atout] '+getPlayerName(data.precedentPlayer)+' passe<br />');
}



function startFirstTurn(data){
	// Joueur actif : currentPlayerToPlay
	$("#currentPlayerToPlay").html(getPlayerName(data.newPlayer));
	
	// On remet toutes les cartes (triées) dans la main
	$("#myCards .cards").html("");
	jQuery.each(data.cards, function(index, value) {
		addCardInHand(value)
	});
	
	// On affiche l'atout demandé
	$("#trumpColorSymbol").html('<img src="'+BASE_URL+'/img/'+trumpColor+'.png" />');
	$("#trumpColorTaker").html(getPlayerName(data.taker));

	
	// On cache le choix de l'atout
	$("#chooseTrump").css('display','none');
	
	// On active la possibilité de jouer
	
	// On active le bouton de choix de carte au premier joueur
	
	
}

$(document).ready(function(){
	setMercureEventHandler();
				        
	// Rejoindre la partie
	setJoinGameEvents();		
	 
	// Coupe deck cartes
	setCutDeckEvents();
	 
	// Choix atout
	setChooseTrumpEvents();
 });
 

 // ----
