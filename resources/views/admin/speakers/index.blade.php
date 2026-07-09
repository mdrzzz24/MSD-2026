<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" type="image/png" href="{{ asset('img/metrodata.png') }}">
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Speakers — {{ config('app.name') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','system-ui','sans-serif']}}}}</script>
</head>
<body class="bg-gray-50 font-sans antialiased">
<div class="flex min-h-screen">
@include('admin.partials.sidebar')
<main class="flex-1 lg:ml-64">
<header class="sticky top-0 z-30 bg-white/80 backdrop-blur border-b border-gray-200">
    <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">
        <div><h1 class="text-lg font-bold text-gray-900">Speakers</h1><p class="text-xs text-gray-500">Manage event speakers</p></div>
        <button onclick="document.getElementById('addForm').classList.toggle('hidden')" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-xl transition">+ Add Speaker</button>
    </div>
</header>

<div class="p-4 sm:p-6 lg:p-8">
    @if (session('success'))
        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl mb-6">{!! session('success') !!}</div>
    @endif

    {{-- Add Form --}}
    <div id="addForm" class="hidden bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h3 class="text-sm font-bold text-gray-900 mb-4">New Speaker</h3>
        <form action="{{ route('admin.speakers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf
            <div class="flex gap-4">
                <div class="flex-shrink-0">
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Photo</label>
                    <div class="relative">
                        <img id="addPhotoPreview" src="" class="w-20 h-20 rounded-xl object-cover border border-gray-200 hidden">
                        <div id="addPhotoPlaceholder" class="w-20 h-20 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-2xl cursor-pointer hover:border-indigo-400 hover:text-indigo-400 transition" onclick="document.getElementById('addPhotoInput').click()">+</div>
                        <input type="file" name="photo" id="addPhotoInput" accept="image/*" class="hidden" onchange="previewAddPhoto(this)">
                    </div>
                </div>
                <div class="flex-1 space-y-3">
                    <div><label class="block text-xs font-semibold text-gray-700 mb-1">Name *</label><input type="text" name="name" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                    <div class="grid grid-cols-2 gap-4">
                        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Title</label><input type="text" name="title" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Company</label><input type="text" name="company" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                    </div>
                </div>
            </div>
            <div><label class="block text-xs font-semibold text-gray-700 mb-1">Bio</label><textarea name="bio" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea></div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition">Save Speaker</button>
        </form>
    </div>

    <script>
    function previewAddPhoto(input) {
        const preview = document.getElementById('addPhotoPreview');
        const placeholder = document.getElementById('addPhotoPlaceholder');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>

    {{-- Speaker Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($speakers as $s)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 {{ $s->is_active ? '' : 'opacity-50' }}">
            <div class="flex items-start gap-4">
                @if ($s->photo)
                    <img src="{{ asset('storage/' . $s->photo) }}" alt="{{ $s->name }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0">
                @else
                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">{{ strtoupper(substr($s->name,0,1)) }}</div>
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-bold text-gray-900">{{ $s->name }}</h3>
                    @if ($s->title)<p class="text-xs text-gray-500">{{ $s->title }}</p>@endif
                    @if ($s->company)<p class="text-xs text-gray-400">{{ $s->company }}</p>@endif
                    <div class="flex items-center gap-1 mt-2">
                        @if ($s->is_active)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700">Active</span>
                        @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-500">Inactive</span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="flex gap-1 mt-3 pt-3 border-t border-gray-100">
                <button onclick="editSpeaker({{ $s->id }},'{{ e($s->name) }}','{{ e($s->title) }}','{{ e($s->company) }}','{{ e($s->photo) }}','{{ e($s->bio) }}')" class="flex-1 px-2 py-1.5 text-[11px] font-medium rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition text-center">Edit</button>
                <form action="{{ route('admin.speakers.toggle', $s) }}" method="POST" class="flex-1">@csrf<button class="w-full px-2 py-1.5 text-[11px] font-medium rounded-lg bg-amber-50 text-amber-700 hover:bg-amber-100 transition">{{ $s->is_active ? 'Disable' : 'Enable' }}</button></form>
                <form action="{{ route('admin.speakers.destroy', $s) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete {{ $s->name }}?')">@csrf @method('DELETE')<button class="w-full px-2 py-1.5 text-[11px] font-medium rounded-lg bg-red-50 text-red-700 hover:bg-red-100 transition">Delete</button></form>
            </div>
        </div>
        @empty
        <div class="col-span-full text-center py-12 text-gray-400 text-sm">No speakers yet. Add your first speaker!</div>
        @endforelse
    </div>
</div>
</main>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;z-index:9999;align-items:center;justify-content:center;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);padding:16px;">
  <div style="background:#fff;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.25);width:100%;max-width:480px;padding:24px;">
    <h3 class="text-lg font-bold text-gray-900 mb-4">Edit Speaker</h3>
    <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-3">
        @csrf @method('PUT')
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <label class="block text-xs font-semibold text-gray-700 mb-1">Photo</label>
                <div class="relative">
                    <img id="editPhotoPreview" src="" class="w-20 h-20 rounded-xl object-cover border border-gray-200 hidden">
                    <div id="editPhotoPlaceholder" class="w-20 h-20 rounded-xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 text-2xl cursor-pointer hover:border-indigo-400 hover:text-indigo-400 transition" onclick="document.getElementById('editPhotoInput').click()">+</div>
                    <input type="file" name="photo" id="editPhotoInput" accept="image/*" class="hidden" onchange="previewEditPhoto(this)">
                    <input type="hidden" name="remove_photo" id="editRemovePhoto" value="0">
                </div>
                <button type="button" id="editRemovePhotoBtn" class="hidden mt-1 text-[10px] text-red-500 hover:text-red-700 font-medium" onclick="removeEditPhoto()">Remove photo</button>
            </div>
            <div class="flex-1 space-y-3">
                <div><label class="block text-xs font-semibold text-gray-700 mb-1">Name *</label><input type="text" name="name" id="editName" required class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-xs font-semibold text-gray-700 mb-1">Title</label><input type="text" name="title" id="editTitle" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                    <div><label class="block text-xs font-semibold text-gray-700 mb-1">Company</label><input type="text" name="company" id="editCompany" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></div>
                </div>
            </div>
        </div>
        <div><label class="block text-xs font-semibold text-gray-700 mb-1">Bio</label><textarea name="bio" id="editBio" rows="2" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500"></textarea></div>
        <div class="flex gap-2">
            <button type="button" onclick="closeEditModal()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg">Cancel</button>
            <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg">Update</button>
        </div>
    </form>
  </div>
</div>

<script>
let _editCurrentPhoto = '';

function editSpeaker(id,name,title,company,photo,bio){
    document.getElementById('editForm').action='/2026-Testing/public/admin/speakers/'+id;
    document.getElementById('editName').value=name;
    document.getElementById('editTitle').value=title;
    document.getElementById('editCompany').value=company;
    document.getElementById('editBio').value=bio;
    document.getElementById('editPhotoInput').value='';
    document.getElementById('editRemovePhoto').value='0';

    const preview = document.getElementById('editPhotoPreview');
    const placeholder = document.getElementById('editPhotoPlaceholder');
    const removeBtn = document.getElementById('editPhotoRemoveBtn');

    if (photo) {
        _editCurrentPhoto = photo;
        preview.src = '/2026-Testing/public/storage/' + photo;
        preview.classList.remove('hidden');
        placeholder.classList.add('hidden');
        removeBtn.classList.remove('hidden');
    } else {
        _editCurrentPhoto = '';
        preview.classList.add('hidden');
        placeholder.classList.remove('hidden');
        removeBtn.classList.add('hidden');
    }

    document.getElementById('editModal').style.display='flex';
}

function previewEditPhoto(input) {
    const preview = document.getElementById('editPhotoPreview');
    const placeholder = document.getElementById('editPhotoPlaceholder');
    const removeBtn = document.getElementById('editPhotoRemoveBtn');
    document.getElementById('editRemovePhoto').value='0';
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
            removeBtn.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function removeEditPhoto() {
    document.getElementById('editPhotoInput').value='';
    document.getElementById('editRemovePhoto').value='1';
    document.getElementById('editPhotoPreview').classList.add('hidden');
    document.getElementById('editPhotoPlaceholder').classList.remove('hidden');
    document.getElementById('editPhotoRemoveBtn').classList.add('hidden');
}

function closeEditModal(){document.getElementById('editModal').style.display='none';}
document.getElementById('editModal').addEventListener('click',function(e){if(e.target===this)closeEditModal();});
</script>
</body>
</html>
