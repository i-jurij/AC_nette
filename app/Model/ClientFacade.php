<?php

declare(strict_types=1);

namespace App\Model;

use App\UI\Accessory\PhoneNumber;
use Nette;
use Nette\Database\Explorer;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Database\UniqueConstraintViolationException;
use Nette\Security\Passwords;
use Nette\Utils\Validators;
use Nette\Utils\Strings;

/**
 * Manages user-related operations such as authentication and adding new users.
 */
class ClientFacade
{
    public const ColumnId = 'id';
    public const ColumnName = 'username';
    public const ColumnImage = 'image';
    public const ColumnPasswordHash = 'password';
    public const ColumnPhone = 'phone';
    public const ColumnPhoneVerified = 'phone_verified';
    public const ColumnEmail = 'email';
    public const ColumnEmailVerified = 'email_verified';
    public const ColumnAuthToken = 'auth_token';
    public const ColumnRating = 'rating';
    public const ColumnCreatedAt = 'created_at';
    public const ColumnUpdatedAt = 'updated_at';
    public string $table;
    private string $table_role_user;

    public function __construct(
        public Explorer $db,
        public Passwords $passwords,
    ) {
        $this->table = 'client';
        $this->table_role_user = 'role_client';
    }

    public static function getColumns(): string
    {
        $ref = new \ReflectionClass(__CLASS__);
        $column_array = $ref->getConstants();

        return \implode(', ', $column_array);
    }


    public function getAllClientsData(): Selection
    {
        $clients_data = $this->db->table($this->table)->select($this->getColumns());

        return $clients_data;
    }


    public function getClientData($id): ActiveRow
    {
        $client_data = $this->db->table($this->table)->select($this->getColumns())->get($id);

        return $client_data;
    }


    public function deleteClientData($id): void
    {
        try {
            $this->db->table($this->table)->where('id', $id)->delete();
            $this->db->table($this->table_role_user)->where('user_id', $id)->delete();
            $this->db->table('offer')->where('client_id', $id)->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function countClients()
    {
        return $count = count($this->db->table($this->table));
    }

    public function prepareAddFormData($data)
    {
        if (!empty($data->email) && Validators::isEmail($data->email)) {
            $email = $data->email;
        }

        if (!empty($data->username) && \preg_match('/^[a-zA-Zа-яА-ЯёЁ0-9\-_ ]{3,25}$/', $data->username)) {
            $username = $data->username;
        }

        if (!empty($data->phone) && PhoneNumber::isValid($data->phone)) {
            $phone = PhoneNumber::toDb($data->phone);
        }

        // $email = !empty($data->email) ? $data->email : $data->username.'@'.$data->username.'.com';
        $data_array = [
            self::ColumnName => (!empty($username)) ? $username : ((!empty($phone)) ? $phone : ((!empty($email)) ? Strings::before($email, '@', 1) : null)),
            self::ColumnPasswordHash => $this->passwords->hash($data->password),
            self::ColumnImage => $data->image ?? null,
            self::ColumnPhone => $phone ?? null,
            self::ColumnPhoneVerified => $data->{self::ColumnPhoneVerified} ?? null,
            self::ColumnEmail => $email ?? null,
            self::ColumnEmailVerified => $data->{self::ColumnEmailVerified} ?? null,
            self::ColumnAuthToken => $this->token(),
            // self::ColumnCreatedAt => $created_at,
            // self::ColumnUpdatedAt => $updated_at,
        ];

        // remove the empty element
        return array_filter($data_array);
    }

    public function add($data): array|string
    {
        $prepared_data = $this->prepareAddFormData($data);

        if (empty($prepared_data[self::ColumnName])) {
            return 'Имя, телефон, почта не указаны или содержат недопустимые символы.';
        }

        $this->db->beginTransaction();
        try {
            $t = $this->db->table($this->table);
            $new_user = $t->insert($prepared_data);

            // check if $data->roles === 'client' from Home:SignPresenter
            // then check if role "client" isset in db table "role"
            // then if isset get id of role "client"
            // else put role "client" to db table and get id
            // then put it to table "role_user"

            if ($data->roles === 'client') {
                $role = $this->db->table('role');
                $role_client_check = $role->where('role_name', 'client')->fetch();

                if (!empty($role_client_check->id)) {
                    $role_id = $role_client_check->id;
                } else {
                    $role_client_add = $role->insert(['role_name' => 'client']);
                    $role_id = $role_client_add->id;
                }
                if (isset($role_id)) {
                    $this->db->table($this->table_role_user)->insert([
                        'user_id' => $new_user->id,
                        'role_id' => $role_id,
                    ]);
                }
            }

            // check if is_array($data->roles)
            if (\is_array($data->roles)) {
                foreach ($data->roles as $id) {
                    $this->db->table($this->table_role_user)->insert([
                        'user_id' => $new_user->id,
                        'role_id' => $id,
                    ]);
                }
            }
            $this->db->commit();

            // return 'ok';
            return $new_user->toArray();
        } catch (UniqueConstraintViolationException $e) {
            $this->db->rollBack();
            \Tracy\Debugger::log($e, \Tracy\Debugger::EXCEPTION);

            return 'Пользователь с таким именем, или номером телефона, или адресом электронной почты уже существует.';
        } catch (Nette\Database\DriverException $e) {
            $this->db->rollBack();
            \Tracy\Debugger::log($e, \Tracy\Debugger::EXCEPTION);

            return 'Ошибка при регистрации. Повторите позже.';
        }
    }


    public function update(int $id, array $data): void
    {
        if (!empty($data['email'])) {
            Validators::assert($data['email'], 'email');
        }
        try {
            foreach ($data as $key => $value) {
                if (!empty($value) && $key !== 'id' && $key !== 'roles') {
                    if ($key === 'password') {
                        $update_data[$key] = $this->passwords->hash($value);
                    } else {
                        $update_data[$key] = $value;
                    }
                }
            }

            $user = $this->db->table($this->table);

            if (!empty($update_data)) {
                $update_data[self::ColumnAuthToken] = $this->token();
                $user->where('id', $id)->update($update_data);
            }

            if (!empty($data['roles']) && is_array($data['roles'])) {
                if ($user->get($id)->related($this->table_role_user . '.user_id')->delete() > 0) {
                    unset($user);
                }
                $roles = [];
                foreach ($data['roles'] as $role_id) {
                    $roles[] = [
                        'user_id' => $id,
                        'role_id' => $role_id,
                    ];
                }
                $this->db->table($this->table_role_user)->insert($roles);
            }
        } catch (\Exception $e) {
            throw new \Exception();
        }
    }

    public function token()
    {
        return Nette\Utils\Random::generate(15);
    }

    protected function prepare($client_id)
    {
        $roles_ids = [];

        $roles_ids_sql = $this->db->table($this->table_role_user)
            ->select('role_id')
            ->where('user_id', $client_id);
        foreach ($roles_ids_sql as $role_id) {
            $roles_ids[] = $role_id['role_id'];
        }
        $roles_sql = $this->db->table('role')
            ->where('id', $roles_ids);

        return $roles_sql;
    }

    public function getRoless($client_id)
    {
        $roles = [];
        foreach ($this->prepare($client_id) as $role) {
            $roles[] = $role->role_name;
        }

        return $roles;
    }

    public function roleWithClientId($client_id)
    {
        $roles = [];
        foreach ($this->prepare($client_id) as $role) {
            $roles[$role->id] = $role->role_name;
        }

        return $roles;
    }

    protected function prepareSearch($data)
    {
        $data_array = [
            self::ColumnName => !empty($data->username) ? strip_tags($data->username) : null,
            self::ColumnPhone => isset($data->phone) ? PhoneNumber::toDb($data->phone) : null,
            self::ColumnEmail => !empty($data->email) ? strip_tags($data->email) : null,
            self::ColumnRating => (int) $data->rating ?? null,
            'roles' => $data->roles ?? null,
        ];

        // remove the empty element
        return array_filter($data_array);
    }

    public function search($data): array
    {
        $users_data = [];
        $pre_data = $this->prepareSearch($data);

        if (!empty($pre_data)) {
            $query = $this->db->table($this->table);

            if (!empty($pre_data[self::ColumnName])) {
                $username = $pre_data[self::ColumnName];
                $query = $query->where('username LIKE ?', "%$username%");
            }
            if (!empty($pre_data[self::ColumnPhone])) {
                $phone = $pre_data[self::ColumnPhone];
                $query = $query->where('phone LIKE ?', "%$phone%");
            }
            if (!empty($pre_data[self::ColumnEmail])) {
                $email = $pre_data[self::ColumnEmail];
                $query = $query->where('email LIKE ?', "%$email%");
            }
            if (!empty($pre_data[self::ColumnRating])) {
                $rating = $pre_data[self::ColumnRating];
                $query = $query->where('rating = ?', $rating);
            }

            foreach ($query as $user) {
                $users_data[$user->id] = [
                    'username' => $user->username,
                    'phone' => $user->phone,
                    'phone_verified' => $user->phone_verified,
                    'email' => $user->email,
                    'email_verified' => $user->email_verified,
                    'rating' => $user->rating,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];

                foreach ($user->related($this->table_role_user . '.user_id') as $row) {
                    $users_data[$user->id]['roles'][] = $row->ref('role', 'role_id');
                }
            }

            if (!empty($pre_data['roles'])) {
                foreach ($users_data as $id => $value) {
                    if (!empty(array_diff($pre_data['roles'], $value['roles']))) {
                        unset($users_data[$id]);
                    }
                }
            }
        }

        return $users_data;
    }

    public function searchBy($field, $data, $strict = true): ?ActiveRow
    {
        if ($field === 'phone') {
            $data = PhoneNumber::toDb($data);
        }
        $query = $this->db->table($this->table);

        if ($strict) {
            return $query->where($field, $data)->fetch();
        } else {
            return $query->where($field . ' LIKE ?', '%' . $data . '%')->fetch();
        }
    }

    /**
     * get clients by type.
     *
     * @param string $type 'customer' or 'executor'
     */
    public function getCl(string $type): Selection
    {
        $customer_role_id = $this->db->table('role')
            ->where('role_name', $type)
            ->fetch()
            ->id;
        $customers_id = $this->db->table($this->table_role_user)
            ->select('user_id')
            ->where('role_id', $customer_role_id);
        $customers = $this->db->table($this->table)
            ->where('id', $customers_id);

        return $customers;
    }
}