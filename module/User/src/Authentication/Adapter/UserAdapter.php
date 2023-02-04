<?php

namespace User\Authentication\Adapter;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Mvc\I18n\Translator;
use SensitiveParameter;
use User\Mapper\User as UserMapper;
use User\Authentication\Service\LoginAttempt as LoginAttemptService;
use User\Service\PwnedPasswords as PwnedPasswordsService;

class UserAdapter implements AdapterInterface
{
    private string $login;

    private string $password;

    public function __construct(
        private readonly Translator $translator,
        private readonly Bcrypt $bcrypt,
        private readonly LoginAttemptService $loginAttemptService,
        private readonly PwnedPasswordsService $pwnedPasswordsService,
        private readonly UserMapper $mapper,
    ) {
    }

    /**
     * Try to authenticate.
     *
     * @return Result
     */
    public function authenticate(): Result
    {
        $user = $this->mapper->findByLogin($this->login);

        if (null === $user) {
            return new Result(
                Result::FAILURE_IDENTITY_NOT_FOUND,
                null,
                [
                    $this->translator->translate('This user could not be found.'),
                ],
            );
        }

        if (
            $user->getMember()->getDeleted()
            || $user->getMember()->getHidden()
            || $user->getMember()->isExpired()
        ) {
            return new Result(
                Result::FAILURE_UNCATEGORIZED,
                null,
                [
                    $this->translator->translate('You cannot sign in to this account at this moment.'),
                ],
            );
        }

        $this->mapper->detach($user);

        if ($this->loginAttemptService->loginAttemptsExceeded($user)) {
            return new Result(
                Result::FAILURE,
                null,
                [
                    $this->translator->translate('Too many login attempts, try again later.'),
                ],
            );
        }

        if (!$this->verifyPassword($this->password, $user->getPassword())) {
            $this->loginAttemptService->logFailedLogin($user);

            return new Result(
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                [
                    $this->translator->translate('Wrong password provided.'),
                ],
            );
        }

        if ($this->pwnedPasswordsService->isPasswordLeaked($this->password)) {
            return new Result(
                // Use Result::FAILURE_CREDENTIAL_INVALID to make this ends up as an error on the password field
                Result::FAILURE_CREDENTIAL_INVALID,
                null,
                [
                    // phpcs:ignore -- user-visible strings should not be split
                    $this->translator->translate('Your password was found in a public data breach and is therefore considered insecure. Please request a password reset before continuing. We recommend using a password generated by a good password manager, such as Bitwarden or 1Password.'),
                ],
            );
        }

        return new Result(Result::SUCCESS, $user);
    }

    /**
     * Verify a password.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function verifyPassword(
        #[SensitiveParameter] string $password,
        #[SensitiveParameter] string $hash,
    ): bool {
        if ($this->bcrypt->verify($password, $hash)) {
            return true;
        }

        return false;
    }

    /**
     * Sets the credentials used to authenticate.
     *
     * @param string $login
     * @param string $password
     */
    public function setCredentials(
        string $login,
        #[SensitiveParameter] string $password,
    ): void {
        $this->login = $login;
        $this->password = $password;
    }

    /**
     * Get the mapper.
     *
     * @return UserMapper
     */
    public function getMapper(): UserMapper
    {
        return $this->mapper;
    }
}
