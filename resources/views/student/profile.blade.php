@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
    <div x-show="isAuthenticated && user.role === 'student'" class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-80 bg-slate-50 border-r border-slate-200/60 hidden xl:flex flex-col p-8 fixed h-full z-50">
            <div class="flex items-center gap-4 mb-12 ml-2">
                <div
                    class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-brand-500/20">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" />
                    </svg>
                </div>
                <span class="text-2xl font-display font-bold text-slate-900 tracking-tight">S-Gestion</span>
            </div>
            <nav class="flex-1 space-y-3">
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 ml-4">Espace Étudiant</p>
                <button
                    class="w-full flex items-center gap-4 px-6 py-4 sidebar-item-active rounded-2xl font-bold transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Mon Profil
                </button>
            </nav>
            <div class="mt-auto pt-8 border-t border-slate-200">
                <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 mb-6">
                <template x-if="user.photo">
                    <img :src="'/' + user.photo" class="w-12 h-12 rounded-2xl object-cover border border-slate-100 shadow-sm">
                </template>
                <template x-if="!user.photo">
                    <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center font-bold text-xl" x-text="user.name ? user.name[0] : 'S'"></div>
                </template>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 truncate" x-text="user.name"></p>
                        <p class="text-[10px] font-bold text-brand-600 uppercase tracking-widest">Étudiant</p>
                    </div>
                </div>
                <button @click="logout()"
                    class="w-full flex items-center justify-center gap-3 py-4 rounded-2xl text-rose-500 font-bold hover:bg-rose-50 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    Déconnecter
                </button>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 xl:ml-80 bg-slate-50 min-h-screen p-6 md:p-12">
            <div class="max-w-4xl mx-auto space-y-12">
                <!-- Header -->
                <div>
                    <h2 class="text-4xl font-display font-bold text-slate-900 mb-2">Mon Profil Académique</h2>
                    <p class="text-slate-500 font-medium">Consultez et mettez à jour vos informations personnelles</p>
                </div>

                <!-- Profile Info -->
                <template x-if="students.length > 0">
                    <div class="space-y-8">
                        <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/40 p-10 border border-slate-100">
                            <div class="flex flex-col md:flex-row items-center gap-10 mb-10 pb-10 border-b border-slate-50">
                                <template x-if="students[0].photo">
                                    <div class="relative group cursor-pointer" @click="$refs.photoInput.click()">
                                        <img :src="'/' + students[0].photo" class="w-32 h-32 rounded-[2.5rem] object-cover shadow-xl border-4 border-white transition-transform group-hover:scale-105">
                                        <div class="absolute inset-0 bg-black/40 rounded-[2.5rem] flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="!students[0].photo">
                                    <div class="w-32 h-32 rounded-[2.5rem] bg-brand-50 flex items-center justify-center text-brand-600 text-5xl font-bold shadow-inner cursor-pointer hover:bg-brand-100 transition-colors" @click="$refs.photoInput.click()" x-text="students[0].first_name[0] + students[0].last_name[0]"></div>
                                </template>
                                <input type="file" x-ref="photoInput" class="hidden" accept="image/*" @change="uploadPhoto($event, students[0].id)">
                                <div class="text-center md:text-left">
                                    <h3 class="text-3xl font-bold text-slate-900 mb-1"
                                        x-text="students[0].first_name + ' ' + students[0].last_name"></h3>
                                    <p
                                        class="text-brand-600 font-bold flex items-center justify-center md:justify-start gap-2">
                                        <span class="w-2 h-2 bg-brand-500 rounded-full animate-pulse"></span>
                                        Dossier Actif
                                    </p>
                                </div>
                                <div class="ml-auto">
                                    <button @click="editStudent(students[0])"
                                        class="px-8 py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-slate-800 transition-all flex items-center gap-3 shadow-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                        Modifier mes infos
                                    </button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                                <div class="space-y-1">
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Prénom</p>
                                    <p class="text-lg font-semibold text-slate-700" x-text="students[0].first_name"></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Nom</p>
                                    <p class="text-lg font-semibold text-slate-700" x-text="students[0].last_name"></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Email
                                        Académique</p>
                                    <p class="text-lg font-semibold text-slate-700" x-text="students[0].email"></p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Téléphone</p>
                                    <p class="text-lg font-semibold text-slate-700"
                                        x-text="students[0].phone || 'Non renseigné'"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <template x-if="students.length === 0">
                    <div class="bg-white rounded-[3rem] p-16 text-center border border-slate-100 shadow-sm">
                        <div
                            class="w-20 h-20 bg-amber-50 rounded-3xl flex items-center justify-center mx-auto mb-6 text-amber-500">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-2">Fiche non trouvée</h3>
                        <p class="text-slate-500 max-w-md mx-auto">Votre compte n'est pas encore relié à une fiche
                            étudiante. Veuillez contacter l'administration pour finaliser votre inscription.</p>
                    </div>
                </template>
            </div>
        </main>
    </div>

    <!-- Edit Modal -->
    <div x-show="isModalOpen"
        class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-md">
        <div class="bg-white w-full max-w-xl rounded-[3rem] p-10 md:p-14 border border-slate-200 shadow-2xl relative"
            @click.away="closeModal()">
            <button @click="closeModal()"
                class="absolute right-8 top-8 p-3 hover:bg-slate-100 rounded-2xl text-slate-400 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="mb-10">
                <h3 class="text-3xl font-display font-bold text-slate-900">Modifier mon profil</h3>
                <p class="text-slate-500 font-medium">Mettez à jour vos coordonnées personnelles</p>
            </div>
            <form @submit.prevent="saveStudent()" class="space-y-6">
                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Prénom</label>
                        <input type="text" x-model="formData.first_name" required
                            class="w-full input-premium p-4 rounded-2xl">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1">Nom</label>
                        <input type="text" x-model="formData.last_name" required
                            class="w-full input-premium p-4 rounded-2xl">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 ml-1">Téléphone</label>
                    <input type="text" x-model="formData.phone" class="w-full input-premium p-4 rounded-2xl">
                </div>
                <div class="pt-6">
                    <button type="submit"
                        class="w-full btn-brand text-white py-5 rounded-3xl font-bold text-lg shadow-xl shadow-brand-500/20">Enregistrer
                        les modifications</button>
                </div>
            </form>
        </div>
    </div>
@endsection