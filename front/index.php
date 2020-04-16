<?php
setcookie('mercureAuthorization','eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJtZXJjdXJlIjp7InN1YnNjcmliZSI6WyIqIl0sInB1Ymxpc2giOlsiKiJdfX0.aFuPpA3XL8PhSoZ1S4EhwvgB2iTSVGrYGyE1fT2pd6g');
?>

<!DOCTYPE html>
<html>

<head>
    <title>Belote par Johan</title>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <style>

    body {
        background-color : #33AA33;

    }

     #pannel_infos{
        background-color : rgba(0,0,0,0.3);
     }

    #pannel_infos > div {
        margin-top : 10px;
    }


    </style>
</head>
<body class="h100">

        <div class="row h-100">
            <div class="col-md-8 h-100">
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">Coéquipier en face moi</div>
                    <div class="col-md-3"></div>
                </div>
                <div class="row">
                    <div class="col-md-2">Adversaire gauche</div>
                    <div class="col-md-8">table</div>
                    <div class="col-md-2">Adversaire droite</div>
                </div>
                <div class="row">
                 Moi
                </div>
            </div>
            <div class="col-md-4 h-100" id="pannel_infos">
                <div>
                    <h1>Belote par Johan</h1>
                    <p>v0.1-alpha1</p>
                    <p>
                        <b>Partie :</b> xxxx<br />
                        <b>Manche n°:</b> #xxx
                    </p>

                </div>

                <div id="points">
                    <h3>Points</h3>
                    <table>
                    <thead><tr><td>#</td><td>Nord-Sud</td><td>Ouest-Est</td></tr></thead>
                     <tbody>
                     <tr><td>1</td><td>+23 (23)</td><td>+70 (70)</td></tr>
                     <tr><td>2</td><td>+85 (108)</td><td>+12 (82)</td></tr>
                     </tbody>
                    </table>
                </div>


                <div id="messages">
                    <h3>Journal d'évènements</h3>
                    <p></p>
                </div>

            </div>
        </div>

    <script type="text/javascript">
		//Ecoute hub Mercure
		const url = new URL('http://localhost:3000/.well-known/mercure');
	    url.searchParams.append('topic', 'https://localhost/belote/back/game/15');

	    const eventSource = new EventSource(url);

    	 // The callback will be called every time an update is published
    	 eventSource.onmessage = e => console.log(e); // do something with the payload
	</script>

</body>

</html>
