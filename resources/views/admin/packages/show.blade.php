@extends('layouts.app')

@section('title', 'Detalhes do Pacote - ' . $package->name)

@section('content')
    <div class="space-y-8">
        <!-- Header -->
        <div
            class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('packages.index') }}"
                    class="w-12 h-12 rounded-2xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-gray-100 transition-all">
                    <i class="bi bi-arrow-left text-xl"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">{{ $package->name }}</h1>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Gestão de Membros e
                        Contribuições</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @if($package->whatsapp_link)
                    <a href="{{ $package->whatsapp_link }}" target="_blank"
                        class="bg-green-600 text-white px-8 py-4 rounded-2xl hover:bg-green-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-green-100">
                        <i class="bi bi-whatsapp mr-2 text-lg"></i> Grupo do WhatsApp
                    </a>
                @endif
                <a href="{{ route('packages.edit', $package) }}"
                    class="bg-orange-600 text-white px-8 py-4 rounded-2xl hover:bg-orange-700 transition-all font-black text-xs uppercase tracking-widest flex items-center shadow-lg shadow-orange-100">
                    <i class="bi bi-pencil-square mr-2"></i> Editar Pacote
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Stats Column -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Informações do Pacote</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-400 uppercase">Valor Mínimo</span>
                            <span
                                class="text-sm font-black text-gray-900">{{ number_format($package->min_amount, 2, ',', '.') }}
                                MT</span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-400 uppercase">Valor Máximo</span>
                            <span class="text-sm font-black text-gray-900">
                                @if($package->max_amount)
                                    {{ number_format($package->max_amount, 2, ',', '.') }} MT
                                @else
                                    Sem Limite
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center pb-4 border-b border-gray-50">
                            <span class="text-xs font-bold text-gray-400 uppercase">Membros Ativos</span>
                            <span class="text-sm font-black text-blue-600">{{ $package->getActiveMembersCount() }}</span>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase block mb-2">Descrição</span>
                            <p class="text-sm text-gray-600 leading-relaxed font-medium">
                                {{ $package->description ?? 'Nenhuma descrição fornecida.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- SMS/WhatsApp Bulk Actions -->
                <div class="bg-blue-600 p-8 rounded-[2.5rem] shadow-lg shadow-blue-100 text-white space-y-6">
                    <div>
                        <h3 class="text-sm font-black uppercase tracking-widest mb-2">Ações de Massa</h3>
                        <p class="text-xs text-blue-100 font-medium">Use as ferramentas abaixo para comunicar com todos os
                            membros deste pacote de uma só vez.</p>
                    </div>

                    <div class="space-y-3">
                        <button
                            onclick="copyToClipboard('{{ $package->whatsapp_template ?? 'Paz do Senhor! Este é um lembrete do Projeto Edificar.' }}', 'Mensagem copiada!', this)"
                            class="w-full bg-white text-blue-600 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-50 transition-all shadow-xl flex items-center justify-center gap-2">
                            <i class="bi bi-clipboard-check"></i> Copiar Mensagem
                        </button>

                        <form action="{{ route('packages.send-bulk-sms', $package) }}" method="POST">
                            @csrf
                            <button type="button"
                                onclick="confirmAction('Tem certeza que deseja disparar SMS para todos os membros deste pacote?', 'Disparar SMS em Massa').then(result => { if(result.isConfirmed) this.closest('form').submit(); })"
                                class="w-full bg-blue-500 text-white border border-blue-400/30 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-600 transition-all shadow-xl flex items-center justify-center gap-2">
                                <i class="bi bi-broadcast"></i> Disparar SMS em Massa
                            </button>
                        </form>

                        <button
                            onclick="copyToClipboard('{{ $package->userCommitments->pluck('user.phone')->filter()->implode(', ') }}', 'Contactos copiados!', this)"
                            class="w-full bg-blue-500/10 text-white border border-white/20 py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-500/30 transition-all flex items-center justify-center gap-2">
                            <i class="bi bi-person-lines-fill"></i> Copiar Contactos
                        </button>
                    </div>

                    @if($package->whatsapp_link)
                        <a href="{{ $package->whatsapp_link }}" target="_blank"
                            class="w-full bg-green-500 text-white py-4 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-green-600 transition-all shadow-xl flex items-center justify-center gap-2">
                            <i class="bi bi-whatsapp"></i> Ir para o Grupo
                        </a>
                    @endif
                </div>
            </div>

            <script>
                function copyToClipboard(text, successMessage, element = null) {
                    if (!text) {
                        alert('Nenhum conteúdo para copiar.');
                        return;
                    }

                    navigator.clipboard.writeText(text).then(() => {
                        const target = element || event.currentTarget;
                        const originalHTML = target.innerHTML;
                        const originalClass = target.className;

                        target.innerHTML = `<i class="bi bi-check2"></i> ${successMessage || ''}`;
                        if (target.tagName === 'BUTTON') {
                            target.classList.add('bg-green-500', 'text-white');
                        }

                        setTimeout(() => {
                            target.innerHTML = originalHTML;
                            target.className = originalClass;
                        }, 2000);
                    }).catch(err => {
                        console.error('Erro ao copiar: ', err);
                        const textArea = document.createElement("textarea");
                        textArea.value = text;
                        document.body.appendChild(textArea);
                        textArea.select();
                        try {
                            document.execCommand('copy');
                            alert(successMessage || 'Copiado!');
                        } catch (err) {
                            alert('Erro ao copiar. Por favor copie manualmente.');
                        }
                        document.body.removeChild(textArea);
                    });
                }
            </script>

            <!-- Members List Column -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Add Member Form -->
                <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6">Adicionar Membro ao Pacote
                    </h3>
                    <form action="{{ route('packages.assign', $package) }}" method="POST"
                        class="flex flex-col md:flex-row gap-4 items-end">
                        @csrf
                        <div class="flex-1">
                            <label for="user_id"
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Selecionar
                                Membro</label>
                            <select name="user_id" id="user_id"
                                class="w-full bg-gray-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="">Escolha um membro...</option>
                                @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} - {{ $user->phone }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-full md:w-32">
                            <label for="committed_amount"
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Valor
                                (MT)</label>
                            <input type="number" name="committed_amount" id="committed_amount"
                                value="{{ $package->min_amount }}" step="0.01"
                                class="w-full bg-gray-50 border-none rounded-2xl py-3 px-4 text-sm font-bold text-gray-900 focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-3 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition-all shadow-lg shadow-blue-100 h-[46px]">
                            <i class="bi bi-person-plus-fill"></i>
                        </button>
                    </form>
                </div>

                <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-8 border-b border-gray-50 flex justify-between items-center">
                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest">Membros Comprometidos</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50/50">
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Membro</th>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Valor</th>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Célula</th>
                                    <th
                                        class="px-8 py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Zona</th>
                                    <th
                                        class="px-8 py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Ações</th>
                                    <th
                                        class="px-8 py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($package->userCommitments as $commitment)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-400 text-xs">
                                                    {{ strtoupper(substr($commitment->user->name, 0, 1)) }}
                                                </div>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-sm font-black text-gray-900 leading-tight">{{ $commitment->user->name }}</span>
                                                    <span
                                                        class="text-[10px] text-gray-400 font-bold uppercase">{{ $commitment->user->phone }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span
                                                class="text-sm font-black text-gray-900">{{ number_format($commitment->committed_amount, 2, ',', '.') }}
                                                MT</span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="text-xs font-bold text-gray-600">
                                                {{ $commitment->user->cell->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <span class="text-xs font-bold text-gray-600">
                                                {{ $commitment->user->cell->supervision->zone->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                @if($commitment->user->phone)
                                                    @php
                                                        $name = $commitment->user->name;
                                                        $smsBody = str_replace('[NOME]', $name, $package->sms_template ?? "Olá [NOME], lembrete de contribuição para o Projetor Edificar.");
                                                        $whatsappBody = str_replace('[NOME]', $name, $package->whatsapp_template ?? "Olá [NOME], este é um lembrete do Projetor Edificar.");
                                                        $cleanPhone = preg_replace('/[^0-9]/', '', $commitment->user->phone);
                                                    @endphp
                                                    <a href="sms:{{ $commitment->user->phone }}?body={{ urlencode($smsBody) }}"
                                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm"
                                                        title="Mandar SMS">
                                                        <i class="bi bi-chat-dots-fill"></i>
                                                    </a>
                                                    <a href="https://wa.me/{{ $cleanPhone }}?text={{ urlencode($whatsappBody) }}"
                                                        target="_blank"
                                                        class="w-8 h-8 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-600 hover:text-white transition-all shadow-sm"
                                                        title="Mandar WhatsApp Individual">
                                                        <i class="bi bi-whatsapp"></i>
                                                    </a>
                                                    <button
                                                        onclick="copyToClipboard('{{ $commitment->user->phone }}', 'Copiado!', this)"
                                                        class="w-8 h-8 rounded-lg bg-gray-50 text-gray-600 flex items-center justify-center hover:bg-gray-600 hover:text-white transition-all shadow-sm"
                                                        title="Copiar Número">
                                                        <i class="bi bi-telephone-outbound"></i>
                                                    </button>
                                                @else
                                                    <span class="text-[10px] text-gray-300 font-bold uppercase">Sem contacto</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <span
                                                class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border 
                                                                                {{ $commitment->isActive() ? 'bg-green-50 text-green-600 border-green-100' : 'bg-red-50 text-red-600 border-red-100' }}">
                                                {{ $commitment->isActive() ? 'Ativo' : 'Encerrado' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-8 py-10 text-center">
                                            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nenhum membro
                                                encontrado neste pacote.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection