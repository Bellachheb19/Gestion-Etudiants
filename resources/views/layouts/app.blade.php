<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>S-Gestion | @yield('title', 'Plateforme Académique')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700&display=swap"
        rel="stylesheet">

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.7.1/firebase-auth-compat.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f7ff', 100: '#e0effe', 200: '#bbdffa', 300: '#7bc1f7',
                            400: '#34a1f1', 500: '#0a83d8', 600: '#0066b8', 700: '#005295',
                            800: '#05467c', 900: '#0a3b67',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            background-color: #f8fafc;
        }

        .auth-bg {
            background: radial-gradient(circle at 0% 0%, #e0effe 0%, transparent 40%),
                radial-gradient(circle at 100% 100%, #e0effe 0%, transparent 40%);
            background-color: #ffffff;
        }

        .premium-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.05);
        }

        .input-premium {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            background: #f1f5f9;
            border: 2px solid transparent;
        }

        .input-premium:focus {
            background: #ffffff;
            border-color: #0a83d8;
            box-shadow: 0 0 0 4px rgba(10, 131, 216, 0.1);
            outline: none;
        }

        .btn-brand {
            background: linear-gradient(135deg, #0a83d8 0%, #0066b8 100%);
            transition: all 0.3s ease;
        }

        .btn-brand:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(10, 131, 216, 0.4);
        }

        .sidebar-item-active {
            background: #ffffff;
            color: #0a83d8;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body x-data="app()" x-cloak class="min-h-screen text-slate-800">
    <!-- Toasts -->
    <div class="fixed bottom-6 right-6 z-[100] space-y-3">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="translate-y-10 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
                class="px-6 py-4 rounded-2xl bg-slate-900 text-white shadow-2xl flex items-center gap-4 min-w-[300px]">
                <div :class="toast.type === 'success' ? 'text-emerald-400' : 'text-rose-400'">
                    <template x-if="toast.type === 'success'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                    <template x-if="toast.type === 'error'">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </template>
                </div>
                <span x-text="toast.message" class="text-sm font-medium"></span>
            </div>
        </template>
    </div>

    @yield('content')

    <script>
        const firebaseConfig = {
            apiKey: "AIzaSyAY1cgHEVRYDXvnjMgGI__Riq00yk-FRGQ",
            authDomain: "gestion-578e6.firebaseapp.com",
            projectId: "gestion-578e6",
            storageBucket: "gestion-578e6.firebasestorage.app",
            messagingSenderId: "335984300936",
            appId: "1:335984300936:web:610a3a0e8b60cb5ad3a03d",
            measurementId: "G-8T5M70KZPH"
        };

        firebase.initializeApp(firebaseConfig);
        const auth = firebase.auth();
        const googleProvider = new firebase.auth.GoogleAuthProvider();

        function app() {
            return {
                isAuthenticated: false,
                isLoading: false,
                authMode: 'login',
                showPassword: false,
                authData: { name: '', email: '', password: '', password_confirmation: '' },
                resetData: { email: '', token: '', password: '', password_confirmation: '' },
                user: { name: '', email: '', role: '', photo: '' },
                students: [],
                toasts: [],
                isModalOpen: false,
                editingId: null,
                formData: { first_name: '', last_name: '', email: '', phone: '', photo: '' },
                token: localStorage.getItem('auth_token'),

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.get('mode') === 'reset-password') {
                        this.authMode = 'reset-password';
                        this.resetData.token = urlParams.get('token');
                        this.resetData.email = urlParams.get('email');
                        window.history.replaceState({}, document.title, window.location.pathname);
                    }

                    const savedUser = localStorage.getItem('user_data');
                    if (this.token && savedUser) {
                        try {
                            this.user = JSON.parse(savedUser);
                            this.isAuthenticated = true;
                            this.fetchStudents();
                            
                            // Auto-redirection si on est sur la page de login mais déjà connecté
                            if (this.authMode === 'login' && (window.location.pathname === '/' || window.location.pathname === '/login')) {
                                if (this.user.role === 'admin') window.location.href = '/admin/dashboard';
                                else window.location.href = '/student/profile';
                            }
                        } catch (e) {
                            localStorage.removeItem('user_data');
                            localStorage.removeItem('auth_token');
                        }
                    } else if (window.location.pathname !== '/' && window.location.pathname !== '/login') {
                        // Si pas authentifié et pas sur login, on y va
                        window.location.href = '/login';
                    }
                },

                toggleAuthMode() {
                    if (this.authMode === 'login') this.authMode = 'register';
                    else if (this.authMode === 'register') this.authMode = 'login';
                    else this.authMode = 'login';
                    this.authData = { name: '', email: '', password: '', password_confirmation: '' };
                },

                async forgotPassword() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('/api/auth/forgot-password', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify({ email: this.authData.email })
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.addToast(data.message, 'success');
                            this.authMode = 'login';
                        } else {
                            throw new Error(data.message || 'Erreur');
                        }
                    } catch (error) {
                        this.addToast(error.message, 'error');
                    } finally { this.isLoading = false; }
                },

                async resetPassword() {
                    this.isLoading = true;
                    try {
                        const response = await fetch('/api/auth/reset-password', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.resetData)
                        });
                        const data = await response.json();
                        if (response.ok) {
                            this.addToast(data.message, 'success');
                            this.authMode = 'login';
                        } else {
                            throw new Error(data.message || 'Erreur');
                        }
                    } catch (error) {
                        this.addToast(error.message, 'error');
                    } finally { this.isLoading = false; }
                },

                async handleAuth() {
                    this.isLoading = true;
                    try {
                        const endpoint = this.authMode === 'register' ? '/api/auth/register' : '/api/auth/login';
                        const response = await fetch(endpoint, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
                            body: JSON.stringify(this.authData)
                        });
                        
                        const data = await response.json();
                        
                        if (response.ok && data.access_token) {
                            this.token = data.access_token;
                            this.user = { 
                                name: data.user.name, 
                                email: data.user.email,
                                role: data.user.role 
                            };
                            localStorage.setItem('auth_token', this.token);
                            localStorage.setItem('user_data', JSON.stringify(this.user));
                            this.isAuthenticated = true;
                            this.addToast('Bienvenue !', 'success');
                            
                            if (this.user.role === 'admin') window.location.href = '/admin/dashboard';
                            else window.location.href = '/student/profile';
                        } else {
                            throw new Error(data.error || data.message || 'Erreur d\'authentification');
                        }
                    } catch (error) {
                        this.addToast(error.message, 'error');
                    } finally { this.isLoading = false; }
                },

                async loginWithGoogle() {
                    this.isLoading = true;
                    try {
                        const result = await auth.signInWithPopup(googleProvider);
                        const idToken = await result.user.getIdToken();
                        await this.serverAuth(idToken, result.user.displayName);
                    } catch (error) {
                        this.addToast(error.message, 'error');
                    } finally { this.isLoading = false; }
                },

                async serverAuth(idToken, name) {
                    const response = await fetch('/api/auth/firebase', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ token: idToken, name: name })
                    });
                    const data = await response.json();
                    if (data.access_token) {
                        this.token = data.access_token;
                        this.user = {
                            name: data.user.name,
                            email: data.user.email,
                            role: data.user.role
                        };
                        localStorage.setItem('auth_token', this.token);
                        localStorage.setItem('user_data', JSON.stringify(this.user));
                        this.isAuthenticated = true;
                        this.addToast('Bienvenue !', 'success');

                        if (this.user.role === 'admin') window.location.href = '/admin/dashboard';
                        else window.location.href = '/student/profile';
                    }
                },

                async logout() {
                    localStorage.clear();
                    auth.signOut();
                    this.isAuthenticated = false;
                    window.location.href = '/login';
                },

                async fetchStudents() {
                    if (!this.token) return;
                    try {
                        const response = await fetch('/api/students', {
                            headers: { 'Authorization': `Bearer ${this.token}` }
                        });
                        this.students = await response.json();
                    } catch (e) { }
                },

                async saveStudent() {
                    const url = this.editingId ? `/api/students/${this.editingId}` : '/api/students';
                    const method = this.editingId ? 'PUT' : 'POST';
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Authorization': `Bearer ${this.token}`,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(this.formData)
                    });
                    if (response.ok) {
                        this.addToast(this.editingId ? 'Profil mis à jour' : 'Étudiant inscrit', 'success');
                        this.fetchStudents();
                        this.closeModal();
                    }
                },

                async uploadPhoto(event, id) {
                    const file = event.target.files[0];
                    if (!file) return;

                    const formData = new FormData();
                    formData.append('photo', file);

                    this.isLoading = true;
                    try {
                        const response = await fetch(`/api/students/${id}/photo`, {
                            method: 'POST',
                            headers: { 'Authorization': `Bearer ${this.token}`, 'Accept': 'application/json' },
                            body: formData
                        });

                        const data = await response.json();
                        if (response.ok) {
                            this.addToast('Photo mise à jour !', 'success');
                            this.fetchStudents();
                            if (this.user.id === id || !this.user.id) {
                                this.user.photo = data.photo;
                                localStorage.setItem('user_data', JSON.stringify(this.user));
                            }
                        } else {
                            throw new Error(data.message || 'Erreur lors de l\'envoi');
                        }
                    } catch (error) {
                        this.addToast(error.message, 'error');
                    } finally {
                        this.isLoading = false;
                        event.target.value = '';
                    }
                },

                async deleteStudent(id) {
                    if (!confirm('Supprimer ce dossier ?')) return;
                    await fetch(`/api/students/${id}`, {
                        method: 'DELETE',
                        headers: { 'Authorization': `Bearer ${this.token}` }
                    });
                    this.fetchStudents();
                    this.addToast('Dossier supprimé', 'success');
                },

                openModal() { this.editingId = null; this.formData = { first_name: '', last_name: '', email: '', phone: '' }; this.isModalOpen = true; },
                editStudent(student) { this.editingId = student.id; this.formData = { ...student }; this.isModalOpen = true; },
                closeModal() { this.isModalOpen = false; },
                addToast(message, type) {
                    const id = Date.now();
                    this.toasts.push({ id, message, type, show: true });
                    setTimeout(() => { this.toasts = this.toasts.filter(t => t.id !== id); }, 4000);
                }
            }
        }
    </script>
</body>

</html>