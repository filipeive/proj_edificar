@extends('layouts.app')

@section('title', 'Editar Contribuição - Portal Life Church')
@section('page-title', 'Editar Contribuição')
@section('page-subtitle', 'Atualizar dados da oferta ou dízimo')

@section('content')
    <div class="max-w-4xl mx-auto space-y-5" x-data="{ 
        proofType: '{{ $contribution->proof_message ? 'message' : 'file' }}',
        amount: {{ $contribution->amount }},
        isDragging: false
    }">
        <!-- Page Header -->
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
            <div>
                <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">
                    <a href="{{ route('contributions.index') }}" class="hover:underline">Contribuições</a> • ID #{{ $contribution->id }}
                </p>
                <div class="mt-0.5 flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Refinar Detalhes</h1>
                    <span class="inline-flex items-center px-2.5 py-0.5 bg-orange-50 text-orange-700 rounded-full text-[10px] font-black uppercase tracking-wider border border-orange-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-1.5 animate-pulse"></span>
                        {{ $contribution->status }}
                    </span>
                </div>
                <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Atualize os dados da oferta ou comprovativo anexado</p>
            </div>
            <div>
                <a href="{{ route('contributions.index') }}"
                    class="inline-flex justify-center items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 sm:py-2 text-xs sm:text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm">
                    <i class="bi bi-arrow-left"></i> <span>Voltar</span>
                </a>
            </div>
        </header>

        <form action="{{ route('contributions.update', $contribution) }}" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 lg:grid-cols-3 gap-4 pb-8">
            @csrf
            @method('PUT')

            <!-- Main Column: General Info -->
            <div class="lg:col-span-2 space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50">
                        <h2 class="text-xs sm:text-sm font-black uppercase tracking-wider text-gray-700 flex items-center gap-2">
                            <i class="bi bi-wallet2 text-orange-700"></i>
                            Informações Financeiras
                        </h2>
                    </div>

                    <div class="p-4 sm:p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Amount -->
                        <div class="space-y-1.5">
                            <label for="amount" class="text-[10px] font-black uppercase tracking-wider text-gray-500">
                                Valor da Contribuição
                            </label>
                            <div class="relative group">
                                <span
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-black text-gray-400">MT</span>
                                <input type="number" name="amount" id="amount" step="0.01" min="0" x-model="amount"
                                    class="w-full pl-10 pr-3 py-2 bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all font-black text-sm sm:text-base text-gray-900 @error('amount') border-red-500 @enderror"
                                    value="{{ old('amount', $contribution->amount) }}" required>
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight">{{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Date -->
                        <div class="space-y-1.5">
                            <label for="contribution_date"
                                class="text-[10px] font-black uppercase tracking-wider text-gray-500">
                                Data do Depósito
                            </label>
                            <div class="relative">
                                <i class="bi bi-calendar3 absolute left-3 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                                <input type="date" name="contribution_date" id="contribution_date"
                                    class="w-full pl-8 pr-3 py-2 bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all font-bold text-xs sm:text-sm text-gray-900 @error('contribution_date') border-red-500 @enderror"
                                    value="{{ old('contribution_date', $contribution->contribution_date->format('Y-m-d')) }}"
                                    required>
                            </div>
                            @error('contribution_date')
                                <p class="text-red-500 text-[10px] font-bold mt-1 uppercase tracking-tight">{{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Proof Upload Section -->
                <div class="rounded-xl border border-gray-200 bg-white shadow-sm overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50/50 flex justify-between items-center">
                        <h2 class="text-xs sm:text-sm font-black uppercase tracking-wider text-gray-700 flex items-center gap-2">
                            <i class="bi bi-file-earmark-check text-orange-700"></i>
                            Comprovativo
                        </h2>

                        <div class="flex p-1 bg-gray-100 rounded-lg border border-gray-200">
                            <button type="button" @click="proofType = 'file'"
                                :class="proofType === 'file' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'"
                                class="px-3 py-1 rounded-md text-xs font-bold transition-all">
                                Ficheiro
                            </button>
                            <button type="button" @click="proofType = 'message'"
                                :class="proofType === 'message' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'"
                                class="px-3 py-1 rounded-md text-xs font-bold transition-all">
                                Mensagem
                            </button>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        <!-- File Upload -->
                        <div x-show="proofType === 'file'" x-cloak x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            @if($contribution->proof_path)
                                <div
                                    class="mb-4 p-3.5 bg-blue-50 border border-blue-200 rounded-xl flex items-center justify-between group/file">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-white rounded-lg shadow-sm flex items-center justify-center text-blue-600">
                                            <i class="bi bi-file-earmark-pdf text-lg"></i>
                                        </div>
                                        <div>
                                            <p
                                                class="text-[10px] text-blue-600 font-black uppercase tracking-wider leading-none mb-1">
                                                Arquivo Atual</p>
                                            <a href="{{ route('contributions.receipt', $contribution) }}" target="_blank"
                                                class="text-xs sm:text-sm font-bold text-blue-900 hover:text-blue-600 transition-colors inline-flex items-center">
                                                Visualizar Comprovativo
                                                <i class="bi bi-box-arrow-up-right ml-1.5 text-xs"></i>
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
                                    :class="isDragging ? 'border-orange-500 bg-orange-50/50' : 'border-gray-300 bg-gray-50/30 hover:border-orange-400 hover:bg-orange-50/20'"
                                    class="border-2 border-dashed rounded-xl p-6 sm:p-8 text-center cursor-pointer transition-all">
                                    <div
                                        class="w-12 h-12 bg-white border border-gray-200 rounded-xl shadow-sm flex items-center justify-center mx-auto mb-3">
                                        <i class="bi bi-cloud-upload text-xl text-orange-700"></i>
                                    </div>
                                    <h3 class="text-sm font-black text-gray-900 mb-1">Novo Comprovativo</h3>
                                    <p class="text-xs font-medium text-gray-500">Arraste o ficheiro ou <span
                                            class="text-orange-700 font-bold underline">navegue</span></p>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mt-3">PDF, JPG,
                                        PNG • Máx 5MB</p>
                                </div>

                                <div id="fileName"
                                    class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg hidden flex items-center gap-2.5">
                                    <div
                                        class="w-7 h-7 bg-green-500 rounded-md flex items-center justify-center text-white">
                                        <i class="bi bi-check-lg text-sm"></i>
                                    </div>
                                    <span id="fileNameText" class="text-xs sm:text-sm font-bold text-green-800"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Message Paste -->
                        <div x-show="proofType === 'message'" x-cloak x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0">
                            <div class="space-y-3">
                                <label class="text-[10px] font-black uppercase tracking-wider text-gray-500">Mensagem
                                    da Operadora</label>
                                <textarea name="proof_message" id="proof_message" rows="5"
                                    class="w-full px-3 py-2 text-xs sm:text-sm bg-white border border-gray-300 focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 rounded-lg transition-all font-medium text-gray-800 placeholder-gray-400"
                                    placeholder="Cole aqui a mensagem completa (M-Pesa, e-Mola, etc)...">{{ old('proof_message', $contribution->proof_message) }}</textarea>
                                <div class="flex items-start gap-2 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                                    <i class="bi bi-info-circle text-orange-600 mt-0.5 text-xs"></i>
                                    <p
                                        class="text-[11px] font-semibold text-orange-800 leading-relaxed">
                                        Certifique-se de colar a mensagem original para uma verificação rápida e sem erros.
                                    </p>
                                </div>
                            </div>
                        </div>

                        @error('proof_path')
                            <p class="text-red-500 text-[10px] font-bold mt-2 uppercase">{{ $message }}</p>
                        @enderror
                        @error('proof_message')
                            <p class="text-red-500 text-[10px] font-bold mt-2 uppercase">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar Column: Summary & Actions -->
            <div class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm">
                    <h3 class="text-xs font-black uppercase tracking-wider text-orange-700 mb-4 flex items-center gap-1.5">
                        <i class="bi bi-lightning-charge-fill"></i>
                        Resumo do Ajuste
                    </h3>

                    <div class="space-y-4">
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-3">
                            <p class="text-[10px] font-black text-gray-500 uppercase tracking-wider mb-0.5">Novo Montante</p>
                            <p class="text-xl sm:text-2xl font-black text-gray-950 tracking-tight flex items-baseline gap-1.5 tabular-nums">
                                <span x-text="formatCurrency(amount)">0,00</span>
                                <span class="text-xs font-black text-orange-700">MT</span>
                            </p>
                        </div>

                        <div class="pt-3 border-t border-gray-200 space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Tipo de
                                    Prova</span>
                                <span class="text-xs font-black text-gray-900 uppercase"
                                    x-text="proofType === 'file' ? 'ANEXO' : 'TEXTO'"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Data</span>
                                <span
                                    class="text-xs font-black text-gray-900 uppercase">{{ $contribution->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        <div class="pt-3 space-y-2">
                            <button type="submit"
                                class="w-full py-2.5 bg-orange-700 text-white rounded-lg font-bold text-xs sm:text-sm hover:bg-orange-800 transition-all shadow-sm flex items-center justify-center gap-2">
                                <i class="bi bi-cloud-check"></i>
                                Gravar Alterações
                            </button>

                            <a href="{{ route('contributions.index') }}"
                                class="w-full py-2 border border-gray-300 bg-white text-gray-700 rounded-lg font-bold text-xs sm:text-sm hover:bg-gray-50 transition-all flex items-center justify-center">
                                Descartar Mudanças
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Hint Card -->
                <div class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm space-y-2">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-orange-50 text-orange-700 border border-orange-200 rounded-lg flex items-center justify-center">
                            <i class="bi bi-lightbulb text-sm"></i>
                        </div>
                        <h4 class="text-xs font-black text-gray-900 uppercase tracking-wider">Dica de Verificação</h4>
                    </div>
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