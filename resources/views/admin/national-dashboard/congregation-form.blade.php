@extends('layouts.app')

@section('title', $congregation ? 'Editar congregação' : 'Registar congregação')
@section('page-title', $congregation ? 'Editar Congregação' : 'Registar Congregação')
@section('page-subtitle', 'Configuração administrativa de uma instituição nacional')

@section('content')
    <div class="w-full space-y-5">
        <a href="{{ route('national.congregations.index') }}" class="text-xs font-bold text-orange-800 hover:underline"><i class="bi bi-arrow-left mr-1"></i>Voltar à lista</a>
        @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif

        <form method="POST" action="{{ $congregation ? route('national.congregations.update', $congregation) : route('national.congregations.store') }}" class="space-y-5 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
            @csrf
            @if($congregation) @method('PUT') @endif
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="mb-1 block text-xs font-bold text-gray-600">Nome oficial</label>
                    <input id="name" name="name" value="{{ old('name', $congregation?->name) }}" required maxlength="150" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label for="slug" class="mb-1 block text-xs font-bold text-gray-600">Slug único</label>
                    <input id="slug" name="slug" value="{{ old('slug', $congregation?->slug) }}" required maxlength="100" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label for="domain" class="mb-1 block text-xs font-bold text-gray-600">Domínio (opcional)</label>
                    <input id="domain" name="domain" value="{{ old('domain', $congregation?->domain) }}" placeholder="gestao.igreja.org" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div>
                    <label for="subdomain" class="mb-1 block text-xs font-bold text-gray-600">Subdomínio (opcional)</label>
                    <input id="subdomain" name="subdomain" value="{{ old('subdomain', $congregation?->subdomain) }}" placeholder="igreja-norte" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <h2 class="text-sm font-black text-gray-900">Módulos da congregação</h2>
                <p class="mt-1 text-xs text-gray-500">Controlo de funcionalidades especiais atribuídas a esta congregação.</p>

                <div class="mt-3 rounded-lg border border-gray-200 bg-gray-50 p-4">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="hidden" name="has_edificar_project" value="0">
                        <input type="checkbox" name="has_edificar_project" value="1"
                            class="mt-0.5 rounded border-gray-300 text-orange-700 focus:ring-orange-700"
                            @checked(old('has_edificar_project', $congregation?->has_edificar_project ?? false))
                            @disabled($congregation?->is_primary)>
                        <div>
                            <span class="text-sm font-bold text-gray-900">Módulo Projeto Edificar (Construção & Pacotes de Compromisso)</span>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Habilita pacotes de compromisso, arrecadação para obras físicas e painel de acompanhamento de engenharia.
                                Se desmarcado, a congregação terá apenas <strong>gestão eclesiástica</strong> (membros, cultos, células, dízimos/ofertas e finanças locais).
                            </p>
                            @if($congregation?->is_primary)
                                <input type="hidden" name="has_edificar_project" value="1">
                                <p class="mt-1 text-[11px] font-semibold text-orange-700"><i class="bi bi-info-circle mr-1"></i>A congregação principal tem o Projeto Edificar permanentemente ativo.</p>
                            @endif
                        </div>
                    </label>
                </div>
            </div>

            <div class="border-t border-gray-100 pt-5">
                <x-location-map id="congregation-location-map" label-text="Localização da congregação" help-text="Clique no mapa para marcar a sede da congregação; arraste o marcador para ajustar a localização." :latitude="$congregation?->latitude" :longitude="$congregation?->longitude" />
            </div>

            @if(!$congregation)
                <div class="border-t border-gray-100 pt-5">
                    <h2 class="text-sm font-black text-gray-900">Administrador local inicial</h2>
                    <p class="mt-1 text-xs text-gray-500">A conta é criada dentro desta congregação e não recebe acesso nacional.</p>
                    <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="admin_name" class="mb-1 block text-xs font-bold text-gray-600">Nome</label>
                            <input id="admin_name" name="admin_name" value="{{ old('admin_name') }}" required class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label for="admin_email" class="mb-1 block text-xs font-bold text-gray-600">Email</label>
                            <input id="admin_email" type="email" name="admin_email" value="{{ old('admin_email') }}" required class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label for="admin_password" class="mb-1 block text-xs font-bold text-gray-600">Palavra-passe inicial</label>
                            <input id="admin_password" type="password" name="admin_password" minlength="10" required autocomplete="new-password" class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                        <div>
                            <label for="admin_password_confirmation" class="mb-1 block text-xs font-bold text-gray-600">Confirmar palavra-passe</label>
                            <input id="admin_password_confirmation" type="password" name="admin_password_confirmation" minlength="10" required autocomplete="new-password" class="w-full rounded-lg border-gray-300 text-sm">
                        </div>
                    </div>
                </div>
            @else
                <div class="border-t border-gray-100 pt-5">
                    <label for="is_active" class="mb-1 block text-xs font-bold text-gray-600">Estado</label>
                    <select id="is_active" name="is_active" class="w-full rounded-lg border-gray-300 text-sm md:max-w-xs" @disabled($congregation->is_primary)>
                        <option value="1" @selected(old('is_active', $congregation->is_active ? '1' : '0') == '1')>Ativa</option>
                        <option value="0" @selected(old('is_active', $congregation->is_active ? '1' : '0') == '0')>Inativa</option>
                    </select>
                    @if($congregation->is_primary)
                        <input type="hidden" name="is_active" value="1">
                        <p class="mt-1 text-[11px] text-gray-500">A congregação principal não pode ser desativada.</p>
                    @endif
                </div>
            @endif

            <div class="flex justify-end border-t border-gray-100 pt-4">
                <button class="inline-flex items-center gap-2 rounded-lg bg-orange-700 px-5 py-2.5 text-sm font-bold text-white hover:bg-orange-800"><i class="bi bi-check2"></i>{{ $congregation ? 'Guardar alterações' : 'Criar congregação' }}</button>
            </div>
        </form>

        @if($congregation)
            <div class="space-y-4 rounded-xl border border-gray-200 bg-white p-5 shadow-sm" x-data="{ addAdminOpen: false, resetModalUser: null }">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                    <div>
                        <h2 class="text-base font-black text-gray-950 flex items-center gap-2">
                            <i class="bi bi-shield-lock text-orange-700"></i> Administradores da congregação
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Utilizadores locais com privilégios de gestão desta igreja. Não têm acesso ao painel nacional.</p>
                    </div>
                    <button type="button" @click="addAdminOpen = !addAdminOpen" class="inline-flex items-center gap-1.5 rounded-lg bg-orange-700 px-3.5 py-2 text-xs font-bold text-white hover:bg-orange-800 transition">
                        <i class="bi bi-person-plus-fill"></i> Registar novo administrador
                    </button>
                </div>

                <!-- Formulário recolhível para adicionar administrador -->
                <div x-show="addAdminOpen" x-cloak class="rounded-xl border border-orange-200 bg-orange-50/50 p-4 transition">
                    <h3 class="text-xs font-black uppercase tracking-wider text-orange-950 mb-3">Registar Administrador Local</h3>
                    <form method="POST" action="{{ route('national.congregations.admins.store', $congregation) }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="new_admin_name" class="mb-1 block text-xs font-bold text-gray-700">Nome completo</label>
                                <input id="new_admin_name" name="admin_name" required maxlength="255" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div>
                                <label for="new_admin_email" class="mb-1 block text-xs font-bold text-gray-700">Email institucional</label>
                                <input id="new_admin_email" type="email" name="admin_email" required maxlength="255" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div>
                                <label for="new_admin_password" class="mb-1 block text-xs font-bold text-gray-700">Palavra-passe (mín. 10 carateres)</label>
                                <input id="new_admin_password" type="password" name="admin_password" minlength="10" required class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div>
                                <label for="new_admin_password_confirmation" class="mb-1 block text-xs font-bold text-gray-700">Confirmar palavra-passe</label>
                                <input id="new_admin_password_confirmation" type="password" name="admin_password_confirmation" minlength="10" required class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                        </div>
                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="addAdminOpen = false" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                            <button type="submit" class="rounded-lg bg-orange-700 px-4 py-1.5 text-xs font-bold text-white hover:bg-orange-800">Criar administrador</button>
                        </div>
                    </form>
                </div>

                <!-- Tabela de administradores existentes -->
                <div class="overflow-x-auto rounded-lg border border-gray-100">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-[10px] font-bold uppercase tracking-wider text-gray-500">
                            <tr>
                                <th class="px-4 py-3">Nome</th>
                                <th class="px-4 py-3">Email</th>
                                <th class="px-4 py-3">Função</th>
                                <th class="px-4 py-3">Estado</th>
                                <th class="px-4 py-3 text-right">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($admins as $adm)
                                <tr class="hover:bg-gray-50/60">
                                    <td class="px-4 py-3 font-bold text-gray-900">{{ $adm->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $adm->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold uppercase text-slate-700">
                                            {{ $adm->role === 'pastor_senior' ? 'Pastor Sénior' : 'Administrador' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $adm->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $adm->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <button type="button" @click="resetModalUser = { id: {{ $adm->id }}, name: '{{ addslashes($adm->name) }}' }" class="inline-flex items-center gap-1 font-bold text-orange-800 hover:underline">
                                            <i class="bi bi-key"></i> Redefinir senha
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">Nenhum administrador registado nesta congregação.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Modal para redefinir senha -->
                <div x-show="resetModalUser" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
                    <div @click.away="resetModalUser = null" class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-4">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <h3 class="text-sm font-black text-gray-900">Redefinir Palavra-passe</h3>
                            <button type="button" @click="resetModalUser = null" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
                        </div>
                        <p class="text-xs text-gray-600">
                            Definir nova palavra-passe para <strong x-text="resetModalUser?.name" class="text-gray-900"></strong>.
                        </p>
                        <form method="POST" :action="`{{ url('national/congregations/' . $congregation->id . '/admins') }}/${resetModalUser?.id}/reset-password`" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nova Palavra-passe (mín. 10 carateres)</label>
                                <input type="password" name="new_password" required minlength="10" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Confirmar Nova Palavra-passe</label>
                                <input type="password" name="new_password_confirmation" required minlength="10" class="w-full rounded-lg border-gray-300 text-sm">
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" @click="resetModalUser = null" class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 hover:bg-gray-50">Cancelar</button>
                                <button type="submit" class="rounded-lg bg-orange-700 px-4 py-1.5 text-xs font-bold text-white hover:bg-orange-800">Guardar nova senha</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
