<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NAS-B</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }
</script>
    <h1>
    <button type="button" onclick="toggleModal('my-modal-logout')" class="bg-red-600 hover:bg-purple-700">
           Déconnexion
    </button>
    <div id="my-modal-logout" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    
        <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg shadow-2xl relative mx-4">
            <div class="text-sm text-gray-300 space-y-4">
                @include('logout')
            </div>
        </div>
    </div>
    </h1>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen flex p-4 gap-4">

    <div class="w-64 bg-gray-800 rounded-xl shadow-lg border border-gray-700 p-6 flex flex-col">
        <div class="p-4 bg-red-900/20 border border-red-500 rounded text-red-200">
                        Onglet en cours de développement.
                    </div>
    </div>

    <div class="flex-1 bg-gray-800 rounded-xl shadow-lg border border-gray-700 flex flex-col overflow-hidden">
        
        @php
            // 1. On appelle d'abord l'action pour charger les rôles depuis le NAS
            $action = app(App\Actions\ListUserAction::class);
            $isCurrentUserAdmin = false;
            
            try {
                $users = $action->execute();
                
                // 2. On vérifie si l'utilisateur connecté est admin du NAS
                $currentUser = Auth::user();
                if ($currentUser) {
                    foreach ($users as $u) {
                        if (isset($u['username']) && $u['username'] === $currentUser->username && isset($u['is_admin']) && $u['is_admin']) {
                            $isCurrentUserAdmin = true;
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                $users = [];
                $errorMessage = $e->getMessage();
            }

            // 3. Onglets de base visibles par tout le monde
            $tabs = [
                'files-info' => 'Informations ',
                'history' => 'Historique',
                'dm' => 'Messages',
            ];

            // 4. Si l'utilisateur est admin, on ajoute les onglets d'administration
            if ($isCurrentUserAdmin) {
                $tabs['files-perm'] = 'Permissions';
                $tabs['users']      = 'Utilisateurs';
                $tabs['site-info']  = 'Autres';
            }

            // 5. Définir l'onglet actif (si l'utilisateur tape un onglet admin alors qu'il ne l'est pas, on le remet sur 'files-info')
            $requestedTab = request('tab', $isCurrentUserAdmin ? 'users' : 'files-info');
            $activeTab = array_key_exists($requestedTab, $tabs) ? $requestedTab : 'files-info';
        @endphp

        <div class="flex bg-gray-850 border-b border-gray-700 overflow-x-auto whitespace-nowrap scrollbar-none">
            @foreach($tabs as $slug => $label)
                <a href="?tab={{ $slug }}" 
                   class="px-6 py-3.5 text-sm font-medium border-b-2 transition-colors duration-150
                   {{ $activeTab === $slug 
                       ? 'border-blue-500 text-blue-400 bg-gray-800/50' 
                       : 'border-transparent text-gray-400 hover:text-gray-200 hover:bg-gray-800/30' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <div class="flex-1 p-8 overflow-y-auto bg-gray-800/40">
            
            @switch($activeTab)
                @case('files-info')
                    @include('Onglets.FilesInfo')
                    @break
                @case('history')
                    @include('Onglets.History')
                    @break
                @case('dm')
                    @include('Onglets.DM')
                    @break
                @case('files-perm')
                    @include('Onglets.FilesPerm')
                    @break
                @case('users')
                    @include('Onglets.UsersList')
                    @break
                @case('site-info')
                    @include('Onglets.SiteInfo')
                    @break
                @default
                    <div class="p-4 bg-red-900/20 border border-red-500 rounded text-red-200">
                        Onglet en cours de développement.
                    </div>
            @endswitch

        </div>
    </div>

</body>
</html>