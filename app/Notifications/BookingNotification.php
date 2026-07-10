<?php

/*
| =============================================================================
| NAMESPACE & IMPORT
| Namespace mengorganisir class. Setiap "use" mengimpor class dari namespace
| lain sehingga bisa digunakan tanpa prefix namespace lengkap.
| =============================================================================
*/
namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/*
| =============================================================================
| CLASS & INHERITANCE
| "extends Notification" — BookingNotification mewarisi dari class Notification
| (milik Laravel). Dengan inheritance, kita bisa override method via(), toDatabase(),
| toArray() untuk menyesuaikan perilaku notifikasi.
|
| TRAIT (use Queueable)
| Trait adalah mekanisme reusability di PHP (mirip multiple inheritance).
| "use Queueable;" mengimpor method-method Queueable ke class ini tanpa
| harus extends class Queueable. Trait menyelesaikan problem diamond problem
| pada multiple inheritance.
| =============================================================================
*/
class BookingNotification extends Notification
{
    use Queueable;

    /*
    | =========================================================================
    | ENCAPSULATION (protected properties)
    | Properti protected: bisa diakses oleh class ini dan child class.
    | Ini adalah ENCAPSULATION — data disembunyikan dari luar class.
    | Hanya method public yang mengekspos behavior.
    | =========================================================================
    */
    protected $booking;
    protected $type;
    protected $message;

    /*
    | CONSTRUCTOR — Method khusus yang otomatis dipanggil saat instance dibuat
    | (new BookingNotification(...)). Biasanya untuk inisialisasi property.
    |
    | TYPE-HINTING / DEPENDENCY INJECTION
    | "Booking $booking" — parameter dipaksa harus bertipe Booking.
    | PHP akan melempar TypeError jika tipe tidak sesuai.
    | "?string $note = null" — nullable type dengan default null.
    */
    public function __construct(Booking $booking, string $type, ?string $note = null)
    {
        /*
        | $this->property — Mengacu pada properti INSTANCE saat ini.
        | $this->booking = $booking — menyimpan parameter ke property.
        */
        $this->booking = $booking;
        $this->type = $type;
        $this->message = $this->generateMessage($note);
    }

    /*
    | PROTECTED METHOD
    | Hanya bisa dipanggil dari dalam class ini dan child class-nya.
    | "Encapsulation" — detail implementasi disembunyikan.
    |
    | RETURN TYPE DECLARATION ": string"
    | Method ini dijamin mengembalikan string. Jika tidak, PHP error.
    */
    protected function generateMessage(?string $note): string
    {
        /*
        | OBJECT COMPOSITION
        | $this->booking->pasien->name — Mengakses property dari object
        | di dalam object (relasi). Booking punya relasi "pasien" (User),
        | dan User punya property "name".
        |
        | NULL COALESCING OPERATOR "??"
        | Jika hasil di kiri null, pakai nilai di kanan.
        */
        $patientName = $this->booking->pasien->name ?? 'Pasien';
        $doctorName = $this->booking->dokter->name ?? 'Dokter';

        /*
        | MATCH EXPRESSION (PHP 8)
        | Mirip switch-case, tapi match mengembalikan nilai (expression),
        | bukan statement. Juga melakukan strict comparison (===).
        */
        return match ($this->type) {
            'booking_created' => "Booking baru dari {$patientName} untuk dr. {$doctorName} pada {$this->booking->tanggal_booking} {$this->booking->jam_booking}.",
            'booking_approved' => "Booking Anda untuk dr. {$doctorName} pada {$this->booking->tanggal_booking} {$this->booking->jam_booking} telah disetujui.",
            'booking_rejected' => "Booking Anda untuk dr. {$doctorName} ditolak. Alasan: " . ($note ?? '-'),
            'booking_completed' => "Booking dengan dr. {$doctorName} selesai. Terima kasih telah berkunjung.",
            'booking_cancelled_by_patient' => "Pasien {$patientName} membatalkan booking untuk dr. {$doctorName} pada {$this->booking->tanggal_booking} {$this->booking->jam_booking}.",
            default => "Status booking Anda telah diperbarui.",
        };
    }

    /*
    | POLYMORPHISM (Method Overriding)
    | ---------------------------------
    | Method via(), toDatabase(), toArray() didefinisikan di parent class
    | Notification. Kita override (tulis ulang) di sini dengan implementasi
    | sendiri. Ini adalah POLYMORPHISM — antarmuka sama, perilaku berbeda.
    | Laravel memanggil method-method ini secara otomatis saat mengirim
    | notifikasi, tanpa peduli class spesifiknya.
    */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'type' => $this->type,
            'message' => $this->message,
        ];
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'type' => $this->type,
            'message' => $this->message,
        ];
    }
}
