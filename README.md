# 🐍 SabryShell

**Web Shell PHP avanzata con interfaccia terminale interattiva**

SabryShell è una web shell PHP che fornisce un'interfaccia terminale stile green-screen con funzionalità avanzate di gestione file, esecuzione comandi e editing testuale. Progettata per amministratori di sistema che necessitano di accesso remoto sicuro via web.

![Login](https://img.shields.io/badge/Version-1.0.0-green.svg)
![PHP](https://img.shields.io/badge/PHP-7.0+-blue.svg)
![License](https://img.shields.io/badge/License-MIT-yellow.svg)

## ✨ Caratteristiche

### 🔐 Sicurezza
- **Autenticazione robusta**: Sistema di login con sessione PHP
- **Logout sicuro**: Distruzione sessione e redirect automatico
- **Input sanitization**: Protezione base contro injection
- **Credenziali configurabili**: Facili da modificare

### 💻 Terminale Interattivo
- **Interfaccia green-screen**: Stile terminale retrò con colore #00ff00
- **Cursore lampeggiante**: Animazione CSS professionale
- **Prompt dinamico**: Mostra utente@host:directory$
- **Scroll automatico**: Mantiene il focus sull'ultimo output

### 📁 Gestione File e Directory
- **Navigazione completa**: Comandi `cd`, `ls`, `pwd`
- **Autocomplete intelligente**: 
  - Premi `Tab` per completare nomi di file/directory
  - Supporto percorsi relativi e assoluti
  - Rilevamento automatico directory (aggiunge `/`)
- **Syntax highlighting**: Directory evidenziate in blu (#5c5cff)
- **Output formattato**: Mantiene la formattazione originale dei comandi

### 📝 Editor di Testo Integrato
- **Emulazione vi/nano**: Apri file con `vi`, `nano` o `edit`
- **Numerazione righe**: Visualizzazione laterale dei numeri di riga
- **Scroll sincronizzato**: Numeri di riga e testo scorrono insieme
- **Salvataggio sicuro**: 
  - `:wq` o pulsante SAVE & EXIT
  - `:q!` o pulsante CANCEL per annullare
- **Highlight sintattico**: Pronto per estensioni future

### 🎯 UX/UI Avanzata
- **Icona Medusa**: Logo mitologico nella pagina di login
- **Design responsive**: Si adatta a diversi screen
- **Storico comandi**: 
  - Navigazione con frecce ↑/↓
  - Persistenza durante la sessione
- **Comandi rapidi**: 
  - `cls` / `clear` per pulire il terminale
  - Supporto comandi shell standard

### 🔧 Funzionalità Tecniche
- **AJAX autocomplete**: Richieste asincrone senza refresh
- **Base64 encoding**: Storico comandi codificato nei form
- **Gestione errori**: Try-catch su operazioni file
- **Cross-platform**: Compatibile con Linux/Unix/Windows

## 📋 Requisiti

- **PHP**: 7.0 o superiore
- **Server Web**: Apache, Nginx, o qualsiasi server con supporto PHP
- **Permessi**: Execute permission per comandi shell
- **Browser moderno**: Chrome, Firefox, Safari, Edge

## 🚀 Installazione

### Metodo 1: Clone Git
```bash
git clone https://github.com/tuo-utente/sabryshell.git
cd sabryshell
