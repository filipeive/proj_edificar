@extends('layouts.app')

@section('title', 'Novo Visitante - Portal Life Church')
@section('page-title', 'Cadastrar Visitante')
@section('page-subtitle', 'Registrar informações do visitante')

@section('content')
    <div class="w-full space-y-8">
        <!-- Header Card -->
        <div class="bg-white p-8 md:p-12 rounded-[2.5rem] shadow-sm border border-gray-100 relative overflow-hidden group">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-orange-50/50 rounded-full -mr-32 -mt-32 transition-transform group-hover:scale-110 duration-700">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="flex flex-col md:flex-row items-center gap-8 text-center md:text-left">
                    <div
                        class="w-24 h-24 rounded-[2rem] bg-gradient-to-br from-orange-500 to-orange-700 text-white flex items-center justify-center text-4xl shadow-2xl shadow-orange-100">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <div class="flex items-center justify-center md:justify-start gap-2 text-xs font-bold text-orange-600 uppercase tracking-widest mb-2">
                            <a href="{{ route('visitors.index') }}" class="hover:underline">Visitantes</a>
                            <i class="bi bi-chevron-right text-[10px]"></i>
                            <span>Novo Cadastro</span>
                        </div>
                        <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">Cadastrar Visitante</h1>
                        <p class="text-gray-500 font-medium">Registe as informações para acompanhamento e integração</p>
                    </div>
                </div>

                <a href="{{ route('visitors.index') }}"
                    class="group flex items-center bg-gray-50 text-gray-500 px-6 py-4 rounded-2xl hover:bg-gray-100 transition-all font-bold text-xs uppercase tracking-widest">
                    <i class="bi bi-arrow-left text-lg mr-2 group-hover:-translate-x-1 transition-transform"></i>
                    Cancelar
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('visitors.store') }}" class="space-y-8 pb-12">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Primary Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Dados Pessoais -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center text-xl">
                                <i class="bi bi-person-badge"></i>
                            </div>
                            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Dados Pessoais</h3>
                        </div>
                        
                        <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nome Completo *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    placeholder="Ex: Manuel dos Santos"
                                    class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-gray-700 transition-all @error('name') ring-2 ring-red-100 border-red-200 @enderror">
                                @error('name')
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Idade</label>
                                <input type="number" name="age" value="{{ old('age') }}" min="1" max="150"
                                    class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-gray-700 transition-all @error('age') ring-2 ring-red-100 border-red-200 @enderror">
                                @error('age')
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Sexo</label>
                                <select name="gender"
                                    class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-gray-700 transition-all custom-select @error('gender') ring-2 ring-red-100 border-red-200 @enderror">
                                    <option value="">Selecione...</option>
                                    <option value="masculino" {{ old('gender') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="feminino" {{ old('gender') == 'feminino' ? 'selected' : '' }}>Feminino</option>
                                </select>
                                @error('gender')
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Contato e Localização -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Contato & Localização</h3>
                        </div>

                        <div class="p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Telefone</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-gray-700 transition-all @error('phone') ring-2 ring-red-100 border-red-200 @enderror"
                                    placeholder="84 123 4567">
                                @error('phone')
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Bairro</label>
                                <input type="text" name="neighborhood" value="{{ old('neighborhood') }}"
                                    class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-gray-700 transition-all @error('neighborhood') ring-2 ring-red-100 border-red-200 @enderror">
                                @error('neighborhood')
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Cidade</label>
                                <input type="text" name="city" value="{{ old('city', 'Maputo') }}"
                                    class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-gray-700 transition-all @error('city') ring-2 ring-red-100 border-red-200 @enderror">
                                @error('city')
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Data da Visita *</label>
                                <input type="date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" required
                                    class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-gray-700 transition-all @error('visit_date') ring-2 ring-red-100 border-red-200 @enderror">
                                @error('visit_date')
                                    <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Observações -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center text-xl">
                                <i class="bi bi-chat-left-text-fill"></i>
                            </div>
                            <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Observações</h3>
                        </div>

                        <div class="p-10 space-y-2">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Notas adicionais</label>
                            <textarea name="notes" rows="4"
                                class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-gray-700 transition-all @error('notes') ring-2 ring-red-100 border-red-200 @enderror"
                                placeholder="Informações adicionais sobre o visitante...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-[10px] font-black uppercase tracking-widest mt-1 ml-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right Column: Context & Metadata -->
                <div class="space-y-8">
                    <!-- Culto e Convite -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                            <i class="bi bi-calendar-check text-blue-600"></i>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Contexto da Visita</h3>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Culto Visitado</label>
                                <select name="service_id"
                                    class="searchable-select w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-sm transition-all custom-select" data-label="Culto">
                                    <option value="">Selecione...</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                            {{ $service->date->format('d/m/Y') }} - {{ $service->service_type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="pt-4 border-t border-gray-50">
                                <label class="flex items-center gap-3 p-4 bg-orange-50/50 rounded-2xl cursor-pointer hover:bg-orange-50 transition-colors group">
                                    <input type="checkbox" name="invited_by_someone" id="invited_by_someone" value="1"
                                        {{ old('invited_by_someone') ? 'checked' : '' }}
                                        class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                                        onchange="document.getElementById('inviter_name_field').classList.toggle('hidden', !this.checked)">
                                    <span class="text-xs font-black text-gray-600 uppercase tracking-widest">Veio a convite de alguém?</span>
                                </label>

                                <div id="inviter_name_field" class="{{ old('invited_by_someone') ? '' : 'hidden' }} mt-4 space-y-2">
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Nome de quem convidou</label>
                                    <input type="text" name="inviter_name" value="{{ old('inviter_name') }}"
                                        class="w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-sm transition-all">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Atribuição -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100">
                        <div class="p-8 border-b border-gray-50 bg-gray-50/30 flex items-center gap-3">
                            <i class="bi bi-diagram-3-fill text-purple-600"></i>
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Integração</h3>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Zona para Integração</label>
                                <select name="zone_id" id="zone_select"
                                    class="searchable-select w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-sm transition-all custom-select" data-label="Zona">
                                    <option value="">Atribuir depois...</option>
                                    @foreach($zones as $zone)
                                        <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                            {{ $zone->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Célula para Integração</label>
                                <select name="cell_id" id="cell_select"
                                    class="searchable-select w-full px-6 py-4 bg-gray-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 rounded-2xl font-bold text-sm transition-all custom-select" data-label="Célula">
                                    <option value="">Selecione a Zona Primeiro...</option>
                                </select>
                            </div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-center px-4 italic leading-relaxed">
                                A atribuição de zona facilita o acompanhamento pelos pastores locais.
                            </p>
                        </div>
                    </div>

                    <!-- Submit Card -->
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 p-8">
                        <button type="submit"
                            class="w-full px-8 py-5 bg-gradient-to-r from-orange-600 to-orange-500 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] transform hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl shadow-orange-600/20 flex items-center justify-center gap-3">
                            <i class="bi bi-check2-circle text-xl"></i>
                            Finalizar Registo
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Toggle inviter name field
        document.getElementById('invited_by_someone').addEventListener('change', function() {
            document.getElementById('inviter_name_field').classList.toggle('hidden', !this.checked);
        });

        // Dynamic Cell Loading
        document.addEventListener('DOMContentLoaded', function() {
            const zoneSelect = document.getElementById('zone_select');
            const cellSelect = document.getElementById('cell_select');

            zoneSelect.addEventListener('change', async function() {
                const zoneId = this.value;
                const tomSelect = cellSelect.tomselect;

                if (!zoneId) {
                    if (tomSelect) {
                        tomSelect.clear();
                        tomSelect.clearOptions();
                        tomSelect.addOption({value: '', text: 'Selecione a Zona Primeiro...'});
                    }
                    return;
                }

                try {
                    const response = await fetch(`{{ route('visitors.cells-by-zone') }}?zone_id=${zoneId}`);
                    const cells = await response.json();

                    if (tomSelect) {
                        tomSelect.clear();
                        tomSelect.clearOptions();
                        
                        if (cells.length > 0) {
                            cells.forEach(cell => {
                                tomSelect.addOption({value: cell.id, text: cell.name});
                            });
                        } else {
                            tomSelect.addOption({value: '', text: 'Nenhuma célula nesta zona'});
                        }
                        
                        tomSelect.refreshOptions(false);
                    }
                } catch (error) {
                    console.error('Error loading cells:', error);
                }
            });

            // Initial trigger if zone is already selected (e.g., validation error)
            if (zoneSelect.value) {
                zoneSelect.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection

