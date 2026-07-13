@extends('layouts.admin')

@section('title', 'Kelola Antrian')

@section('content')
<div class="page-header">
    <h4>Kelola Antrian</h4>
    <p>Kelola antrian pasien. Panggil pasien, tentukan ruangan, dan pantau status secara real-time.</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
    <div class="overflow-x-auto">
        <table id="dataTable" class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">No</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Nomor Antrian</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Pasien</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Dokter</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Keluhan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Ruangan</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $i => $a)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $i + 1 }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm font-semibold">{{ $a->nomor_antrian }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $a->pasien->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $a->dokter->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm text-xs max-w-[150px] truncate">{{ Str::limit($a->complaint, 40) ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ \Carbon\Carbon::parse($a->jam_antrian)->format('H:i') }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">{{ $a->room->room_number ?? '-' }}</td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">
                        @php
                            $badgeClass = match($a->status) {
                                'menunggu' => 'badge-menunggu',
                                'dipanggil' => 'badge-dipanggil',
                                'sedang_dilayani' => 'bg-indigo-100 text-indigo-800',
                                'selesai' => 'badge-selesai',
                                'dibatalkan' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="badge-status {{ $badgeClass }}">{{ ucfirst(str_replace('_', ' ', $a->status)) }}</span>
                    </td>
                    <td class="px-4 py-3 border-b border-gray-100 text-sm">
                        <div class="flex gap-1 flex-wrap">
                            @if($a->status == 'menunggu')
                            <button onclick="openPanggil({{ $a->id }})" class="btn-sm btn-success">Panggil</button>
                            <form action="{{ route('admin.kelola-antrian.status', $a->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan antrian ini?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="dibatalkan">
                                <button class="btn-sm btn-danger">Batalkan</button>
                            </form>
                            @elseif($a->status == 'dipanggil')
                            <form action="{{ route('admin.kelola-antrian.status', $a->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="sedang_dilayani">
                                <button class="btn-sm btn-primary">Layani</button>
                            </form>
                            <form action="{{ route('admin.kelola-antrian.status', $a->id) }}" method="POST" class="inline" onsubmit="return confirm('Batalkan antrian ini?')">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="dibatalkan">
                                <button class="btn-sm btn-danger">Batalkan</button>
                            </form>
                            @elseif($a->status == 'sedang_dilayani')
                            <form action="{{ route('admin.kelola-antrian.status', $a->id) }}" method="POST" class="inline">
                                @csrf @method('PUT')
                                <input type="hidden" name="status" value="selesai">
                                <button class="btn-sm btn-success">Selesai</button>
                            </form>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div id="modalPanggil" class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h5 class="font-bold text-gray-800 text-lg">Panggil Pasien</h5>
            <button onclick="closeModal()" class="p-1 hover:bg-gray-100 rounded-lg text-2xl">&times;</button>
        </div>
        <form id="formPanggil" method="POST">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="form-label">Pilih Ruangan <span class="text-red-500">*</span></label>
                <select name="room_id" class="form-select-custom" required>
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($rooms as $room)
                    <option value="{{ $room->id }}">{{ $room->room_number }} {{ $room->description ? '- ' . $room->description : '' }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary w-full py-3 text-base font-bold">Konfirmasi Panggil</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openPanggil(id) {
    document.getElementById('formPanggil').action = "{{ url('admin/kelola-antrian') }}/" + id + "/panggil";
    document.getElementById('modalPanggil').classList.remove('hidden');
    document.getElementById('modalPanggil').classList.add('flex');
}
function closeModal() {
    document.getElementById('modalPanggil').classList.add('hidden');
    document.getElementById('modalPanggil').classList.remove('flex');
}
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('modalPanggil').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    $('#dataTable').DataTable();
});
</script>
@endpush
@endsection
