<div class="text-sm text-white space-y-4">
    <form action="{{ route('logout') }}" method="POST" class="inline">
        @csrf
        <p>
            Confirmer la déconnexion ?
        </p>
        <button type="button" onclick="toggleModalDel('my-modal-logout')" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-2  text-sm">
            Non
        </button>
        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 transition focus:ring-4 focus:ring-red-900 focus:outline-none">
            <svg class="w-4 h-4 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            oui
        </button>
    </form>
</div>