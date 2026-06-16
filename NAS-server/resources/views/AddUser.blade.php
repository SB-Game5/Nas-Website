<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un utilisateur NAS</title>
    <script src="https://cdn.tailwindcss.com"></script> </head>
<body>

    <div>
        <h1>Créer User</h1>

        @if(session('success'))
            <div class="text-green-600">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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

            <div>
                <label for="shell">Shell par défaut</label>
                <select name="shell" id="shell" 
                    >
                    <option value="/bin/bash">/bin/bash (Recommandé)</option>
                    <option value="/bin/sh">/bin/sh</option>
                    <option value="/sbin/nologin">/sbin/nologin (Pas d'accès SSH)</option>
                </select>
            </div>

            <button type="submit" onclick="this.disabled=true; this.form.submit();" 
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md shadow-sm transition duration-150 ease-in-out mt-4">
                Envoyer
            </button>
        </form>
    </div>

</body>
</html>