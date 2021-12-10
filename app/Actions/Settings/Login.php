<?php

namespace App\Actions\Settings;

use App\Exceptions\Internal\InvalidConfigOption;
use App\Exceptions\InvalidPropertyException;
use App\Exceptions\ModelDBException;
use App\Exceptions\UnauthenticatedException;
use App\Facades\AccessControl;
use App\Legacy\Legacy;
use App\Models\Logs;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class Login
{
	/**
	 * @throws ModelDBException
	 * @throws UnauthenticatedException
	 * @throws ModelNotFoundException
	 * @throws InvalidPropertyException
	 * @throws InvalidConfigOption
	 */
	public function do(string $username, string $password, ?string $oldUsername, ?string $oldPassword, string $ip): void
	{
		try {
			$hashedUsername = bcrypt($username);
			$hashedPassword = bcrypt($password);
		} catch (\InvalidArgumentException $e) {
			throw new InvalidPropertyException('Could not hash username or password', $e);
		}

		if (Legacy::SetPassword($hashedUsername, $hashedPassword)) {
			return;
		}

		// > 4.0.8
		/** @var User $adminUser */
		$adminUser = User::query()->find(0);
		if ($adminUser->password === '' && $adminUser->username === '') {
			$adminUser->username = $hashedUsername;
			$adminUser->password = $hashedPassword;
			$adminUser->save();
			AccessControl::login($adminUser);

			return;
		}

		if (AccessControl::is_admin()) {
			if ($adminUser->password === '' || Hash::check($oldPassword, $adminUser->password)) {
				$adminUser->username = $hashedUsername;
				$adminUser->password = $hashedPassword;
				$adminUser->save();
				unset($adminUser);

				return;
			}
			unset($adminUser);

			throw new UnauthenticatedException('Password is invalid');
		}

		// is this necessary ?
		if (AccessControl::is_logged_in()) {
			$id = AccessControl::id();

			// this is probably sensitive to timing attacks...
			/** @var User $user */
			$user = User::query()->findOrFail($id);

			if ($user->lock) {
				Logs::notice(__METHOD__, __LINE__, 'Locked user (' . $user->username . ') tried to change their identity from ' . $ip);
				throw new UnauthenticatedException('Account is locked');
			}

			if (User::query()->where('username', '=', $username)->where('id', '!=', $id)->count()) {
				Logs::notice(__METHOD__, __LINE__, 'User (' . $user->username . ') tried to change their identity to ' . $username . ' from ' . $ip);

				throw new InvalidPropertyException('Username already exists.');
			}

			// TODO: This looks suspicious.
			// Users can only change the username/password of their own
			// account and must be authenticated in order to do so.
			// (See above, we use `AccessControl::id()` and query for the
			// currently authenticated user).
			// The user name of the currently authenticated user is visible on
			// the GUI anyway.
			// Why do we re-check whether users have been able to correctly
			// present their current (aka old) username again?
			// This does not seem to make any sense security-wise.
			if ($user->username === $oldUsername && Hash::check($oldPassword, $user->password)) {
				Logs::notice(__METHOD__, __LINE__, 'User (' . $user->username . ') changed their identity for (' . $username . ') from ' . $ip);
				$user->username = $username;
				$user->password = $hashedPassword;
				$user->save();
			}
			Logs::notice(__METHOD__, __LINE__, 'User (' . $user->username . ') tried to change their identity from ' . $ip);

			throw new UnauthenticatedException('Previous username or password are invalid');
		}
	}
}
