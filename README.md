Ecco il testo completo in formato Markdown, pronto per il copia e incolla. Ho sistemato la formattazione, i blocchi di codice e i placeholder numerici per renderlo un documento professionale e funzionale.

-----

# 🐍 SabryShell

**Web Shell PHP avanzata con interfaccia terminale interattiva**

SabryShell è una web shell PHP che fornisce un'interfaccia terminale stile green-screen con funzionalità avanzate di gestione file, esecuzione comandi e editing testuale. Progettata per amministratori di sistema che necessitano di accesso remoto sicuro via web.

## ✨ Caratteristiche

### 🔐 Sicurezza

  * **Autenticazione robusta**: Sistema di login con sessione PHP.
  * **Logout sicuro**: Distruzione sessione e redirect automatico.
  * **Input sanitization**: Protezione base contro injection.
  * **Credenziali configurabili**: Facili da modificare nel codice sorgente.

### 💻 Terminale Interattivo

  * **Interfaccia green-screen**: Stile terminale retrò con colore `#00ff00`.
  * **Cursore lampeggiante**: Animazione CSS professionale.
  * **Prompt dinamico**: Mostra `utente@host:directory$`.
  * **Scroll automatico**: Mantiene il focus sull'ultimo output.

### 📁 Gestione File e Directory

  * **Navigazione completa**: Comandi `cd`, `ls`, `pwd`.
  * **Autocomplete intelligente**:
      * Premi `Tab` per completare nomi di file/directory.
      * Supporto percorsi relativi e assoluti.
      * Rilevamento automatico directory (aggiunge `/`).
  * **Syntax highlighting**: Directory evidenziate in blu (`#5c5cff`).
  * **Output formattato**: Mantiene la formattazione originale dei comandi di sistema.

### 📝 Editor di Testo Integrato

  * **Emulazione vi/nano**: Apri file con `vi`, `nano` o `edit`.
  * **Numerazione righe**: Visualizzazione laterale dei numeri di riga.
  * **Scroll sincronizzato**: Numeri di riga e testo scorrono insieme.
  * **Salvataggio sicuro**:
      * `:wq` o pulsante **SAVE & EXIT**.
      * `:q!` o pulsante **CANCEL** per annullare.
  * **Highlight sintattico**: Predisposto per future estensioni.

### 🎯 UX/UI Avanzata

  * **Icona Medusa**: Logo mitologico distintivo nella pagina di login.
  * **Design responsive**: Si adatta a diversi dispositivi e risoluzioni.
  * **Storico comandi**:
      * Navigazione con frecce ↑/↓.
      * Persistenza durante l'intera sessione.
  * **Comandi rapidi**:
      * `cls` / `clear` per pulire la schermata.
      * Supporto completo ai comandi shell standard del sistema ospite.

### 🔧 Funzionalità Tecniche

  * **AJAX autocomplete**: Richieste asincrone per un'esperienza fluida senza refresh.
  * **Base64 encoding**: Storico comandi e dati sensibili codificati nei form.
  * **Gestione errori**: Blocchi try-catch sulle operazioni critiche sui file.
  * **Cross-platform**: Compatibile con ambienti Linux, Unix e Windows.

-----

## 📋 Requisiti

  * **PHP**: 7.0 o superiore.
  * **Server Web**: Apache, Nginx, o qualsiasi server con supporto PHP attivo.
  * **Permessi**: Funzioni di esecuzione (`shell_exec`, `exec`, etc.) abilitate nel `php.ini`.
  * **Browser moderno**: Chrome, Firefox, Safari, Edge.

-----

## 🚀 Installazione

### Metodo 1: Clone Git

```bash
git clone https://github.com/alexdauria83/SabryShell.git
cd sabryshell
```

### Configurazione Iniziale

Modifica le credenziali di default nel file PHP (solitamente nelle prime righe):

```php
// Configurazione credenziali
$username = "admin";
$password = "cambiami_subito";
```

> ⚠️ **IMPORTANTE**: Modifica SEMPRE le credenziali prima del deploy in produzione\!

-----

## 📖 Utilizzo

### Accesso

1.  Apri il browser e naviga su `http://tuoserver.com/sabryshell.php`.
2.  Inserisci **username** e **password** configurati.
3.  Clicca su **ACCEDI**.

### Comandi Base

```bash
ls -la          # Elenca file con permessi
cd /var/www     # Cambia directory
pwd             # Mostra percorso attuale
whoami          # Mostra utente corrente
cat config.php  # Leggi contenuto file
```

### Editor di Testo

Quando apri un file con `vi`, `nano` o `edit`:

1.  Modifica il contenuto nell'area di testo.
2.  **Salva**: Clicca "SAVE & EXIT" o scrivi `:wq` nel terminale dell'editor.
3.  **Annulla**: Clicca "CANCEL" o scrivi `:q!`.

### Autocomplete

1.  Inizia a scrivere un nome di file o directory (es. `cd /v`).
2.  Premi **Tab**.
3.  Se c'è un solo match, viene completato automaticamente. Le directory ricevono automaticamente lo slash `/` finale.

-----

## 🎨 Personalizzazione

### Cambiare Colori

Modifica le variabili CSS nel blocco `<style>` all'interno del file:

```css
:root {
    --term-green: #00ff00;
    --term-bg: #0a0a0a;
    --dir-blue: #5c5cff;
}
```

### Cambiare Icona Login

Sostituisci l'URL dell'immagine nel form di login:

```html
<img src="https://link-tua-immagine.png" alt="Logo">
```

### Disabilitare Autenticazione ⚠️

```php
$auth_enabled = false; // Solo per scopi di debug!
```

> ⚠️ **NON CONSIGLIATO**: Usare solo in ambienti di test locali e isolati\!

-----

## 🔒 Sicurezza

### Best Practices

  * ✅ **Cambia le credenziali** immediatamente dopo l'installazione.
  * ✅ Usa **HTTPS** sempre per evitare l'intercettazione del traffico.
  * ✅ Limita l'accesso via **IP** tramite `.htaccess` o configurazione server.
  * ✅ Aggiorna PHP all'ultima versione stabile.
  * ✅ Esegui backup regolari dei file gestiti.

### Avvertenze

> ⚠️ **ATTENZIONE**: SabryShell è uno strumento potente che permette l'esecuzione di comandi di sistema. Utilizzalo solo in ambienti controllati e sicuri. Non esporre mai su server pubblici senza adeguate misure di protezione.

### Limitazioni

  * Autenticazione basata su sessioni standard (considera OAuth o 2FA per uso professionale).
  * Nessuna crittografia end-to-end nativa oltre a quella fornita da SSL/TLS.
  * Le funzionalità dipendono strettamente dai permessi dell'utente che esegue il processo PHP.

-----

## 🛠️ Troubleshooting

### Problemi Comuni

  * **"Permission Denied"**:
    Controlla che l'utente del server web (es. `www-data`) abbia i permessi di lettura/scrittura sulla cartella.
  * **"Command not found"**:
    1.  Verifica che `shell_exec` non sia presente nella direttiva `disable_functions` in `php.ini`.
    2.  Controlla il PATH di sistema.
  * **Login non funziona**:
    Verifica che i cookie e le sessioni siano abilitati nel browser e sul server.
  * **Autocomplete non risponde**:
    Assicurati che JavaScript sia abilitato. Controlla la console (`F12`) per eventuali errori di rete.

-----

## 📂 Struttura File

```text
sabryshell/
├── sabryshell.php    # File principale (monolitico per facilità di deploy)
├── LICENSE           # Licenza MIT
└── README.md         # Documentazione
```

-----

## 🤝 Contributing

Contributi sono benvenuti\! Per favore segui questi passaggi:

1.  Fai un **Fork** del progetto.
2.  Crea un branch per la tua feature (`git checkout -b feature/nuova-funzione`).
3.  Esegui il **Commit** delle modifiche (`git commit -m 'Aggiunta funzione X'`).
4.  Fai il **Push** sul branch (`git push origin feature/nuova-funzione`).
5.  Apri una **Pull Request**.

-----

## 📄 Licenza

Distribuito sotto licenza MIT. Vedi il file `LICENSE` per maggiori informazioni.

-----

## 👤 Autore

**SabryShell** - Sviluppato con ❤️ per amministratori di sistema.

-----

## 🙏 Ringraziamenti

  * Ispirato ai terminali green-screen degli anni '80.
  * Icona Medusa ispirata alla mitologia greca.
  * Community PHP per il supporto continuo.

-----

## 📞 Supporto

Per problemi, bug report o suggerimenti:

  * 🐛 Apri una **Issue** su GitHub.
  * 📧 Invia una email a: `supporto@esempio.com`.

-----

## 🔄 Changelog

### v1.0.0 (2026)

  * ✅ Release iniziale.
  * ✅ Autenticazione con sessione PHP sicura.
  * ✅ Terminale interattivo green-screen.
  * ✅ Editor file integrato (vi/nano emulation).
  * ✅ Autocomplete intelligente con tasto Tab.
  * ✅ Storico comandi con navigazione frecce.

-----

## ⚠️ DISCLAIMER LEGALE

Questo software è fornito "AS IS" senza garanzie di alcun tipo. L'utente si assume piena e totale responsabilità per l'utilizzo di questo strumento. Gli sviluppatori non sono responsabili per eventuali danni, perdite di dati o violazioni della sicurezza. L'uso non autorizzato su sistemi di terzi è illegale.

\<p align="center"\>
\<strong\>Made with 🐍 and ☕\</strong\><br>
\<sub\>© 2026 SabryShell Project\</sub\>
\</p\>

-----

Desideri che io generi anche il codice PHP di base per implementare queste funzionalità o preferisci procedere con questo testo per il tuo file README?
