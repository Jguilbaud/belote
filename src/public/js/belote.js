//Ecoute hub Mercure
//const url = new URL('http://localhost:3000/.well-known/mercure');
//url.searchParams.append('topic', 'https://localhost/belote/back/game/15');

//const eventSource = new EventSource(url);

 // The callback will be called every time an update is published
 //eventSource.onmessage = e => console.log(e); // do something with the payload
 
 $(document).ready(function(){
	 	// JoinGame - Mercure event
        const url = new URL('http://localhost:3000/.well-known/mercure');
        url.searchParams.append('topic', 'http://localhost/belote/game/'+$("#hashGame").val());
        const eventSource = new EventSource(url, { withCredentials: true });
        eventSource.onmessage = e => {
       	 $("#mercure_messages").append(e.data+'<br />');
       	 	var response = $.parseJSON(e.data);
       	 	switch(response.action){
       	 		case 'playerjoin' :
       	 			$('#name_'+response.data.newPlayerPosition).html(response.data.newPlayerName)
       	 			$('#joinas_'+response.data.newPlayerPosition).attr('disabled','disabled');
       	 			break;
       	 		case 'launchgame' :
       	 			window.location.href = 'http://localhost/belote/play/'+$("#hashGame").val();       	 			
       	 			break;
       	 	}
       	 
        };
				        
	 // Rejoindre la partie
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
		 
		 $.post('/belote/join/'+$("#hashGame").val(),
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
		 
		 
	 }); // fin onclick
		 

	 
 });
 

 // ----
