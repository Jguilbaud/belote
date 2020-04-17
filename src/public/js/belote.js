//Ecoute hub Mercure
const url = new URL('http://localhost:3000/.well-known/mercure');
url.searchParams.append('topic', 'https://localhost/belote/back/game/15');

const eventSource = new EventSource(url);

 // The callback will be called every time an update is published
 eventSource.onmessage = e => console.log(e); // do something with the payload