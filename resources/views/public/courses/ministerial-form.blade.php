@extends('layouts.auth')

@section('title', 'Inscrição - ' . $course->name)

@section('content')

    {{-- Bootstrap Icons CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --bg-page: #020617;
            --bg-card: rgba(30, 41, 59, 0.7);
            --bg-input: #0f172a;
            --orange: #f97316;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #020617 100%);
            min-height: 100vh;
        }

        .page-wrap {
            max-width: 600px;
            margin: 0 auto;
            padding: 60px 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .church-eyebrow {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: var(--orange);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .page-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.1;
        }

        .form-card {
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
            margin-left: 4px;
        }

        .form-control {
            width: 100%;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 14px 18px;
            color: white;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--orange);
            box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        }

        .form-group {
            margin-bottom: 24px;
        }

        .btn-submit {
            width: 100%;
            background: var(--orange);
            color: white;
            border: none;
            border-radius: 12px;
            padding: 16px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-submit:hover {
            background: #ea580c;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(249, 115, 22, 0.4);
        }

        .radio-card-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .radio-card {
            position: relative;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 12px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
        }

        .radio-card input {
            position: absolute;
            opacity: 0;
        }

        .radio-card.active {
            border-color: var(--orange);
            background: rgba(249, 115, 22, 0.05);
        }

        .radio-card span {
            font-size: 0.9rem;
            font-weight: 600;
        }
    </style>

    <div class="page-wrap">
        <header class="page-header">
            <div class="church-eyebrow">
                <i class="bi bi-gem"></i> Portal Life Church
            </div>
            <h1 class="page-title">{{ $course->name }}</h1>
            <p class="text-slate-400">Formulário de Inscrição Individual</p>
        </header>

        <div class="form-card">
            <form action="{{ route('public.forms.ministerial.store') }}" method="POST">
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                <div class="form-group">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="full_name" class="form-control" placeholder="Seu nome completo" required
                        value="{{ old('full_name') }}">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">E-mail (Opcional)</label>
                        <input type="email" name="email" class="form-control" placeholder="seu@email.com"
                            value="{{ old('email') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Telefone / WhatsApp</label>
                        <input type="text" name="phone" class="form-control" placeholder="(+258) ..." required
                            value="{{ old('phone') }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Você já é membro da Life Church?</label>
                    <div class="radio-card-group"
                        x-data="{ isMember: {{ old('is_church_member', 'null') === '1' ? 'true' : (old('is_church_member', 'null') === '0' ? 'false' : 'null') }} }">
                        <label class="radio-card" :class="{ 'active': isMember === true }">
                            <input type="radio" name="is_church_member" value="1" @click="isMember = true" {{ old('is_church_member') == '1' ? 'checked' : '' }} required>
                            <span>Sim</span>
                        </label>
                        <label class="radio-card" :class="{ 'active': isMember === false }">
                            <input type="radio" name="is_church_member" value="0" @click="isMember = false" {{ old('is_church_member') == '0' ? 'checked' : '' }} required>
                            <span>Não</span>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label class="form-label">Zona (Se souber)</label>
                        <select name="zone_id" class="form-control">
                            <option value="">Selecione uma zona</option>
                            @foreach($zones as $zone)
                                <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                    {{ $zone->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nome da Célula</label>
                        <input type="text" name="cell_name" class="form-control" placeholder="Nome da sua célula"
                            value="{{ old('cell_name') }}">
                    </div>
                </div>

                @if($classes->count() > 0)
                    <div class="form-group">
                        <label class="form-label">Turma Preferencial</label>
                        <select name="course_class_id" class="form-control">
                            <option value="">Selecione uma turma</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ old('course_class_id') == $class->id ? 'selected' : '' }}>
                                    {{ $class->name }} ({{ $class->start_date?->format('d/m/Y') ?? 'A definir' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="form-group">
                    <label class="form-label">Observações Adicionais</label>
                    <textarea name="observations" class="form-control" rows="3"
                        placeholder="Algo mais que queira nos dizer?">{{ old('observations') }}</textarea>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="bi bi-check2-circle"></i> Confirmar Inscrição
                </button>
            </form>
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('welcome') }}"
                class="text-slate-500 hover:text-orange-500 transition-colors text-sm font-medium">
                <i class="bi bi-arrow-left"></i> Voltar ao Início
            </a>
        </div>
    </div>

    @if(session('success'))
        <script>
            Swal.fire({
                title: 'Sucesso!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonColor: '#f97316'
            });
        </script>
    @endif

    @if($errors->any())
        <script>
            Swal.fire({
                title: 'Erro!',
                text: "Por favor, verifique os campos do formulário.",
                icon: 'error',
                confirmButtonColor: '#f97316'
            });
        </script>
    @endif

@endsection