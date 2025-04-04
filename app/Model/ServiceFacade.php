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

    public function getService(array $params)
    {
        $this->service = $this->db->table('service');

        return [];
    }
}
