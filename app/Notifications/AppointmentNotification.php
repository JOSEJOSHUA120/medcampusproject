<?php

namespace App\Notifications;

use App\Models\Antrian;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AppointmentNotification extends Notification
{
    use Queueable;

    protected $antrian;
    protected $type;
    protected $message;
    protected $roomNumber;

    public function __construct(Antrian $antrian, string $type, ?string $roomNumber = null)
    {
        $this->antrian = $antrian;
        $this->type = $type;
        $this->roomNumber = $roomNumber;
        $this->message = $this->generateMessage();
    }

    protected function generateMessage(): string
    {
        $patientName = $this->antrian->pasien->user->name ?? 'Pasien';
        $doctorName = $this->antrian->dokter->user->name ?? 'Dokter';

        return match ($this->type) {
            'created' => "Antrian berhasil dibuat. Nomor antrian: {$this->antrian->nomor_antrian}. Silakan menunggu.",
            'called' => "Anda dipanggil. Silakan menuju Ruangan {$this->roomNumber}.",
            'confirmed' => "Kehadiran Anda telah dikonfirmasi. Silakan menunggu panggilan dokter.",
            'room_assigned' => "Ruangan {$this->roomNumber} telah ditentukan untuk Anda.",
            'cancelled' => "Jadwal antrian Anda telah dibatalkan.",
            'completed' => "Pelayanan selesai. Terima kasih telah berkunjung.",
            'new_patient' => "Pasien baru {$patientName} telah masuk antrian untuk dr. {$doctorName}.",
            'patient_confirmed' => "Pasien {$patientName} telah konfirmasi hadir untuk dr. {$doctorName}.",
            'room_full' => "Peringatan: Semua ruangan terisi penuh.",
            default => "Status antrian Anda telah diperbarui.",
        };
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'antrian_id' => $this->antrian->id,
            'type' => $this->type,
            'message' => $this->message,
            'room_number' => $this->roomNumber,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'antrian_id' => $this->antrian->id,
            'type' => $this->type,
            'message' => $this->message,
            'room_number' => $this->roomNumber,
        ];
    }
}
