<?php
require __DIR__ . '/../src/autoload.php';

// Handle Source Code Request
if (isset($_GET['action']) && $_GET['action'] === 'source' && isset($_GET['file'])) {
    $file = basename($_GET['file']); // Security: prevent directory traversal
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        header('Content-Type: text/plain');
        echo file_get_contents($path);
    } else {
        http_response_code(404);
        echo "File not found.";
    }
    exit;
}

// Handle Run Request (Wrap output in HTML)
if (isset($_GET['action']) && $_GET['action'] === 'run' && isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $path = __DIR__ . '/' . $file;
    
    if (file_exists($path)) {
        ob_start();
        include $path;
        $output = ob_get_clean();
    } else {
        $output = "File not found.";
    }
    ?>
    <!DOCTYPE html>
    <html class="dark">
    <head>
        <meta charset="UTF-8">
        <script src="https://cdn.tailwindcss.com"></script>
        <script>
            tailwind.config = {
                darkMode: 'class',
                theme: {
                    extend: {
                        colors: {
                            slate: { 850: '#1e293b', 900: '#0f172a', 950: '#020617' }
                        },
                        fontFamily: { mono: ['JetBrains Mono', 'monospace'] }
                    }
                }
            }
            
            // Listen for theme changes from parent
            window.addEventListener('message', (event) => {
                if (event.data.type === 'theme') {
                    if (event.data.theme === 'dark') {
                        document.documentElement.classList.add('dark');
                    } else {
                        document.documentElement.classList.remove('dark');
                    }
                }
            });

            // Initial theme check (request from parent)
            window.parent.postMessage({ type: 'requestTheme' }, '*');
        </script>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500&display=swap');
            body { font-family: 'JetBrains Mono', monospace; }
        </style>
    </head>
    <body class="bg-white dark:bg-slate-950 text-slate-800 dark:text-slate-300 p-6 transition-colors duration-300 min-h-screen">
        <pre class="whitespace-pre-wrap"><?php echo htmlspecialchars($output); ?></pre>
    </body>
    </html>
    <?php
    exit;
}

$examples = [
    '01-basic-usage.php' => ['title' => 'Basic Usage', 'icon' => '⚡'],
    '02-filters.php' => ['title' => 'Filters', 'icon' => '🔧'],
    '03-async.php' => ['title' => 'Async Hooks', 'icon' => '🚀'],
];

$currentFile = isset($_GET['file']) ? basename($_GET['file']) : '01-basic-usage.php';
if (!array_key_exists($currentFile, $examples)) {
    $currentFile = '01-basic-usage.php';
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hookx Examples</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/tokyo-night-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        slate: { 850: '#1e293b', 900: '#0f172a', 950: '#020617' },
                        primary: { 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #475569; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #64748b; }
    </style>
</head>
<body class="bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-300 h-screen flex flex-col overflow-hidden transition-colors duration-300">
    
    <!-- Header -->
    <header class="flex-none h-16 bg-white/80 dark:bg-slate-900/95 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 z-50">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-600 rounded-lg flex items-center justify-center shadow-lg shadow-primary-500/20">
                <span class="text-white font-bold text-lg">Hx</span>
            </div>
            <h1 class="text-lg font-bold text-slate-900 dark:text-white tracking-tight">Hookx <span class="font-normal text-slate-500 dark:text-slate-400">Examples</span></h1>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="../docs/" class="flex items-center gap-2 text-sm font-medium text-slate-600 dark:text-slate-400 hover:text-primary-600 dark:hover:text-primary-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="hidden sm:inline">Docs</span>
            </a>
            
            <a href="https://github.com/sponsors/AlizHarb" target="_blank" class="hidden sm:flex items-center gap-2 px-3 py-1.5 text-sm font-medium text-pink-600 bg-pink-50 hover:bg-pink-100 dark:text-pink-400 dark:bg-pink-900/20 dark:hover:bg-pink-900/30 rounded-full transition-colors border border-pink-200 dark:border-pink-800">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                <span>Sponsor</span>
            </a>

            <button id="theme-toggle" class="p-2 text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200 transition-colors">
                <svg id="icon-sun" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <svg id="icon-moon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
            </button>

            <a href="https://github.com/AlizHarb/hookx" target="_blank" class="text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
            </a>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-white dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800 overflow-y-auto hidden md:block">
            <div class="p-4">
                <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-4 px-2">Examples</h3>
                <nav class="space-y-1">
                    <?php foreach ($examples as $file => $info): ?>
                        <?php $active = $file === $currentFile; ?>
                        <a href="?file=<?php echo $file; ?>" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg transition-colors <?php echo $active ? 'bg-primary-50 dark:bg-primary-500/10 text-primary-600 dark:text-primary-400' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50 hover:text-slate-900 dark:hover:text-white'; ?>">
                            <span><?php echo $info['icon']; ?></span>
                            <span class="flex-1"><?php echo $info['title']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50 dark:bg-slate-900">
            <!-- Toolbar -->
            <div class="flex-none h-12 bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4">
                <div class="flex items-center gap-4">
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white"><?php echo $examples[$currentFile]['title']; ?></h2>
                    
                    <!-- Tabs -->
                    <div class="flex bg-slate-100 dark:bg-slate-800 rounded-lg p-1">
                        <button onclick="switchTab('preview')" id="tab-preview" class="px-3 py-1 text-xs font-medium rounded-md transition-all bg-white dark:bg-slate-700 text-slate-900 dark:text-white shadow-sm">Preview</button>
                        <button onclick="switchTab('code')" id="tab-code" class="px-3 py-1 text-xs font-medium rounded-md transition-all text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Code</button>
                    </div>
                </div>
                
                <a href="?file=<?php echo $currentFile; ?>" target="_blank" class="text-xs text-slate-500 hover:text-primary-600 flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Open in New Tab
                </a>
            </div>

            <!-- Content Area -->
            <div class="flex-1 relative overflow-hidden">
                <!-- Preview View -->
                <div id="view-preview" class="absolute inset-0 w-full h-full">
                    <iframe id="preview-frame" src="?action=run&file=<?php echo $currentFile; ?>" class="w-full h-full border-0 bg-white dark:bg-slate-950"></iframe>
                </div>

                <!-- Code View -->
                <div id="view-code" class="absolute inset-0 w-full h-full hidden bg-[#1a1b26] overflow-auto">
                    <pre><code class="language-php p-4 text-sm" id="code-content">Loading...</code></pre>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Theme Logic
        const themeToggle = document.getElementById('theme-toggle');
        const iconSun = document.getElementById('icon-sun');
        const iconMoon = document.getElementById('icon-moon');
        const html = document.documentElement;

        function updateTheme(isDark) {
            if (isDark) {
                html.classList.add('dark');
                iconSun.classList.remove('hidden');
                iconMoon.classList.add('hidden');
                localStorage.setItem('theme', 'dark');
            } else {
                html.classList.remove('dark');
                iconSun.classList.add('hidden');
                iconMoon.classList.remove('hidden');
                localStorage.setItem('theme', 'light');
            }
        }

        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        updateTheme(savedTheme === 'dark' || (!savedTheme && prefersDark));

        themeToggle.addEventListener('click', () => {
            updateTheme(!html.classList.contains('dark'));
        });

        // Sync theme with iframe
        const iframe = document.getElementById('preview-frame');
        
        function sendThemeToIframe() {
            const isDark = html.classList.contains('dark');
            iframe.contentWindow.postMessage({ 
                type: 'theme', 
                theme: isDark ? 'dark' : 'light' 
            }, '*');
        }

        // Listen for requests from iframe
        window.addEventListener('message', (event) => {
            if (event.data.type === 'requestTheme') {
                sendThemeToIframe();
            }
        });

        // Update iframe when theme changes
        const originalUpdateTheme = updateTheme;
        updateTheme = function(isDark) {
            originalUpdateTheme(isDark);
            if (iframe && iframe.contentWindow) {
                sendThemeToIframe();
            }
        }

        // Tab Logic
        const viewPreview = document.getElementById('view-preview');
        const viewCode = document.getElementById('view-code');
        const tabPreview = document.getElementById('tab-preview');
        const tabCode = document.getElementById('tab-code');
        let codeLoaded = false;

        async function switchTab(tab) {
            if (tab === 'preview') {
                viewPreview.classList.remove('hidden');
                viewCode.classList.add('hidden');
                
                tabPreview.classList.add('bg-white', 'dark:bg-slate-700', 'text-slate-900', 'dark:text-white', 'shadow-sm');
                tabPreview.classList.remove('text-slate-500', 'dark:text-slate-400');
                
                tabCode.classList.remove('bg-white', 'dark:bg-slate-700', 'text-slate-900', 'dark:text-white', 'shadow-sm');
                tabCode.classList.add('text-slate-500', 'dark:text-slate-400');
            } else {
                viewPreview.classList.add('hidden');
                viewCode.classList.remove('hidden');
                
                tabCode.classList.add('bg-white', 'dark:bg-slate-700', 'text-slate-900', 'dark:text-white', 'shadow-sm');
                tabCode.classList.remove('text-slate-500', 'dark:text-slate-400');
                
                tabPreview.classList.remove('bg-white', 'dark:bg-slate-700', 'text-slate-900', 'dark:text-white', 'shadow-sm');
                tabPreview.classList.add('text-slate-500', 'dark:text-slate-400');

                if (!codeLoaded) {
                    try {
                        const response = await fetch('?action=source&file=<?php echo $currentFile; ?>');
                        const text = await response.text();
                        const codeBlock = document.getElementById('code-content');
                        codeBlock.textContent = text;
                        hljs.highlightElement(codeBlock);
                        codeLoaded = true;
                    } catch (e) {
                        document.getElementById('code-content').textContent = 'Error loading source code.';
                    }
                }
            }
        }
    </script>
</body>
</html>
