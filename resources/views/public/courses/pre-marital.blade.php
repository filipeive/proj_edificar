@extends('layouts.auth')

@section('title', 'Inscrição - Curso Pré-Marital')

@section('content')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500&display=swap"
        rel="stylesheet">

    <style>
        /* ═══════════════════════════════════════════
           RESET & TOKENS
        ═══════════════════════════════════════════ */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            /* Life Church Dark Palette */
            --bg-page: #020617;
            /* Darker Slate */
            --bg-card: rgba(30, 41, 59, 0.7);
            /* Translucent Slate-800 */
            --bg-section: rgba(15, 23, 42, 0.6);
            /* Translucent Slate-900 */
            --bg-input: #0f172a;
            --bg-hover: #1e293b;
            --bg-pill: #1e293b;
            --bg-pill-active: #334155;

            /* Vibrant Orange Accent */
            --orange: #f97316;
            --orange-dim: #c2410c;
            --orange-glow: rgba(249, 115, 22, 0.15);
            --orange-pale: rgba(249, 115, 22, 0.05);

            /* Borders */
            --border: rgba(255, 255, 255, 0.1);
            --border-focus: #f97316;
            --border-hover: rgba(255, 255, 255, 0.2);

            /* Text */
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --text-label: #94a3b8;

            /* States */
            --error: #ef4444;
            --error-bg: rgba(239, 68, 68, 0.1);
            --error-border: rgba(239, 68, 68, 0.2);
            --success: #10b981;
            --success-bg: rgba(16, 185, 129, 0.1);
            --success-border: rgba(16, 185, 129, 0.2);

            /* Layout */
            --radius-sm: 6px;
            --radius: 10px;
            --radius-lg: 16px;
            --radius-pill: 9999px;
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.5);

            /* Z-index stack */
            --z-base: 1;
            --z-raised: 5;
            --z-dropdown: 10;
            --z-overlay: 20;
        }

        /* ── Custom Scrollbar Mastery ── */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-page);
        }

        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--orange);
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: var(--text-primary);
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #020617 100%);
            min-height: 100vh;
            overflow-y: auto;
            /* Ensure scroll is allowed */
        }

        /* ═══════════════════════════════════════════
           PAGE LAYOUT
        ═══════════════════════════════════════════ */
        .page-wrap {
            position: relative;
            z-index: var(--z-base);
            max-width: 760px;
            margin: 0 auto;
            padding: 40px 16px 40px; /* Further reduced bottom padding */
        }

        .form-logo {
            display: block;
            max-width: 120px;
            height: auto;
            margin: 0 auto 30px;
            filter: drop-shadow(0 0 15px rgba(249, 115, 22, 0.3));
            animation: fadeIn 0.8s ease both;
        }

        /* ═══════════════════════════════════════════
           HEADER
        ═══════════════════════════════════════════ */
        .page-header {
            text-align: center;
            margin-bottom: 44px;
            animation: fadeDown .55s ease both;
        }

        .church-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 10.5px;
            font-weight: 500;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--orange);
            margin-bottom: 18px;
        }

        .church-eyebrow::before,
        .church-eyebrow::after {
            content: '';
            display: block;
            width: 32px;
            height: 1px;
            background: linear-gradient(to right, transparent, var(--orange-dim));
        }

        .church-eyebrow::after {
            background: linear-gradient(to left, transparent, var(--orange-dim));
        }

        .page-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: clamp(2.2rem, 6.5vw, 3.4rem);
            font-weight: 300;
            color: var(--text-primary);
            line-height: 1.1;
            letter-spacing: -0.01em;
        }

        .page-title em {
            font-style: italic;
            color: var(--orange);
            font-weight: 400;
        }

        .page-sub {
            margin-top: 12px;
            font-size: 14px;
            color: var(--text-secondary);
            letter-spacing: .02em;
        }

        /* ═══════════════════════════════════════════
           ALERTS
        ═══════════════════════════════════════════ */
        .alert {
            position: relative;
            z-index: var(--z-raised);
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 18px;
            border-radius: var(--radius);
            border: 1px solid;
            margin-bottom: 28px;
            font-size: 13.5px;
            animation: fadeIn .35s ease both;
        }

        .alert-success {
            background: var(--success-bg);
            border-color: var(--success-border);
            color: var(--success);
        }

        .alert-error {
            background: var(--error-bg);
            border-color: var(--error-border);
            color: var(--error);
        }

        .alert i {
            font-size: 17px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .alert ul {
            list-style: none;
            padding: 0;
        }

        .alert ul li+li {
            margin-top: 3px;
        }

        .alert ul li::before {
            content: '· ';
            font-weight: 700;
        }

        /* ═══════════════════════════════════════════
           FORM CARD
        ═══════════════════════════════════════════ */
        .form-card {
            position: relative;
            z-index: var(--z-base);
            background: var(--bg-card);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: visible;
            animation: fadeUp .6s ease both .08s;
        }

        /* ═══════════════════════════════════════════
           FORM SECTIONS
        ═══════════════════════════════════════════ */
        .form-section {
            position: relative;
            z-index: var(--z-base);
            padding: 30px 36px;
            border-bottom: 1px solid var(--border);
        }

        .form-section:first-child {
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        .form-section:last-of-type {
            border-bottom: none;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 26px;
        }

        .section-icon {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 12px;
            background: var(--orange-pale);
            border: 1px solid var(--orange-dim);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--orange);
            font-size: 18px;
        }

        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        .section-desc {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 1px;
        }

        /* ═══════════════════════════════════════════
           GRID
        ═══════════════════════════════════════════ */
        .field-grid {
            display: grid;
            gap: 18px;
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

        @media (max-width: 600px) {
            .form-section {
                padding: 22px 18px;
            }

            .col-2,
            .col-3 {
                grid-template-columns: 1fr;
            }
        }

        /* ═══════════════════════════════════════════
           FIELDS
        ═══════════════════════════════════════════ */
        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .field-label {
            font-size: 11px;
            font-weight: 500;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--text-label);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .field-label i {
            font-size: 13px;
            color: var(--orange-dim);
        }

        .req {
            color: var(--orange);
        }

        .field-input,
        .field-select,
        .field-textarea {
            position: relative;
            z-index: var(--z-base);
            width: 100%;
            padding: 10px 14px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            outline: none;
            -webkit-appearance: none;
            appearance: none;
            transition: border-color .18s, box-shadow .18s, background .18s;
        }

        .field-input::placeholder,
        .field-textarea::placeholder {
            color: var(--text-muted);
        }

        .field-select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%238A8070' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 13px center;
            padding-right: 36px;
            cursor: pointer;
        }

        .field-select option {
            background: #1E1B17;
            color: var(--text-primary);
        }

        .field-input:hover,
        .field-select:hover {
            border-color: var(--border-hover);
        }

        .field-input:focus,
        .field-select:focus,
        .field-textarea:focus {
            border-color: var(--border-focus);
            box-shadow: 0 0 0 3px var(--orange-glow);
            background: var(--bg-hover);
            z-index: var(--z-dropdown);
        }

        .field-input.is-invalid,
        .field-select.is-invalid,
        .field-textarea.is-invalid {
            border-color: var(--error-border);
            box-shadow: 0 0 0 3px rgba(224, 80, 80, .1);
        }

        .field-textarea {
            resize: vertical;
            min-height: 88px;
            line-height: 1.6;
        }

        .field-error {
            font-size: 12px;
            color: var(--error);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .field-hint {
            font-size: 12px;
            color: var(--text-muted);
            font-style: italic;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* ═══════════════════════════════════════════
           RADIO PILLS
        ═══════════════════════════════════════════ */
        .radio-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
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
            gap: 7px;
            padding: 9px 18px;
            background: var(--bg-pill);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-pill);
            color: var(--text-secondary);
            font-size: 13.5px;
            cursor: pointer;
            user-select: none;
            transition: all .18s;
        }

        .radio-pill label i {
            font-size: 15px;
        }

        .radio-pill label:hover {
            border-color: var(--border-hover);
            color: var(--text-primary);
            background: var(--bg-hover);
        }

        .radio-pill input:checked+label {
            border-color: var(--orange);
            background: var(--orange-pale);
            color: var(--orange);
            font-weight: 600;
            box-shadow: 0 0 15px var(--orange-glow);
        }

        /* ═══════════════════════════════════════════
           CONDITIONAL BLOCKS
           Uses max-height transition for smooth animation.
           z-index is set ABOVE siblings so dropdowns
           inside open panels are never clipped.
        ═══════════════════════════════════════════ */
        .cond-block {
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            pointer-events: none;
            transition: max-height .38s ease, opacity .25s ease, margin-top .25s ease;
            margin-top: 0;
            position: relative;
            z-index: var(--z-base);
        }

        .cond-block.open {
            max-height: 2000px;
            /* large enough for any content */
            opacity: 1;
            pointer-events: auto;
            margin-top: 20px;
            z-index: var(--z-raised);
            /* lift open panel above siblings */
        }

        .cond-panel {
            background: var(--bg-section);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: visible;
            /* crucial: do NOT clip children */
        }

        .cond-panel-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            border-bottom: 1px solid var(--border);
            color: var(--orange);
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            border-radius: var(--radius) var(--radius) 0 0;
            background: rgba(249, 115, 22, 0.03);
        }

        .cond-panel-header i {
            font-size: 15px;
        }

        .cond-panel-body {
            padding: 22px;
        }

        /* Address dual panel */
        .address-grid {
            display: grid;
            gap: 16px;
        }

        .address-grid.two-cols {
            grid-template-columns: 1fr 1fr;
        }

        @media (max-width: 600px) {
            .address-grid.two-cols {
                grid-template-columns: 1fr;
            }
        }

        .address-person {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            overflow: visible;
        }

        .address-person-header {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            background: rgba(249, 115, 22, .05);
            border-bottom: 1px solid var(--border);
            font-size: 11.5px;
            font-weight: 700;
            color: var(--orange);
            letter-spacing: .07em;
            text-transform: uppercase;
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .address-person-body {
            padding: 16px;
        }

        /* ═══════════════════════════════════════════
           FORM FOOTER
        ═══════════════════════════════════════════ */
        .form-footer {
            position: relative;
            z-index: var(--z-base);
            padding: 26px 36px;
            background: var(--bg-section);
            border-top: 1px solid var(--border);
            border-radius: 0 0 var(--radius-lg) var(--radius-lg);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
        }

        @media (max-width: 600px) {
            .form-footer {
                padding: 22px 18px;
            }
        }

        .btn-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            width: 100%;
            max-width: 320px;
            padding: 14px 40px;
            background: var(--orange);
            color: #fff;
            border: none;
            border-radius: var(--radius-pill);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
            cursor: pointer;
            transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 25px -5px rgba(249, 115, 22, 0.4);
        }

        .btn-submit i {
            font-size: 16px;
        }

        .btn-submit:hover {
            background: #ea580c;
            box-shadow: 0 15px 35px -5px rgba(249, 115, 22, 0.5);
            transform: translateY(-2px);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--text-muted);
            text-decoration: none;
            transition: color .18s;
        }

        .btn-back i {
            font-size: 14px;
            transition: transform .18s;
        }

        .btn-back:hover {
            color: var(--text-secondary);
        }

        .btn-back:hover i {
            transform: translateX(-3px);
        }

        /* ═══════════════════════════════════════════
           ANIMATIONS
        ═══════════════════════════════════════════ */
        @keyframes fadeDown {
            from {
                opacity: 0;
                transform: translateY(-16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>

    <div class="page-wrap">

        {{-- ══ HEADER ══ --}}
        <header class="page-header">
            <div class="church-eyebrow">
                <i class="bi bi-gem"></i>
                Portal Life Church
            </div>
            <h1 class="page-title">{!! str_replace(' & ', '<br><em>& ', $course->name) !!}</h1>
            <p class="page-sub"><i class="bi bi-pencil-square"></i>&nbsp; Formulário de Inscrição</p>
        </header>

        {{-- ══ ALERTS (Hidden, handled by SweetAlert) ══ --}}
        @if(session('success'))
            <div id="swal-success" data-message="{{ session('success') }}" style="display:none;"></div>
        @endif

        @if ($errors->any())
            <div id="swal-error" data-errors='@json($errors->all())' style="display:none;"></div>
        @endif

        {{-- ══ FORM CARD ══ --}}
        <div class="form-card">
            <img src="{{ asset('images/logo-white-orange.png') }}" alt="Life Church" class="form-logo">
            <form method="POST" action="{{ route('public.forms.pre-marital.store') }}" novalidate>
                @csrf
                <input type="hidden" name="course_id" value="{{ $course->id }}">

                {{-- ╔══════════════════════════════════
                ║ 1 · IDENTIFICAÇÃO DO CASAL
                ╚══════════════════════════════════ --}}
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
                                <option value="" disabled {{ old('relationship_type') ? '' : 'selected' }}>Selecione...
                                </option>
                                <option value="namoro" {{ old('relationship_type') == 'namoro' ? 'selected' : '' }}>
                                    Relacionamento / Namoro</option>
                                <option value="noivos" {{ old('relationship_type') == 'noivos' ? 'selected' : '' }}>Noivos
                                </option>
                                <option value="vivendo_maritalmente" {{ old('relationship_type') == 'vivendo_maritalmente' ? 'selected' : '' }}>Vivendo Maritalmente</option>
                                <option value="casados" {{ old('relationship_type') == 'casados' ? 'selected' : '' }}>Casados
                                </option>
                                </option>
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

                    {{-- ── Morada: única (coabitando/casados) ── --}}
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
                                        <label class="field-label" for="province"><i class="bi bi-map"></i>
                                            Província</label>
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

                    {{-- ── Morada: separada (namorados/noivos) ── --}}
                    <div class="cond-block" id="addr-dual">
                        <div class="address-grid two-cols">
                            <div class="address-person">
                                <div class="address-person-header">
                                    <i class="bi bi-person-fill"></i> Morada do Parceiro
                                </div>
                                <div class="address-person-body">
                                    <div class="field-grid">
                                        <div class="field">
                                            <label class="field-label" for="husband_address"><i class="bi bi-geo-alt"></i>
                                                Endereço</label>
                                            <input id="husband_address" name="address" type="text"
                                                class="field-input @error('address') is-invalid @enderror"
                                                value="{{ old('address') }}" placeholder="Rua, Bairro">
                                            @error('address')
                                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="field">
                                            <label class="field-label" for="husband_city"><i class="bi bi-building"></i>
                                                Cidade</label>
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
                                            <label class="field-label" for="wife_address"><i class="bi bi-geo-alt"></i>
                                                Endereço</label>
                                            <input id="wife_address" name="wife_address" type="text"
                                                class="field-input @error('wife_address') is-invalid @enderror"
                                                value="{{ old('wife_address') }}" placeholder="Rua, Bairro">
                                            @error('wife_address')
                                                <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="field">
                                            <label class="field-label" for="wife_city"><i class="bi bi-building"></i>
                                                Cidade</label>
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

                </div>{{-- /section identificação --}}

                {{-- ╔══════════════════════════════════
                ║ 2 · CONTACTOS
                ╚══════════════════════════════════ --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="bi bi-telephone-fill"></i></div>
                        <div>
                            <div class="section-title">Contactos</div>
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
                        Preencha pelo menos um dos contactos acima.
                    </p>
                </div>

                {{-- ╔══════════════════════════════════
                ║ 3 · VÍNCULO À IGREJA
                ╚══════════════════════════════════ --}}
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
                                                <span
                                                    style="font-weight:300;text-transform:none;letter-spacing:0;color:var(--text-muted);">(opcional)</span>
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

                </div>{{-- /section igreja --}}

                {{-- ╔══════════════════════════════════
                ║ 4 · OBSERVAÇÕES
                ╚══════════════════════════════════ --}}
                <div class="form-section">
                    <div class="section-header">
                        <div class="section-icon"><i class="bi bi-chat-text-fill"></i></div>
                        <div>
                            <div class="section-title">Observações</div>
                            <div class="section-desc">Informação adicional que queiram partilhar</div>
                        </div>
                    </div>
                    <div class="field">
                        <label class="field-label" for="observations">
                            <i class="bi bi-pencil-square"></i> Mensagem (opcional)
                        </label>
                        <textarea id="observations" name="observations" rows="4"
                            class="field-textarea @error('observations') is-invalid @enderror"
                            placeholder="Partilhe qualquer informação adicional relevante para a vossa inscrição...">{{ old('observations') }}</textarea>
                        @error('observations')
                            <span class="field-error"><i class="bi bi-x-circle"></i> {{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- ╔══════════════════════════════════
                ║ FOOTER / SUBMIT
                ╚══════════════════════════════════ --}}
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
        </div>{{-- /form-card --}}
    </div>{{-- /page-wrap --}}

    <script>
        (function () {
            'use strict';

            /* ─────────────────────────────────────────
               Helpers
            ───────────────────────────────────────── */
            function openBlock(el) {
                if (!el) return;
                el.classList.add('open');
                el.querySelectorAll('input, select, textarea').forEach(function (f) {
                    f.removeAttribute('tabindex');
                    f.removeAttribute('disabled');
                    if (f.tagName === 'SELECT' && f.tomselect && typeof f.tomselect.enable === 'function') {
                        f.tomselect.enable();
                    }
                });
            }

            function closeBlock(el) {
                if (!el) return;
                el.classList.remove('open');
                el.querySelectorAll('input, select, textarea').forEach(function (f) {
                    f.setAttribute('tabindex', '-1');
                    f.setAttribute('disabled', 'disabled');
                    if (f.tagName === 'SELECT' && f.tomselect && typeof f.tomselect.disable === 'function') {
                        f.tomselect.disable();
                    }
                });
            }

            /* ─────────────────────────────────────────
               1. TIPO DE RELAÇÃO → morada condicional
                  dating / engaged       → addr-dual  (moradas separadas)
                  cohabiting / married   → addr-single (morada única)
                  (nada selecionado)     → nenhum
            ───────────────────────────────────────── */
            var relSelect = document.getElementById('relationship_type');
            var addrSingle = document.getElementById('addr-single');
            var addrDual = document.getElementById('addr-dual');

            function applyAddressLogic() {
                var v = relSelect ? relSelect.value : '';
                if (v === 'vivendo_maritalmente' || v === 'casados') {
                    openBlock(addrSingle);
                    closeBlock(addrDual);
                } else if (v === 'namoro' || v === 'noivos') {
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
            applyAddressLogic(); // restore old() on page load

            /* ─────────────────────────────────────────
               2. SÃO MEMBROS → info de membro
            ───────────────────────────────────────── */
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
            applyMemberLogic(); // restore old() on page load

            /* ─────────────────────────────────────────
               3. ZONA → campo "outra zona"
            ───────────────────────────────────────── */
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
            applyZoneLogic(); // restore old() on page load

            /* ─────────────────────────────────────────
               4. SWEETALERT FEEDBACK
            ───────────────────────────────────────── */
            const swalConfig = {
                background: '#1e293b',
                color: '#f8fafc',
                confirmButtonColor: '#f97316',
                customClass: {
                    popup: 'premium-swal-popup',
                    confirmButton: 'premium-swal-button'
                }
            };

            // Success Alert
            const successEl = document.getElementById('swal-success');
            if (successEl) {
                Swal.fire({
                    ...swalConfig,
                    icon: 'success',
                    iconColor: '#10b981',
                    title: 'Sucesso!',
                    text: successEl.dataset.message,
                    timer: 5000,
                    timerProgressBar: true
                });
            }

            // Error Alert
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
