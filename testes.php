<?php

$branco = "\e[97m";
$preto = "\e[30m\e[1m";
$amarelo = "\e[93m";
$laranja = "\e[38;5;208m";
$azul   = "\e[34m";
$lazul  = "\e[36m";
$cln    = "\e[0m";
$verde  = "\e[92m";
$fverde = "\e[32m";
$vermelho    = "\e[91m";
$magenta = "\e[35m";
$azulbg = "\e[44m";
$lazulbg = "\e[106m";
$verdebg = "\e[42m";
$lverdebg = "\e[102m";
$amarelobg = "\e[43m";
$lamarelobg = "\e[103m";
$vermelhobg = "\e[101m";
$cinza = "\e[37m";
$ciano = "\e[36m";
$bold   = "\e[1m";

function keller_banner(){
  echo "\e[37m
           KellerSS Android\e[36m Fucking Cheaters\e[91m\e[37m discord.gg/allianceoficial\e[91m
            
                            )       (     (          (     
                        ( /(       )\ )  )\ )       )\ )  
                        )\()) (   (()/( (()/(  (   (()/(  
                        |((_)\  )\   /(_)) /(_)) )\   /(_)) 
                        |_ ((_)((_) (_))  (_))  ((_) (_))   
                        | |/ / | __|| |   | |   | __|| _ \  
                        ' <  | _| | |__ | |__ | _| |   /  
                        _|\_\ |___||____||____||___||_|_\  



                    \e[36m{C} Coded By - KellerSS | v2.0 Enhanced Edition                                   
\e[32m
  \n";
}

echo $cln;

function atualizar()
{
    global $cln, $bold, $fverde;
    echo "\n\e[91m\e[1m[+] KellerSS Updater [+]\nAtualizando, por favor aguarde...\n\n$cln";
    system("git fetch origin && git reset --hard origin/master && git clean -f -d");
    echo $bold . $fverde . "[i] Atualização concluida! Por favor reinicie o Scanner \n" . $cln;
    exit;
}

// ========== NOVA FUNÇÃO: Verificação de Hash MD5 de Arquivos Críticos ==========
function verificarHashArquivos() {
    global $bold, $vermelho, $amarelo, $fverde, $azul, $cln;
    
    echo $bold . $azul . "[+] Verificando integridade de arquivos críticos via MD5...\n";
    
    // Arquivos críticos do Free Fire para verificação
    $arquivosCriticos = [
        '/data/app/com.dts.freefireth*/base.apk',
        '/data/app/com.dts.freefiremax*/base.apk',
        '/data/data/com.dts.freefireth/lib',
        '/data/data/com.dts.freefiremax/lib'
    ];
    
    $modificacaoDetectada = false;
    
    foreach ($arquivosCriticos as $padrao) {
        // Expande wildcards
        $comando = 'adb shell "ls ' . $padrao . ' 2>/dev/null"';
        $arquivos = shell_exec($comando);
        
        if (!empty($arquivos)) {
            $arquivos = explode("\n", trim($arquivos));
            foreach ($arquivos as $arquivo) {
                if (empty($arquivo)) continue;
                
                // Calcula MD5 do arquivo
                $comandoMD5 = 'adb shell "md5sum ' . escapeshellarg($arquivo) . ' 2>/dev/null"';
                $resultadoMD5 = shell_exec($comandoMD5);
                
                if ($resultadoMD5 && preg_match('/^([a-f0-9]{32})\s+(.+)$/i', trim($resultadoMD5), $matches)) {
                    $hashAtual = $matches[1];
                    $nomeArquivo = basename($arquivo);
                    
                    // Verifica tamanho do arquivo
                    $comandoTamanho = 'adb shell "stat -c %s ' . escapeshellarg($arquivo) . ' 2>/dev/null"';
                    $tamanho = trim(shell_exec($comandoTamanho));
                    
                    // Tamanhos suspeitos (muito pequenos ou muito grandes para APK base)
                    if ($tamanho && is_numeric($tamanho)) {
                        if (strpos($arquivo, 'base.apk') !== false) {
                            // APK base do FF geralmente tem entre 50MB e 600MB
                            $tamanhoMB = $tamanho / (1024 * 1024);
                            if ($tamanhoMB < 50 || $tamanhoMB > 700) {
                                echo $bold . $vermelho . "[!] TAMANHO SUSPEITO DETECTADO: $nomeArquivo\n";
                                echo $bold . $amarelo . "[!] Tamanho: " . number_format($tamanhoMB, 2) . " MB\n";
                                echo $bold . $amarelo . "[!] MD5: $hashAtual\n";
                                $modificacaoDetectada = true;
                            }
                        }
                    }
                    
                    // Verifica modificação recente do APK
                    $comandoDataMod = 'adb shell "stat -c %Y ' . escapeshellarg($arquivo) . ' 2>/dev/null"';
                    $timestampMod = trim(shell_exec($comandoDataMod));
                    
                    if ($timestampMod && is_numeric($timestampMod)) {
                        $diferencaHoras = (time() - $timestampMod) / 3600;
                        if ($diferencaHoras < 24) {
                            echo $bold . $amarelo . "[!] APK modificado nas últimas 24h: $nomeArquivo\n";
                            echo $bold . $amarelo . "[!] MD5: $hashAtual\n";
                            $modificacaoDetectada = true;
                        }
                    }
                }
            }
        }
    }
    
    if (!$modificacaoDetectada) {
        echo $bold . $fverde . "[i] Integridade dos arquivos principais OK.\n\n";
    } else {
        echo $bold . $vermelho . "[!] Modificações suspeitas detectadas! Possível APK modificado!\n\n";
    }
}

// ========== NOVA FUNÇÃO: Detecção de Painéis Conhecidos ==========
function detectarPaineisConhecidos() {
    global $bold, $vermelho, $amarelo, $fverde, $azul, $cln;
    
    echo $bold . $azul . "[+] Verificando presença de painéis de cheat conhecidos...\n";
    
    // Lista de painéis conhecidos e suas assinaturas
    $paineisConhecidos = [
        'Uriel Xiter' => ['uriel', 'xiter', 'paineluriel'],
        'FFH4X' => ['ffh4x', 'ff.h4x', 'ffh4xinjector'],
        'Painel Xit' => ['painelxit', 'xit.ff', 'xitff'],
        'Dan Xiter' => ['danxiter', 'dan.xiter'],
        'Granjeiro FF' => ['granjeiroff', 'granjeiro.ff'],
        'XPRO Panel' => ['xpro', 'xproff', 'xpropanel'],
        'Dark Aura' => ['darkaura', 'dark.aura', 'auraxiter'],
        'Painel Freestyle' => ['freestyle', 'sadx', 'freestylepanel'],
        'BossBe Panel' => ['bossbe', 'boss.be'],
        'Antiban Panel' => ['antiban', 'antibanpanel'],
        'Painel Obito' => ['obito', 'painelobito']
    ];
    
    $painelDetectado = false;
    
    // Busca em diretórios comuns
    $diretorios = [
        '/sdcard',
        '/sdcard/Download',
        '/data/data/com.termux/files/home',
        '/data/local/tmp',
        '/sdcard/Android/data',
        '/storage/emulated/0'
    ];
    
    foreach ($diretorios as $dir) {
        foreach ($paineisConhecidos as $nomePainel => $assinaturas) {
            foreach ($assinaturas as $assinatura) {
                // Busca por arquivos
                $comandoBusca = 'adb shell "find ' . escapeshellarg($dir) . ' -type f -iname \"*' . $assinatura . '*\" 2>/dev/null | head -20"';
                $resultado = shell_exec($comandoBusca);
                
                if ($resultado && !empty(trim($resultado))) {
                    echo $bold . $vermelho . "[!] PAINEL DETECTADO: $nomePainel\n";
                    echo $bold . $amarelo . "[!] Arquivos encontrados:\n";
                    $linhas = explode("\n", trim($resultado));
                    foreach (array_slice($linhas, 0, 5) as $linha) {
                        if (!empty($linha)) {
                            echo $bold . $amarelo . "    - " . basename($linha) . "\n";
                        }
                    }
                    $painelDetectado = true;
                    break 2;
                }
            }
        }
    }
    
    // Verifica processos em execução com nomes suspeitos
    $comandoProcessos = 'adb shell "ps -A | grep -iE \"(xiter|ffh4x|painel|aimbot|wallhack|cheat|mod)\" 2>/dev/null"';
    $processosAtivos = shell_exec($comandoProcessos);
    
    if ($processosAtivos && !empty(trim($processosAtivos))) {
        echo $bold . $vermelho . "[!] PROCESSOS SUSPEITOS EM EXECUÇÃO:\n";
        $linhas = explode("\n", trim($processosAtivos));
        foreach ($linhas as $linha) {
            if (!empty($linha)) {
                echo $bold . $amarelo . "    - " . trim($linha) . "\n";
            }
        }
        $painelDetectado = true;
    }
    
    if (!$painelDetectado) {
        echo $bold . $fverde . "[i] Nenhum painel conhecido detectado.\n\n";
    } else {
        echo $bold . $vermelho . "\n[!] ========== ATENÇÃO ==========\n";
        echo $bold . $vermelho . "[!] PAINEL DE CHEAT DETECTADO!\n";
        echo $bold . $vermelho . "[!] APLIQUE O W.O IMEDIATAMENTE!\n";
        echo $bold . $vermelho . "[!] ==============================\n\n";
    }
}

// ========== NOVA FUNÇÃO: Detecção de Aimbot/Auto Headshot ==========
function detectarAimbotHeadshot() {
    global $bold, $vermelho, $amarelo, $fverde, $azul, $cln;
    
    echo $bold . $azul . "[+] Verificando indícios de Aimbot/Auto Headshot...\n";
    
    $aimbotDetectado = false;
    
    // Verifica serviços de acessibilidade suspeitos
    $comandoAccessibility = 'adb shell "settings get secure enabled_accessibility_services 2>/dev/null"';
    $servicos = shell_exec($comandoAccessibility);
    
    if ($servicos && !empty(trim($servicos)) && trim($servicos) !== 'null') {
        $servicosSuspeitos = ['xiter', 'aimbot', 'auto', 'cheat', 'mod', 'hack'];
        $servicosLower = strtolower($servicos);
        
        foreach ($servicosSuspeitos as $suspeito) {
            if (strpos($servicosLower, $suspeito) !== false) {
                echo $bold . $vermelho . "[!] SERVIÇO DE ACESSIBILIDADE SUSPEITO:\n";
                echo $bold . $amarelo . "[!] " . trim($servicos) . "\n";
                $aimbotDetectado = true;
                break;
            }
        }
    }
    
    // Verifica aplicativos com permissões de desenho sobre outros apps
    $comandoOverlay = 'adb shell "dumpsys package | grep -A 5 SYSTEM_ALERT_WINDOW | grep packageName" 2>/dev/null';
    $appsOverlay = shell_exec($comandoOverlay);
    
    if ($appsOverlay && !empty(trim($appsOverlay))) {
        $linhas = explode("\n", trim($appsOverlay));
        foreach ($linhas as $linha) {
            if (preg_match('/packageName=([^\s]+)/', $linha, $matches)) {
                $packageName = $matches[1];
                $packageLower = strtolower($packageName);
                
                // Filtra apps suspeitos
                $termosSuspeitos = ['xiter', 'aimbot', 'cheat', 'hack', 'mod', 'ffh4x', 'painel'];
                foreach ($termosSuspeitos as $termo) {
                    if (strpos($packageLower, $termo) !== false) {
                        echo $bold . $vermelho . "[!] APP COM OVERLAY SUSPEITO: $packageName\n";
                        $aimbotDetectado = true;
                    }
                }
            }
        }
    }
    
    // Verifica bibliotecas nativas suspeitas
    $comandoLibs = 'adb shell "ls /data/data/com.dts.freefireth/lib/*.so 2>/dev/null; ls /data/data/com.dts.freefiremax/lib/*.so 2>/dev/null"';
    $libs = shell_exec($comandoLibs);
    
    if ($libs && !empty(trim($libs))) {
        $linhas = explode("\n", trim($libs));
        foreach ($linhas as $lib) {
            $libLower = strtolower(basename($lib));
            $termosSuspeitos = ['mod', 'hack', 'cheat', 'aim', 'inject', 'hook'];
            
            foreach ($termosSuspeitos as $termo) {
                if (strpos($libLower, $termo) !== false) {
                    echo $bold . $vermelho . "[!] BIBLIOTECA NATIVA SUSPEITA: " . basename($lib) . "\n";
                    $aimbotDetectado = true;
                }
            }
        }
    }
    
    if (!$aimbotDetectado) {
        echo $bold . $fverde . "[i] Nenhum indício direto de Aimbot detectado.\n\n";
    } else {
        echo $bold . $vermelho . "[!] Evidências de Aimbot/Auto Headshot encontradas!\n\n";
    }
}

// ========== NOVA FUNÇÃO: Detecção de Módulos Xposed/Magisk ==========
function detectarModulosRoot() {
    global $bold, $vermelho, $amarelo, $fverde, $azul, $cln;
    
    echo $bold . $azul . "[+] Verificando módulos Xposed/Magisk/LSPosed...\n";
    
    $moduloDetectado = false;
    
    // Verifica presença do Magisk
    $comandoMagisk = 'adb shell "which magisk 2>/dev/null; ls /data/adb/magisk 2>/dev/null; ls /sbin/.magisk 2>/dev/null"';
    $resultadoMagisk = shell_exec($comandoMagisk);
    
    if ($resultadoMagisk && !empty(trim($resultadoMagisk))) {
        echo $bold . $vermelho . "[!] MAGISK DETECTADO NO DISPOSITIVO!\n";
        $moduloDetectado = true;
    }
    
    // Verifica módulos do Magisk
    $comandoModulos = 'adb shell "ls /data/adb/modules 2>/dev/null"';
    $modulos = shell_exec($comandoModulos);
    
    if ($modulos && !empty(trim($modulos))) {
        echo $bold . $amarelo . "[!] Módulos Magisk encontrados:\n";
        $linhas = explode("\n", trim($modulos));
        foreach ($linhas as $modulo) {
            if (!empty($modulo)) {
                echo $bold . $amarelo . "    - $modulo\n";
                $moduloDetectado = true;
            }
        }
    }
    
    // Verifica Xposed/LSPosed
    $comandoXposed = 'adb shell "ls /data/data/de.robv.android.xposed.installer 2>/dev/null; ls /data/data/org.lsposed.manager 2>/dev/null"';
    $resultadoXposed = shell_exec($comandoXposed);
    
    if ($resultadoXposed && !empty(trim($resultadoXposed))) {
        echo $bold . $vermelho . "[!] XPOSED/LSPOSED DETECTADO!\n";
        $moduloDetectado = true;
    }
    
    // Verifica aplicativos que ocultam root
    $appsOcultarRoot = ['Hide My Root', 'Magisk Hide', 'RootCloak'];
    $comandoApps = 'adb shell "pm list packages -f 2>/dev/null"';
    $todosApps = shell_exec($comandoApps);
    
    if ($todosApps) {
        $todosAppsLower = strtolower($todosApps);
        $termosSuspeitos = ['hide', 'root', 'cloak', 'conceal', 'stealth'];
        
        foreach ($termosSuspeitos as $termo) {
            if (strpos($todosAppsLower, $termo) !== false && 
                (strpos($todosAppsLower, 'root') !== false || strpos($todosAppsLower, 'magisk') !== false)) {
                echo $bold . $amarelo . "[!] App para ocultar root detectado (termo: $termo)\n";
                $moduloDetectado = true;
            }
        }
    }
    
    if (!$moduloDetectado) {
        echo $bold . $fverde . "[i] Nenhum módulo root detectado.\n\n";
    } else {
        echo $bold . $vermelho . "[!] Dispositivo com root modificado detectado!\n\n";
    }
}

// ========== FUNÇÃO MELHORADA: Detecção de Bypass de Funções Shell ==========
function detectarBypassShell() {
    global $bold, $vermelho, $amarelo, $fverde, $azul, $branco, $cln;
    
    $bypassDetectado = false;
    
    echo $bold . $azul . "[+] Verificando funções maliciosas no ambiente shell...\n";
    
    $funcoesTeste = [
        'pkg' => 'adb shell "type pkg 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'git' => 'adb shell "type git 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"', 
        'cd' => 'adb shell "type cd 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'stat' => 'adb shell "type stat 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'adb' => 'adb shell "type adb 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'ls' => 'adb shell "type ls 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"',
        'find' => 'adb shell "type find 2>/dev/null | grep -q function && echo FUNCTION_DETECTED"'
    ];
    
    foreach ($funcoesTeste as $funcao => $comando) {
        $resultado = shell_exec($comando);
        if ($resultado !== null && strpos($resultado, 'FUNCTION_DETECTED') !== false) {
            echo $bold . $vermelho . "[!] BYPASS DETECTADO: Função '$funcao' foi sobrescrita!\n";
            $bypassDetectado = true;
        }
    }
    
    // Testa diretórios críticos
    echo $bold . $azul . "[+] Testando acesso a diretórios críticos...\n";
    
    $diretoriosCriticos = [
        '/system/bin',
        '/data/data/com.dts.freefireth/files',
        '/data/data/com.dts.freefiremax/files',
        '/storage/emulated/0/Android/data'
    ];
    
    foreach ($diretoriosCriticos as $diretorio) {
        $comandoTestDir = 'adb shell "ls -la \"' . $diretorio . '\" 2>/dev/null | head -3"';
        $resultadoTestDir = shell_exec($comandoTestDir);
        
        if (empty($resultadoTestDir) || trim($resultadoTestDir ?? '') === '' || 
            ($resultadoTestDir !== null && (strpos($resultadoTestDir, 'Permission denied') !== false ||
            strpos($resultadoTestDir, 'blocked') !== false ||
            strpos($resultadoTestDir, 'redirected') !== false))) {
            
            if ($resultadoTestDir !== null && (strpos($resultadoTestDir, 'blocked') !== false ||
                strpos($resultadoTestDir, 'redirected') !== false ||
                strpos($resultadoTestDir, 'bypass') !== false)) {
                
                echo $bold . $vermelho . "[!] BYPASS DETECTADO: Acesso bloqueado/redirecionado ao diretório: $diretorio\n";
                echo $bold . $amarelo . "[!] Resposta: " . trim($resultadoTestDir ?? '') . "\n";
                $bypassDetectado = true;
            }
        }
    }
    
    // Verifica processos suspeitos
    echo $bold . $azul . "[+] Verificando processos suspeitos...\n";
    
    $comandoProcessos = 'adb shell "ps | grep -E \"(bypass|redirect|fake)\" | grep -vE \"(drm_fake_vsync|mtk_drm_fake_vsync|mtk_drm_fake_vs)\" 2>/dev/null"';
    $resultadoProcessos = shell_exec($comandoProcessos);
    
    if ($resultadoProcessos !== null && !empty(trim($resultadoProcessos))) {
        $linhasProcessos = explode("\n", trim($resultadoProcessos));
        $processosSuspeitos = [];
        
        foreach ($linhasProcessos as $linha) {
            if (!empty(trim($linha)) && 
                strpos($linha, '[kblockd]') === false && 
                strpos($linha, 'kworker') === false &&
                strpos($linha, '[ksoftirqd]') === false &&
                strpos($linha, '[migration]') === false &&
                strpos($linha, 'mtk_drm_fake_vsync') === false &&
                strpos($linha, 'mtk_drm_fake_vs') === false &&
                strpos($linha, 'drm_fake_vsync') === false) {
                $processosSuspeitos[] = $linha;
            }
        }
        
        if (!empty($processosSuspeitos)) {
            echo $bold . $vermelho . "[!] BYPASS DETECTADO: Processos suspeitos em execução!\n";
            echo $bold . $amarelo . "[!] Processos encontrados:\n" . implode("\n", $processosSuspeitos) . "\n";
            $bypassDetectado = true;
        }
    }
    
    // Verifica arquivos de configuração
    echo $bold . $azul . "[+] Verificando arquivos de configuração...\n";
    $arquivosConfig = [
        '~/.bashrc', '~/.bash_profile', '~/.profile', '~/.zshrc', 
        '~/.config/fish/config.fish', '/data/data/com.termux/files/usr/etc/bash.bashrc'
    ];
    
    foreach ($arquivosConfig as $arquivo) {
        $comandoVerificar = 'adb shell "if [ -f ' . $arquivo . ' ]; then cat ' . $arquivo . ' | grep -E \"(function pkg|function git|function cd|function stat|function adb|function ls|function find)\" 2>/dev/null; fi"';
        $resultadoArquivo = shell_exec($comandoVerificar);
        
        if ($resultadoArquivo !== null && !empty(trim($resultadoArquivo))) {
            echo $bold . $vermelho . "[!] BYPASS DETECTADO: Funções maliciosas em $arquivo!\n";
            echo $bold . $amarelo . "[!] Conteúdo detectado:\n" . trim($resultadoArquivo) . "\n";
            $bypassDetectado = true;
        }
    }
    
    // Testa comportamento real do git
    echo $bold . $azul . "[+] Testando comportamento real das funções...\n";
    
    $comandoTestGitReal = 'adb shell "cd /tmp 2>/dev/null || cd /data/local/tmp; git clone --help 2>&1 | head -1"';
    $resultadoGitHelp = shell_exec($comandoTestGitReal);
    
    if (empty($resultadoGitHelp) || strpos($resultadoGitHelp, 'usage: git') === false) {
        $comandoTestClone = 'adb shell "cd /tmp 2>/dev/null || cd /data/local/tmp; timeout 5 git clone https://github.com/kellerzz/KellerSS-Android test-repo 2>&1 | head -3"';
        $resultadoClone = shell_exec($comandoTestClone);
        
        if (strpos($resultadoClone, 'wendell77x') !== false || 
            strpos($resultadoClone, 'Comando bloqueado') !== false ||
            strpos($resultadoClone, 'blocked') !== false) {
            echo $bold . $vermelho . "[!] BYPASS DETECTADO: Git clone sendo redirecionado!\n";
            echo $bold . $amarelo . "[!] Resposta: " . trim($resultadoClone) . "\n";
            $bypassDetectado = true;
        }
    }
    
    // Testa comando pkg
    $comandoTestPkgReal = 'adb shell "pkg --help 2>&1 | head -1"';
    $resultadoPkgHelp = shell_exec($comandoTestPkgReal);
    
    if (empty($resultadoPkgHelp) || strpos($resultadoPkgHelp, 'Usage:') === false) {
        $comandoTestPkgInstall = 'adb shell "timeout 3 pkg install --help 2>&1"';
        $resultadoPkgInstall = shell_exec($comandoTestPkgInstall);
        
        if (strpos($resultadoPkgInstall, 'Comando bloqueado') !== false ||
            strpos($resultadoPkgInstall, 'blocked') !== false ||
            empty(trim($resultadoPkgInstall))) {
            echo $bold . $vermelho . "[!] BYPASS DETECTADO: Comando pkg sendo bloqueado!\n";
            echo $bold . $amarelo . "[!] Resposta: " . trim($resultadoPkgInstall) . "\n";
            $bypassDetectado = true;
        }
    }
    
    // Testa manipulação da função stat
    echo $bold . $azul . "[+] Testando manipulação da função stat...\n";
    
    $arquivoTeste = '/data/local/tmp/test_stat_' . time();
    $comandoCriarArquivo = 'adb shell "echo test > ' . $arquivoTeste . ' 2>/dev/null"';
    shell_exec($comandoCriarArquivo);
    
    sleep(1);
    $comandoStatTeste = 'adb shell "stat ' . $arquivoTeste . ' 2>/dev/null"';
    $resultadoStatTeste = shell_exec($comandoStatTeste);
    
    if (!empty($resultadoStatTeste)) {
        preg_match('/Access: (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $resultadoStatTeste, $matchAccess);
        preg_match('/Modify: (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $resultadoStatTeste, $matchModify);
        preg_match('/Change: (\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/', $resultadoStatTeste, $matchChange);
        
        if ($matchAccess && $matchModify && $matchChange) {
            $timestampAccess = strtotime($matchAccess[1]);
            $timestampModify = strtotime($matchModify[1]);
            $timestampChange = strtotime($matchChange[1]);
            $timestampAtual = time();
            
            $diferencaAtual = abs($timestampAtual - $timestampModify);
            $diferencaInterna = abs($timestampAccess - $timestampModify);
            
            if ($diferencaAtual > 86400 || $diferencaInterna > 300) {
                echo $bold . $vermelho . "[!] BYPASS DETECTADO";}
}
}
}
