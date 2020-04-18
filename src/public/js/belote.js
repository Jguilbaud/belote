//Ecoute hub Mercure
//const url = new URL('http://localhost:3000/.well-known/mercure');
//url.searchParams.append('topic', 'https://localhost/belote/back/game/15');

//const eventSource = new EventSource(url);

 // The callback will be called every time an update is published
 //eventSource.onmessage = e => console.log(e); // do something with the payload
 
 $(document).ready(function(){
	 
	 // Rejoindre la partie
	 $(".btnJoinGame").on('click', function(event){
		 event.preventDefault();
		 // alert($(this).attr('id'));
		 var position = 'guest';
		 switch($(this).attr('id')){
			 case 'joinAsNorth': 
				 position = 'N';
				 break;
			 case 'joinAsEast': 
				 position = 'E';
				 break;
			 case 'joinAsSouth': 
				 position = 'S';
				 break;
			 case 'joinAsWest': 
				 position = 'W';
				 break;
		 
		 }
		 
		 $.post('/belote/join/'+$("#hashGame").val(),
				    {
			 			pseudo: $("#pseudo").val(),
			 			playerPosition: position
				    }, function(data) {
				        alert(data);
				        
				      //JoinGame - Mercure event
				        const url = new URL('http://localhost:3000/.well-known/mercure');
				        url.searchParams.append('topic', 'http://localhost/belote/game/d6a5b30070');
				        const eventSource = new EventSource(url);
				        eventSource.onmessage = e => {
				       	 $("#mercure_messages").append(e.data+'<br />');
				        };
				});
		 
		 
	 }); // fin onclick
		 

	 
 });
 

 // ----