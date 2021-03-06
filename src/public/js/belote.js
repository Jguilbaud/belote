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
	$("#game_messages p").prepend(message+'<br />');
}

function setGameMessage(message){
	$("#gameInfo").html(message);
}

function setCurrentPlayer(playerPosition){
	$("#currentPlayerToPlay").html(getPlayerName(playerPosition));
}

function getColorImgSrc(colorCode){
	switch(colorCode){
	case 'h': return BASE_URL+'/img/heart.png';
	case 'd': return BASE_URL+'/img/diamond.png';
	case 's': return BASE_URL+'/img/spade.png';
	case 'c': return BASE_URL+'/img/club.png';
		default:
			return;
	}
	
}

function setMercureEventHandler(){
	const url = new URL(MERCURE_BASE_URL);
    url.searchParams.append('topic', BASE_URL+'/game/'+$("#hashGame").val());
    
    if($("#playerPosition").val()){
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

   	 		case 'choosetrumpnextplayer' :   	 			
   	 			chooseTrumpNextPlayer(response.data);       	 			
	 			break;
	 			
	 		case 'recutdeck' :   	 			
	 			recutDeck(response.data);       	 			
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
	$(".btnJoinGame").unbind('click');
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
	$("#chooseTrump img.trump:not(.disabled)").unbind('click');
	$("#chooseTrump #btnChooseTrump").unbind('click');
	$("#chooseTrump #btnPassTrump").unbind('click');
					
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
	$("#cutDeck #btnCutDeck").unbind('click');
	$("#cutDeck #btnCutDeck").on('click', function(event){
		 event.preventDefault();
		// On désactive la zone de pli à tout le monde
		$("#turnCards").addClass('hidden');
		 sendPostToBack('/play/'+$("#hashGame").val()+'/cutdeck', {value:$('#cutDeckValue').val()},function(){
		 });
		 
	 });
}

function setPlayCardEvents(){
	$("#myCards .cards img").unbind('click');
	$("#myCards #btnPlayCard").unbind('click');
	
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
	setCurrentPlayer(data.newPlayer);
	
	// Numéro de manche
	$("#round_id").html(data.numRound);

    // On cache la coupe de deck
    $("#cutDeck").addClass("hidden");
    
            
	// On désactive la zone de pli à tout le monde
	// On vide et cache la zone de jeu
	$("#turnCards").addClass("hidden");
	$("#turnCards div.playerCard").html('');
	$("#turnCards div.playerName").html('');
	
	// proposedTrumpCard
    // On affiche la carte
    $("#proposedTrumpCard img").attr('src',BASE_URL+'/img/cards/'+data.proposedTrumpCard+'.png');
    $("#chooseTrump").removeClass("hidden");
    
    // Si le joueur devient le joueur actif, on active les boutons
	if($("#playerPosition").val() == data.newPlayer){
		$("#btnPassTrump").removeAttr("disabled");
		$("#btnChooseTrump").removeAttr("disabled");
	}else{
		// Sinon on les désactive
		$("#btnPassTrump").attr("disabled","disabled");
		$("#btnChooseTrump").attr("disabled","disabled");
	}

    // On désactive les choix d'atouts pour ensuite activer seulement celui
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
	
	// On cache la zone de jeu (cas à partir du 2eme tour)
	$("#turnCards").addClass('hidden');
	
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
		proposedTrump = $(".trump:not(.disabled)");
		$(".trump.disabled").removeClass("disabled");
		$(proposedTrump).addClass("disabled");
		$(proposedTrump).unbind('click');	
	}
	setChooseTrumpEvents();
	
	// On logs dans les messages l'evenement
	logEvent('[Choix atout] '+getPlayerName(data.precedentPlayer)+' passe');
}



function startFirstTurn(data){
	// Joueur actif : currentPlayerToPlay
	setCurrentPlayer(data.newPlayer);
	
	// On logs le choix de l'atout dans les messages l'evenement
	logEvent('[Choix atout] '+getPlayerName(data.taker)+' prend à <img class="icon" src="'+getColorImgSrc(data.trumpColor)+'" />');
	
	
	// On remet toutes les cartes (triées) dans la main
	$("#myCards .cards").html("");
	jQuery.each(data.cards, function(index, value) {
		addCardInHand(value)
	});
	
	// On affiche l'atout demandé
	$("#trumpColorSymbol").html('<img src="'+getColorImgSrc(data.trumpColor)+'" />');
	$("#trumpColorTaker").html(getPlayerName(data.taker));
	$("#trumpColor").removeClass('hidden');
	
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
	logEvent('[Carte jouée] '+getPlayerName(data.player)+' a joué : <img class="icon" src="'+getColorImgSrc(data.card.substring(0,1))+'" />'+data.card.substring(1,2));
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
    logEvent('[Carte jouée] '+getPlayerName(data.player)+' a joué : <img class="icon" src="'+getColorImgSrc(data.card.substring(0,1))+'" />'+data.card.substring(1,2));
    // On affiche le gagnant du pli
    logEvent('[Fin du tour] '+getPlayerName(data.winner)+' gagne le pli !');
    setGameMessage(getPlayerName(data.winner)+' gagne le pli !');
    logEvent('-------');
    
    // {"action":"changeturn","data":{"hashGame":"e6405d1c32","player":"n","card":"sa","winner":"n","newTurnNum":3}}
    
    // On affiche la carte
    $("#turnCards #subboard_"+data.cardPosition+" .playerCard").html('<img src="'+BASE_URL+'/img/cards/'+data.card+'.png" />');
    $("#turnCards #subboard_"+data.cardPosition+" .playerName").html(getPlayerName(data.player));
 
    // Joueur actif : currentPlayerToPlay
    $("#currentPlayerToPlay").html(getPlayerName(data.winner));
        
    // Si on est désormais le joueur actif
    if($("#playerPosition").val() == data.winner){
            $("#btnPlayCard").removeAttr('disabled');
    }else{
            $("#btnPlayCard").attr('disabled','disabled');
    }
}

function recutDeck(data){
	
	// Si on est désormais le joueur actif
    if($("#playerPosition").val() == data.player){
        $("#cutDeck").removeClass('hidden');
    	setCutDeckEvents();
    }
    setGameMessage(getPlayerName(data.cutter)+' doit couper le deck');
    
    // On vide les mains des joueurs
    $("#myCards .cards").html('');
    
    // On cache le choix de l'atout
	$("#chooseTrump").addClass('hidden');
	
}
 
function changeRound(data){
	logEvent('[Carte jouée] '+getPlayerName(data.player)+' a joué : '+data.card);
	setGameMessage(getPlayerName(data.winner)+' gagne le pli !');
    logEvent(getPlayerName(data.winner)+' gagne le pli !');
    logEvent('############ ');
	logEvent('## Nouvelle manche n° : '+data.newRoundNum);
	logEvent(' - Donneur : '+getPlayerName(data.dealer));

	// On met à jour les points
	$("#points table tbody").append('<tr><td>'+data.points.numRound+'</td><td>'+data.points.pointsNS+' ('+data.points.totalPointsNS+')</td><td>'+data.points.pointsWE+' ('+data.points.totalPointsWE+')</td></tr>');
	
    // On affiche la carte proposée à l'atout
	$("#turnCards #subboard_"+data.cardPosition+" .playerCard").html('<img src="'+BASE_URL+'/img/cards/'+data.card+'.png" />');
	$("#turnCards #subboard_"+data.cardPosition+" .playerName").html(getPlayerName(data.player));
        
	// On met à jour le numéro de manche
	$("#round_id").html(data.newRoundNum);

     // Joueur actif : currentPlayerToPlay
    $("#currentPlayerToPlay").html(getPlayerName(data.winner));
	
	setGameMessage(getPlayerName(data.cutter)+' doit couper le deck');
	
	// Si on est désormais le joueur actif
    if($("#playerPosition").val() == data.cutter){
        $("#cutDeck").removeClass('hidden');
    }
}
function endGame(data){
    logEvent('[Carte jouée] '+getPlayerName(data.player)+' a joué : '+data.card);
    
    // On affiche la carte
    $("#turnCards #subboard_"+data.cardPosition+" .playerCard").html('<img src="'+BASE_URL+'/img/cards/'+data.card+'.png" />');
    $("#turnCards #subboard_"+data.cardPosition+" .playerName").html(getPlayerName(data.player));
    
    setGameMessage('La partie est terminée !');
    
    // On désactive la zone de pli à tout le monde
    $("#btnPlayCard").attr('disabled','disabled');

    // On met à jour les points
    $("#points table tbody").append('<tr><td>'+data.points.numRound+'</td><td>'+data.points.pointsNS+' ('+data.points.totalPointsNS+')</td><td>'+data.points.pointsWE+' ('+data.points.totalPointsWE+')</td></tr>');
    // TODO autre chose à faire à la fin du jeu ? :)
	
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
