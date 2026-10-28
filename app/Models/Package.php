<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

class Package
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT p.*,
                   d.name as destination_name,
                   d.country as destination_country,
                   d.cover_image as destination_cover,
                   (
                       SELECT pi.image_url 
                       FROM package_images pi 
                       WHERE pi.package_id = p.id 
                       ORDER BY pi.sort_order ASC 
                       LIMIT 1
                   ) as cover_image
            FROM packages p
            LEFT JOIN destinations d ON d.id = p.primary_destination_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filters['status'];
        } else {
            $sql .= " AND p.status IN ('publicado', 'pendente_validacao')";
        }

        if (!empty($filters['destination'])) {
            $sql .= " AND (d.slug = ? OR p.primary_destination_id = ?)";
            $params[] = $filters['destination'];
            $params[] = (int) $filters['destination'];
        }

        if (!empty($filters['category'])) {
            $sql .= " AND EXISTS (
                SELECT 1 FROM package_categories pc 
                JOIN categories c ON c.id = pc.category_id 
                WHERE pc.package_id = p.id AND (c.slug = ? OR c.id = ?)
            )";
            $params[] = $filters['category'];
            $params[] = (int) $filters['category'];
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND (p.price_per_person >= ? OR p.total_price >= ?)";
            $params[] = (float) $filters['min_price'];
            $params[] = (float) $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $sql .= " AND (p.price_per_person <= ? OR p.total_price <= ?)";
            $params[] = (float) $filters['max_price'];
            $params[] = (float) $filters['max_price'];
        }

        if (!empty($filters['search'])) {
            $sql .= " AND (p.title LIKE ? OR p.summary LIKE ? OR d.name LIKE ?)";
            $term = '%' . trim($filters['search']) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        // Sorting
        $sort = $filters['sort'] ?? 'recommended';
        if ($sort === 'price-asc') {
            $sql .= " ORDER BY COALESCE(p.price_per_person, p.total_price, 9999999) ASC";
        } elseif ($sort === 'price-desc') {
            $sql .= " ORDER BY COALESCE(p.price_per_person, p.total_price, 0) DESC";
        } elseif ($sort === 'duration-asc') {
            $sql .= " ORDER BY p.duration_days ASC";
        } else {
            $sql .= " ORDER BY p.is_featured DESC, p.created_at DESC";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll();

        foreach ($rows as &$r) {
            if (empty($r['cover_image'])) {
                $r['cover_image'] = $r['destination_cover'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80';
            }
        }

        return $rows;
    }

    public function findBySlug(string $slug, bool $onlyPublic = true): ?array
    {
        $sql = "
            SELECT p.*,
                   d.name as destination_name,
                   d.country as destination_country,
                   d.cover_image as destination_cover
            FROM packages p
            LEFT JOIN destinations d ON d.id = p.primary_destination_id
            WHERE p.slug = ?
        ";
        if ($onlyPublic) {
            $sql .= " AND p.status IN ('publicado', 'pendente_validacao')";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$slug]);
        $pkg = $stmt->fetch();
        if (!$pkg) {
            return null;
        }

        // Itineraries
        $itStmt = $this->db->prepare("SELECT * FROM itineraries WHERE package_id = ? ORDER BY day_number ASC");
        $itStmt->execute([$pkg['id']]);
        $pkg['itineraries'] = $itStmt->fetchAll();

        // Images
        $imStmt = $this->db->prepare("SELECT * FROM package_images WHERE package_id = ? ORDER BY sort_order ASC");
        $imStmt->execute([$pkg['id']]);
        $pkg['images'] = $imStmt->fetchAll();

        // Categories
        $catStmt = $this->db->prepare("
            SELECT c.* FROM categories c
            JOIN package_categories pc ON pc.category_id = c.id
            WHERE pc.package_id = ?
        ");
        $catStmt->execute([$pkg['id']]);
        $pkg['categories'] = $catStmt->fetchAll();

        $pkg['cover_image'] = !empty($pkg['images']) ? $pkg['images'][0]['image_url'] : ($pkg['destination_cover'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80');

        return $pkg;
    }

    public function findFeatured(int $limit = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*,
                   d.name as destination_name,
                   d.country as destination_country,
                   d.cover_image as destination_cover,
                   (
                       SELECT pi.image_url 
                       FROM package_images pi 
                       WHERE pi.package_id = p.id 
                       ORDER BY pi.sort_order ASC 
                       LIMIT 1
                   ) as cover_image
            FROM packages p
            LEFT JOIN destinations d ON d.id = p.primary_destination_id
            WHERE p.status = 'publicado'
            ORDER BY p.is_featured DESC, p.created_at DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        foreach ($rows as &$r) {
            if (empty($r['cover_image'])) {
                $r['cover_image'] = $r['destination_cover'] ?: 'https://images.unsplash.com/photo-1488646953014-85cb44e25828?auto=format&fit=crop&w=1200&q=80';
            }
        }

        return $rows;
    }
}
