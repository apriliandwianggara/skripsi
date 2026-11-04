@extends('layouts.admin')

@section('title', 'Kelola Pengguna & Hak Akses - Sekarsa Coffee')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-black text-gray-900 font-serif">Pengguna & Hak Akses</h1>
            <p class="text-xs text-gray-500 mt-0.5">Kelola akun Admin/Owner dan Kasir yang memiliki akses ke sistem.</p>
        </div>
        <button onclick="openCreateUserModal()" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-sm flex items-center space-x-1.5 self-start">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Akun Baru</span>
        </button>
    </div>

    <!-- User Table -->
    <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 uppercase font-bold">
                        <th class="py-3.5 px-4">Nama Lengkap</th>
                        <th class="py-3.5 px-4">Username & Email</th>
                        <th class="py-3.5 px-4">Peran (Role)</th>
                        <th class="py-3.5 px-4">Nomor HP</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-gray-50/70 transition">
                        <td class="py-3 px-4 font-bold text-gray-900">
                            <div class="flex items-center space-x-2.5">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span>{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4">
                            <span class="font-mono text-gray-800 block">{{ $user->username }}</span>
                            <span class="text-gray-400 block text-[11px]">{{ $user->email }}</span>
                        </td>
                        <td class="py-3 px-4">
                            <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase
                                {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $user->role === 'admin' ? 'Owner / Admin' : 'Kasir Utama' }}
                            </span>
                        </td>
                        <td class="py-3 px-4 text-gray-600 font-mono">
                            {{ $user->phone ?? '-' }}
                        </td>
                        <td class="py-3 px-4">
                            <span class="inline-flex items-center space-x-1 text-xs font-semibold {{ $user->is_active ? 'text-emerald-700' : 'text-red-600' }}">
                                <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                                <span>{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </span>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center space-x-1.5">
                                <button type="button" onclick="openEditUserModal({{ json_encode($user) }})" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Ubah">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus akun {{ $user->name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal User Form -->
<div id="userModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-gray-100 max-h-[90vh] overflow-y-auto">
        <h3 class="font-bold text-gray-900 text-base" id="userModalTitle">Tambah Akun Baru</h3>
        <p class="text-xs text-gray-500 mt-0.5">Akun untuk masuk ke dashboard Sekarsa Coffee.</p>

        <form id="userForm" action="{{ route('admin.users.store') }}" method="POST" class="mt-4 space-y-3.5">
            @csrf
            <div id="userMethodField"></div>

            <div>
                <label for="uName" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="uName" required placeholder="Contoh: Budi Santoso"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="uUsername" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" id="uUsername" required placeholder="kasir2"
                        class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs font-mono focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label for="uRole" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Peran (Role) <span class="text-red-500">*</span></label>
                    <select name="role" id="uRole" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="kasir">Kasir</option>
                        <option value="admin">Admin / Owner</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="uEmail" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="uEmail" required placeholder="kasir@sekarsacoffee.com"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="uPhone" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">No. WhatsApp / HP</label>
                <input type="text" name="phone" id="uPhone" placeholder="081234567890"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label for="uPassword" class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kata Sandi <span id="passReq" class="text-red-500">*</span></label>
                <input type="password" name="password" id="uPassword" placeholder="Minimal 6 karakter"
                    class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <p id="passHint" class="text-[10px] text-gray-400 mt-1 hidden">Kosongkan jika tidak ingin mengubah kata sandi.</p>
            </div>

            <div id="uStatusContainer" class="hidden pt-1">
                <label class="flex items-center space-x-2 cursor-pointer">
                    <input type="checkbox" name="is_active" id="uActive" value="1" checked class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                    <span class="text-xs font-semibold text-gray-700">Akun Aktif (Dapat Masuk)</span>
                </label>
            </div>

            <div class="pt-2 flex space-x-2">
                <button type="button" onclick="closeUserModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-xs transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl text-xs shadow-md transition">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openCreateUserModal() {
        document.getElementById('userModalTitle').innerText = 'Tambah Akun Baru';
        document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
        document.getElementById('userMethodField').innerHTML = '';
        document.getElementById('uName').value = '';
        document.getElementById('uUsername').value = '';
        document.getElementById('uEmail').value = '';
        document.getElementById('uPhone').value = '';
        document.getElementById('uRole').value = 'kasir';
        document.getElementById('uPassword').value = '';
        document.getElementById('uPassword').required = true;
        document.getElementById('passReq').classList.remove('hidden');
        document.getElementById('passHint').classList.add('hidden');
        document.getElementById('uStatusContainer').classList.add('hidden');
        document.getElementById('userModal').classList.remove('hidden');
    }

    function openEditUserModal(user) {
        document.getElementById('userModalTitle').innerText = 'Ubah Akun: ' + user.name;
        document.getElementById('userForm').action = "/admin/users/" + user.id;
        document.getElementById('userMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('uName').value = user.name;
        document.getElementById('uUsername').value = user.username;
        document.getElementById('uEmail').value = user.email;
        document.getElementById('uPhone').value = user.phone || '';
        document.getElementById('uRole').value = user.role;
        document.getElementById('uPassword').value = '';
        document.getElementById('uPassword').required = false;
        document.getElementById('passReq').classList.add('hidden');
        document.getElementById('passHint').classList.remove('hidden');
        document.getElementById('uActive').checked = user.is_active;
        document.getElementById('uStatusContainer').classList.remove('hidden');
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }
</script>
@endpush
@endsection
