<x-mail::message>
    # Réinitialisation de mot de passe

    Bonjour,

    Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte sur
    **S-Gestion**.

    Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe :

    <x-mail::button :url="$url">
        Réinitialiser mon mot de passe
    </x-mail::button>

    Ce lien de réinitialisation expirera dans 60 minutes.

    Si vous n'avez pas demandé de réinitialisation de mot de passe, aucune autre action n'est requise.

    Cordialement,<br>
    L'équipe {{ config('app.name') }}
</x-mail::message>