<?php
/**
 * Gerador de Hash de Senhas para PHP (password_hash / Bcrypt & Argon2)
 *
 * Ferramenta utilitária web para geração de hashes de senhas seguros utilizando
 * as funções nativas do PHP (password_hash).
 *
 * @author Deivid Viquiato Pereira (VKT CLOUD / VKT Sistemas)
 * @license MIT
 */

// Pré-configurações
$senhaOriginal = '';
$algoritmo = 'PASSWORD_DEFAULT';
$cost = 10;
$hashGerado = '';
$tempoExecucao = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $senhaOriginal = $_POST['senha'] ?? '';
    $algoritmo = $_POST['algoritmo'] ?? 'PASSWORD_DEFAULT';
    $cost = isset($_POST['cost']) ? (int)$_POST['cost'] : 10;

    if (!empty($senhaOriginal)) {
        $inicio = microtime(true);

        $algoConst = PASSWORD_DEFAULT;
        if ($algoritmo === 'PASSWORD_BCRYPT') {
            $algoConst = PASSWORD_BCRYPT;
        } elseif (defined('PASSWORD_ARGON2ID') && $algoritmo === 'PASSWORD_ARGON2ID') {
            $algoConst = PASSWORD_ARGON2ID;
        }

        $options = [];
        if ($algoConst === PASSWORD_BCRYPT || $algoConst === PASSWORD_DEFAULT) {
            $options['cost'] = max(4, min(15, $cost));
        }

        $hashGerado = password_hash($senhaOriginal, $algoConst, $options);
        $fim = microtime(true);
        $tempoExecucao = number_format(($fim - $inicio) * 1000, 2);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerador de Hash de Senhas - PHP Password Hash Generator</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Estilização suave de barras de rolagem e transições */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 font-sans min-h-screen flex flex-col justify-between">

    <!-- CABEÇALHO -->
    <header class="bg-gradient-to-r from-emerald-700 via-teal-800 to-slate-900 py-8 px-4 text-center shadow-xl border-b border-slate-700/50 relative">
        <div class="max-w-3xl mx-auto">
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-3">
                <i class="fa-solid fa-shield-halved"></i> Ferramenta Dev / Segurança PHP
            </span>
            <h1 class="text-3xl md:text-4xl font-black tracking-wide text-white drop-shadow">
                <i class="fa-solid fa-key text-emerald-400 mr-2"></i> PHP Hash Generator
            </h1>
            <p class="text-slate-300 mt-2 text-sm md:text-base font-medium">
                Gere hashes seguros utilizando as funções nativas <code class="bg-slate-800 px-2 py-0.5 rounded text-emerald-400 font-mono text-xs">password_hash()</code> para seus projetos.
            </p>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="max-w-xl w-full mx-auto my-8 px-4 flex-1">
        
        <div class="bg-slate-800 rounded-2xl shadow-2xl p-6 md:p-8 border border-slate-700">
            <form method="POST" action="" class="space-y-5">
                
                <!-- INPUT DA SENHA -->
                <div>
                    <label for="senha" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2 flex items-center gap-2">
                        <i class="fa-solid fa-lock text-emerald-400"></i> Senha para Criptografar
                    </label>
                    <div class="relative">
                        <input type="password" id="senha" name="senha" required 
                               value="<?php echo htmlspecialchars($senhaOriginal); ?>"
                               placeholder="Digite a senha desejada..."
                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-3 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-all font-mono text-sm pr-12">
                        <button type="button" onclick="alternarVisibilidadeSenha()" 
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-400 p-2 transition-colors"
                                title="Mostrar / Ocultar Senha">
                            <i id="iconeOlho" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- OPÇÕES AVANÇADAS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <!-- Algoritmo -->
                    <div>
                        <label for="algoritmo" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-microchip text-slate-500"></i> Algoritmo
                        </label>
                        <select id="algoritmo" name="algoritmo" 
                                class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 font-medium">
                            <option value="PASSWORD_DEFAULT" <?php echo $algoritmo === 'PASSWORD_DEFAULT' ? 'selected' : ''; ?>>BCRYPT (Default)</option>
                            <option value="PASSWORD_BCRYPT" <?php echo $algoritmo === 'PASSWORD_BCRYPT' ? 'selected' : ''; ?>>BCRYPT Explícito</option>
                            <?php if (defined('PASSWORD_ARGON2ID')): ?>
                                <option value="PASSWORD_ARGON2ID" <?php echo $algoritmo === 'PASSWORD_ARGON2ID' ? 'selected' : ''; ?>>ARGON2ID</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Custo Bcrypt -->
                    <div>
                        <label for="cost" class="block text-xs font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-sliders text-slate-500"></i> Custo Bcrypt (4-15)
                        </label>
                        <input type="number" id="cost" name="cost" min="4" max="15" value="<?php echo $cost; ?>"
                               class="w-full bg-slate-900 border border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-emerald-500 font-mono">
                    </div>
                </div>

                <!-- BOTAO SUBMIT -->
                <button type="submit" 
                        class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-black text-sm uppercase tracking-wider py-3.5 px-4 rounded-xl shadow-lg transition-all transform active:scale-[0.99] flex items-center justify-center gap-2 mt-4">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> Gerar Hash Seguro
                </button>
            </form>

            <?php if (!empty($hashGerado)): ?>
                <!-- RESULTADO DO HASH -->
                <div class="mt-8 pt-6 border-t border-slate-700/80 space-y-4 animate-fadeIn">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                            <i class="fa-solid fa-check-circle"></i> Hash Gerado com Sucesso
                        </span>
                        <?php if ($tempoExecucao !== null): ?>
                            <span class="text-[11px] text-slate-400 font-mono bg-slate-900 px-2 py-0.5 rounded border border-slate-700">
                                <i class="fa-solid fa-stopwatch text-slate-500"></i> <?php echo $tempoExecucao; ?> ms
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- CAMPO DO HASH E BOTÃO COPIAR -->
                    <div class="flex flex-col sm:flex-row items-stretch gap-2 bg-slate-900 p-3 rounded-xl border border-slate-700">
                        <textarea id="hashResultInput" readonly rows="2" 
                                  class="bg-transparent text-xs font-mono text-emerald-300 flex-1 outline-none resize-none break-all leading-relaxed p-1"><?php echo htmlspecialchars($hashGerado); ?></textarea>
                        
                        <button onclick="copiarHash()" id="btnCopiarHash" 
                                class="bg-emerald-600 hover:bg-emerald-500 text-slate-950 text-xs font-black px-4 py-2 rounded-lg transition-all flex items-center justify-center gap-1.5 shrink-0 shadow">
                            <i class="fa-solid fa-copy"></i> Copiar
                        </button>
                    </div>

                    <!-- DICA SQL PARA PRÓXIMOS PROJETOS -->
                    <div class="bg-slate-900/60 p-3.5 rounded-xl border border-slate-700/60 space-y-2">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block flex items-center gap-1.5">
                            <i class="fa-solid fa-database text-slate-500"></i> Comando SQL Exemplo (MySQL)
                        </span>
                        <div class="flex items-center justify-between bg-slate-950 p-2 rounded-lg border border-slate-800">
                            <code class="text-[11px] font-mono text-slate-300 truncate">
                                UPDATE usuarios SET senha = '<?php echo htmlspecialchars($hashGerado); ?>' WHERE usuario = 'admin';
                            </code>
                            <button onclick="copiarSql()" id="btnCopiarSql" class="text-slate-400 hover:text-emerald-400 text-xs px-2 py-1 shrink-0" title="Copiar SQL">
                                <i class="fa-solid fa-copy"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>

    </main>

    <!-- RODAPÉ COMPATÍVEL COM REPOSITÓRIO GITHUB -->
    <footer class="py-6 px-4 text-center text-xs text-slate-500 border-t border-slate-800 bg-slate-950/60 space-y-2">
        <p class="flex items-center justify-center gap-2">
            <i class="fa-brands fa-github text-slate-400 text-sm"></i>
            <span>Projeto utilitário aberto para GitHub</span>
            <span>•</span>
            <span class="text-slate-400">Licença MIT</span>
        </p>
        <p class="text-[11px] text-slate-600">Desenvolvido para ambientes PHP 7.4+ e PHP 8.x com suporte nativo a <code class="text-slate-500 font-mono">password_verify()</code></p>
    </footer>

    <!-- SCRIPTS INTERATIVOS -->
    <script>
        function alternarVisibilidadeSenha() {
            const input = document.getElementById('senha');
            const icone = document.getElementById('iconeOlho');
            if (input.type === 'password') {
                input.type = 'text';
                icone.classList.remove('fa-eye');
                icone.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icone.classList.remove('fa-eye-slash');
                icone.classList.add('fa-eye');
            }
        }

        function copiarHash() {
            const hashArea = document.getElementById('hashResultInput');
            const btn = document.getElementById('btnCopiarHash');
            
            navigator.clipboard.writeText(hashArea.value).then(() => {
                btn.innerHTML = `<i class="fa-solid fa-check"></i> Copiado!`;
                btn.classList.remove('bg-emerald-600', 'hover:bg-emerald-500');
                btn.classList.add('bg-teal-400');

                setTimeout(() => {
                    btn.innerHTML = `<i class="fa-solid fa-copy"></i> Copiar`;
                    btn.classList.remove('bg-teal-400');
                    btn.classList.add('bg-emerald-600', 'hover:bg-emerald-500');
                }, 2000);
            });
        }

        function copiarSql() {
            const hash = document.getElementById('hashResultInput').value;
            const sql = `UPDATE usuarios SET senha = '${hash}' WHERE usuario = 'admin';`;
            const btn = document.getElementById('btnCopiarSql');

            navigator.clipboard.writeText(sql).then(() => {
                btn.innerHTML = `<i class="fa-solid fa-check text-emerald-400"></i>`;
                setTimeout(() => {
                    btn.innerHTML = `<i class="fa-solid fa-copy"></i>`;
                }, 2000);
            });
        }
    </script>
</body>
</html>