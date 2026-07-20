<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração Inicial - {{ config('app.name', 'Portal Life Church') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
        }

        .auth-overlay {
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #020617 100%);
            z-index: -1;
            overflow: hidden;
        }

        .auth-overlay::before {
            content: '';
            position: absolute;
            top: -20%;
            right: -20%;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.15) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite;
        }

        .auth-overlay::after {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -20%;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.12) 0%, transparent 70%);
            animation: pulse 8s ease-in-out infinite reverse;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4 md:p-8">
    <div class="auth-overlay"></div>

    <div class="w-full max-w-4xl" x-data="setupWizard()">
        <!-- Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-gradient-to-tr from-orange-600 to-amber-500 text-white shadow-xl shadow-orange-500/20 mb-4">
                <i class="bi bi-rocket-takeoff-fill text-3xl"></i>
            </div>
            <h1 class="text-3xl md:text-4xl font-black text-white tracking-tight mb-2">Assistente de Instalação</h1>
            <p class="text-gray-400 font-medium text-sm md:text-base">Configure a sua congregação / campus em 4 passos simples</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-10 max-w-2xl mx-auto px-4">
            <div class="flex items-center justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex-1 flex items-center">
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl flex items-center justify-center font-bold text-sm md:text-base transition-all duration-300 shadow-lg"
                                :class="currentStep > index ? 'bg-emerald-500 text-white shadow-emerald-500/20' : currentStep === index ? 'bg-orange-600 text-white shadow-orange-600/30 ring-4 ring-orange-500/20' : 'bg-slate-800 text-slate-500 border border-slate-700'">
                                <i :class="currentStep > index ? 'bi-check-lg' : step.icon"></i>
                            </div>
                            <span class="text-[11px] md:text-xs mt-2 font-bold tracking-wider uppercase transition-colors"
                                :class="currentStep >= index ? 'text-orange-400' : 'text-slate-500'"
                                x-text="step.title"></span>
                        </div>
                        <div x-show="index < steps.length - 1" class="h-1 flex-1 mx-2 transition-all duration-500 rounded-full"
                            :class="currentStep > index ? 'bg-emerald-500' : 'bg-slate-800'">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Alert Notification -->
        <template x-if="errorMessage">
            <div class="max-w-2xl mx-auto mb-6 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm font-semibold flex items-center gap-3">
                <i class="bi bi-exclamation-triangle-fill text-rose-400 text-lg"></i>
                <span x-text="errorMessage"></span>
            </div>
        </template>

        <!-- Steps Container Card -->
        <div class="glass-card rounded-[2.5rem] shadow-2xl p-6 md:p-10 relative overflow-hidden">
            <!-- Step 1: Church Info -->
            <div x-show="currentStep === 0" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500">
                        <i class="bi bi-building text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-white">Dados da Congregação / Campus</h2>
                        <p class="text-xs text-gray-400">Identificação institucional para relatórios e documentos</p>
                    </div>
                </div>

                <form @submit.prevent="submitStep1" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nome da Congregação / Igreja *</label>
                        <input type="text" x-model="formData.church_name" required placeholder="Ex: Life Church Quelimane"
                            class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">E-mail Institucional *</label>
                            <input type="email" x-model="formData.church_email" required placeholder="contacto@igreja.org"
                                class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Telefone de Contacto</label>
                            <input type="text" x-model="formData.church_phone" placeholder="+258 84 000 0000"
                                class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Cidade / Distrito</label>
                            <input type="text" x-model="formData.church_city" placeholder="Ex: Quelimane"
                                class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Província</label>
                            <input type="text" x-model="formData.church_province" placeholder="Ex: Zambézia"
                                class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Endereço Físico / Bairro</label>
                        <input type="text" x-model="formData.church_address" placeholder="Av. Julius Nyerere, Bairro Central"
                            class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Descrição / Lema Institucional</label>
                        <textarea x-model="formData.church_description" rows="2" placeholder="Uma igreja relevante que edifica vidas..."
                            class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition"></textarea>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit" :disabled="loading"
                            class="px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-700 text-white rounded-2xl font-bold hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-orange-500/20 disabled:opacity-50 flex items-center gap-2">
                            <span x-show="!loading">Próximo Passo <i class="bi bi-arrow-right ml-1"></i></span>
                            <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin mr-1"></i> Processando...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Admin User -->
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500">
                        <i class="bi bi-person-badge text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-white">Criar Conta do Pastor / Administrador Principal</h2>
                        <p class="text-xs text-gray-400">Esta conta terá permissão total para gerir a congregação</p>
                    </div>
                </div>

                <form @submit.prevent="submitStep2" class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nome Completo *</label>
                        <input type="text" x-model="formData.admin_name" required placeholder="Pastor Filipe"
                            class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">E-mail de Acesso *</label>
                            <input type="email" x-model="formData.admin_email" required placeholder="pastor@igreja.org"
                                class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Telefone Pessoal</label>
                            <input type="text" x-model="formData.admin_phone" placeholder="+258 84 000 0000"
                                class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Palavra-passe *</label>
                            <input type="password" x-model="formData.admin_password" required minlength="6" placeholder="••••••••"
                                class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Confirmar Palavra-passe *</label>
                            <input type="password" x-model="formData.admin_password_confirmation" required minlength="6" placeholder="••••••••"
                                class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <button type="button" @click="currentStep--"
                            class="px-6 py-3.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-2xl font-bold transition flex items-center gap-2">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </button>
                        <button type="submit" :disabled="loading"
                            class="px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-700 text-white rounded-2xl font-bold hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-orange-500/20 disabled:opacity-50 flex items-center gap-2">
                            <span x-show="!loading">Próximo Passo <i class="bi bi-arrow-right ml-1"></i></span>
                            <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin mr-1"></i> Processando...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Branding & Initial Structure -->
            <div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 border border-orange-500/20 flex items-center justify-center text-orange-500">
                        <i class="bi bi-palette text-xl"></i>
                    </div>
                    <div>
                        <h2 class="text-xl md:text-2xl font-black text-white">Estrutura Inicial & Marca</h2>
                        <p class="text-xs text-gray-400">Configure a primeira Zona Pastoral e as cores da marca (opcional)</p>
                    </div>
                </div>

                <form @submit.prevent="submitStep3" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Nome da 1ª Zona Pastoral (Opcional)</label>
                        <input type="text" x-model="formData.initial_zone_name" placeholder="Ex: Zona Central, Zona A"
                            class="w-full bg-slate-900/80 border border-slate-700/80 rounded-2xl px-5 py-3.5 text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
                        <p class="text-[11px] text-slate-400 mt-1.5">Permite iniciar logo com uma zona configurada para agrupar supervisões e células.</p>
                    </div>

                    <div class="border-t border-slate-800 pt-5">
                        <h4 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                            <i class="bi bi-paint-bucket text-orange-400"></i> Cores do Tema
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Cor Primária</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" x-model="formData.color_primary" class="w-10 h-10 rounded-xl bg-transparent border-0 cursor-pointer">
                                    <span class="text-xs font-mono text-slate-300" x-text="formData.color_primary"></span>
                                </div>
                            </div>
                            <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Cor Secundária</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" x-model="formData.color_secondary" class="w-10 h-10 rounded-xl bg-transparent border-0 cursor-pointer">
                                    <span class="text-xs font-mono text-slate-300" x-text="formData.color_secondary"></span>
                                </div>
                            </div>
                            <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-800">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">Cor de Destaque</label>
                                <div class="flex items-center gap-3">
                                    <input type="color" x-model="formData.color_accent" class="w-10 h-10 rounded-xl bg-transparent border-0 cursor-pointer">
                                    <span class="text-xs font-mono text-slate-300" x-text="formData.color_accent"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <button type="button" @click="currentStep--"
                            class="px-6 py-3.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-2xl font-bold transition flex items-center gap-2">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </button>
                        <button type="submit" :disabled="loading"
                            class="px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-700 text-white rounded-2xl font-bold hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-orange-500/20 disabled:opacity-50 flex items-center gap-2">
                            <span x-show="!loading">Avançar para Conclusão <i class="bi bi-arrow-right ml-1"></i></span>
                            <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin mr-1"></i> Processando...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 4: Complete -->
            <div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                <div class="text-center py-6">
                    <div class="w-24 h-24 bg-emerald-500/10 border border-emerald-500/30 rounded-3xl flex items-center justify-center mx-auto mb-6 text-emerald-400 shadow-xl shadow-emerald-500/10">
                        <i class="bi bi-check-circle-fill text-5xl"></i>
                    </div>
                    <h2 class="text-2xl md:text-3xl font-black text-white mb-2">Tudo Pronto!</h2>
                    <p class="text-slate-400 text-sm max-w-md mx-auto mb-8">A sua congregação está configurada e pronta para ser utilizada. Clique no botão abaixo para ir para a página de acesso.</p>

                    <button @click="completeSetup" :disabled="loading"
                        class="px-10 py-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white rounded-2xl font-black text-base hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-emerald-500/20 disabled:opacity-50 inline-flex items-center gap-3">
                        <span x-show="!loading"><i class="bi bi-box-arrow-in-right text-xl"></i> Aceder ao Portal Agora</span>
                        <span x-show="loading"><i class="bi bi-arrow-repeat animate-spin text-xl"></i> Finalizando...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setupWizard() {
            return {
                currentStep: 0,
                loading: false,
                errorMessage: '',
                steps: [
                    { title: 'Congregação', icon: 'bi-building' },
                    { title: 'Pastor / Admin', icon: 'bi-person-badge' },
                    { title: 'Estrutura', icon: 'bi-palette' },
                    { title: 'Concluir', icon: 'bi-check-circle' }
                ],
                formData: {
                    church_name: '',
                    church_description: '',
                    church_email: '',
                    church_phone: '',
                    church_address: '',
                    church_city: '',
                    church_province: '',
                    admin_name: '',
                    admin_email: '',
                    admin_phone: '',
                    admin_password: '',
                    admin_password_confirmation: '',
                    initial_zone_name: '',
                    color_primary: '#f97316',
                    color_secondary: '#3b82f6',
                    color_accent: '#10b981'
                },

                async submitStep1() {
                    this.loading = true;
                    this.errorMessage = '';
                    try {
                        const response = await fetch('{{ url("/setup/step1") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                church_name: this.formData.church_name,
                                church_description: this.formData.church_description,
                                church_email: this.formData.church_email,
                                church_phone: this.formData.church_phone,
                                church_address: this.formData.church_address,
                                church_city: this.formData.church_city,
                                church_province: this.formData.church_province
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.currentStep++;
                        } else {
                            this.errorMessage = data.message || 'Verifique as informações preenchidas.';
                        }
                    } catch (error) {
                        this.errorMessage = 'Erro ao conectar ao servidor. Tente novamente.';
                    }
                    this.loading = false;
                },

                async submitStep2() {
                    this.loading = true;
                    this.errorMessage = '';
                    if (this.formData.admin_password !== this.formData.admin_password_confirmation) {
                        this.errorMessage = 'As palavras-passe introduzidas não coincidem.';
                        this.loading = false;
                        return;
                    }

                    try {
                        const response = await fetch('{{ url("/setup/step2") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                name: this.formData.admin_name,
                                email: this.formData.admin_email,
                                phone: this.formData.admin_phone,
                                password: this.formData.admin_password,
                                password_confirmation: this.formData.admin_password_confirmation
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.currentStep++;
                        } else {
                            this.errorMessage = data.message || 'Não foi possível criar o utilizador. Verifique se o e-mail já existe.';
                        }
                    } catch (error) {
                        this.errorMessage = 'Erro ao cadastrar utilizador. Tente novamente.';
                    }
                    this.loading = false;
                },

                async submitStep3() {
                    this.loading = true;
                    this.errorMessage = '';
                    try {
                        const response = await fetch('{{ url("/setup/step3") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                initial_zone_name: this.formData.initial_zone_name,
                                color_primary: this.formData.color_primary,
                                color_secondary: this.formData.color_secondary,
                                color_accent: this.formData.color_accent
                            })
                        });
                        const data = await response.json();
                        if (data.success) {
                            this.currentStep++;
                        } else {
                            this.errorMessage = data.message || 'Erro ao guardar personalização.';
                        }
                    } catch (error) {
                        this.errorMessage = 'Erro ao processar personalização.';
                    }
                    this.loading = false;
                },

                async completeSetup() {
                    this.loading = true;
                    this.errorMessage = '';
                    try {
                        const response = await fetch('{{ url("/setup/complete") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            window.location.href = data.redirect;
                        } else {
                            this.errorMessage = data.message || 'Erro ao finalizar configuração.';
                        }
                    } catch (error) {
                        this.errorMessage = 'Erro ao finalizar instalação.';
                    }
                    this.loading = false;
                }
            }
        }
    </script>
</body>

</html>