@extends('layouts.auth')

@section('title', 'Inscrição - Curso Pré-Marital')

@section('content')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;700;800&display=swap" rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════
           RESET & TOKENS (ULTRA LIGHT THEME)
        ═══════════════════════════════════════════ */
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            /* Life Church Light Palette */
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --bg-section: #ffffff;
            --bg-input: #f8fafc;
            --bg-hover: #f1f5f9;
            --bg-pill: #f1f5f9;
            --bg-pill-active: #ea580c;

            /* Vibrant Orange Accent */
            --orange: #ea580c;
            --orange-dim: #c2410c;
            --orange-glow: rgba(234, 88, 12, 0.15);
            --orange-pale: rgba(234, 88, 12, 0.08);

            /* Borders */
            --border: #e2e8f0;
            --border-focus: #ea580c;
            --border-hover: #cbd5e1;

            /* Text */
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --text-label: #475569;

            /* States */
            --error: #dc2626;
            --error-bg: #fef2f2;
            --error-border: #fecaca;
            --success: #059669;
            --success-bg: #ecfdf5;
            --success-border: #a7f3d0;

            /* Layout */
            --radius-sm: 10px;
            --radius: 16px;
            --radius-lg: 32px;
            --radius-pill: 9999px;
            --shadow-lg: 0 20px 45px -10px rgba(15, 23, 42, 0.08);

            /* Z-index stack */
            --z-base: 1;
            --z-raised: 5;
            --z-dropdown: 10;
            --z-overlay: 20;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-primary);
            background-color: #f8fafc !important;
            background-image: radial-gradient(#e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
            min-height: 100vh;
        }

        /* ═══════════════════════════════════════════
           PAGE LAYOUT & HEADER
        ═══════════════════════════════════════════ */
        .page-wrap {
            position: relative;
            z-index: var(--z-base);
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 20px 60px;
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            background: rgba(234, 88, 12, 0.08);
            border: 1px solid rgba(234, 88, 12, 0.2);
            border-radius: var(--radius-pill);
            color: var(--orange);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin-bottom: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeDown .55s ease both;
        }

        .brand-logo-img {
            max-width: 140px;
            height: auto;
            margin: 0 auto 20px;
            display: block;
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.4rem, 6.5vw, 3.6rem);
            font-weight: 400;
            color: var(--text-primary);
            line-height: 1.15;
            letter-spacing: -0.01em;
        }

        .page-title em {
            font-style: italic;
            color: var(--orange);
            font-weight: 600;
        }

        .page-sub {
            margin-top: 12px;
            font-size: 15px;
            color: var(--text-muted);
            font-weight: 500;
        }

        /* ═══════════════════════════════════════════
           FORM CARD
        ═══════════════════════════════════════════ */
        .form-card {
            position: relative;
            z-index: var(--z-base);
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: fadeUp .6s ease both .08s;
        }

        .form-section {
            padding: 32px 40px;
            border-bottom: 1px solid #f1f5f9;
        }

        .form-section:last-of-type {
            border-bottom: none;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 28px;
        }

        .section-icon {
            width: 46px;
            height: 46px;
            flex-shrink: 0;
            border-radius: 16px;
            background: var(--orange-pale);
            border: 1px solid rgba(234, 88, 12, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--orange);
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.1);
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .section-desc {
            font-size: 13px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* ═══════════════════════════════════════════
           FIELDS & INPUTS
        ═══════════════════════════════════════════ */
        .field-grid {
            display: grid;
            gap: 20px;
        }

        .col-2 {
            grid-template-columns: 1fr 1fr;
        }

        .col-3 {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .span-full {
            grid-column: 1 / -1;
        }

        @media (max-width: 640px) {
            .form-section {
                padding: 24px 20px;
            }

            .col-2, .col-3 {
                grid-template-columns: 1fr;
            }
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--text-label);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .field-label i {
            font-size: 13px;
            color: var(--orange);
        }

        .req {
            color: var(--orange);
            font-weight: 800;
        }

        .field-input,
        .field-select,
        .field-textarea {
            width: 100%;
            padding: 12px 16px;
            background: var(--bg-input);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 600;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
            transition: all .2s ease;
        }

        .field-input::placeholder,
        .field-textarea::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .field-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10'%3E%3Cpath d='M1 2l6 6 6-6' stroke='%2364748b' stroke-width='2' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 44px;
            cursor: pointer;
        }

        .field-select option {
            background: #ffffff !important;
            color: #0f172a !important;
            font-weight: 600;
        }

        .field-input:hover,
        .field-select:hover {
            border-color: var(--border-hover);
        }

        .field-input:focus,
        .field-select:focus,
        .field-textarea:focus {
            border-color: var(--orange);
            box-shadow: 0 0 0 4px var(--orange-glow);
            background: #ffffff;
        }

        .field-input.is-invalid,
        .field-select.is-invalid,
        .field-textarea.is-invalid {
            border-color: var(--error);
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        }

        .field-textarea {
            resize: vertical;
            min-height: 100px;
            line-height: 1.6;
        }

        .field-error {
            font-size: 12px;
            color: var(--error);
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .field-hint {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ═══════════════════════════════════════════
           RADIO PILLS (CHECKED STYLES)
        ═══════════════════════════════════════════ */
        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .radio-pill {
            position: relative;
        }

        .radio-pill input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
            pointer-events: none;
        }

        .radio-pill label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: var(--bg-pill);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-pill);
            color: var(--text-secondary);
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            user-select: none;
            transition: all .2s ease;
        }

        .radio-pill label i {
            font-size: 15px;
        }

        .radio-pill label:hover {
            border-color: var(--border-hover);
            color: var(--text-primary);
            background: var(--bg-hover);
        }

        .radio-pill input:checked + label {
            border-color: var(--orange);
            background: var(--orange);
            color: #ffffff;
            box-shadow: 0 8px 20px -4px rgba(234, 88, 12, 0.4);
        }

        .radio-pill input:checked + label i {
            color: #ffffff;
        }

        /* ═══════════════════════════════════════════
           CONDITIONAL PANELS
        ═══════════════════════════════════════════ */
        .cond-block {
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            pointer-events: none;
            transition: max-height .35s ease, opacity .25s ease, margin-top .25s ease;
            margin-top: 0;
        }

        .cond-block.open {
            max-height: 2000px;
            opacity: 1;
            pointer-events: auto;
            margin-top: 20px;
        }

        .cond-panel {
            background: #f8fafc;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .cond-panel-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            color: var(--orange);
            font-size: 11.5px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            background: rgba(234, 88, 12, 0.04);
        }

        .cond-panel-body {
            padding: 24px;
        }

        .address-grid {
            display: grid;
            gap: 16px;
        }

        .address-grid.two-cols {
            grid-template-columns: 1fr 1fr;
        }

        @media (max-width: 640px) {
            .address-grid.two-cols {
                grid-template-columns: 1fr;
            }
        }

        .address-person {
            background: #ffffff;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            overflow: hidden;
        }

        .address-person-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: rgba(234, 88, 12, 0.05);
            border-bottom: 1px solid var(--border);
            font-size: 11.5px;
            font-weight: 800;
            color: var(--orange);
            letter-spacing: .07em;
            text-transform: uppercase;
        }

        .address-person-body {
            padding: 18px;
        }

        /* ═══════════════════════════════════════════
           FOOTER BUTTONS
        ═══════════════════════════════════════════ */
        .form-footer {
            padding: 32px 40px;
            background: #f8fafc;
            border-top: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            max-width: 360px;
            padding: 16px 40px;
            background: var(--orange);
            color: #ffffff;
            border: none;
            border-radius: var(--radius-pill);
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all .25s ease;
            box-shadow: 0 12px 30px -5px rgba(234, 88, 12, 0.4);
        }

        .btn-submit:hover {
            background: #c2410c;
            box-shadow: 0 16px 36px -5px rgba(234, 88, 12, 0.5);
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: color .18s;
        }

        .btn-back:hover {
            color: var(--text-primary);
        }

        @keyframes fadeDown {
            from { opacity: 0; transform: translateY(-16px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>

    <div class="page-wrap">

        {{-- ══ HEADER ══ --}}
        <header class="page-header">
            <img src="{{ asset('images/logo.png') }}" alt="Life Church" class="brand-logo-img" onerror="this.style.display='none'">
            <div class="header-badge">
                <i class="bi bi-gem"></i>
                Portal Life Church
            </div>
            <h1 class="page-title">{!! str_replace(' & ', '<br><em>& ', $course->name) !!}</h1>
            <p class="page-sub"><i class="bi bi-pencil-square"></i>&nbsp; Formulário de Inscrição Online</p>
        </header>

        {{-- ══ SUCCESS CONFIRMATION RECEIPT ══ --}}
        @if(session('success'))
            @php
                $createdEnrollment = session('enrollment_success') ? \App\Models\CoupleEnrollment::find(session('enrollment_success')) : null;
                $husband = $createdEnrollment->husband_name ?? '';
                $wife = $createdEnrollment->wife_name ?? '';
                $phone = $createdEnrollment->husband_phone ?? $createdEnrollment->wife_phone ?? '';
                $id = $createdEnrollment->id ?? '';
                $rawPhone = preg_replace('/\D/', '', $phone);
                $waText = urlencode("Graça e Paz! Confirmamos a inscrição do casal {$husband} & {$wife} no curso {$course->name} (Ref: #" . sprintf('%04d', $id) . "). Portal Life Church.");
                $waUrl = $rawPhone ? "https://api.whatsapp.com/send?phone={$rawPhone}&text={$waText}" : "https://api.whatsapp.com/send?text={$waText}";
            @endphp

            <div id="swal-success"
                data-message="{{ session('success') }}"
                data-husband="{{ $husband }}"
                data-wife="{{ $wife }}"
                data-phone="{{ $phone }}"
                data-id="{{ $id }}"
                data-course="{{ $course->name }}"
                style="display:none;"></div>

            <div style="background: #ffffff; border: 2px solid #10b981; border-radius: 28px; padding: 36px 28px; text-align: center; box-shadow: 0 20px 40px -10px rgba(16, 185, 129, 0.12); margin-bottom: 36px; animation: fadeUp 0.5s ease;">
                <div style="width: 70px; height: 70px; background: #ecfdf5; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; color: #10b981; font-size: 34px;">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <span style="background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; padding: 5px 16px; border-radius: 99px;">
                    Comprovativo Nº #{{ sprintf('%04d', $id) }}
                </span>
                <h2 style="font-size: 24px; font-weight: 800; color: #0f172a; margin: 14px 0 6px;">Inscrição Registada com Sucesso!</h2>
                <p style="font-size: 17px; font-weight: 800; color: #ea580c; margin-bottom: 10px;">{{ $husband ? $husband . ' & ' . $wife : 'Casal Registado' }}</p>
                <p style="font-size: 14px; color: #475569; max-width: 500px; margin: 0 auto 26px; line-height: 1.6;">
                    A vossa inscrição no curso <strong>{{ $course->name }}</strong> foi recebida com sucesso. Entraremos em contacto em breve para o Encontro de Orientação.
                </p>
                <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 14px;">
                    <a href="{{ $waUrl }}" target="_blank" style="display: inline-flex; align-items: center; gap: 8px; background: #25D366; color: #ffffff; font-size: 13.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 28px; border-radius: 16px; text-decoration: none; box-shadow: 0 8px 22px -4px rgba(37, 211, 102, 0.4);">
                        <i class="bi bi-whatsapp" style="font-size: 18px;"></i> Notificar por WhatsApp
                    </a>
                    <button onclick="window.print()" style="display: inline-flex; align-items: center; gap: 8px; background: #0f172a; color: #ffffff; font-size: 13.5px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; padding: 14px 28px; border-radius: 16px; border: none; cursor: pointer; box-shadow: 0 8px 22px -4px rgba(15, 23, 42, 0.2);">
                        <i class="bi bi-printer-fill" style="font-size: 18px;"></i> Imprimir Comprovativo
                    </button>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div id="swal-error" data-errors='@json($errors->all())' style="display:none;"></div>
        @endif

        {{-- ══ FORM CARD ══ --}}
        <div class="form-card">
            <form method="POST" action="{{ route('public.forms.pre-marital.store') }}" novalidate>
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                {{-- 1 · IDENTIFICAÇÃO DO CASAL --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="bi bi-people-fill"></i></div>
                        <div>
                            <div class="section-title">Identificação do Casal</div>
                            <div class="section-desc">Dados gerais sobre a vossa relação</div>
                        </div>
                    </div>

                    <div class="field-grid col-2">

                        <div class="field span-full">
                            <label class="field-label" for="couple_name">
                                <i class="bi bi-heart-fill"></i> Nome do Casal <span class="req">*</span>
                            </label>
                            <input id="couple_name" name="couple_name" type="text"
                                class="field-input @error('couple_name') is-invalid @enderror"
                                value="{{ old('couple_name') }}" placeholder="Ex: João &amp; Maria Silva" required>
                            @error('couple_name')
                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label class="field-label" for="relationship_type">
                                <i class="bi bi-diagram-3"></i> Tipo de Relação <span class="req">*</span>
                            </label>
                            <select id="relationship_type" name="relationship_type"
                                class="field-select @error('relationship_type') is-invalid @enderror" required>
                                <option value="" disabled {{ old('relationship_type') ? '' : 'selected' }}>Selecione...</option>
                                <option value="em_relacionamento" {{ old('relationship_type') == 'em_relacionamento' || old('relationship_type') == 'namoro' ? 'selected' : '' }}>
                                    Em relacionamento</option>
                                <option value="noivos" {{ old('relationship_type') == 'noivos' ? 'selected' : '' }}>Noivos</option>
                                <option value="vivendo_maritalmente" {{ old('relationship_type') == 'vivendo_maritalmente' ? 'selected' : '' }}>Vivendo Maritalmente</option>
                                <option value="casados" {{ old('relationship_type') == 'casados' ? 'selected' : '' }}>Casados</option>
                            </select>
                            @error('relationship_type')
                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                        <div class="field">
                            <label class="field-label" for="years_together_text">
                                <i class="bi bi-hourglass-split"></i> Tempo de Relacionamento
                            </label>
                            <input id="years_together_text" name="years_together_text" type="text"
                                class="field-input @error('years_together_text') is-invalid @enderror"
                                value="{{ old('years_together_text') }}" placeholder="Ex: 2 anos e 3 meses">
                            @error('years_together_text')
                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>

                    </div>

                    {{-- Morada única (coabitando/casados) --}}
                    <div class="cond-block" id="addr-single">
                        <div class="cond-panel">
                            <div class="cond-panel-header">
                                <i class="bi bi-house-door-fill"></i> Morada do Casal
                            </div>
                            <div class="cond-panel-body">
                                <div class="field-grid col-2">
                                    <div class="field span-full">
                                        <label class="field-label" for="address">
                                            <i class="bi bi-geo-alt-fill"></i> Endereço
                                        </label>
                                        <input id="address" name="address" type="text"
                                            class="field-input @error('address') is-invalid @enderror"
                                            value="{{ old('address') }}" placeholder="Rua, Bairro, Cidade">
                                        @error('address')
                                            <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="field">
                                        <label class="field-label" for="city"><i class="bi bi-building"></i> Cidade</label>
                                        <input id="city" name="city" type="text"
                                            class="field-input @error('city') is-invalid @enderror"
                                            value="{{ old('city') }}" placeholder="Maputo">
                                        @error('city')
                                            <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="field">
                                        <label class="field-label" for="province"><i class="bi bi-map"></i> Província</label>
                                        <input id="province" name="province" type="text"
                                            class="field-input @error('province') is-invalid @enderror"
                                            value="{{ old('province') }}" placeholder="Maputo">
                                        @error('province')
                                            <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Morada separada (namorados/noivos) --}}
                    <div class="cond-block" id="addr-dual">
                        <div class="address-grid two-cols">
                            <div class="address-person">
                                <div class="address-person-header">
                                    <i class="bi bi-person-fill"></i> Morada do Parceiro
                                </div>
                                <div class="address-person-body">
                                    <div class="field-grid">
                                        <div class="field">
                                            <label class="field-label" for="husband_address"><i class="bi bi-geo-alt"></i> Endereço</label>
                                            <input id="husband_address" name="address" type="text"
                                                class="field-input @error('address') is-invalid @enderror"
                                                value="{{ old('address') }}" placeholder="Rua, Bairro">
                                            @error('address')
                                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="field">
                                            <label class="field-label" for="husband_city"><i class="bi bi-building"></i> Cidade</label>
                                            <input id="husband_city" name="husband_city" type="text"
                                                class="field-input @error('husband_city') is-invalid @enderror"
                                                value="{{ old('husband_city') }}" placeholder="Maputo">
                                            @error('husband_city')
                                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="address-person">
                                <div class="address-person-header">
                                    <i class="bi bi-person-fill"></i> Morada da Parceira
                                </div>
                                <div class="address-person-body">
                                    <div class="field-grid">
                                        <div class="field">
                                            <label class="field-label" for="wife_address"><i class="bi bi-geo-alt"></i> Endereço</label>
                                            <input id="wife_address" name="wife_address" type="text"
                                                class="field-input @error('wife_address') is-invalid @enderror"
                                                value="{{ old('wife_address') }}" placeholder="Rua, Bairro">
                                            @error('wife_address')
                                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="field">
                                            <label class="field-label" for="wife_city"><i class="bi bi-building"></i> Cidade</label>
                                            <input id="wife_city" name="wife_city" type="text"
                                                class="field-input @error('wife_city') is-invalid @enderror"
                                                value="{{ old('wife_city') }}" placeholder="Maputo">
                                            @error('wife_city')
                                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- 2 · CONTACTOS --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="section-title">Contactos Telefónicos</div>
                            <div class="section-desc">Pelo menos um contacto é obrigatório</div>
                        </div>
                    </div>
                    <div class="field-grid col-2">
                        <div class="field">
                            <label class="field-label" for="husband_phone">
                                <i class="bi bi-phone"></i> Contacto do Parceiro
                            </label>
                            <input id="husband_phone" name="husband_phone" type="tel"
                                class="field-input @error('husband_phone') is-invalid @enderror"
                                value="{{ old('husband_phone') }}" placeholder="+258 8X XXX XXXX">
                            @error('husband_phone')
                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                        <div class="field">
                            <label class="field-label" for="wife_phone">
                                <i class="bi bi-phone"></i> Contacto da Parceira
                            </label>
                            <input id="wife_phone" name="wife_phone" type="tel"
                                class="field-input @error('wife_phone') is-invalid @enderror"
                                value="{{ old('wife_phone') }}" placeholder="+258 8X XXX XXXX">
                            @error('wife_phone')
                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <p class="field-hint" style="margin-top:10px;">
                        <i class="bi bi-info-circle"></i>
                        Preencha pelo menos um dos contactos para podermos enviar os detalhes do curso.
                    </p>
                </div>

                {{-- 3 · VÍNCULO À IGREJA --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="bi bi-building-fill"></i></div>
                        <div>
                            <div class="section-title">Vínculo à Igreja</div>
                            <div class="section-desc">Relação com a comunidade Life Church</div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field-label">
                            <i class="bi bi-person-check-fill"></i> São membros da Igreja? <span class="req">*</span>
                        </label>
                        <div class="radio-group">
                            <div class="radio-pill">
                                <input type="radio" id="member_both" name="is_church_member" value="both" {{ old('is_church_member') == 'both' ? 'checked' : '' }}>
                                <label for="member_both">
                                    <i class="bi bi-check-circle-fill"></i> Sim, ambos somos membros
                                </label>
                            </div>
                            <div class="radio-pill">
                                <input type="radio" id="member_one" name="is_church_member" value="one" {{ old('is_church_member') == 'one' ? 'checked' : '' }}>
                                <label for="member_one">
                                    <i class="bi bi-person-check"></i> 1 de nós é membro
                                </label>
                            </div>
                            <div class="radio-pill">
                                <input type="radio" id="member_none" name="is_church_member" value="none" {{ old('is_church_member') == 'none' ? 'checked' : '' }}>
                                <label for="member_none">
                                    <i class="bi bi-x-circle-fill"></i> Nenhum é membro
                                </label>
                            </div>
                        </div>
                        @error('is_church_member')
                            <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Condicional: info de membro --}}
                    <div class="cond-block" id="member-info">
                        <div class="cond-panel">
                            <div class="cond-panel-header">
                                <i class="bi bi-layers-fill"></i> Informações de Membro · Igreja Edificar
                            </div>
                            <div class="cond-panel-body">
                                <div class="field-grid">

                                    <div class="field-grid col-2">
                                        <div class="field">
                                            <label class="field-label" for="zone_id">
                                                <i class="bi bi-pin-map-fill"></i> Zona de Pertença
                                            </label>
                                            <select id="zone_id" name="zone_id"
                                                class="field-select @error('zone_id') is-invalid @enderror">
                                                <option value="">Selecione a Zona</option>
                                                @foreach($zones as $zone)
                                                    <option value="{{ $zone->id }}" {{ old('zone_id') == $zone->id ? 'selected' : '' }}>
                                                        {{ $zone->name }}
                                                    </option>
                                                @endforeach
                                                <option value="other" {{ old('zone_id') == 'other' ? 'selected' : '' }}>
                                                    Outra / Não listada
                                                </option>
                                            </select>
                                            @error('zone_id')
                                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="field" id="zone-other-field" style="display:none;">
                                            <label class="field-label" for="cell_zone">
                                                <i class="bi bi-pencil-fill"></i> Especifique a Zona
                                            </label>
                                            <input id="cell_zone" name="cell_zone" type="text"
                                                class="field-input @error('cell_zone') is-invalid @enderror"
                                                value="{{ old('cell_zone') }}" placeholder="Nome da zona de célula"
                                                tabindex="-1">
                                            @error('cell_zone')
                                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="field">
                                        <label class="field-label" for="leader_name">
                                            <i class="bi bi-person-badge-fill"></i> Líder de Célula / Supervisor
                                        </label>
                                        <input id="leader_name" name="leader_name" type="text"
                                            class="field-input @error('leader_name') is-invalid @enderror"
                                            value="{{ old('leader_name') }}" placeholder="Nome do líder ou supervisor">
                                        @error('leader_name')
                                            <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                        @enderror
                                    </div>

                                    @if(isset($classes) && is_object($classes) && $classes->isNotEmpty())
                                        <div class="field">
                                            <label class="field-label" for="course_class_id">
                                                <i class="bi bi-calendar3-fill"></i> Turma para frequência
                                                <span style="font-weight:400;text-transform:none;letter-spacing:0;color:var(--text-muted);">(opcional)</span>
                                            </label>
                                            <select id="course_class_id" name="course_class_id"
                                                class="field-select @error('course_class_id') is-invalid @enderror">
                                                <option value="">Selecione a Turma</option>
                                                @foreach($classes as $year => $yearClasses)
                                                    @if(is_object($yearClasses) || is_array($yearClasses))
                                                        <optgroup label="Turmas de {{ $year }}">
                                                            @foreach($yearClasses as $class)
                                                                <option value="{{ $class->id }}" {{ old('course_class_id') == $class->id ? 'selected' : '' }}>
                                                                    {{ $class->name }}
                                                                </option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('course_class_id')
                                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endif

                                    <div class="field">
                                        <label class="field-label">
                                            <i class="bi bi-patch-check-fill"></i>
                                            Tem Recomendação Pastoral ou da Supervisão? <span class="req">*</span>
                                        </label>
                                        <div class="radio-group">
                                            <div class="radio-pill">
                                                <input type="radio" id="rec_yes" name="has_pastoral_recommendation"
                                                    value="1" {{ old('has_pastoral_recommendation') == '1' ? 'checked' : '' }}>
                                                <label for="rec_yes">
                                                    <i class="bi bi-check-circle-fill"></i> Sim, possuo
                                                </label>
                                            </div>
                                            <div class="radio-pill">
                                                <input type="radio" id="rec_no" name="has_pastoral_recommendation" value="0"
                                                    {{ old('has_pastoral_recommendation') == '0' ? 'checked' : '' }}>
                                                <label for="rec_no">
                                                    <i class="bi bi-x-circle-fill"></i> Não possuo
                                                </label>
                                            </div>
                                        </div>
                                        @error('has_pastoral_recommendation')
                                            <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- 4 · OBSERVAÇÕES --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="bi bi-chat-text-fill"></i></div>
                        <div>
                            <div class="section-title">Observações Finais</div>
                            <div class="section-desc">Informação adicional que queiram partilhar</div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="field-label" for="observations">
                            <i class="bi bi-pencil-square"></i> Mensagem (opcional)
                        </label>
                        <textarea id="observations" name="observations" rows="4"
                            class="field-textarea @error('observations') is-invalid @enderror"
                            placeholder="Partilhe qualquer informação adicional relevante para a vossa inscrição..."></textarea>
                        @error('observations')
                            <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- FOOTER / SUBMIT --}}
                <div class="form-footer">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-send-check-fill"></i>
                        Finalizar Inscrição
                    </button>
                    <a href="{{ url('/') }}" class="btn-back">
                        <i class="bi bi-arrow-left"></i>
                        Voltar para o Início
                    </a>
                </div>

            </form>
        </div>
    </div>

    <script>
        (function () {
            'use strict';

            function openBlock(el) {
                if (!el) return;
                el.classList.add('open');
                el.querySelectorAll('input, select, textarea').forEach(function (f) {
                    f.removeAttribute('tabindex');
                    f.removeAttribute('disabled');
                });
            }

            function closeBlock(el) {
                if (!el) return;
                el.classList.remove('open');
                el.querySelectorAll('input, select, textarea').forEach(function (f) {
                    f.setAttribute('tabindex', '-1');
                    f.setAttribute('disabled', 'disabled');
                });
            }

            var relSelect = document.getElementById('relationship_type');
            var addrSingle = document.getElementById('addr-single');
            var addrDual = document.getElementById('addr-dual');

            function applyAddressLogic() {
                var v = relSelect ? relSelect.value : '';
                if (v === 'vivendo_maritalmente' || v === 'casados') {
                    openBlock(addrSingle);
                    closeBlock(addrDual);
                } else if (v === 'namoro' || v === 'noivos' || v === 'em_relacionamento') {
                    closeBlock(addrSingle);
                    openBlock(addrDual);
                } else {
                    closeBlock(addrSingle);
                    closeBlock(addrDual);
                }
            }

            if (relSelect) {
                relSelect.addEventListener('change', applyAddressLogic);
            }
            applyAddressLogic();

            var memberInfo = document.getElementById('member-info');

            function applyMemberLogic() {
                var checked = document.querySelector('[name="is_church_member"]:checked');
                var val = checked ? checked.value : '';
                if (val === 'both' || val === 'one' || val === '1') {
                    openBlock(memberInfo);
                } else {
                    closeBlock(memberInfo);
                }
            }

            document.querySelectorAll('[name="is_church_member"]').forEach(function (r) {
                r.addEventListener('change', applyMemberLogic);
            });
            applyMemberLogic();

            var zoneSelect = document.getElementById('zone_id');
            var zoneOtherField = document.getElementById('zone-other-field');

            function applyZoneLogic() {
                if (!zoneSelect || !zoneOtherField) return;
                var isOther = zoneSelect.value === 'other';
                zoneOtherField.style.display = isOther ? '' : 'none';
                var inp = zoneOtherField.querySelector('input');
                if (inp) {
                    isOther ? inp.removeAttribute('tabindex') : inp.setAttribute('tabindex', '-1');
                }
            }

            if (zoneSelect) {
                zoneSelect.addEventListener('change', applyZoneLogic);
            }
            applyZoneLogic();

            const swalConfig = {
                background: '#ffffff',
                color: '#0f172a',
                confirmButtonColor: '#ea580c',
                customClass: {
                    popup: 'light-swal-popup',
                    confirmButton: 'light-swal-button'
                }
            };

            const successEl = document.getElementById('swal-success');
            if (successEl && typeof Swal !== 'undefined') {
                const husband = successEl.dataset.husband;
                const wife = successEl.dataset.wife;
                const phone = successEl.dataset.phone;
                const id = successEl.dataset.id;
                const course = successEl.dataset.course;

                let htmlContent = `
                    <div style="text-align: center; padding: 10px 0;">
                        <div style="background: #ecfdf5; border: 1px solid #a7f3d0; border-radius: 16px; padding: 16px; margin-bottom: 16px;">
                            <div style="color: #059669; font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 4px;">Comprovativo Nº #${id ? String(id).padStart(4, '0') : 'REGISTADO'}</div>
                            <div style="color: #0f172a; font-size: 15px; font-weight: 800;">${husband ? husband + ' & ' + wife : 'Inscrição Confirmada'}</div>
                            <div style="color: #ea580c; font-size: 12px; font-weight: 700; margin-top: 4px;">${course}</div>
                        </div>
                        <p style="color: #64748b; font-size: 12.5px; line-height: 1.5; margin-bottom: 8px;">A vossa inscrição foi efetuada com sucesso! Guardem o comprovativo ou enviem a confirmação para a secretaria pelo WhatsApp.</p>
                    </div>
                `;

                let rawPhone = phone ? phone.replace(/\D/g, '') : '';
                let waText = encodeURIComponent(`Graça e Paz! Confirmamos a inscrição do casal ${husband} & ${wife} no curso ${course} (Ref #${id ? String(id).padStart(4, '0') : ''}). Portal Life Church.`);
                let waUrl = rawPhone ? `https://api.whatsapp.com/send?phone=${rawPhone}&text=${waText}` : `https://api.whatsapp.com/send?text=${waText}`;

                Swal.fire({
                    ...swalConfig,
                    icon: 'success',
                    iconColor: '#10b981',
                    title: 'Inscrição Efetuada com Sucesso!',
                    html: htmlContent,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-whatsapp" style="margin-right: 6px;"></i> Notificar por WhatsApp',
                    cancelButtonText: '<i class="bi bi-printer" style="margin-right: 6px;"></i> Imprimir Comprovativo',
                    confirmButtonColor: '#25D366',
                    cancelButtonColor: '#0f172a',
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.open(waUrl, '_blank');
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        window.print();
                    }
                });
            }

            const errorEl = document.getElementById('swal-error');
            if (errorEl) {
                const errors = JSON.parse(errorEl.dataset.errors);
                Swal.fire({
                    ...swalConfig,
                    icon: 'error',
                    iconColor: '#ef4444',
                    title: 'Atenção!',
                    html: `<div style="text-align: left; font-size: 0.9em; margin-top: 10px;">
                            <ul style="list-style-type: none; padding: 0;">
                                ${errors.map(err => `<li style="margin-bottom: 5px;"><i class="bi bi-x-circle-fill" style="color: #ef4444; margin-right: 8px;"></i>${err}</li>`).join('')}
                            </ul>
                           </div>`,
                });
            }
        })();
    </script>
@endsection
