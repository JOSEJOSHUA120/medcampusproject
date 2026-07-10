<?php

namespace App\Notifications;

use App\Models\JadwalDokter;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class JadwalNotification extends Notification
{
    use Queueable;

    protected $jadwal;
    protected $type;
    protected $message;

    public function __construct(JadwalDokter $jadwal, string $type, ?array $oldData = null)
    {
        $this->jadwal = $jadwal;
        $this->type = $type;
        $this->message = $this->generateMessage($oldData);
    }

    protected function generateMessage(?array $oldData): string
    {
        $doctorName = $this->jadwal->dokter->dokter->nama_dokter ?? $this->jadwal->dokter->name ?? 'Dokter';
        $hari = $this->jadwal->hari;
        $mulai = substr($this->jadwal->jam_mulai, 0, 5);
        $selesai = substr($this->jadwal->jam_selesai, 0, 5);

        return match ($this->type) {
            'jadwal_created' => "Jadwal praktik baru untuk {$doctorName}: {$hari} {$mulai}-{$selesai}.",
            'jadwal_updated' => "Jadwal praktik {$hari} untuk {$doctorName} telah diperbarui dari {$oldData['jam_mulai']}-{$oldData['jam_selesai']} menjadi {$mulai}-{$selesai}.",
            'jadwal_deleted' => "Jadwal praktik {$hari} {$mulai}-{$selesai} untuk {$doctorName} telah dihapus.",
            'jadwal_status_changed' => "Jadwal praktik {$hari} {$mulai}-{$selesai} untuk {$doctorName} diubah menjadi " . ($this->jadwal->status == 'aktif' ? 'aktif' : 'nonaktif') . ".",
            default => "Jadwal praktik telah diperbarui.",
        };
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'jadwal_id' => $this->jadwal->id,
            'type' => $this->type,
            'message' => $this->message,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'jadwal_id' => $this->jadwal->id,
            'type' => $this->type,
            'message' => $this->message,
        ];
    }
}