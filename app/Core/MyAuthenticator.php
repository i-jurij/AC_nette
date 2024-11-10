<?php

declare(strict_types=1);

namespace App\Core;

use App\Model\UserFacade;
// use App\Model\UserTableColumns;
use Nette;
use Nette\Database\Explorer;
use Nette\Security\AuthenticationException;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\SimpleIdentity;

final class MyAuthenticator implements Nette\Security\Authenticator, Nette\Security\IdentityHandler
{
    // Dependency injection of database explorer and password utilities
    public function __construct(
        private Explorer $sqlite,
        private Passwords $passwords,
        // private UserTableColumns $u_T_C,
        private UserFacade $userfacade,
    ) {
    }

    /**
     * Authenticate a user based on provided credentials.
     * Throws an AuthenticationException if authentication fails.
     */
    public function authenticate(string $username, string $password): SimpleIdentity
    {
        // Fetch the user details from the database by username
        $user = $this->sqlite->table($this->userfacade::TableName)
            ->where($this->userfacade::ColumnName, $username)
            ->fetch();

        // Authentication checks
        if (!$user) {
            throw new AuthenticationException('The username is incorrect.', $this->userfacade::IdentityNotFound);
        } elseif (!$this->passwords->verify($password, $user[$this->userfacade::ColumnPasswordHash])) {
            throw new AuthenticationException('The password is incorrect.', self::InvalidCredential);
        } elseif ($this->passwords->needsRehash($user[$this->userfacade::ColumnPasswordHash])) {
            $user->update([
                $this->userfacade::ColumnPasswordHash => $this->passwords->hash($password),
            ]);
        }

        // Return user identity without the password hash
        $arr = $user->toArray();
        unset($arr[$this->userfacade::ColumnPasswordHash]);

        $roles = $this->userfacade->getRoless($user[$this->userfacade::ColumnId]);

        return new SimpleIdentity($user[$this->userfacade::ColumnId], $roles, $arr);
        // return new Nette\Security\SimpleIdentity($row[$this->userfacade::ColumnId], $row[$this->userfacade::ColumnRole], $arr);
    }

    public function sleepIdentity(IIdentity $identity): SimpleIdentity
    {
        // мы возвращаем идентификатор прокси, где в качестве идентификатора выступает auth_token
        return new SimpleIdentity($identity->{$this->userfacade::ColumnAuthToken});
    }

    public function wakeupIdentity(IIdentity $identity): ?SimpleIdentity
    {
        // заменить идентификатор прокси на полный идентификатор, как в authenticate()
        $row = $this->sqlite->table($this->userfacade::TableName)
            ->where($this->userfacade::ColumnAuthToken, $identity->getId())
            ->fetch();
        if (!empty($row)) {
            $arr = $row->toArray();
            unset($arr[$this->userfacade::ColumnPasswordHash]);
            $roles = $this->userfacade->getRoless($row[$this->userfacade::ColumnId]);
        }

        return $row
            ? new SimpleIdentity($row[$this->userfacade::ColumnId], $roles, $arr)
            : null;
    }
}
