@php
    $treeAction = app(App\Actions\FileTreeAction::class);
    
    $nasPath = '/ServeurSB'; 

    try {
        $fileTreeHtml = $treeAction->execute($nasPath);
    } catch (\Exception $e) {
        $fileTreeHtml = "<p class='text-red-400 p-4'>Erreur SSH : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
@endphp

<div class="p-6 bg-gray-900 border border-gray-700 shadow-lg flex flex-col h-full">
    <h2 class="text-xl font-bold text-gray-100 mb-2 border-b border-gray-700 pb-2">Explorateur NAS (SSH)</h2>
    
    <div class="mb-4 p-2.5 bg-gray-800 border border-gray-700 text-xs text-gray-400 font-mono overflow-x-auto whitespace-nowrap">
        Sélection : <span id="selected-path-display" class="text-blue-400 font-bold">Aucun élément sélectionné</span>
    </div>

    <div id="pft-container" class="text-sm overflow-y-auto max-h-[500px]">
        {!! $fileTreeHtml !!}
    </div>
</div>

<script>

var currentSelectedPath= localStorage.getItem('nas_selected_path') || null; //stock the selected path internally
    function init_interactive_file_tree() {
    const container = document.getElementById("pft-container");
    if (!container) return;
    var allSubTrees = container.getElementsByTagName("UL");
    for (var k = 0; k < allSubTrees.length; k++) {
        allSubTrees[k].classList.remove('hidden');
        allSubTrees[k].style.display = "block";
    }
    if (currentSelectedPath) {
        document.getElementById('selected-path-display').textContent = currentSelectedPath;
        const savedLi = container.querySelector(`li[data-path="${CSS.escape(currentSelectedPath)}"]`);
        if (savedLi) {
            const targetHighlight = savedLi.querySelector('span') || savedLi;
            targetHighlight.classList.add('bg-blue-600/30', 'text-white', 'font-semibold', 'p-0.5');
        }
    }

    container.addEventListener('click', function(e) {
        const targetLi = e.target.closest('li[data-path]');
        if (!targetLi) return;
        
        e.stopPropagation();

        const clickedPath = targetLi.getAttribute('data-path');

        container.querySelectorAll('.bg-blue-600\\/30').forEach(el => {
            el.classList.remove('bg-blue-600/30', 'text-white', 'font-semibold', 'p-0.5');
        });

        if (e.target.tagName === 'SPAN' || e.target.tagName === 'LI') {
            const highlightElement = e.target.tagName === 'SPAN' ? e.target : e.target;
            highlightElement.classList.add('bg-blue-600/30', 'text-white', 'font-semibold', 'p-0.5');
        }

        currentSelectedPath = clickedPath;
        localStorage.setItem('nas_selected_path', currentSelectedPath);
        document.getElementById('selected-path-display').textContent = currentSelectedPath;
        

        const urlParams = new URLSearchParams(window.location.search);
        const currentTab = urlParams.get('tab') || 'files-info';
        urlParams.set('tab', currentTab); 
        urlParams.set('selected_path', currentSelectedPath); 
        
        window.location.search = urlParams.toString();
    });
    }

init_interactive_file_tree();
</script>

<style>
    .pft-directory { list-style-type: none; margin-top: 4px; }
    .pft-file { list-style-type: none; margin-top: 3px; }
    #pft-container ul { margin-left: 12px; border-left: 1px dashed #4b5563; padding-left: 8px; }
</style>