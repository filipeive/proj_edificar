# 📱💻 Guia de Compilação Multiplataforma (Capacitor & Electron) — Edificar

Este documento detalha como gerar e compilar o **Edificar** para **Mobile (iOS / Android via Capacitor)** e **Desktop (Linux / macOS / Windows via Electron)**.

---

## 📱 1. Mobile Native App (Capacitor)

### Pré-requisitos
- Node.js >= 18
- Android Studio (para compilar `.apk` / `.aab` Android)
- Xcode (para compilar `.ipa` iOS - requer macOS)

### Instalação e Inicialização do Capacitor
```bash
# 1. Instalar as dependências do Capacitor
npm install @capacitor/core @capacitor/cli @capacitor/android @capacitor/ios

# 2. Adicionar as plataformas desejadas
npx cap add android
npx cap add ios

# 3. Sincronizar os recursos web
npx cap sync
```

### Abrir nos ambientes nativos
```bash
# Para Android:
npx cap open android

# Para iOS (no macOS):
npx cap open ios
```

---

## 💻 2. Desktop Native App (Electron)

### Executar em Desenvolvimento
```bash
cd electron
npm install
npm start
```

### Gerar Instaladores Nativos

#### Linux (`.AppImage`, `.deb`)
```bash
cd electron
npm run build:linux
```

#### Windows (`.exe` - NSIS)
```bash
cd electron
npm run build:win
```

#### macOS (`.dmg`)
```bash
cd electron
npm run build:mac
```

---

## 🎨 3. Funcionalidades PWA / Native Incluídas
- **Bottom Navigation Bar**: Barra de navegação inferior nativa nos tamanhos de tela mobile (`md:hidden`).
- **Safe Area Insets**: Suporte nativo para entalhes/notches (`viewport-fit=cover`, `env(safe-area-inset-top)`, `env(safe-area-inset-bottom)`).
- **Submenus Dinâmicos**: Compatibilidade total de navegação nos sidebars mobile e desktop.
