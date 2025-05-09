<h1>Bonjour {{ $participant->name }},</h1>

<p>Vous êtes invité(e) à rejoindre la session : <strong>{{ $meet->title }}</strong></p>

<p><strong>Date :</strong> {{ $meet->scheduled_at }}</p>

<p>Voici votre lien de participation :</p>
<p><a href="https://meet.enumera.tech/{{ $meet->jitsi_room_name }}?jwt={{ $participant->meet_token }}">
    Rejoindre la session
</a></p>

<p>Merci.</p>
