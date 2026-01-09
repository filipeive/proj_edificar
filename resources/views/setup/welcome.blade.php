<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração Inicial - {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-12" x-data="setupWizard()">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl font-black text-gray-900 mb-4">Bem-vindo! 🎉</h1>
            <p class="text-xl text-gray-600">Vamos configurar o seu sistema em poucos passos</p>
        </div>

        <!-- Progress Bar -->
        <div class="max-w-3xl mx-auto mb-12">
            <div class="flex items-center justify-between">
                <template x-for="(step, index) in steps" :key="index">
                    <div class="flex-1 flex items-center">
                        <div class="flex flex-col items-center flex-1">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold transition-all"
                                :class="currentStep > index ? 'bg-green-500 text-white' : currentStep === index ? 'bg-blue-600 text-white' : 'bg-gray-300 text-gray-600'">
                                <i :class="currentStep > index ? 'bi-check-lg' : step.icon"></i>
                            </div>
                            <span class="text-xs mt-2 font-semibold"
                                :class="currentStep >= index ? 'text-gray-900' : 'text-gray-400'"
                                x-text="step.title"></span>
                        </div>
                        <div x-show="index < steps.length - 1" class="h-1 flex-1 mx-2 transition-all"
                            :class="currentStep > index ? 'bg-green-500' : 'bg-gray-300'">
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Steps Container -->
        <div class="max-w-2xl mx-auto bg-white rounded-3xl shadow-2xl p-8">
            <!-- Step 1: Church Info -->
            <div x-show="currentStep === 0" x-transition>
                <h2 class="text-3xl font-black text-gray-900 mb-6">Informações da Igreja</h2>
                <form @submit.prevent="submitStep1">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nome da Igreja *</label>
                            <input type="text" x-model="formData.church_name" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Descrição</label>
                            <textarea x-model="formData.church_description" rows="3"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                                <input type="email" x-model="formData.church_email" required
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Telefone</label>
                                <input type="text" x-model="formData.church_phone"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Endereço</label>
                            <input type="text" x-model="formData.church_address"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="flex justify-end mt-8">
                        <button type="submit" :disabled="loading"
                            class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 disabled:opacity-50">
                            <span x-show="!loading">Próximo</span>
                            <span x-show="loading">Processando...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 2: Admin User -->
            <div x-show="currentStep === 1" x-transition>
                <h2 class="text-3xl font-black text-gray-900 mb-6">Criar Primeiro Administrador</h2>
                <form @submit.prevent="submitStep2">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Nome Completo *</label>
                            <input type="text" x-model="formData.admin_name" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                            <input type="email" x-model="formData.admin_email" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Senha *</label>
                            <input type="password" x-model="formData.admin_password" required minlength="6"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Confirmar Senha *</label>
                            <input type="password" x-model="formData.admin_password_confirmation" required
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none">
                        </div>
                    </div>
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep--"
                            class="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300">
                            Voltar
                        </button>
                        <button type="submit" :disabled="loading"
                            class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 disabled:opacity-50">
                            <span x-show="!loading">Próximo</span>
                            <span x-show="loading">Processando...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 3: Branding -->
            <div x-show="currentStep === 2" x-transition>
                <h2 class="text-3xl font-black text-gray-900 mb-6">Personalização Visual</h2>
                <form @submit.prevent="submitStep3">
                    <div class="space-y-6">
                        <p class="text-gray-600">Personalize as cores do seu sistema (opcional)</p>
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Cor Primária</label>
                                <input type="color" x-model="formData.color_primary" value="#3B82F6"
                                    class="w-full h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Cor Secundária</label>
                                <input type="color" x-model="formData.color_secondary" value="#F97316"
                                    class="w-full h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Cor de Destaque</label>
                                <input type="color" x-model="formData.color_accent" value="#8B5CF6"
                                    class="w-full h-12 rounded-xl border-2 border-gray-200 cursor-pointer">
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-between mt-8">
                        <button type="button" @click="currentStep--"
                            class="px-8 py-3 bg-gray-200 text-gray-700 rounded-xl font-bold hover:bg-gray-300">
                            Voltar
                        </button>
                        <button type="submit" :disabled="loading"
                            class="px-8 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 disabled:opacity-50">
                            <span x-show="!loading">Próximo</span>
                            <span x-show="loading">Processando...</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Step 4: Complete -->
            <div x-show="currentStep === 3" x-transition>
                <div class="text-center py-8">
                    <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="bi bi-check-lg text-5xl text-green-600"></i>
                    </div>
                    <h2 class="text-3xl font-black text-gray-900 mb-4">Tudo Pronto!</h2>
                    <p class="text-gray-600 mb-8">O sistema foi configurado com sucesso.</p>
                    <button @click="completeSetup" :disabled="loading"
                        class="px-12 py-4 bg-green-600 text-white rounded-xl font-bold text-lg hover:bg-green-700 disabled:opacity-50">
                        <span x-show="!loading">Ir para o Sistema</span>
                        <span x-show="loading">Finalizando...</span>
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
                steps: [
                    { title: 'Igreja', icon: 'bi-building' },
                    { title: 'Admin', icon: 'bi-person-badge' },
                    { title: 'Visual', icon: 'bi-palette' },
                    { title: 'Concluir', icon: 'bi-check-circle' }
                ],
                formData: {
                    church_name: '',
                    church_description: '',
                    church_email: '',
                    church_phone: '',
                    church_address: '',
                    admin_name: '',
                    admin_email: '',
                    admin_password: '',
                    admin_password_confirmation: '',
                    color_primary: '#3B82F6',
                    color_secondary: '#F97316',
                    color_accent: '#8B5CF6'
                },

                async submitStep1() {
                    this.loading = true;
                    try {
                        const response = await fetch('/setup/step1', {
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
                                church_address: this.formData.church_address
                            })
                        });
                        const data = await response.json();
                        if (data.success) this.currentStep++;
                    } catch (error) {
                        alert('Erro ao processar. Tente novamente.');
                    }
                    this.loading = false;
                },

                async submitStep2() {
                    this.loading = true;
                    try {
                        const response = await fetch('/setup/step2', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                name: this.formData.admin_name,
                                email: this.formData.admin_email,
                                password: this.formData.admin_password,
                                password_confirmation: this.formData.admin_password_confirmation
                            })
                        });
                        const data = await response.json();
                        if (data.success) this.currentStep++;
                    } catch (error) {
                        alert('Erro ao criar administrador. Verifique os dados.');
                    }
                    this.loading = false;
                },

                async submitStep3() {
                    this.loading = true;
                    try {
                        const response = await fetch('/setup/step3', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                color_primary: this.formData.color_primary,
                                color_secondary: this.formData.color_secondary,
                                color_accent: this.formData.color_accent
                            })
                        });
                        const data = await response.json();
                        if (data.success) this.currentStep++;
                    } catch (error) {
                        alert('Erro ao salvar personalização.');
                    }
                    this.loading = false;
                },

                async completeSetup() {
                    this.loading = true;
                    try {
                        const response = await fetch('/setup/complete', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await response.json();
                        if (data.success) {
                            window.location.href = data.redirect;
                        }
                    } catch (error) {
                        alert('Erro ao finalizar configuração.');
                    }
                    this.loading = false;
                }
            }
        }
    </script>
</body>

</html>