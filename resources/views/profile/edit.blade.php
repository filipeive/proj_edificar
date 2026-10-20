@extends('layouts.app')

@section('title', 'Meu Perfil - Portal Life Church')

@section('content')
<div class="space-y-4">
    <!-- Header Executivo -->
    <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 text-white flex items-center justify-center font-black text-xl shadow-sm">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Conta de Usuário</span>
                        <span class="inline-flex items-center rounded-md bg-orange-50 px-2 py-0.5 text-[10px] font-black uppercase tracking-wider text-orange-700 border border-orange-200">
                            {{ str_replace('_', ' ', $user->role) }}
                        </span>
                    </div>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight">{{ $user->name }}</h1>
                    <p class="text-xs text-gray-500 font-medium">{{ $user->email }}</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if($user->cell)
                    <span class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50/60 px-2.5 py-1 text-xs font-bold text-indigo-700">
                        <i class="bi bi-geo-alt-fill text-indigo-500"></i> Célula: {{ $user->cell->name }}
                    </span>
                @endif
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-medium text-gray-600">
                    <i class="bi bi-calendar-check text-gray-400"></i> Membro desde {{ $user->created_at->format('d/m/Y') }}
                </span>
                <span class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-gray-50 px-2.5 py-1 text-xs font-mono font-medium text-gray-500">
                    ID #{{ str_pad($user->id, 6, '0', STR_PAD_LEFT) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Grid de Formulários -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Edit Profile Form -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="border-b border-gray-100 bg-gray-50/75 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-gear text-orange-600 text-sm"></i>
                        <h2 class="text-xs font-black uppercase tracking-wider text-gray-700">Informações Pessoais</h2>
                    </div>
                    <span class="text-[10px] text-gray-400 font-medium">Dados de contato</span>
                </div>

                <div class="p-4 sm:p-5">
                    @php
                        $notificationPrefs = array_merge(
                            \App\Models\User::notificationPreferenceDefaults(),
                            $user->notification_preferences ?? []
                        );
                    @endphp
                    <form action="{{ route('profile.update') }}" method="POST" id="profileUpdateForm" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="name" class="block text-xs font-bold text-gray-700 mb-1">Nome Completo</label>
                            <input type="text" name="name" id="name" required
                                class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                                value="{{ old('name', $user->name) }}">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="email" class="block text-xs font-bold text-gray-700 mb-1">Endereço de E-mail</label>
                                <input type="email" name="email" id="email" required
                                    class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                                    value="{{ old('email', $user->email) }}">
                            </div>

                            <div>
                                <label for="phone" class="block text-xs font-bold text-gray-700 mb-1">Telefone / WhatsApp</label>
                                <input type="tel" name="phone" id="phone"
                                    class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                                    value="{{ old('phone', $user->phone) }}" placeholder="Ex: 840000000">
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <label class="block text-xs font-black uppercase tracking-wider text-gray-700 mb-2">
                                <i class="bi bi-bell-fill text-orange-500 mr-1"></i> Preferências de Notificação
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-56 overflow-y-auto pr-1">
                                @php
                                    $prefLabels = [
                                        'contribution_created' => 'Contribuição criada',
                                        'contribution_pending_validation' => 'Pendente para validar',
                                        'pending_contributions' => 'Resumo pendentes (batch)',
                                        'contribution_verified' => 'Contribuição verificada',
                                        'contribution_rejected' => 'Contribuição rejeitada',
                                        'contribution_verified_manager' => 'Verificada (pacote)',
                                        'contribution_rejected_manager' => 'Rejeitada (pacote)',
                                        'commitment_chosen' => 'Compromisso escolhido',
                                        'commitment_expiring' => 'Compromisso a expirar',
                                        'member_created' => 'Conta criada',
                                        'member_added_to_cell' => 'Adicionado à célula',
                                        'user_promoted' => 'Promoção de cargo',
                                    ];
                                @endphp

                                @foreach($prefLabels as $key => $label)
                                    <label class="flex items-center justify-between gap-2 rounded-lg border border-gray-200 bg-gray-50/60 px-3 py-2 text-xs hover:bg-gray-100/70 transition-colors cursor-pointer">
                                        <span class="font-medium text-gray-700 text-[11px]">{{ $label }}</span>
                                        <input type="hidden" name="notification_preferences[{{ $key }}]" value="0">
                                        <input type="checkbox" name="notification_preferences[{{ $key }}]" value="1"
                                            class="rounded border-gray-300 text-orange-600 focus:ring-orange-500 h-4 w-4"
                                            {{ old("notification_preferences.$key", $notificationPrefs[$key] ?? true) ? 'checked' : '' }}>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-3 flex justify-end">
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-orange-600 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-orange-700 transition-colors">
                                <i class="bi bi-check2"></i>
                                Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Password Form -->
        <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="border-b border-gray-100 bg-gray-50/75 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-shield-lock text-gray-700 text-sm"></i>
                        <h2 class="text-xs font-black uppercase tracking-wider text-gray-700">Segurança da Conta</h2>
                    </div>
                    <span class="text-[10px] text-gray-400 font-medium">Troca de credenciais</span>
                </div>

                <div class="p-4 sm:p-5">
                    <form action="{{ route('password.update') }}" method="POST" id="passwordUpdateForm" class="space-y-4">
                        @csrf
                        @method('put')

                        <div>
                            <label for="current_password" class="block text-xs font-bold text-gray-700 mb-1">Senha Atual</label>
                            <input type="password" name="current_password" id="current_password" required
                                class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                                placeholder="••••••••">
                        </div>

                        <div>
                            <label for="password" class="block text-xs font-bold text-gray-700 mb-1">Nova Senha</label>
                            <input type="password" name="password" id="password" required
                                class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Mínimo de 8 caracteres">
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-xs font-bold text-gray-700 mb-1">Confirmar Nova Senha</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full rounded-lg border-gray-300 text-xs sm:text-sm focus:border-orange-500 focus:ring-orange-500"
                                placeholder="Confirme a nova senha">
                        </div>

                        <div class="rounded-lg bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 flex items-start gap-2">
                            <i class="bi bi-info-circle-fill text-amber-600 mt-0.5"></i>
                            <div>
                                <p class="font-bold">Recomendações de segurança:</p>
                                <p class="text-[11px] text-amber-700 mt-0.5">Crie uma senha forte combinando letras maiúsculas, números e símbolos especiais.</p>
                            </div>
                        </div>

                        <div class="pt-3 flex justify-end">
                            <button type="button"
                                onclick="confirmAction('Alterar Senha', 'Tem certeza que deseja alterar sua senha?', 'question', 'Sim, alterar', 'passwordUpdateForm')"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-gray-900 px-4 py-2 text-xs font-bold text-white shadow-sm hover:bg-black transition-colors">
                                <i class="bi bi-key"></i>
                                Redefinir Senha
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if (session('status') === 'profile-updated')
    <script>
        window.addEventListener('load', () => {
            showSuccess('Suas informações foram atualizadas com sucesso!');
        });
    </script>
@endif

@if (session('status') === 'password-updated')
    <script>
        window.addEventListener('load', () => {
            showSuccess('Sua senha foi alterada com sucesso!');
        });
    </script>
@endif

@if ($errors->any())
    <script>
        window.addEventListener('load', () => {
            showError('Não foi possível processar sua solicitação. Verifique os dados inseridos.');
        });
    </script>
@endif
@endsection
