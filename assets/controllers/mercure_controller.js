import {Controller} from 'stimulus';

export default class extends Controller {
    connect() {
        $(document).ready(function () {
            const eventSource = new EventSource('http://sf.local/.well-known/mercure?topic=test', {withCredentials: true});
            eventSource.onmessage = event => {
                const data = JSON.parse(event.data);
                const message = document.createElement('li');
                message.setAttribute('class', 'list-group-item');
                message.innerText = data.message;
                $('#messages').append(message);
            }
        });

        $('#sendMessageBtn').click(function () {
            $.ajax({
                type: 'POST',
                url: 'http://sf.local/mercure/publish',
                data: {
                    'message': $('[name=message]').val()
                },
                dataType: 'json'
            });
        });
    }
}
