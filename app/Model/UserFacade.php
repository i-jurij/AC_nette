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

/**
 * Manages user-related operations such as authentication and adding new users.
 */
final class UserFacade extends UserTableColumns
{
    use \App\UI\Accessory\RequireLoggedUser;

    // Minimum password length requirement for users
    public const PasswordMinLength = 7;
    protected Selection $table;

    // Dependency injection of database explorer and password utilities
    public function __construct(
        public Explorer $sqlite,
        private Passwords $passwords,
    ) {
        $this->table = $this->sqlite->table(self::TableName);
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function getAllUsersData(): Selection
    {
        $users_data = $this->table->select($this->getColumns());

        return $users_data;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function getUserData($id): ActiveRow
    {
        $user_data = $this->table->select($this->getColumns())->get($id);

        return $user_data;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function deleteUserData($id): void
    {
        try {
            $this->table->where('id', $id)->delete();
            $this->sqlite->table('role_user')->where('user_id', $id)->delete();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function countUsers()
    {
        return $count = count($this->table);
    }

    /**
     * Add a new user to the database.
     * Throws a DuplicateNameException if the username is already taken.
     */
    public function shortAdd(string $username, string $password): void
    {
        try {
            $role_admin_id = $this->sqlite->table('role')
                ->select('id')
                ->where('role_name', 'admin')
                ->fetch();
            $row = $this->sqlite->table(self::TableName)->insert([
                self::ColumnName => $username,
                self::ColumnPasswordHash => $this->passwords->hash($password),
                self::ColumnAuthToken => $this->token(),
            ]);

            // insert into users_roles users (first admin user) id, role "admin" id
            $this->sqlite->table('role_user')->insert([
                'user_id' => $row->id,
                'role_id' => $role_admin_id['id'],
            ]);
        } catch (UniqueConstraintViolationException $e) {
            throw new DuplicateNameException();
        }
    }

    protected function prepareAddFormData($data)
    {
        if (!empty($data->email)) {
            Validators::assert($data->email, 'email');
        }
        // $email = !empty($data->email) ? $data->email : $data->username.'@'.$data->username.'.com';
        $data_array = [
            self::ColumnName => $data->username,
            self::ColumnPasswordHash => $this->passwords->hash($data->password),
            self::ColumnImage => $data->image ?? null,
            self::ColumnPhone => PhoneNumber::toDb($data->phone) ?? null,
            self::ColumnEmail => $data->email ?? null,
            self::ColumnAuthToken => $this->token(),
            // self::ColumnCreatedAt => $created_at,
            // self::ColumnUpdatedAt => $updated_at,
        ];

        // remove the empty element
        return array_filter($data_array);
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function add($data): void // object
    {
        try {
            $new_user = $this->sqlite->table(self::TableName)
                ->insert($this->prepareAddFormData($data));

            if (\is_array($data->roles)) {
                foreach ($data->roles as $id) {
                    $this->sqlite->table('role_user')->insert([
                        'user_id' => $new_user->id,
                        'role_id' => $id,
                    ]);
                }
            }
            // email or sms to new user with auth_token for verification
            // $this->email->to()->text('form with links to verification cli or accessory);
        } catch (Exception $e) {
            throw new Exception();
        }

        // return $new_user;
    }

    #[Requires(methods: 'POST', sameOrigin: true)]
    public function update($id, $data): void
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

            $user = $this->sqlite->table(self::TableName);

            if (!empty($update_data)) {
                $user->where('id', $id)->update($update_data);
            }

            if (!empty($data['roles']) && is_array($data['roles'])) {
                if ($user->get($id)->related('role_user.user_id')->delete() > 0) {
                    unset($user);
                }
                $roles = [];
                foreach ($data['roles'] as $role_id) {
                    $roles[] = [
                        'user_id' => $id,
                        'role_id' => $role_id,
                    ];
                }
                $this->sqlite->table('role_user')->insert($roles);
            }
        } catch (Exception $e) {
            throw new Exception();
        }
    }

    public function token()
    {
        return Nette\Utils\Random::generate(15);
    }

    protected function prepare($user_id)
    {
        $roles_ids = [];

        $roles_ids_sql = $this->sqlite->table('role_user')
            ->select('role_id')
            ->where('user_id', $user_id);
        foreach ($roles_ids_sql as $role_id) {
            $roles_ids[] = $role_id['role_id'];
        }
        $roles_sql = $this->sqlite->table('role')
            ->where('id', $roles_ids);

        return $roles_sql;
    }

    public function getRoless($user_id)
    {
        $roles = [];
        foreach ($this->prepare($user_id) as $role) {
            $roles[] = $role->role_name;
        }

        return $roles;
    }

    public function roleWithUserId($user_id)
    {
        $roles = [];
        foreach ($this->prepare($user_id) as $role) {
            $roles[$role->id] = $role->role_name;
        }

        return $roles;
    }

    protected function prepareSearch($data)
    {
        $data_array = [
            self::ColumnName => $data->username ?? null,
            self::ColumnPhone => PhoneNumber::toDb($data->phone) ?? null,
            self::ColumnEmail => $data->email ?? null,
            'roles' => $data->roles ?? null,
        ];

        // remove the empty element
        return array_filter($data_array);
    }

    public function search($data)
    {
        $users_data = null;
        $pre_data = $this->prepareSearch($data);
        if (!empty($pre_data)) {
            $query = $this->table;

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

            foreach ($query as $user) {
                $users_data[$user->id] = [
                    'username' => $user->username,
                    'phone' => $user->phone,
                    'phone_verified' => $user->phone_verified,
                    'email' => $user->email,
                    'email_verified' => $user->email_verified,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ];

                foreach ($user->related('role_user.user_id') as $row) {
                    $users_data[$user->id]['roles'][] = $row->ref('role', 'role_id');
                    if (!empty($pre_data['roles'])) {
                        if (!\in_array($row->role_id, $pre_data['roles'])) {
                            unset($users_data[$user->id]);
                        }
                    }
                }
            }
        }

        return $users_data;
    }
}

/**
 * Custom exception for duplicate usernames.
 */
class DuplicateNameException extends \Exception
{
}
