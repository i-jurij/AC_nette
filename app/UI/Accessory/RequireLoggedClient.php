<?php

declare(strict_types=1);

namespace App\UI\Accessory;

/**
 * Trait to enforce user authentication.
 * Redirects unauthenticated users to the sign-in page.
 */
trait RequireLoggedClient
{
    /**
     * Injects the requirement for a logged-in user.
     * Attaches a callback to the startup event of the presenter.
     */
    public function injectRequireLoggedClient(): void
    {
        $this->onStartup[] = function () {
            $user = $this->getUser();
            // If the user isn't logged in, redirect them to the sign-in page
            if ($user->isLoggedIn()) {
                return;
            } elseif ($user->getLogoutReason() === $user::LogoutInactivity) {
                $this->flashMessage('Войдите на сайт');
                $this->redirect(':Home:Sign:in', ['backlink' => $this->storeRequest()]);
            } else {
                //$this->redirect(':Home:Sign:in');
                $this->redirect(':Home:Sign:in', ['backlink' => $this->storeRequest()]);
            }
        };
    }
}
