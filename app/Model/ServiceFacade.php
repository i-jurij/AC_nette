<?php

declare(strict_types=1);

namespace App\Model;

use Nette\Database\Explorer;
use Nette\Database\Table\Selection;

/**
 * Manages user-related operations such as authentication and adding new users.
 */
final class ServiceFacade
{
    public Selection $service;
    public Selection $category;

    public function __construct(public Explorer $db)
    {
    }

    public function getAllServices(): array
    {
        $res = [];
        $this->category = $this->db->table('category');
        foreach ($this->category as $cat) {
            $res[$cat->id] = [
                'category_id' => $cat->id,
                'category_image' => $cat->image,
                'category_name' => $cat->name,
                'category_description' => $cat->description,
                'category_created_at' => $cat->created_at,
                'category_updated_at' => $cat->updated_at,
            ];
            foreach ($cat->related('service.category_id') as $service) {
                $res[$cat->id]['services'][$service->id] = $service->toArray();
            }
        }

        return $res;
    }

    public function getCategories(): array
    {
        return $this->db->table('category')->fetchAll();
    }
    public function getServices()
    {
        return $this->db->table('service')->select('id, category_id, name');
    }

    public function getCategory(int $category_id)
    {
        return $this->db->table('category')->where('id = ?', $category_id)->fetch();
    }

    public function getService(int $category_id)
    {
        return $this->db->table('service')->where('category_id = ?', $category_id)->fetch();
    }

    public function getServ(int $service_id)
    {
        return $this->db->table('service')->get($service_id);
    }

    public function deleteCategory(int $category_id): int
    {
        $count = $this->db->table('category')
            ->where('id', $category_id)
            ->delete();
        if ($count > 0) {
            $count = $this->db->table('service')->where('category_id = ?', $category_id)->delete();
        }
        return $count;
    }

    public function deleteService(int $service_id): int
    {
        return $this->db->table('service')->where('id', $service_id)->delete();
    }

    public function addService(array $data): int
    {
        $res = $this->db->table('service')->insert([
            'category_id' => (int) $data['category_id'],
            'name' => htmlspecialchars(strip_tags($data['name'])),
        ]);

        return !empty($res->id) ? $res->id : 0;
    }

    public function addCategory(string $name): int
    {
        $res = $this->db->table('category')->insert([
            'name' => $name,
        ]);

        return !empty($res->id) ? $res->id : 0;
    }

    public function addServices(array $data): int // $data = [ 0 => ['category_id' => int, 'name' => 'string'], 1 => ...]
    {
        $res = $this->db->table('service')->insert($data);

        return !empty($res->id) ? $res->id : 0;
    }


    public function upCategory(int $id, array $data): int
    {
        $res = $this->db->table('category')->where('id', $id)->update($data);

        return !empty($res) ? $res : 0;
    }

    public function upService(int $id, array $data): int
    {
        $res = $this->db->table('service')->where('id', $id)->update($data);

        return !empty($res) ? $res : 0;
    }

}
