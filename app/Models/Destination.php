<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Destination
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(bool $onlyPublished = true): array
    {
        $sql = "
            SELECT d.*,
                   (SELECT count(*) FROM packages p WHERE p.primary_destination_id = d.id AND p.status != 'rascunho') as package_count
            FROM destinations d
        ";
        if ($onlyPublished) {
            $sql .= " WHERE d.status = 'publicado'";
        }
        $sql .= " ORDER BY d.is_featured DESC, d.name ASC";

        return $this->db->query($sql)->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM destinations WHERE slug = ?");
        $stmt->execute([$slug]);
        $dest = $stmt->fetch();
        if (!$dest) {
            return null;
        }

        // Get associated packages
        $pkgStmt = $this->db->prepare("
            SELECT p.*,
                   (
                       SELECT pi.image_url 
                       FROM package_images pi 
                       WHERE pi.package_id = p.id 
                       ORDER BY pi.sort_order ASC 
                       LIMIT 1
                   ) as cover_image
            FROM packages p 
            WHERE p.primary_destination_id = ? AND p.status != 'rascunho'
            ORDER BY p.is_featured DESC
        ");
        $pkgStmt->execute([$dest['id']]);
        $dest['packages'] = $pkgStmt->fetchAll();

        return $dest;
    }
}
