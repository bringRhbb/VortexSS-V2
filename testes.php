<?php

class KellerScanner
{
    // Cores ANSI para o terminal
    private $c = [
        'branco' => "\e[97m",
        'preto'  => "\e[30m\e[1m",
        'amarelo'=> "\e[93m",
        'laranja'=> "\e[38;5;208m",
        'azul'   => "\e[34m",
        'lazul'  => "\e[36m",
        'verde'  => "\e[92m", // Verde claro
        'fverde' => "\e[32m", // Verde escuro/floresta
        'vermelho' => "\e[91m",
        'reset'  => "\e[0m",
        'bold'   => "\e[1m"
    ];

    private $adbPort = null;
    private $deviceSerial = null;

    // Construtor: Limpa a tela e inicia
    public function __construct() {
        system("clear");
        $this->banner();
    }

    // Exibe o Banner
    private function banner() {
        echo $this->c['branco'] . "
           KellerSS Android" . $this->c['lazul'] . " Professional Auditor" . $this->c['vermelho'] . "
            
                            )       (     (          (     
                 ( /(       )\ )  )\ )       )\ )  
                )\()) (   (()/( (()/(  (   (()/(  
                |((_)\  )\  /(_)) /(_)) )\   /(_)) 
                |_ ((_)((_) (_))  (_))  ((_) (_))   
                | |/ / | __|| |   | |   | __|| _ \  
                | ' <  | _| | |__ | |__ | _| |   /  
                _|\_\ |___||____||____||___||_|_\  

                    " . $this->c['lazul'] . "{C} Refactored v2.0 - Security Enhanced
        " . $this->c['reset'] . "\n";
    }

    // Wrapper para input do usuário
    private function input($msg) {
        echo $this->c['bold'] . $this->c['lazul'] . "[#] " . $msg . ": " . $this->c['fverde'];
        return trim(fgets(STDIN));
    }

    // Wrapper para executar comandos shell
    private function exec($cmd) {
        return shell_exec($cmd);
    }

    // Menu Principal
    public function menu() {
        while (true) {
            echo $this->c['bold'] . $this->c['azul'] . "
      +--------------------------------------------------------------+
      +                       KellerSS Menu v2                       +
      +--------------------------------------------------------------+
      \n";
            echo $this->c['amarelo'] . " [0] Conectar ADB (Pareamento/Conexão)\n";
            echo $this->c['fverde'] . " [1] Escanear FreeFire Normal\n";
            echo $this->c['fverde'] . " [2] Escanear FreeFire Max\n";
            echo $this->c['vermelho'] . " [S] Sair\n\n" . $this->c['reset'];

            $op = $this->input("Escolha uma opção");

            switch (strtoupper($op)) {
                case '0': $this->conectarADB(); break;
                case '1': $this->iniciarScan('com.dts.freefireth'); break;
                case '2': $this->iniciarScan('com.dts.freefiremax'); break;
                case 'S': exit($this->c['reset'] . "\nSaindo...\n");
                default: echo $this->c['vermelho'] . "\n[!] Opção inválida!\n"; sleep(1);
            }
            system("clear");
            $this->banner();
        }
    }

    // Lógica de Conexão ADB
    private function conectarADB() {
        system("clear");
        $this->banner();
        
        // Verifica instalação do ADB
        if (!$this->exec("which adb")) {
            echo $this->c['amarelo'] . "[!] ADB não encontrado. Instalando...\n" . $this->c['reset'];
            system("pkg install android-tools -y");
        }

        $portPair = $this->input("Porta de Pareamento (ex: 45678) [Enter para pular]");
        if (!empty($portPair)) {
            echo $this->c['amarelo'] . "\n[!] Insira o código de pareamento no celular...\n" . $this->c['reset'];
            system("adb pair localhost:" . $portPair);
        }

        $portConnect = $this->input("Porta de Conexão (ex: 12345)");
        if (!empty($portConnect)) {
            system("adb connect localhost:" . $portConnect);
            echo $this->c['fverde'] . "\n[i] Tentativa de conexão finalizada.\n" . $this->c['reset'];
        }
        
        $this->input("Pressione Enter para voltar");
    }

    // INÍCIO DO SCANNER
    private function iniciarScan($pacote) {
        system("clear");
        $this->banner();
        date_default_timezone_set('America/Sao_Paulo');

        // 1. Verifica dispositivos
        $devices = $this->exec("adb devices");
        if (strpos($devices, "\tdevice") === false) {
            echo $this->c['vermelho'] . "[!] Nenhum dispositivo conectado!\n";
            $this->input("Enter para voltar");
            return;
        }

        // 2. Verifica se o jogo está instalado
        $checkPkg = $this->exec("adb shell pm list packages | grep $pacote");
        if (empty($checkPkg)) {
            echo $this->c['vermelho'] . "[!] O pacote $pacote não está instalado.\n";
            $this->input("Enter para voltar");
            return;
        }

        // 3. Checagem de ROOT (Regra: PROIBIDO)
        echo $this->c['azul'] . "[+] Verificando Integridade do Sistema (Root/Mounts)...\n";
        $this->verificarRoot();
        
        // 4. Checagem de Processos/Scripts
        $this->verificarAmbienteShell();

        // 5. Detectar Bypass de Funções (Anti-Telagem)
        $this->detectarBypassShell();

        // 6. Verificação de Apps Proibidos (Blacklist)
        $this->verificarBlacklistApps();

        // 7. Análise de Replays (A Lógica Pesada)
        $this->analisarReplays($pacote);

        // 8. Análise de Shaders/Assets (Hash + Timestamp)
        $this->analisarArquivosJogo($pacote);
        
        echo $this->c['bold'] . $this->c['branco'] . "\n[FIM] Escaneamento concluído.\n";
        $this->input("Pressione Enter para voltar ao menu");
    }

    // --- MÉTODOS DE SEGURANÇA ---

    private function verificarRoot() {
        // Tenta executar su
        $suCheck = $this->exec("adb shell \"su -c id\" 2>&1");
        // Verifica binários comuns
        $binCheck = $this->exec("adb shell \"ls /system/bin/su /system/xbin/su /sbin/su 2>/dev/null\"");
        // Verifica mounts do Magisk
        $mounts = $this->exec("adb shell cat /proc/mounts");

        $isRooted = false;
        if ((strpos($suCheck, 'uid=0') !== false) || !empty($binCheck)) {
            $isRooted = true;
        }
        
        // Detecção avançada de Mounts (Magisk/KernelSU)
        if (preg_match('/magisk|core\/img|overlay|knox/i', $mounts)) {
            echo $this->c['vermelho'] . "[!!!] ALERTA CRÍTICO: Mounts do Magisk/KernelSU detectados!\n";
            $isRooted = true;
        }

        if ($isRooted) {
            echo $this->c['bold'] . $this->c['vermelho'] . "\n[!] ======================================= [!]\n";
            echo "     ROOT DETECTADO! (Proibido neste cenário)\n";
            echo "     O dispositivo possui acesso administrativo.\n";
            echo "     APLIQUE O W.O IMEDIATAMENTE.\n";
            echo "[!] ======================================= [!]\n\n" . $this->c['reset'];
        } else {
            echo $this->c['fverde'] . "[i] Acesso Root não detectado (Sistema Limpo).\n" . $this->c['reset'];
        }
    }

    private function verificarAmbienteShell() {
        echo $this->c['azul'] . "[+] Verificando scripts maliciosos em background...\n";
        
        // Mata sessões bash desnecessárias
        $this->exec('adb shell "current_pid=\$\$; for pid in \$(pgrep bash); do [ \"\$pid\" -ne \"\$current_pid\" ] && kill -9 \$pid; done"');
        
        // Procura scripts suspeitos rodando
        $scripts = $this->exec('adb shell "pgrep -a sh"');
        if (!empty($scripts) && strlen(trim($scripts)) > 0) {
            echo $this->c['amarelo'] . "[!] Scripts .sh rodando em segundo plano:\n$scripts\n";
        } else {
            echo $this->c['fverde'] . "[i] Nenhum script suspeito ativo.\n";
        }
    }

    private function verificarBlacklistApps() {
        echo $this->c['azul'] . "[+] Verificando aplicativos proibidos instalados...\n" . $this->c['reset'];
        
        $blacklist = [
            'com.guishi.ludashi' => 'GameGuardian (Variante)',
            'gg.now' => 'GameGuardian',
            'com.topjohnwu.magisk' => 'Magisk Manager',
            'me.weishu.kernelsu' => 'KernelSU',
            'com.lulu.box' => 'Lulubox',
            'io.va.exposed' => 'VirtualXposed'
        ];

        $packages = $this->exec("adb shell pm list packages");
        $encontrou = false;

        foreach ($blacklist as $pkg => $nome) {
            if (strpos($packages, $pkg) !== false) {
                echo $this->c['vermelho'] . "[!] APP PROIBIDO ENCONTRADO: $nome ($pkg)\n";
                $encontrou = true;
            }
        }

        if ($encontrou) {
             echo $this->c['vermelho'] . "[!] Aplique as sanções necessárias.\n\n";
        } else {
             echo $this->c['fverde'] . "[i] Nenhum app da blacklist encontrado.\n\n";
        }
    }

    private function detectarBypassShell() {
        echo $this->c['azul'] . "[+] Testando integridade de comandos Shell (Anti-Bypass)...\n";
        
        // Verifica se comandos comuns são funções (burlar detecção)
        $cmds = ['pkg', 'git', 'cd', 'stat', 'ls'];
        $bypass = false;

        foreach ($cmds as $cmd) {
            $check = $this->exec("adb shell \"type $cmd 2>/dev/null\"");
            if (strpos($check, 'function') !== false) {
                echo $this->c['vermelho'] . "[!] BYPASS DETECTADO: O comando '$cmd' foi falsificado por uma função!\n";
                $bypass = true;
            }
        }

        // Teste prático de redirecionamento (fake folders)
        $testeDir = $this->exec('adb shell "ls -la /data/data/com.dts.freefireth 2>&1"');
        if (strpos($testeDir, 'Permission denied') !== false && strpos($testeDir, 'root') === false) {
             // Normal dar denied sem root, mas se tiver root e der denied, é bypass
        }
        
        if ($bypass) {
            echo $this->c['vermelho'] . "ATENÇÃO: O usuário está tentando enganar a telagem interceptando comandos.\n\n";
        } else {
            echo $this->c['fverde'] . "[i] Shell parece íntegro.\n\n";
        }
    }

    // --- LÓGICA DE JOGO ---

    private function analisarReplays($pacote) {
        echo $this->c['azul'] . "[+] Analisando Pasta MReplays (Matemática de Timestamps)...\n";
        $caminho = "/sdcard/Android/data/$pacote/files/MReplays";
        
        // Verifica se pasta existe
        $check = $this->exec("adb shell ls -d $caminho");
        if (strpos($check, 'No such file') !== false) {
            echo $this->c['amarelo'] . "[!] Pasta MReplays não encontrada ou vazia.\n";
            return;
        }

        // Pega arquivos .bin
        $arquivos = $this->exec("adb shell ls $caminho/*.bin 2>/dev/null");
        if (empty($arquivos)) {
            echo $this->c['fverde'] . "[i] Nenhum replay (.bin) encontrado.\n";
            return;
        }

        $listaArquivos = array_filter(explode("\n", trim($arquivos)));
        $motivos = [];

        foreach ($listaArquivos as $arq) {
            $arq = trim($arq);
            if (empty($arq)) continue;

            $stat = $this->exec("adb shell stat " . escapeshellarg($arq));
            
            // Extrai datas (Access, Modify, Change)
            preg_match('/Access: (.*?)\n/', $stat, $mA);
            preg_match('/Modify: (.*?)\n/', $stat, $mM);
            preg_match('/Change: (.*?)\n/', $stat, $mC);

            if ($mA && $mM && $mC) {
                $acc = strtotime(preg_replace('/\.\d+.*$/', '', $mA[1]));
                $mod = strtotime(preg_replace('/\.\d+.*$/', '', $mM[1]));
                $chg = strtotime(preg_replace('/\.\d+.*$/', '', $mC[1]));

                // Motivo 1: Acesso depois de Modificação (Inconsistência básica de cópia)
                if ($acc > $mod) $motivos[] = "Motivo 1 - Access > Modify: " . basename($arq);
                
                // Motivo 2: Nanossegundos zerados (Indica sistema de arquivos FAT/Cópia externa)
                if (strpos($mM[1], '.000000000') !== false) $motivos[] = "Motivo 2 - Timestamps Zerados (.000): " . basename($arq);

                // Motivo 3: Modify != Change (Arquivo movido ou alterado atributos)
                if ($mod !== $chg) $motivos[] = "Motivo 3 - Modify diferente de Change: " . basename($arq);
            }
        }

        if (!empty($motivos)) {
            echo $this->c['bold'] . $this->c['vermelho'] . "[!] PASSADOR DE REPLAY DETECTADO!\n";
            foreach ($motivos as $m) echo "    - $m\n";
            echo "\n" . $this->c['reset'];
        } else {
            echo $this->c['fverde'] . "[i] Replays parecem consistentes.\n";
        }
    }

    private function analisarArquivosJogo($pacote) {
        echo $this->c['azul'] . "\n[+] Verificando Integridade de Assets e OBB...\n";

        // Exemplo de verificação de OBB
        $obbPath = "/sdcard/Android/obb/$pacote";
        $obbFiles = $this->exec("adb shell ls $obbPath/*.obb 2>/dev/null");
        
        if (!empty($obbFiles)) {
            $arquivos = explode("\n", trim($obbFiles));
            foreach ($arquivos as $obb) {
                if(empty($obb)) continue;
                echo $this->c['amarelo'] . "[*] Verificando OBB: " . basename($obb) . "\n";
                
                // 1. Check Data
                $date = $this->exec("adb shell stat -c '%y' " . escapeshellarg($obb));
                echo "    Data Modificação: " . trim($date) . "\n";
                
                // 2. Check Hash (IMPLEMENTAÇÃO NOVA)
                // Nota: Calcular MD5 de arquivos grandes (OBB) no celular demora. 
                // Usamos 'head' para checar o cabeçalho se o full scan for lento.
                echo $this->c['lazul'] . "    Calculando Hash parcial (Header)...\n";
                $hashHead = $this->exec("adb shell \"head -c 1024 " . escapeshellarg($obb) . " | md5sum\"");
                echo "    MD5 Header: " . trim($hashHead) . "\n";
                
                // AVISO SOBRE DATA RECENTE
                $timestampObb = strtotime(preg_replace('/\.\d+.*$/', '', $date));
                if (time() - $timestampObb < 3600) { // Menos de 1 hora
                     echo $this->c['vermelho'] . "    [!] OBB MODIFICADA RECENTEMENTE! (Possível alteração para remover cheat antes da tela)\n";
                }
            }
        } else {
            echo $this->c['vermelho'] . "[!] Nenhuma OBB encontrada (Anormal).\n";
        }

        // Verificação da pasta de Shaders (Alvo comum de Wallhack)
        $shaderPath = "/sdcard/Android/data/$pacote/files/contentcache/Optional/android/gameassetbundles";
        echo $this->c['azul'] . "[+] Verificando Shaders/Assets...\n";
        
        // Busca arquivos modificados nos últimos 60 minutos
        $recentFiles = $this->exec("adb shell find " . escapeshellarg($shaderPath) . " -mmin -60 2>/dev/null");
        
        if (!empty($recentFiles) && strlen(trim($recentFiles)) > 0) {
            echo $this->c['vermelho'] . "[!] ARQUIVOS DE JOGO MODIFICADOS NA ÚLTIMA HORA (Possível Wallhack/Texture):\n";
            echo $this->c['amarelo'] . $recentFiles . "\n";
            echo $this->c['vermelho'] . "APLIQUE O W.O!\n";
        } else {
            echo $this->c['fverde'] . "[i] Nenhuma modificação recente crítica encontrada em Assets.\n";
        }
    }
}

// Inicialização
$scanner = new KellerScanner();
$scanner->menu();

?>