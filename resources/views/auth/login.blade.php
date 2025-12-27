@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
    <div x-show="!isAuthenticated" class="auth-bg min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-[480px]">
            <div class="text-center mb-10">
                <div
                    class="w-16 h-16 bg-brand-600 rounded-2xl shadow-xl shadow-brand-500/20 mx-auto flex items-center justify-center text-white mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-display font-bold text-slate-900 mb-2">
                    <span x-show="authMode === 'login'">Content de vous revoir</span>
                    <span x-show="authMode === 'register'">Créer un compte</span>
                    <span x-show="authMode === 'forgot-password'">Mot de passe oublié</span>
                    <span x-show="authMode === 'reset-password'">Réinitialisation</span>
                </h1>
                <p class="text-slate-500 font-medium">
                    <span x-show="authMode === 'login'">Accédez à votre espace de gestion</span>
                    <span x-show="authMode === 'register'">Rejoignez notre plateforme académique</span>
                    <span x-show="authMode === 'forgot-password'">Entrez votre email pour recevoir votre nouveau mot de
                        passe</span>
                    <span x-show="authMode === 'reset-password'">Choisissez votre nouveau mot de passe</span>
                </p>
            </div>

            <div class="premium-card rounded-[2.5rem] p-8 md:p-12 border border-slate-200/60">
                <!-- Login & Register Form -->
                <form x-show="authMode === 'login' || authMode === 'register'" @submit.prevent="handleAuth()"
                    class="space-y-6">
                    <div x-show="authMode === 'register'" x-transition class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Nom complet</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </span>
                            <input type="text" x-model="authData.name" placeholder="Ex: Jean Dupont"
                                class="w-full input-premium py-4 pl-12 pr-4 rounded-2xl"
                                :required="authMode === 'register'">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Adresse email</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </span>
                            <input type="email" x-model="authData.email" placeholder="mail@exemple.com"
                                class="w-full input-premium py-4 pl-12 pr-4 rounded-2xl" required>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between items-center ml-1">
                            <label class="text-sm font-bold text-slate-700">Mot de passe</label>
                            <button x-show="authMode === 'login'" type="button" @click="authMode = 'forgot-password'"
                                class="text-xs font-bold text-brand-600 hover:underline">Oublié ?</button>
                        </div>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 118 0v4h-8z" />
                                </svg>
                            </span>
                            <input :type="showPassword ? 'text' : 'password'" x-model="authData.password"
                                placeholder="••••••••" class="w-full input-premium py-4 pl-12 pr-12 rounded-2xl" required>
                        </div>
                    </div>

                    <button type="submit" :disabled="isLoading"
                        class="w-full btn-brand py-4 rounded-2xl text-white font-bold text-lg shadow-lg shadow-brand-500/30 flex items-center justify-center gap-3">
                        <template x-if="!isLoading"><span
                                x-text="authMode === 'login' ? 'Se connecter' : 'Créer le compte'"></span></template>
                        <template x-if="isLoading"><svg class="animate-spin h-6 w-6 text-white" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg></template>
                    </button>

                    <div class="text-center pt-2">
                        <p class="text-slate-500 font-medium">
                            <span x-text="authMode === 'login' ? 'Nouveau ici ?' : 'Déjà un compte ?'"></span>
                            <button type="button" @click="toggleAuthMode()"
                                class="text-brand-600 font-bold hover:underline ml-1">
                                <span x-text="authMode === 'login' ? 'Ouvrir un compte' : 'Se connecter'"></span>
                            </button>
                        </p>
                    </div>

                    <div x-show="authMode === 'login'" class="relative py-4">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-xs uppercase"><span
                                class="bg-white px-2 text-slate-400 font-bold tracking-widest">Ou continuer avec</span>
                        </div>
                    </div>

                    <button x-show="authMode === 'login'" type="button" @click="loginWithGoogle()"
                        class="w-full py-4 border-2 border-slate-100 rounded-2xl flex items-center justify-center gap-4 font-bold text-slate-700 hover:bg-slate-50 hover:border-slate-200 transition-all">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" class="w-6 h-6"
                            alt="Google">
                        Google
                    </button>
                </form>

                <!-- Forgot Password Form -->
                <form x-show="authMode === 'forgot-password'" @submit.prevent="forgotPassword()" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Adresse email</label>
                        <input type="email" x-model="authData.email" placeholder="mail@exemple.com"
                            class="w-full input-premium py-4 px-6 rounded-2xl" required>
                    </div>
                    <button type="submit" :disabled="isLoading"
                        class="w-full btn-brand py-4 rounded-2xl text-white font-bold text-lg shadow-lg">Recevoir un nouveau
                        mot de passe</button>
                    <button type="button" @click="authMode = 'login'"
                        class="w-full text-sm font-bold text-slate-400 hover:text-slate-600">Retour à la connexion</button>
                </form>

                <!-- Reset Password Form -->
                <form x-show="authMode === 'reset-password'" @submit.prevent="resetPassword()" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Nouveau mot de passe</label>
                        <input type="password" x-model="resetData.password" placeholder="••••••••"
                            class="w-full input-premium py-4 px-6 rounded-2xl" required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Confirmer le mot de passe</label>
                        <input type="password" x-model="resetData.password_confirmation" placeholder="••••••••"
                            class="w-full input-premium py-4 px-6 rounded-2xl" required>
                    </div>
                    <button type="submit" :disabled="isLoading"
                        class="w-full btn-brand py-4 rounded-2xl text-white font-bold text-lg shadow-lg">Réinitialiser le
                        mot de passe</button>
                </form>
            </div>
        </div>
    </div>
@endsection