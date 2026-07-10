@extends('layouts.dokter')

@section('title', 'Riwayat Pasien')

@section('content')
<div class="page-header">
    <h4>Riwayat Pasien</h4>
    <p>Riwayat pemeriksaan pasien yang pernah Anda tangani.</p>
</div>

<div class="card-dashboard p-4 mb-5">
    <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
        <div class="relative flex-1 max-w-xs">
            <input type="text" id="searchPasien" class="form-input-custom text-sm" placeholder="Cari nama pasien..." oninput="filterPasien(this.value)">
        </div>
        <span class="text-xs text-gray-400">Total {{ $pasien->count() }} pasien</span>
    </div>
</div>

<div id="pasienList" class="space-y-4">
    @forelse($pasien as $p)
    <div class="card-pasien bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
        <button type="button" class="w-full text-left toggle-riwayat px-5 py-4 flex items-center gap-4 hover:bg-gray-50/50 transition" data-target="riwayat-{{ $p->id }}">
            <div class="w-11 h-11 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 text-white flex items-center justify-center font-bold text-sm flex-shrink-0 shadow-sm">
                {{ strtoupper(substr($p->user->name ?? '?', 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <h5 class="font-bold text-gray-800 text-sm truncate">{{ $p->user->name ?? 'Tanpa Nama' }}</h5>
                <div class="flex items-center gap-2 text-xs text-gray-400 mt-0.5">
                    <span>{{ $p->no_telp ?? '-' }}</span>
                    @if($p->tanggal_lahir)
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    <span>{{ \Carbon\Carbon::parse($p->tanggal_lahir)->age }} th</span>
                    @endif
                    @if($p->jenis_kelamin)
                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                    <span>{{ $p->jenis_kelamin == 'L' ? 'Laki-Laki' : 'Perempuan' }}</span>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3 flex-shrink-0">
                <span class="inline-flex items-center gap-1 bg-primary-50 text-primary-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    {{ $p->rekamMedis->count() }} kunjungan
                </span>
                <svg class="w-4 h-4 text-gray-300 arrow-icon transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </div>
        </button>

        <div id="riwayat-{{ $p->id }}" class="riwayat-content border-t border-gray-100" style="display:none">
            @forelse($p->rekamMedis as $r)
            <div class="px-5 py-4 {{ !$loop->last ? 'border-b border-gray-50' : '' }} hover:bg-gray-50/30 transition">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $r->created_at->format('d M Y') }}</span>
                            <span class="text-[11px] text-gray-400">{{ $r->created_at->format('H:i') }}</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Diagnosa</span>
                                <p class="text-gray-800 mt-0.5">{{ $r->diagnosa ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Tindakan</span>
                                <p class="text-gray-800 mt-0.5">{{ $r->tindakan ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Resep Obat</span>
                                <p class="text-gray-800 mt-0.5">{{ $r->resep_obat ?? '-' }}</p>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase tracking-wider text-gray-400 font-semibold">Catatan Dokter</span>
                                <p class="text-gray-800 mt-0.5">{{ $r->catatan_dokter ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-6 text-center text-gray-400 text-sm">Belum ada rekam medis.</div>
            @endforelse
        </div>
    </div>
    @empty
    <div class="text-center py-16 text-gray-400">
        <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p>Belum ada riwayat pasien.</p>
    </div>
    @endforelse
</div>

@push('scripts')
<script>
document.querySelectorAll('.toggle-riwayat').forEach(btn => {
    btn.addEventListener('click', function() {
        let target = document.getElementById(this.dataset.target);
        let icon = this.querySelector('.arrow-icon');
        if (target.style.display === 'none') {
            target.style.display = 'block';
            icon.classList.add('rotate-180');
        } else {
            target.style.display = 'none';
            icon.classList.remove('rotate-180');
        }
    });
});

function filterPasien(val) {
    let keyword = val.toLowerCase().trim();
    document.querySelectorAll('.card-pasien').forEach(card => {
        let name = card.querySelector('h5').textContent.toLowerCase();
        card.style.display = !keyword || name.includes(keyword) ? '' : 'none';
    });
}
</script>
<style>
.arrow-icon.rotate-180 { transform: rotate(180deg); }
</style>
@endpush
@endsection
