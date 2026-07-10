@extends('layouts.dokter')

@section('title', 'Rekam Medis')

@section('content')
<div class="page-header">
    <h4>Rekam Medis</h4>
    <p>Data rekam medis yang telah Anda buat.</p>
</div>

<div class="card-dashboard p-4 mb-5">
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Total <strong>{{ $data->total() }}</strong> rekam medis</span>
    </div>
</div>

<div class="card-dashboard p-4 mb-5">
    <div class="flex-1 relative">
        <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        <input type="text" id="searchInput" placeholder="Cari nama atau email pasien..." class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
    </div>
</div>

<div class="space-y-2 mb-8" id="pasienList">
    @forelse($pasien as $p)
    <div class="pasien-item bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
        <form action="{{ route('dokter.rekam-medis.pilih-pasien.store') }}" method="POST" class="block">
            @csrf
            <input type="hidden" name="pasien_id" value="{{ $p->id }}">
            <button type="submit" class="w-full text-left p-4 flex items-center gap-4 hover:bg-gray-50/50 transition">
                <div class="w-10 h-10 rounded-full flex-shrink-0 overflow-hidden border border-gray-200">
                    <img src="{{ $p->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($p->user->email ?? '') }}" alt="foto" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <h5 class="font-semibold text-gray-800 text-sm">{{ $p->user->name ?? '-' }}</h5>
                    <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                        <span>{{ $p->user->email ?? '-' }}</span>
                        @if($p->jenis_kelamin)
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span>{{ $p->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        @endif
                        @if($p->no_telp)
                        <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                        <span>{{ $p->no_telp }}</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center gap-1.5 text-xs font-medium text-primary-600 bg-primary-50 px-3 py-1.5 rounded-lg">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Pilih
                </div>
            </button>
        </form>
    </div>
    @empty
    <div class="text-center py-10 text-gray-400">
        <p>Belum ada pasien terdaftar.</p>
    </div>
    @endforelse
</div>

<div class="space-y-3">
    @foreach($data as $r)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
        <div class="px-5 py-4 flex items-center gap-4 border-b border-gray-50 bg-gray-50/30">
            <div class="w-10 h-10 rounded-full flex-shrink-0 overflow-hidden border border-gray-200 shadow-sm">
                <img src="{{ $r->pasien->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($r->pasien->user->email ?? '') }}" alt="foto" class="w-full h-full object-cover">
            </div>
            <div class="flex-1 min-w-0">
                <h5 class="font-bold text-gray-800 text-sm">{{ $r->pasien->user->name ?? '-' }}</h5>
                <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                    <span>{{ $r->created_at->format('d M Y') }}</span>
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    <span>{{ $r->created_at->format('H:i') }}</span>
                    @if($r->antrian)
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    <span>Antrian #{{ $r->antrian->nomor_antrian }}</span>
                    @endif
                </div>
            </div>
            <a href="{{ route('dokter.rekam-medis.edit', $r->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs font-medium text-gray-600 hover:bg-primary-50 hover:border-primary-200 hover:text-primary-700 transition">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit
            </a>
        </div>
        <div class="px-5 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                        <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Diagnosa</span>
                    </div>
                    <p class="text-sm text-gray-800 leading-relaxed">{{ $r->diagnosa ?? '-' }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
                        <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Tindakan</span>
                    </div>
                    <p class="text-sm text-gray-800 leading-relaxed">{{ $r->tindakan ?? '-' }}</p>
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-400"></span>
                        <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Resep Obat</span>
                    </div>
                    @if($r->resepObat->count())
                        <div class="space-y-1">
                        @foreach($r->resepObat as $ro)
                            <div class="inline-flex items-center gap-1.5 bg-green-50 text-green-800 text-xs font-medium px-2.5 py-1 rounded-full">
                                <span>{{ $ro->obat->nama_obat ?? '-' }}</span>
                                <span class="text-green-500">x{{ $ro->jumlah }}</span>
                            </div>
                        @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-800 leading-relaxed">{{ $r->resep_obat ?? '-' }}</p>
                    @endif
                </div>
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                        <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Catatan Dokter</span>
                    </div>
                    <p class="text-sm text-gray-800 leading-relaxed">{{ $r->catatan_dokter ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-6">
    {{ $data->links('pagination::bootstrap-5') }}
</div>

@push('scripts')
<style>
.pagination { display: flex; gap: 4px; list-style: none; padding: 0; margin: 0; flex-wrap: wrap; }
.pagination .page-item .page-link { display: block; padding: 6px 12px; font-size: 14px; color: #374151; background: #fff; border: 1px solid #e5e7eb; border-radius: 8px; text-decoration: none; }
.pagination .page-item .page-link:hover { background: #f9fafb; }
.pagination .page-item.active .page-link { background: #2563eb; color: #fff; border-color: #2563eb; }
.pagination .page-item.disabled .page-link { color: #9ca3af; background: #f9fafb; pointer-events: none; }
</style>
<script>
document.getElementById('searchInput').addEventListener('input', function() {
    let q = this.value.toLowerCase();
    document.querySelectorAll('.pasien-item').forEach(function(el) {
        let name = el.querySelector('h5')?.textContent?.toLowerCase() || '';
        let info = el.querySelector('.text-xs')?.textContent?.toLowerCase() || '';
        el.style.display = (name.includes(q) || info.includes(q)) ? '' : 'none';
    });
});
</script>
@endpush
@endsection
