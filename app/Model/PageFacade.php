<?php

namespace App\Model;

use Nette\Database\Connection;

final class PageFacade
{
    protected Connection $db_pages;
    private $check_table;

    public function __construct(private Connection $sqlite)
    {
        $this->db_pages = $sqlite;
    }

    public function getPagesData()
    {
        $check_table = $this->db_pages->fetch('SELECT count(*) FROM sqlite_master WHERE type=? AND name=?', 'table', 'pages');
        if ($check_table['count(*)'] > 0) {
            $check_empty = $this->db_pages->fetchField('SELECT EXISTS(SELECT 1 FROM pages LIMIT ?);', 3);
            if ($check_empty > 0) {
                $data = $this->db_pages->query('SELECT * FROM pages WHERE published=? ORDER BY ?', true, 'DESC');
                foreach ($data as $key => $value) {
                    foreach ($value as $k => $val) {
                        $db_data[$key][$k] = $val;
                    }
                }
            }
        }

        return !empty($db_data) ? $db_data : [];
    }

    public function add()
    {
    }

    public function delete()
    {
    }
}
