<div>
@php

try {
        $username = auth()->user()?->username ?? 'guest';
        $selectedPath = request('selected_path', '/ServeurSB');
        $myDto = new App\DTOs\ListInfoDTO($username, $selectedPath);
        $statResult = app(App\Actions\ListInfoAction::class)->execute($myDto);
        
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
    }
@endphp
@if(isset($errorMessage))
        <div class="p-4 mb-4 bg-red-900/40 border border-red-500 text-red-200 text-sm rounded-xl">
            <span class="font-bold">Erreur système :</span> {{ $errorMessage }}
        </div>
    @endif
    @if(isset($statResult))
        <div class="overflow-x-auto border border-gray-800 shadow-md rounded-xl">
            <pre class="bg-gray-900 text-green-400 p-4 font-mono text-xs whitespace-pre-wrap">{{ $statResult['stat'] ?? 'Aucune donnée disponible' }}</pre>
        </div>

        <div class="mt-4 flex gap-2 text-xs font-bold">
            <span class="px-2 py-1 rounded {{ ($statResult['can_read'] ?? false) ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                Lecture : {{ ($statResult['can_read'] ?? false) ? 'OUI' : 'NON' }}
            </span>
            <span class="px-2 py-1 rounded {{ ($statResult['can_write'] ?? false) ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                Écriture : {{ ($statResult['can_write'] ?? false) ? 'OUI' : 'NON' }}
            </span>
            <span class="px-2 py-1 rounded {{ ($statResult['can_execute'] ?? false) ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                Exécution : {{ ($statResult['can_execute'] ?? false) ? 'OUI' : 'NON' }}
            </span>
        </div>
    @endif