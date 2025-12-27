@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div x-show="isAuthenticated && user.role === 'admin'" class="min-h-screen flex">
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
                <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4 ml-4">Tableau de bord</p>
                <button
                    class="w-full flex items-center gap-4 px-6 py-4 sidebar-item-active rounded-2xl font-bold transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    Étudiants
                </button>
            </nav>
            <div class="mt-auto pt-8 border-t border-slate-200">
                <div class="bg-white p-4 rounded-3xl shadow-sm border border-slate-100 flex items-center gap-4 mb-6">
                    <template x-if="user.photo">
                        <img :src="'/' + user.photo"
                            class="w-12 h-12 rounded-2xl object-cover border border-slate-100 shadow-sm">
                    </template>
                    <template x-if="!user.photo">
                        <div class="w-12 h-12 rounded-2xl bg-brand-100 text-brand-600 flex items-center justify-center font-bold text-xl"
                            x-text="user.name ? user.name[0] : 'A'"></div>
                    </template>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-slate-900 truncate" x-text="user.name"></p>
                        <p class="text-[10px] font-bold text-brand-600 uppercase tracking-widest">Administrateur</p>
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
            <div class="max-w-6xl mx-auto space-y-12">
                <!-- Header -->
                <div class="flex flex-col md:flex-row justify-between md:items-end gap-6">
                    <div>
                        <h2 class="text-4xl font-display font-bold text-slate-900 mb-2">Annuaire Étudiant</h2>
                        <p class="text-slate-500 font-medium">Gérez les dossiers et informations académiques</p>
                    </div>
                    <button @click="openModal()"
                        class="w-full md:w-auto btn-brand text-white px-10 py-4 rounded-2xl font-bold shadow-xl shadow-brand-500/20 flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Inscrire un étudiant
                    </button>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Stat Card -->
                    <div class="bg-white p-8 rounded-[2.5rem] border border-slate-100 shadow-sm">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <p class="text-3xl font-display font-bold text-slate-900" x-text="students.length"></p>
                        <p class="text-slate-500 font-semibold text-sm">Total Inscrits</p>
                    </div>
                    <!-- Add more stats if needed -->
                </div>

                <!-- Table -->
                <div class="bg-white rounded-[3rem] shadow-xl shadow-slate-200/40 p-1 border border-slate-100">
                    <div x-show="students.length === 0" class="text-center py-20 px-6">
                        <p class="text-slate-400">Aucun étudiant inscrit pour le moment.</p>
                    </div>
                    <div x-show="students.length > 0" class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="text-slate-400 font-bold text-[11px] uppercase tracking-[0.2em]">
                                    <th class="px-10 py-8">Profil Étudiant</th>
                                    <th class="px-10 py-8">Coordonnées</th>
                                    <th class="px-10 py-8 text-right pr-12">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                <template x-for="student in students" :key="student.id">
                                    <tr class="group hover:bg-slate-50/80 transition-all duration-300">
                                        <td class="px-10 py-7">
                                            <div class="flex items-center gap-5">
                                                <div class="relative group cursor-pointer"
                                                    @click="$refs['photoInput' + student.id].click()">
                                                    <template x-if="student.photo">
                                                        <img :src="'/' + student.photo"
                                                            class="w-14 h-14 rounded-2xl object-cover border-2 border-white shadow-sm transition-transform group-hover:scale-105">
                                                    </template>
                                                    <template x-if="!student.photo">
                                                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center font-bold text-brand-600 text-xl border-2 border-white shadow-sm group-hover:bg-slate-200 transition-colors"
                                                            x-text="student.first_name[0] + student.last_name[0]"></div>
                                                    </template>
                                                    <div
                                                        class="absolute inset-0 bg-black/20 rounded-2xl flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                    </div>
                                                </div>
                                                <input type="file" :x-ref="'photoInput' + student.id" class="hidden"
                                                    accept="image/*" @change="uploadPhoto($event, student.id)">
                                                <div>
                                                    <p class="text-lg font-bold text-slate-900 leading-none mb-1.5"
                                                        x-text="student.first_name + ' ' + student.last_name"></p>
                                                    <p
                                                        class="text-xs text-brand-600 font-bold bg-brand-50 px-2 py-0.5 rounded-full inline-block uppercase tracking-wider">
                                                        Étudiant</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-7">
                                            <div class="space-y-1">
                                                <p class="text-slate-600 font-medium" x-text="student.email"></p>
                                                <p class="text-xs text-slate-400 font-medium"
                                                    x-text="student.phone || 'Pas de numéro'"></p>
                                            </div>
                                        </td>
                                        <td class="px-10 py-7">
                                            <div
                                                class="flex justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                                <button @click="editStudent(student)"
                                                    class="p-4 bg-white shadow-sm border border-slate-100 text-brand-600 rounded-2xl hover:bg-brand-600 hover:text-white transition-all transform hover:scale-110">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                </button>
                                                <button @click="deleteStudent(student.id)"
                                                    class="p-4 bg-white shadow-sm border border-slate-100 text-rose-500 rounded-2xl hover:bg-rose-500 hover:text-white transition-all transform hover:scale-110">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal -->
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
                <h3 class="text-3xl font-display font-bold text-slate-900"
                    x-text="editingId ? 'Modifier l\'étudiant' : 'Inscrire un étudiant'"></h3>
                <p class="text-slate-500 font-medium">Saisissez les informations de l'élève</p>
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
                    <label class="text-sm font-bold text-slate-700 ml-1">Email</label>
                    <input type="email" x-model="formData.email" required class="w-full input-premium p-4 rounded-2xl">
                </div>
                <div class="space-y-2">
                    <label class="text-sm font-bold text-slate-700 ml-1">Téléphone</label>
                    <input type="text" x-model="formData.phone" class="w-full input-premium p-4 rounded-2xl">
                </div>
                <div class="pt-6">
                    <button type="submit"
                        class="w-full btn-brand text-white py-5 rounded-3xl font-bold text-lg shadow-xl shadow-brand-500/20"
                        x-text="editingId ? 'Mettre à jour' : 'Inscrire'"></button>
                </div>
            </form>
        </div>
    </div>
@endsection