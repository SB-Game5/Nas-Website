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
    
</head>
<body class="bg-black text-gray min-h-screen flex flex-col p-4 gap-4">
    @php
    $currentUser = Auth::user();
@endphp
<div>
    <h1 class="text-white"> Bien le bonjour !</h1>
    <h2 class="text-white"> Bienvenue sur le NAS-B, <span class="text-blue">{{ $currentUser->name}}</span>  !</h2>
    <button type="button" onclick="toggleModal('my-modal-logout')" class="text-white bg-red-600 hover:bg-purple-700">
           Déconnexion
    </button>
    <div id="my-modal-logout" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm">
    
        <div class="bg-gray-900 border border-white -xl p-6 w-full max-w-lg  relative mx-4 ">
            <div class="text-sm text-gray-300 space-y-4">
                @include('logout')
            </div>
        </div>
    </div>
</div>
<!-- // Put the two square side by side, below the first div -->
<div class="flex gap-4 flex-1"> 
    <div class="flex-1 bg-gray-800 border border-gray-700 -xl p-6 overflow-y-auto ">
        <div >
                        @include('Onglets.FilesTree')
                    </div>
    </div>

    <div class="flex-1 bg-gray-800 border border-gray-700 -xl p-6 overflow-y-auto shadow-lg">
        
        @php
            // 1. On appelle d'abord l'action pour charger les rôles depuis le NAS
            $action = app(App\Actions\ListUserAction::class);
            $isCurrentUserAdmin = false;
            
            try {
                $users = $action->execute();
                
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

            $tabs = [
                'history' => 'Historique',
                'dm' => 'Messages',
            ];
            if (!$isCurrentUserAdmin) {
                $tabs['files-info'] = 'Informations ';
            }
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
                    <div class="p-4 bg-red-900/20 border border-red-500 text-red-200">
                        Onglet en cours de développement.
                    </div>
            @endswitch

        </div>
    </div>
</div>
</body>
</html>