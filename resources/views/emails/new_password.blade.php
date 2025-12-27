<x-mail::message>
    # Votre nouveau mot de passe

    Bonjour,

    Suite à votre demande de mot de passe oublié, un nouveau mot de passe a été généré pour votre compte.

    Voici votre nouveau mot de passe temporaire :
    **{{ $newPassword }}**

    Nous vous recommandons vivement de le changer dès votre première connexion dans les paramètres de votre profil.

    <x-mail::button :url="url('/')">
        Se connecter
    </x-mail::button>

    Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet e-mail.

    Cordialement,<br>
    L'équipe {{ config('app.name') }}
</x-mail::message>