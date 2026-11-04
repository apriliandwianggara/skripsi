@extends('layouts.admin')

@section('title', 'Manajemen Pengguna - ' . (\App\Models\Setting::get()->cafe_name ?? 'Sekarsa Coffee'))

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold font-serif text-gray-900 tracking-tight flex items-center space-x-2">
                <i class="fa-solid fa-users-gear text-emerald-600"></i>
                <span>Manajemen Pengguna</span>
            </h1>
            <p class="text-xs text-gray-500 mt-1">
                Kelola hak akses akun Administrator (Owner) dan Pegawai (Barista).
            </p>
        </div>

        <button onclick="openCreateModal()" class="px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
            <i class="fa-solid fa-user-plus"></i>
            <span>Tambah Pengguna Baru</span>
        </button>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-[11px] uppercase tracking-wider text-gray-400 border-b border-gray-200 font-semibold">
                    <tr>
                        <th class="p-3.5">Nama & Username</th>
                        <th class="p-3.5">Kontak</th>
                        <th class="p-3.5">Hak Akses (Role)</th>
                        <th class="p-3.5">Status Akun</th>
                        <th class="p-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="p-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-gray-900 block">{{ $user->name }}</span>
                                        <span class="text-[11px] text-gray-400 font-mono">@<span>{{ $user->username }}</span></span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5">
                                <span class="text-gray-800 block">{{ $user->email }}</span>
                                <span class="text-[11px] text-gray-400 font-mono">{{ $user->phone ?: '-' }}</span>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold {{ $user->role === 'admin' ? 'bg-amber-100 text-amber-900 border border-amber-200' : 'bg-blue-100 text-blue-900 border border-blue-200' }}">
                                    <i class="fa-solid {{ $user->role === 'admin' ? 'fa-crown text-amber-500' : 'fa-mug-hot text-blue-500' }} mr-1.5 text-xs"></i>
                                    {{ $user->role === 'admin' ? 'Administrator' : 'Pegawai / Barista' }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-emerald-600' : 'bg-red-600' }} mr-1"></span>
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="p-3.5 text-right space-x-1">
                                <button onclick="openEditModal({{ json_encode($user) }})" class="p-2 text-emerald-700 hover:bg-emerald-50 rounded-lg inline-block font-semibold transition" title="Ubah">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>

                                @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg font-semibold transition" title="Hapus">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">Belum ada pengguna</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Tambah / Ubah Pengguna -->
<div id="userModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-4">
            <h3 class="font-bold text-base text-gray-900" id="modalUserTitle">Tambah Pengguna Baru</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="userForm" method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div id="userMethodContainer"></div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="inputName" required placeholder="Contoh: Barista Budi" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" id="inputUsername" required placeholder="budi" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Hak Akses <span class="text-red-500">*</span></label>
                    <select name="role" id="inputRole" required class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="pegawai">Pegawai / Barista</option>
                        <option value="admin">Administrator / Owner</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="inputEmail" required placeholder="budi@sekarsa.com" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase text-gray-700 mb-1">No. HP / WA</label>
                    <input type="tel" name="phone" id="inputPhone" placeholder="08xxxxxxxxxx" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-gray-700 mb-1">
                    Kata Sandi <span id="passReqStar" class="text-red-500">*</span>
                </label>
                <input type="password" name="password" id="inputPassword" placeholder="Minimal 6 karakter" class="w-full text-xs p-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <span id="passHint" class="text-[10px] text-gray-400 mt-1 hidden">Kosongkan jika tidak ingin mengubah kata sandi.</span>
            </div>

            <div id="activeStatusWrapper" class="hidden">
                <label class="flex items-center space-x-2 text-xs font-semibold text-gray-700">
                    <input type="checkbox" name="is_active" id="inputIsActive" value="1" class="rounded text-emerald-600 focus:ring-emerald-500">
                    <span>Akun Pengguna Aktif</span>
                </label>
            </div>

            <div class="flex items-center space-x-2 pt-3">
                <button type="button" onclick="closeUserModal()" class="flex-1 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-semibold transition">
                    Batal
                </button>
                <button type="submit" class="flex-1 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-md">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openCreateModal() {
        document.getElementById('modalUserTitle').innerText = 'Tambah Pengguna Baru';
        document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
        document.getElementById('userMethodContainer').innerHTML = '';
        document.getElementById('inputName').value = '';
        document.getElementById('inputUsername').value = '';
        document.getElementById('inputEmail').value = '';
        document.getElementById('inputPhone').value = '';
        document.getElementById('inputRole').value = 'pegawai';
        document.getElementById('inputPassword').required = true;
        document.getElementById('passReqStar').classList.remove('hidden');
        document.getElementById('passHint').classList.add('hidden');
        document.getElementById('activeStatusWrapper').classList.add('hidden');
        document.getElementById('userModal').classList.remove('hidden');
    }

    function openEditModal(user) {
        document.getElementById('modalUserTitle').innerText = 'Ubah Akun Pengguna';
        document.getElementById('userForm').action = `/admin/users/${user.id}`;
        document.getElementById('userMethodContainer').innerHTML = '@method("PUT")';
        document.getElementById('inputName').value = user.name;
        document.getElementById('inputUsername').value = user.username;
        document.getElementById('inputEmail').value = user.email;
        document.getElementById('inputPhone').value = user.phone || '';
        document.getElementById('inputRole').value = user.role;
        document.getElementById('inputPassword').required = false;
        document.getElementById('passReqStar').classList.add('hidden');
        document.getElementById('passHint').classList.remove('hidden');
        document.getElementById('inputIsActive').checked = user.is_active;
        document.getElementById('activeStatusWrapper').classList.remove('hidden');
        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeUserModal() {
        document.getElementById('userModal').classList.add('hidden');
    }
</script>
@endpush
