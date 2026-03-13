@extends('layouts.app')

@section('title', 'Editar Contribuição - Portal Life Church')
@section('page-title', 'Editar Contribuição')
@section('page-subtitle', 'Atualizar dados da oferta ou dízimo')

@section('content')
    <div class="max-w-4xl mx-auto space-y-8" x-data="{ 
        proofType: '{{ $contribution->proof_message ? 'message' : 'file' }}',
        amount: {{ $contribution->amount }},
        isDragging: false
    }">
        <!-- Header Card -->
        <div
            class="bg-white/70 backdrop-blur-xl p-8 rounded-[2.5rem] shadow-sm border border-white/20 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="flex items-center gap-4">
                <div
                    class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-xl shadow-blue-200">
                    <i class="bi bi-pencil-square text-3xl"></i>
                </div>
                <div>
                    <div
                        class="flex items-center gap-2 text-[10px] font-black text-blue-600 uppercase tracking-widest mb-1">
                        <a href="{{ route('contributions.index') }}" class="hover:underline">Contribuições</a>
                        <i class="bi bi-chevron-right text-[8px]"></i>
                        <span>ID: #{{ $contribution->id }}</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 tracking-tight">Refinar Detalhes</h1>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right hidden md:block">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Estado Atual</p>
                    <span
                        class="inline-flex items-center px-3 py-1 bg-orange-50 text-orange-600 rounded-full text-[10px] font-black uppercase tracking-widest border border-orange-100 mt-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-2 animate-pulse"></span>
                        {{ $contribution->status }}
                    </span>
                </div>
            </div>
        </div>

        <form action="{{ route('contributions.update', $contribution) }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-12">
            @csrf
            @method('PUT')

            <!-- Main Column: General Info -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white/80 backdrop-blur-md rounded-[2.5rem] shadow-sm border border-white/40 overflow-hidden">
                    <div class="p-8 border-b border-gray-50/50 bg-gray-50/30">
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                            <i class="bi bi-wallet2 text-blue-600"></i>
                            Informações Financeiras
                        </h2>
                    </div>

                    <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Amount -->
                        <div class="space-y-3">
                            <label for="amount" class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">
                                Valor da Contribuição
                            </label>
                            <div class="relative group">
                                <span
                                    class="absolute left-5 top-1/2 -translate-y-1/2 font-black text-gray-400 group-focus-within:text-blue-600 transition-colors">MT</span>
                                <input type="number" name="amount" id="amount" step="0.01" min="0" x-model="amount"
                                    class="w-full pl-12 pr-6 py-4 bg-gray-50/50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-black text-2xl text-gray-800 @error('amount') border-red-500 @enderror"
                                    value="{{ old('amount', $contribution->amount) }}" required>
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase tracking-tight">{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div class="space-y-3">
                            <label for="contribution_date"
                                class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">
                                Data do Depósito
                            </label>
                            <div class="relative">
                                <i class="bi bi-calendar3 absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="date" name="contribution_date" id="contribution_date"
                                    class="w-full pl-12 pr-6 py-4 bg-gray-50/50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl transition-all font-bold text-gray-700 @error('contribution_date') border-red-500 @enderror"
                                    value="{{ old('contribution_date', $contribution->contribution_date->format('Y-m-d')) }}"
                                    required>
                            </div>
                            @error('contribution_date')
                                <p class="text-red-500 text-[10px] font-bold mt-1 ml-1 uppercase tracking-tight">{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Proof Upload Section -->
                <div class="bg-white/80 backdrop-blur-md rounded-[2.5rem] shadow-sm border border-white/40 overflow-hidden">
                    <div class="p-8 border-b border-gray-50/50 bg-gray-50/30 flex justify-between items-center">
                        <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                            <i class="bi bi-file-earmark-check text-blue-600"></i>
                            Comprovativo
                        </h2>

                        <div class="flex p-1 bg-gray-100 rounded-xl">
                            <button type="button" @click="proofType = 'file'"
                                :class="proofType === 'file' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500'"
                                class="px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest transition-all duration-300">
                                Ficheiro
                            </button>
                            <button type="button" @click="proofType = 'message'"
                                :class="proofType === 'message' ? 'bg-white text-blue-600 shadow-sm' : 'text-gray-500'"
                                class="px-4 py-1.5 rounded-lg text-xs font-black uppercase tracking-widest transition-all duration-300">
                                Mensagem
                            </button>
                        </div>
                    </div>

                    <div class="p-8">
                        <!-- File Upload -->
                        <div x-show="proofType === 'file'" x-cloak x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            @if($contribution->proof_path)
                                <div
                                    class="mb-6 p-5 bg-blue-50/50 border border-blue-100 rounded-[1.5rem] flex items-center justify-between group/file">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center text-blue-600">
                                            <i class="bi bi-file-earmark-pdf text-xl"></i>
                                        </div>
                                        <div>
                                            <p
                                                class="text-[10px] text-blue-400 font-black uppercase tracking-widest leading-none mb-1">
                                                Arquivo Atual</p>
                                            <a href="{{ route('contributions.receipt', $contribution) }}" target="_blank"
                                                class="text-sm font-bold text-blue-900 hover:text-blue-600 transition-colors inline-flex items-center">
                                                Visualizar Comprovativo
                                                <i class="bi bi-box-arrow-up-right ml-2 text-xs"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <span
                                        class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded-md text-[9px] font-black uppercase">Válido</span>
                                </div>
                            @endif

                            <div class="relative group" @dragover.prevent="isDragging = true"
                                @dragleave.prevent="isDragging = false"
                                @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; updateFileName($event)">

                                <input type="file" name="proof_path" x-ref="fileInput" @change="updateFileName"
                                    class="hidden" accept=".pdf,.jpg,.jpeg,.png">

                                <div @click="$refs.fileInput.click()"
                                    :class="isDragging ? 'border-blue-500 bg-blue-50/50' : 'border-gray-200 bg-gray-50/30 hover:border-blue-400 hover:bg-blue-50/20'"
                                    class="border-2 border-dashed rounded-[2rem] p-12 text-center cursor-pointer transition-all duration-300">
                                    <div
                                        class="w-20 h-20 bg-white rounded-3xl shadow-lg shadow-gray-200/50 flex items-center justify-center mx-auto mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-500">
                                        <i class="bi bi-cloud-upload text-3xl text-blue-600"></i>
                                    </div>
                                    <h3 class="text-lg font-black text-gray-900 mb-2">Novo Comprovativo</h3>
                                    <p class="text-sm font-bold text-gray-500">Arraste o ficheiro ou <span
                                            class="text-blue-600 underline">navegue</span></p>
                                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mt-6">PDF, JPG,
                                        PNG • Máx 5MB</p>
                                </div>

                                <div id="fileName"
                                    class="mt-4 p-4 bg-green-50 border border-green-100 rounded-2xl hidden flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center text-white">
                                        <i class="bi bi-check-lg"></i>
                                    </div>
                                    <span id="fileNameText" class="text-sm font-black text-green-700"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Message Paste -->
                        <div x-show="proofType === 'message'" x-cloak x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="space-y-4">
                                <label class="text-[10px] font-black uppercase tracking-widest text-gray-400 ml-1">Mensagem
                                    da Operadora</label>
                                <textarea name="proof_message" id="proof_message" rows="8"
                                    class="w-full px-8 py-6 bg-gray-50/50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-[2rem] transition-all font-medium text-gray-700 placeholder-gray-300 resize-none shadow-inner"
                                    placeholder="Cole aqui a mensagem completa (M-Pesa, e-Mola, etc)...">{{ old('proof_message', $contribution->proof_message) }}</textarea>
                                <div class="flex items-start gap-2 p-4 bg-orange-50 rounded-2xl">
                                    <i class="bi bi-info-circle text-orange-500 mt-0.5"></i>
                                    <p
                                        class="text-[10px] font-bold text-orange-700 leading-relaxed uppercase tracking-tighter">
                                        Certifique-se de colar a mensagem original para uma verificação rápida e sem erros.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @error('proof_path')
                            <p class="text-red-500 text-[10px] font-bold mt-4 ml-1 uppercase">{{ $message }}</p>
                        @enderror
                        @error('proof_message')
                            <p class="text-red-500 text-[10px] font-bold mt-4 ml-1 uppercase">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar Column: Summary & Actions -->
            <div class="space-y-8">
                <div
                    class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-gray-200 relative overflow-hidden group">
                    <div
                        class="absolute -right-10 -top-10 w-40 h-40 bg-blue-600/20 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-1000">
                    </div>

                    <h3 class="text-xs font-black uppercase tracking-[0.2em] text-blue-400 mb-8 flex items-center gap-2">
                        <i class="bi bi-lightning-charge-fill"></i>
                        Resumo do Ajuste
                    </h3>

                    <div class="space-y-6 relative z-10">
                        <div>
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Novo Montante</p>
                            <p class="text-4xl font-black tracking-tight flex items-baseline gap-2 tabular-nums">
                                <span x-text="formatCurrency(amount)">0,00</span>
                                <span class="text-sm font-black text-blue-500">MT</span>
                            </p>
                        </div>

                        <div class="pt-6 border-t border-white/10 space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Tipo de
                                    Prova</span>
                                <span class="text-[10px] font-black uppercase"
                                    x-text="proofType === 'file' ? 'ANEXO' : 'TEXTO'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Data</span>
                                <span
                                    class="text-[10px] font-black uppercase">{{ $contribution->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="pt-8">
                            <button type="submit"
                                class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black text-sm uppercase tracking-[0.1em] hover:bg-blue-500 hover:scale-[1.02] active:scale-95 transition-all shadow-xl shadow-blue-600/30 flex items-center justify-center gap-3">
                                <i class="bi bi-cloud-check text-lg"></i>
                                Gravar Alterações
                            </button>

                            <a href="{{ route('contributions.index') }}"
                                class="w-full mt-4 py-4 bg-white/5 text-gray-400 rounded-2xl font-black text-[10px] uppercase tracking-widest hover:bg-white/10 hover:text-white transition-all flex items-center justify-center">
                                Descartar Mudanças
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hint Card -->
                <div class="bg-white/50 backdrop-blur-sm border border-white/40 p-6 rounded-[2rem] space-y-4">
                    <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center">
                        <i class="bi bi-lightbulb"></i>
                    </div>
                    <h4 class="text-sm font-black text-gray-900 uppercase tracking-tight">Dica de Verificação</h4>
                    <p class="text-xs text-gray-600 leading-relaxed font-medium">
                        As contribuições "pendentes" estão aguardando validação da tesouraria. Garanta que o comprovativo
                        esteja legível para agilizar o processo.
                    </p>
                </div>
            </div>
        </form>
    </div>

    <script>
        function updateFileName(event) {
            const input = event.target.files ? event.target : event.dataTransfer;
            const fileName = document.getElementById('fileName');
            const fileNameText = document.getElementById('fileNameText');

            if (input.files && input.files.length > 0) {
                fileNameText.textContent = input.files[0].name;
                fileName.classList.remove('hidden');
            }
        }

        function formatCurrency(value) {
            if (!value) return '0,00';
            return parseFloat(value).toLocaleString('pt-MZ', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        input[type="date"]::-webkit-calendar-picker-indicator {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            cursor: pointer;
        }
    </style>
@endsection