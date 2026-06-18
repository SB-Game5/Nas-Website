<div>
<button type="button" onclick="toggleModal('my-modal')" class="bg-green-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition">
    Ajouter un nouvel utilisateur
</button>
<h1> </h1>

<div id="my-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    
    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg shadow-2xl relative mx-4">

        <div class="text-sm text-gray-300 space-y-4">
           @include('Onglets.Users.AddUser')
        </div>
    </div>
</div>

<script>
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }
</script>
<div id="my-modal-del" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
    
    <div class="bg-gray-900 border border-gray-700 rounded-xl p-6 w-full max-w-lg shadow-2xl relative mx-4">
        <div class="text-sm text-gray-300 space-y-4">
           @include('Onglets.Users.DeleteUser')
        </div>
    </div>
</div>
<script>
    function toggleModalDel(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.toggle('hidden');
        }
    }

    function getusername(button, modalId) {
        const row = button.closest('tr');
        const username = row?.cells[0]?.textContent?.trim() || '';
        const target = document.getElementById('delete-user-name');
        const hiddenInput = document.getElementById('delete-username-input');

        if (target) {
            target.textContent = username;
        }

        if (hiddenInput) {
            hiddenInput.value = username;
        }

        toggleModalDel(modalId);
    }
</script>


<div class="w-full space-y-4">

    @php
        $action = app(App\Actions\ListUserAction::class);
        
        try {
            $users = $action->execute();
        } catch (\Exception $e) {
            $users = [];
            $errorMessage = $e->getMessage();
        }
    @endphp

    {{-- En cas d'erreur SSH --}}
    @if(isset($errorMessage))
        <div class="p-4 bg-red-900/40 border border-red-500 text-red-200 rounded-lg text-sm">
            {{ $errorMessage }}
        </div>
    @endif
    <div class="overflow-x-auto rounded-xl border border-gray-800 shadow-md">
        <table class="w-full text-sm text-left text-gray-300 bg-gray-900">
            <thead class="text-xs uppercase bg-gray-800 text-gray-400 border-b border-gray-700">
            </thead>
            
            <tbody class="divide-y divide-gray-800">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-800/50 transition">
                        <td class="px-6 py-4 font-medium text-gray-100">
                            {{ $user }}
                        </td>
                        <td class="px-6 py-4"></td>
                        <td class="px-6 py-4">
                           <button type="button" onclick="getusername(this, 'my-modal-del')" class="bg-red-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md transition">
                                Supprimer
                            </button>
                        </td>
                    </tr>
                @empty
                    @if(!isset($errorMessage))
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">
                                Aucun utilisateur Samba trouvé sur le serveur.
                            </td>
                        </tr>
                    @endif
                @endforelse
            </tbody>
        </table>
    </div>

</div>