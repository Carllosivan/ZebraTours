<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use Throwable;

class NotificationService
{
    public static function notifyNewBooking(array $booking): void
    {
        $subject = "Novo Pedido de Cotação - {$booking['reference_code']} ({$booking['customer_name']})";
        $body = "Recebeu um novo pedido de cotação na Zebra Tours:\n\n"
              . "Referência: {$booking['reference_code']}\n"
              . "Cliente: {$booking['customer_name']}\n"
              . "Contacto: {$booking['phone_country_code']} {$booking['customer_phone']}\n"
              . "Email: " . ($booking['customer_email'] ?: 'Não indicado') . "\n"
              . "Serviço: {$booking['service_type']}\n"
              . "Destino: " . ($booking['destination'] ?: 'Geral') . "\n"
              . "Datas: " . ($booking['preferred_dates'] ?: 'A definir') . "\n"
              . "Viajantes: {$booking['travelers_count']}\n"
              . "Mensagem: " . ($booking['message'] ?: 'Nenhuma') . "\n";

        self::send($subject, $body);
    }

    public static function notifyContact(array $contact): void
    {
        $subject = "Nova Mensagem de Contacto - {$contact['subject']} ({$contact['name']})";
        $body = "Mensagem recebida através do website Zebra Tours:\n\n"
              . "Nome: {$contact['name']}\n"
              . "Email: {$contact['email']}\n"
              . "Assunto: {$contact['subject']}\n"
              . "Mensagem:\n{$contact['message']}\n";

        self::send($subject, $body);
    }

    private static function send(string $subject, string $body): bool
    {
        try {
            $db = Database::getConnection();
            $email = $db->query("SELECT setting_value FROM settings WHERE setting_key = 'email'")->fetchColumn();
            $to = $email ?: 'reservas@ztours.co.mz';

            // If system has sendmail configured, attempt native mail()
            $headers = "From: no-reply@zebratours.co.mz\r\n"
                     . "Reply-To: {$to}\r\n"
                     . "X-Mailer: PHP/" . PHP_VERSION . "\r\n"
                     . "Content-Type: text/plain; charset=UTF-8\r\n";

            @mail((string) $to, $subject, $body, $headers);
            return true;
        } catch (Throwable $e) {
            error_log('[NotificationService Warning]: ' . $e->getMessage());
            return false;
        }
    }
}
