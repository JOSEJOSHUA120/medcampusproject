@extends('layouts.admin')

@section('title', 'Jadwal Dokter')

@section('content')
<div class="page-header">
    <h4>Jadwal Dokter</h4>
    <p>Atur jadwal praktik dokter. Setiap jadwal akan menghasilkan slot booking berdasarkan durasi dan kuota.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 mb-6">
    <h5 class="font-bold text-gray-800 dark:text-white mb-5">
        <svg class="w-5 h-5 inline text-blue-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
        Tambah Jadwal Baru
    </h5>
    <form action="{{ route('admin.jadwal-dokter.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div>
                <label class="form-label">Dokter <span class="text-red-500">*</span></label>
                <select name="user_id" class="form-input-custom" required>
                    <option value="">-- Pilih Dokter --</option>
                    @foreach($dokters as $d)
                    <option value="{{ $d->id }}">{{ $d->dokter->nama_dokter ?? $d->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Hari <span class="text-red-500">*</span></label>
                <select name="hari" class="form-input-custom" required>
                    <option value="">-- Pilih Hari --</option>
                    @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $hari)
                    <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Jam Mulai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_mulai" class="form-input-custom" required>
            </div>
            <div>
                <label class="form-label">Jam Selesai <span class="text-red-500">*</span></label>
                <input type="time" name="jam_selesai" class="form-input-custom" required>
            </div>
            <div>
                <label class="form-label">Durasi (menit) <span class="text-red-500">*</span></label>
                <input type="number" name="durasi_slot" class="form-input-custom" value="30" min="15" step="5" required>
            </div>
            <div>
                <label class="form-label">Kuota <span class="text-red-500">*</span></label>
                <input type="number" name="kuota" class="form-input-custom" value="10" min="0" required>
            </div>
        </div>
        <button type="submit" class="btn-primary mt-5">
            <svg class="w-5 h-5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Tambah Jadwal
        </button>
    </form>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <h5 class="font-bold text-gray-800 dark:text-white mb-4">
        <svg class="w-5 h-5 inline text-blue-600 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        Daftar Jadwal
    </h5>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700 border-b border-gray-100 dark:border-gray-600">
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Hari</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Jam Praktik</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Durasi Slot</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Kuota</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 dark:divide-gray-700">
                @foreach($data as $i => $j)
                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-700/30 transition">
                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-2.5">
                            <img src="{{ $j->dokter->foto ?? 'https://i.pravatar.cc/300?u=' . urlencode($j->dokter->email ?? '') }}" alt="foto" class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-gray-600">
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $j->dokter->dokter->nama_dokter ?? $j->dokter->name }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="inline-flex items-center gap-1.5 bg-blue-50 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-xs font-semibold px-2.5 py-1 rounded-md">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $j->hari }}
                        </div>
                    </td>
                    <td class="px-4 py-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ substr($j->jam_mulai, 0, 5) }} - {{ substr($j->jam_selesai, 0, 5) }}</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $j->durasi_slot }} menit</td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">{{ $j->kuota }} pasien</td>
                    <td class="px-4 py-3 text-sm">
                        @if($j->status == 'aktif')
                        <span class="inline-flex items-center gap-1 bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-semibold px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs font-semibold px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                            Nonaktif
                        </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-sm">
                        <div class="flex items-center gap-1.5">
                            <a href="{{ route('admin.jadwal-dokter.edit', $j->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-semibold hover:bg-primary-100 dark:hover:bg-primary-900/50 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                Edit
                            </a>
                            <form action="{{ route('admin.jadwal-dokter.destroy', $j->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')" class="inline">
                                <button class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 text-xs font-semibold hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($data->isEmpty())
        <div class="text-center py-12">
            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            <p class="text-gray-400 dark:text-gray-500 text-sm">Belum ada jadwal dokter.</p>
        </div>
        @endif
    </div>
</div>
@endsection
