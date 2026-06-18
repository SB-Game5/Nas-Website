    <div>
        <h1>Créer User</h1>

        @if(request('success'))
            <div class="p-3 rounded mb-4 text-sm bg-green-900/40 border border-green-500 text-green-200">
                {{ request('success') }}
            </div>
        @endif

        @if(request('error'))
            <div class="p-3 rounded mb-4 text-sm bg-red-900/40 border border-red-500 text-red-200">
                {{ request('error') }}
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
            @csrf 

            <div>
                <label for="username" >Nom d'utilisateur</label>
                <input type="text" name="username" id="username" value="{{ old('username') }}" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border" 
                    placeholder="">
            </div>

            <div>
                <label for="password">Mot de passe Linux</label>
                <input type="password" name="password" id="password" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-2 border" 
                    placeholder="">
            </div>

            <button type="submit" onclick="this.disabled=true; this.form.submit();" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out mt-4">
                Envoyer
            </button>
            <button type="button" onclick="toggleModal('my-modal')" class="bg-gray-800 hover:bg-gray-700 text-gray-300 px-4 py-2 rounded-md text-sm">
                Fermer
            </button>
        </form>
    </div>

    