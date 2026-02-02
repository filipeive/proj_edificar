@extends('layouts.app')

@section('title', 'Registar Contribuição - Portal Life Church')
@section('page-title', 'Registar Contribuição')
@section('page-subtitle', 'Adicione uma nova contribuição')

@section('content')
    <div class="grid grid-max-w-3xl mx-auto">
        <div class="bg-white rounded-lg shadow p-8">
            <form action="{{ route('contributions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-8 pb-8 border-b">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="bi bi-person-check mr-2"></i>Membro Contribuinte
                    </h3>

                    @if($canRegisterForOthers)

                        <div class="flex space-x-4 mb-6 p-3 bg-gray-50 rounded-lg">
                            <button type="button" id="btnMyContribution"
                                class="flex-1 py-2 rounded-lg font-semibold bg-blue-600 text-white transition hover:bg-blue-700"
                                onclick="toggleMemberSelection(false)">
                                <i class="bi bi-person-fill"></i> Minha Contribuição
                            </button>
                            <button type="button" id="btnOtherMember"
                                class="flex-1 py-2 rounded-lg font-semibold text-gray-700 bg-gray-200 transition hover:bg-gray-300"
                                onclick="toggleMemberSelection(true)">
                                <i class="bi bi-people-fill"></i> Para Outro Membro
                            </button>
                        </div>

                        <input type="hidden" name="user_id" id="userIdHidden" value="{{ $currentUser->id }}">

                        <div id="memberSelectionBlock" class="hidden">
                            <label for="user_id_select" class="block text-sm font-medium text-gray-700 mb-2">
                                Selecione o Membro da
                                {{ $currentUser->role === 'lider_celula' ? 'Célula' : ($currentUser->role === 'supervisor' ? 'Supervisão' : 'Zona') }}
                            </label>
                            <select id="user_id_select"
                                class="searchable-select w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent custom-select @error('user_id') border-red-500 @enderror"
                                onchange="updateSelectedMemberInfo()" data-label="Membro">
                                <option value="">-- Selecione um membro --</option>
                                @foreach($members as $member)
                                    {{-- A opção do usuário logado deve ser incluída, mas pode ser excluída no controller se for
                                    preferido --}}
                                    <option value="{{ $member->id }}" data-email="{{ $member->email }}"
                                        data-cell="{{ $member->cell?->name }}" @selected(($preselectedUserId == $member->id) || (old('user_id') == $member->id))>
                                        {{ $member->name }} @if($member->id === $currentUser->id) (Você) @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror

                            <div id="memberInfo" class="mt-4 hidden bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Email</p>
                                        <p id="memberEmail" class="font-medium text-gray-800"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Célula</p>
                                        <p id="memberCell" class="font-medium text-gray-800"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-700 font-semibold mb-2">Contribuição para: {{ $currentUser->name }}</p>
                        <input type="hidden" name="user_id" value="{{ $currentUser->id }}">
                    @endif

                    <div class="mt-4 p-3 bg-gray-100 rounded-lg text-sm">
                        Registando contribuição para: <span id="summaryMemberDisplay"
                            class="font-bold text-gray-800">{{ $currentUser->name }}</span>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-6">
                        <i class="bi bi-cash-coin mr-2"></i>Dados da Contribuição
                    </h3>

                    {{-- Informação do Pacote Atual --}}
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Seu Compromisso Atual:
                            <span class="font-semibold text-gray-800">
                                {{ $currentPackage->name ?? 'Nenhum' }}
                                @if($currentPackage->committed_amount > 0)
                                    (Comprometido: {{ number_format($currentPackage->committed_amount, 2, ',', '.') }} MT)
                                @else
                                    (Sem compromisso ativo)
                                @endif
                            </span>
                        </label>

                        {{-- Seleção do Pacote (Apenas se precisar trocar o pacote na contribuição) --}}
                        <select name="package_id" id="package_id"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent custom-select @error('package_id') border-red-500 @enderror"
                            required>
                            <option value="">-- Selecione o Pacote Referente à Contribuição --</option>
                            @foreach($packages as $package)
                                <option value="{{ $package->id }}" @selected(old('package_id') == $package->id || ($preselectedPackageId == $package->id) || ($currentPackage->id ?? null) == $package->id)>
                                    {{ $package->name }} ({{ number_format($package->min_amount, 2, ',', '.') }} -
                                    {{ number_format($package->max_amount, 2, ',', '.') }} MT)
                                </option>
                            @endforeach
                        </select>
                        @error('package_id')
                            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                Valor (MT) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-3 text-gray-500 font-semibold">MT</span>
                                <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                                    class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('amount') border-red-500 @enderror"
                                    placeholder="0.00" value="{{ old('amount') }}" required>
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contribution_date" class="block text-sm font-medium text-gray-700 mb-2">
                                Data da Contribuição <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="contribution_date" id="contribution_date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contribution_date') border-red-500 @enderror"
                                value="{{ old('contribution_date', now()->format('Y-m-d')) }}" required>
                            @error('contribution_date')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-blue-700">
                            <i class="bi bi-info-circle mr-2"></i>
                            <strong>Intervalo de Contribuições:</strong> Do dia 20 do mês anterior até ao dia 5 do mês atual
                        </p>
                    </div>
                </div>

                <div class="mb-8">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">
                        <i class="bi bi-paperclip mr-2"></i>Comprovativo (Opcional)
                    </h3>

                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer hover:border-gray-400 transition"
                        id="dropZone">
                        <input type="file" name="proof_path" id="proof_path" class="hidden" accept=".pdf,.jpg,.jpeg,.png">
                        <i class="bi bi-cloud-upload text-5xl text-gray-400 mb-3 block"></i>
                        <p class="text-gray-600 font-medium mb-1">Clique para enviar ou arraste o arquivo</p>
                        <p class="text-sm text-gray-500">PDF, JPG, PNG (Máx. 5MB)</p>
                    </div>

                    <div id="fileName" class="mt-4 hidden p-4 bg-green-50 border border-green-200 rounded-lg">
                        <div class="flex items-center">
                            <i class="bi bi-check-circle text-green-600 text-2xl mr-3"></i>
                            <div>
                                <p class="text-sm text-green-600 font-medium">Arquivo selecionado:</p>
                                <p id="fileNameText" class="text-green-800 font-semibold"></p>
                            </div>
                        </div>
                    </div>

                    @error('proof_path')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-6 mb-8">
                    <h4 class="font-bold text-gray-800 mb-3">
                        <i class="bi bi-check2-square mr-2"></i>Resumo da Contribuição
                    </h4>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-white rounded p-3">
                            <p class="text-xs text-gray-500">Membro</p>
                            <p id="summaryMember" class="font-bold text-gray-800 mt-1">{{ $currentUser->name }}</p>
                        </div>
                        <div class="bg-white rounded p-3">
                            <p class="text-xs text-gray-500">Valor</p>
                            <p id="summaryAmount" class="font-bold text-green-600 mt-1">-</p>
                        </div>
                        <div class="bg-white rounded p-3">
                            <p class="text-xs text-gray-500">Data</p>
                            <p id="summaryDate" class="font-bold text-gray-800 mt-1">-</p>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-4">
                    <button type="submit"
                        class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition font-bold text-lg">
                        <i class="bi bi-check-circle mr-2"></i>Registar Contribuição
                    </button>
                    <a href="{{ route('contributions.index') }}"
                        class="flex-1 bg-gray-200 text-gray-800 px-6 py-3 rounded-lg hover:bg-gray-300 transition font-bold text-lg text-center">
                        <i class="bi bi-arrow-left mr-2"></i>Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Variáveis globais para o toggle
        const userIdHidden = document.getElementById('userIdHidden');
        const memberSelectionBlock = document.getElementById('memberSelectionBlock');
        const memberSelect = document.getElementById('user_id_select');
        const summaryMemberDisplay = document.getElementById('summaryMemberDisplay');
        const btnMyContribution = document.getElementById('btnMyContribution');
        const btnOtherMember = document.getElementById('btnOtherMember');
        const memberInfoBlock = document.getElementById('memberInfo');

        // Variáveis de Contexto (Passadas do Controller)
        const managedPackageIds = @json($managedPackageIds);
        const myPackageId = "{{ $currentPackage->id ?? '' }}";
        const isResponsavelPacote = {{ $currentUser->role === 'responsavel_pacote' ? 'true' : 'false' }};

        // Função para alternar entre "Minha Contribuição" e "Outro Membro"
        function toggleMemberSelection(isOther) {
            if (isOther) {
                // Mudar para "Outro Membro"
                memberSelectionBlock?.classList.remove('hidden');
                memberSelect?.setAttribute('name', 'user_id');
                userIdHidden?.removeAttribute('name');

                btnOtherMember?.classList.remove('bg-gray-200', 'text-gray-700');
                btnOtherMember?.classList.add('bg-blue-600', 'text-white');
                btnMyContribution?.classList.add('bg-gray-200', 'text-gray-700');
                btnMyContribution?.classList.remove('bg-blue-600', 'text-white');

                updateSelectedMemberInfo();

                // Filtro de Pacotes: Se for Responsável, mostrar apenas os gerenciados
                if (isResponsavelPacote) {
                    filterPackagesForOthers();
                }

            } else {
                // Mudar para "Minha Contribuição"
                memberSelectionBlock?.classList.add('hidden');
                userIdHidden?.setAttribute('name', 'user_id');
                memberSelect?.removeAttribute('name');

                btnMyContribution?.classList.remove('bg-gray-200', 'text-gray-700');
                btnMyContribution?.classList.add('bg-blue-600', 'text-white');
                btnOtherMember?.classList.add('bg-gray-200', 'text-gray-700');
                btnOtherMember?.classList.remove('bg-blue-600', 'text-white');

                memberInfoBlock?.classList.add('hidden');
                if (summaryMemberDisplay) {
                    summaryMemberDisplay.textContent = '{{ $currentUser->name }}';
                }

                // Filtro de Pacotes: Se for Responsável, mostrar apenas o próprio
                if (isResponsavelPacote) {
                    filterPackagesForMe();
                }
            }
        }

        function filterPackagesForMe() {
            const packageSelect = document.getElementById('package_id');
            if (!packageSelect) return;

            // Iterar opções e mostrar apenas o meu pacote
            Array.from(packageSelect.options).forEach(option => {
                if (option.value === "") return; // Pular placeholder

                // Mostrar apenas se for o ID do meu pacote
                if (option.value == myPackageId) {
                    option.hidden = false;
                    option.disabled = false;
                    if (!packageSelect.value) packageSelect.value = option.value; // Auto-selecionar se vazio
                } else {
                    option.hidden = true;
                    option.disabled = true;
                }
            });

            // Se o atual selecionado ficou escondido, resetar
            if (packageSelect.value != myPackageId && myPackageId) {
                packageSelect.value = myPackageId;
            }
        }

        function filterPackagesForOthers() {
            const packageSelect = document.getElementById('package_id');
            if (!packageSelect) return;

            Array.from(packageSelect.options).forEach(option => {
                if (option.value === "") return;

                // Mostrar apenas se estiver na lista de gerenciados
                // managedPackageIds é array de numeros/strings
                if (managedPackageIds.includes(parseInt(option.value))) {
                    option.hidden = false;
                    option.disabled = false;
                } else {
                    option.hidden = true;
                    option.disabled = true;
                }
            });

            // Se o valor selecionado agora for inválido (escondido), resetar
            // Mas cuidado para não desmarcar se for válido.
            if (packageSelect.value && !managedPackageIds.includes(parseInt(packageSelect.value))) {
                packageSelect.value = "";
            }
        }

        // Função para atualizar as informações quando o outro membro for selecionado
        function updateSelectedMemberInfo() {
            if (!memberSelect) return;

            const selectedOption = memberSelect.options[memberSelect.selectedIndex];

            if (memberSelect.value) {
                document.getElementById('memberEmail').textContent = selectedOption.dataset.email;
                document.getElementById('memberCell').textContent = selectedOption.dataset.cell || 'N/A';
                memberInfoBlock.classList.remove('hidden');
                summaryMemberDisplay.textContent = selectedOption.textContent.replace(' (Você)', '');
            } else {
                memberInfoBlock.classList.add('hidden');
                summaryMemberDisplay.textContent = 'Selecione';
            }
        }

        // As outras funções (updateFileName, drag&drop, listeners para amount/date) permanecem aqui.

        // Atualizar resumo ao digitar valor
        document.getElementById('amount')?.addEventListener('change', function () {
            document.getElementById('summaryAmount').textContent =
                this.value ? parseFloat(this.value).toFixed(2) + ' MT' : '-';
        });

        // Atualizar resumo ao selecionar data
        document.getElementById('contribution_date')?.addEventListener('change', function () {
            const date = new Date(this.value);
            document.getElementById('summaryDate').textContent =
                date.toLocaleDateString('pt-PT', { day: '2-digit', month: '2-digit', year: 'numeric' });
        });

        // Drag & Drop para arquivo
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('proof_path');
        const fileName = document.getElementById('fileName');
        const fileNameText = document.getElementById('fileNameText');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('bg-blue-50', 'border-blue-400');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('bg-blue-50', 'border-blue-400');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('bg-blue-50', 'border-blue-400');
            fileInput.files = e.dataTransfer.files;
            updateFileName();
        });

        fileInput.addEventListener('change', updateFileName);

        function updateFileName() {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                fileNameText.textContent = file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
                fileName.classList.remove('hidden');
            }
        }

        // Inicialização da página
        document.addEventListener('DOMContentLoaded', function () {
            const preselectedUserId = "{{ $preselectedUserId }}";
            const currentUserId = "{{ $currentUser->id }}";

            // Se a opção de toggle existe (ou seja, não é um Membro simples)
            if (typeof toggleMemberSelection === 'function' && document.getElementById('userIdHidden')) {
                if (preselectedUserId && preselectedUserId !== currentUserId) {
                    toggleMemberSelection(true);
                } else {
                    toggleMemberSelection(false); // Define o padrão para Minha Contribuição
                }
            }

            if (!document.getElementById('contribution_date').value) {
                document.getElementById('contribution_date').valueAsDate = new Date();
            }

            // Trigger summary updates
            if (memberSelect && memberSelect.value) {
                updateSelectedMemberInfo();
            }
        });
    </script>
@endsection