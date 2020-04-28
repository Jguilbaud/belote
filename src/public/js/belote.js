function sendPostToBack(uri,data,callbackSuccess){
	$.post(BASE_URL+uri,data,callbackSuccess);	   
}
function redirectToUri(uri){
	window.location.href = BASE_URL+uri;
}


function getPlayerName(playerPosition){
	return $("#playerName_"+playerPosition).text()
}


function logEvent(message){
	$("#mercure_messages p").append(message+'<br />');
}

function setGameMessage(message){
	$("#gameInfo").html(message);
}

function setCurrentPlayer(playerPosition){
	$("#currentPlayerToPlay").html(getPlayerName(playerPosition));
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
	 		case 'cardplayed' :   	 			
	 			cardPlayed(response.data);       	 			
	 			break;
	 		case 'changeturn' :   	 			
	 			changeTurn(response.data);       	 			
	 			break;	
	 		case 'changeround' :   	 			
	 			changeRound(response.data);       	 			
	 			break;	
	 		case 'endgame' :   	 			
	 			endGame(response.data);       	 			
	 			break;	
   	 	}
   	 
    };
	
	
}

function addCardInHand(cardCode){	
	$("#myCards .cards").append('<img src="'+BASE_URL+'/img/cards/'+cardCode+'.png" id="mycard_'+cardCode+'" />');
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


function setCutDeckEvents(){
	$("#cutDeck #btnCutDeck").on('click', function(event){
		 event.preventDefault();
		// On désactive la zone de pli à tout le monde
		$("#turnCards").addClass('hidden');
		 sendPostToBack('/play/'+$("#hashGame").val()+'/cutdeck', {value:$('#cutDeckValue').val()},function(){
			 $("#chooseTrump").addClass('hidden');
		 });
		 
	 });
}

function setPlayCardEvents(){
	
	$("#myCards .cards img").on('click', function(event){
		 
		 $("#myCards .cards img").removeClass("selected");
		 $(this).addClass("selected");		 
	 });
	
	
	$("#myCards #btnPlayCard").on('click', function(event){
		
		if($("#myCards .cards img.selected").length){
			var card = $("#myCards .cards img.selected").attr("id").replace('mycard_','');

			sendPostToBack('/play/'+$("#hashGame").val()+'/playcard', {card:card},function(){
				$("#myCards .cards img.selected").remove();
			 });
		}else{
			alert("Vous devez choisir une carte");
		}
		
	});
	
}

function showProposedTrump(data){
	
	// Joueur actif : currentPlayerToPlay
	setCurrentPlayer(data.firstPlayer);
	
	// Numéro de manche
	$("#round_id").html(data.numRound);
        
    // On cache la coupe de deck
    $("#cutDeck").addClass("hidden");
	
	// proposedTrumpCard
    // On affiche la carte
    $("#proposedTrumpCard img").attr('src',BASE_URL+'/img/cards/'+data.proposedTrumpCard+'.png');
    $("#chooseTrump").removeClass("hidden");

    // On désactive les choix d'atouts poru ensuite activer seulement celui
	// qu'il faut
    $("#chooseTrump img.trump").removeClass("disabled").addClass("disabled");
    // On active seulement le premier
    switch(data.proposedTrumpCard.substr(0,1)){
        case 's' :
            $("#chooseTrump_spade").removeClass("disabled");
            break;
        case 'h' :
            $("#chooseTrump_heart").removeClass("disabled");
            break;
        case 'd' :
            $("#chooseTrump_diamond").removeClass("disabled");
            break;
        case 'c' :
            $("#chooseTrump_club").removeClass("disabled");
            break;
    }
    setChooseTrumpEvents();

	// Cartes
	jQuery.each(data.cards, function(index, value) {
		addCardInHand(value)
	});
	
}


function chooseTrumpNextPlayer(data){
	// Joueur actif : currentPlayerToPlay
	setCurrentPlayer(data.newPlayer);
	
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
	logEvent('[Choix atout] '+getPlayerName(data.precedentPlayer)+' passe');
}



function startFirstTurn(data){
	// Joueur actif : currentPlayerToPlay
	setCurrentPlayer(data.newPlayer);
	
	// On remet toutes les cartes (triées) dans la main
	$("#myCards .cards").html("");
	jQuery.each(data.cards, function(index, value) {
		addCardInHand(value)
	});
	
	// On affiche l'atout demandé
	$("#trumpColorSymbol").html('<img src="'+BASE_URL+'/img/'+trumpColor+'.png" />');
	$("#trumpColorTaker").html(getPlayerName(data.taker));

	
	// On cache le choix de l'atout
	$("#chooseTrump").addClass('hidden');

	// On active la possibilité de jouer
	$("#turnCards").removeClass('hidden');
	// On permet de sélectionner les cartes
	setPlayCardEvents();
	
	// On active le bouton de choix de carte au premier joueur
	if($("#playerPosition").val() == data.newPlayer){
		$("#btnPlayCard").removeAttr('disabled');
	}
	
}



function cardPlayed(data){
	logEvent('[Carte jouée] '+getPlayerName(data.player)+' a joué : '+data.card);
	// On affiche la carte
        if(data.cardPosition == 1){
            // Si c'est la première carte on vide les emplacements
            $("#turnCards  .playerCard").html('');
            $("#turnCards  .playerName").html('');
        }
        // On affiche la carte
	$("#turnCards #subboard_"+data.cardPosition+" .playerCard").html('<img src="'+BASE_URL+'/img/cards/'+data.card+'.png" />');
	$("#turnCards #subboard_"+data.cardPosition+" .playerName").html(getPlayerName(data.player));
	
	// Joueur actif : currentPlayerToPlay
	setCurrentPlayer(data.newPlayer);

	// Si on est désormais le joueur actif
	if($("#playerPosition").val() == data.newPlayer){
		$("#btnPlayCard").removeAttr('disabled');
	}else{
		$("#btnPlayCard").attr('disabled','disabled');
	}
}

function changeTurn(data){
    logEvent('[Carte jouée] '+getPlayerName(data.player)+' a joué : '+data.card);
    
    // {"action":"changeturn","data":{"hashGame":"e6405d1c32","player":"n","card":"sa","winner":"n","newTurnNum":3}}
    
    // On affiche la carte
    $("#turnCards #subboard_"+data.cardPosition+" .playerCard").html('<img src="'+BASE_URL+'/img/cards/'+data.card+'.png" />');
    $("#turnCards #subboard_"+data.cardPosition+" .playerName").html(getPlayerName(data.player));
    
    
    // On affiche le gagnant
    setGameMessage(getPlayerName(data.winner)+' gagne le pli !');
    logEvent(getPlayerName(data.winner)+' gagne le pli !');

    // Joueur actif : currentPlayerToPlay
   $("#currentPlayerToPlay").html(getPlayerName(data.winner));
        
    // Si on est désormais le joueur actif
    if($("#playerPosition").val() == data.winner){
            $("#btnPlayCard").removeAttr('disabled');
    }else{
            $("#btnPlayCard").attr('disabled','disabled');
    }
}
 
function changeRound(data){
	logEvent('[Carte jouée] '+getPlayerName(data.player)+' a joué : '+data.card);
	logEvent('## Nouvelle manche n° : '+data.newRoundNum);
	logEvent(' - Donneur : '+getPlayerName(data.dealer));

        // On affiche la carte
	$("#turnCards #subboard_"+data.cardPosition+" .playerCard").html('<img src="'+BASE_URL+'/img/cards/'+data.card+'.png" />');
	$("#turnCards #subboard_"+data.cardPosition+" .playerName").html(getPlayerName(data.player));
	
        
	// On met à jour le numéro de manche
	$("#round_id").html(data.newRoundNum);
        
	// On désactive la zone de pli à tout le monde
	$("#btnPlayCard").attr('disabled','disabled');

	// On met à jour les points
	$("#points table tbody").append('<tr><td>'+data.points.numRound+'</td><td>'+data.points.pointsNS+' ('+data.points.totalPointsNS+')</td><td>'+data.points.pointsWE+' ('+data.points.totalPointsWE+')</td></tr>');
	
	setGameMessage(getPlayerName(data.cutter)+' doit couper le deck');
	
	// Si on est désormais le joueur actif
        if($("#playerPosition").val() == data.cutter){
            $("#cutDeck").removeClass('hidden');
        }
}
function endGame(data){
	// TODO
}

$(document).ready(function(){
	setMercureEventHandler();
				        
	// Rejoindre la partie
	setJoinGameEvents();		
	 
	// Coupe deck cartes
	setCutDeckEvents();
	 
	// Choix atout
	setChooseTrumpEvents();
	
	// Jouer une carte
	setPlayCardEvents();
 });
 

 // ----
