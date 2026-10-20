@extends('layouts.app')

@section('title', 'Meus Compromissos - Portal Life Church')
@section('page-title', 'Pacotes de Compromisso')
@section('page-subtitle', 'Escolha seu nível de compromisso mensal')

@section('content')
<div class="space-y-5">
    <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-3 sm:gap-4">
        <div>
            <p class="text-[10px] sm:text-xs font-black uppercase tracking-[0.18em] text-orange-700">Projeto Edificar</p>
            <h1 class="mt-0.5 text-xl sm:text-2xl font-black text-gray-950 tracking-tight">Pacotes de Compromisso</h1>
            <p class="mt-0.5 text-xs sm:text-sm text-gray-500">Escolha seu nível de compromisso mensal</p>
        </div>
    </header>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-xl p-3.5 sm:p-4">
            <h3 class="text-xs sm:text-sm font-bold text-red-800 mb-1.5">
                <i class="bi bi-exclamation-triangle-fill mr-1.5"></i>Ocorreram alguns erros:
            </h3>
            <ul class="list-disc list-inside text-red-700 text-xs sm:text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
        <h3 class="text-xs sm:text-sm font-black text-blue-900 mb-1">
            <i class="bi bi-info-circle mr-1.5"></i>Como Funciona?
        </h3>
        <p class="text-blue-800 text-xs sm:text-sm">
            Escolha um pacote de compromisso mensal. Você pode contribuir qualquer valor dentro do intervalo do seu pacote.
            Pode alterar seu pacote a qualquer momento.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
        @foreach($packages as $package)
        <div
            class="rounded-xl border border-gray-200 bg-white p-4 sm:p-5 shadow-sm hover:border-gray-300 transition flex flex-col justify-between @if($currentCommitment && $currentCommitment->package_id === $package->id) ring-2 ring-orange-600 border-orange-600 @endif">
            <div>
                <h3 class="text-sm sm:text-base font-black text-gray-950 mb-2">{{ $package->name }}</h3>
                <div class="mb-3">
                    <p class="text-xl sm:text-2xl font-black text-orange-700 tracking-tight">
                        {{ number_format($package->min_amount, 0) }}
                    </p>
                    <p class="text-xs text-gray-500 font-semibold">
                        até
                        @if($package->max_amount)
                            {{ number_format($package->max_amount, 0) }}
                        @else
                            ∞
                        @endif
                        MT
                    </p>
                </div>
                <p class="text-xs text-gray-600 mb-4">{{ $package->description }}</p>
            </div>
            @php($isActive = $currentCommitment && $currentCommitment->package_id === $package->id)

            <form action="{{ route('commitments.choose') }}" method="POST" class="w-full">
                @csrf
                <input type="hidden" name="package_id" value="{{ $package->id }}">
                <button type="button"
                    onclick="confirmCommitmentChange(this.form, '{{ $package->name }}', '{{ number_format($package->min_amount, 2, ',', '.') }}', {{ $isActive ? 'true' : 'false' }})"
                    class="w-full px-3.5 py-2 rounded-lg transition text-xs font-bold {{ $isActive ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-default' : 'bg-orange-700 hover:bg-orange-800 text-white shadow-sm' }}">
                    @if($isActive)
                        <span class="inline-flex items-center">
                            <i class="bi bi-check-lg mr-1.5"></i> Pacote Atual
                        </span>
                    @else
                        Mudar para este Pacote
                    @endif
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
    <script>
        window.confirmCommitmentChange = function (form, packageName, minAmount, isActive) {
            if (isActive) {
                Swal.fire({
                    title: 'Pacote Atual',
                    text: 'Este já é o seu pacote de compromisso atual.',
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            Swal.fire({
                title: 'Alterar Compromisso?',
                html: `Ao mudar para o <strong>${packageName}</strong>, está a assumir um novo compromisso mínimo de <strong>${minAmount} MT</strong> mensais.<br><br>Esta ação irá encerrar o seu pacote atual e iniciar este novo. Deseja continuar?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, Mudar de Pacote',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
@endpush
@endsection