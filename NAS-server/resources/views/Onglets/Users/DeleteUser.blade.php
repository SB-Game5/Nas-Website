<div class="text-sm text-gray-300 space-y-4">
     <form action="{{ route('users.remove') }}" method="POST" class="space-y-4"
           onsubmit="document.getElementById('delete-username-input').value = document.getElementById('delete-user-name').textContent.trim();">
        @csrf
        <input type="hidden" name="username" id="delete-username-input" value="">
        <p>
            Êtes-vous sûr de vouloir supprimer <span id="delete-user-name"></span> ?
        </p>
        <button type="button" onclick="toggleModalDel('my-modal-del')" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-2 rounded-md text-sm">
            Non
        </button>
        <button type="submit" class="bg-red-800 hover:bg-gray-700 text-gray-300 px-4 py-2 rounded-md text-sm">
            Oui
        </button>
    </form>
</div>