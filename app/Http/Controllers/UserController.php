<?php
/**
 * UserController file
 * php version 8.0
 *
 * @category Class
 * @package  Controller
 * @author   Pelin Nikita <pelin.dev@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/pelindev/teleport.git
 */

namespace App\Http\Controllers;

use App\Models\User;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;

use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Collection;

/**
 * UserController class
 * php version 8.0
 *
 * @category Class
 * @package  Controller
 * @author   Pelin Nikita <pelin.dev@gmail.com>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://github.com/pelindev/teleport.git
 */
class UserController extends Controller
{
    /**
     * Creates new user
     *
     * @param \App\Http\Requests\CreateUserRequest $request request
     *
     * @return \App\Models\User|\Illuminate\Http\JsonResponse
     **/
    public function create(CreateUserRequest $request) : User|JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $user = new User($validatedData);
            $user->save();
    
            return User::find($user->id);
        } catch (\Throwable $e) {
            report($e);
            return $this->sendError(
                'Something went wrong',
                500
            );
        }
    }

    /**
     * Returns all users
     *
     * @return \Illuminate\Database\Eloquent\Collection
     **/
    public function getAll() : Collection
    {
        return User::all();
    }

    /**
     * Returns user by id
     *
     * @param int $id user id
     *
     * @return \App\Models\User|\Illuminate\Http\JsonResponse
     **/
    public function getOneById(int $id) : User|JsonResponse
    {
        return User::findOrFail($id);
    }

    /**
     * Update user by id
     *
     * @param \App\Http\Requests\UpdateUserRequest $request request
     * @param int                                  $id      user id
     *
     * @return \App\Models\User|\Illuminate\Http\JsonResponse
     **/
    public function update(UpdateUserRequest $request, int $id) : User|JsonResponse
    {
        try {
            $validatedData = $request->validated();
            $user = User::find($id);

            if (!$user) {
                return $this->sendError(
                    "User with id $id not found",
                    404
                );
            }
    
            $user->name     = $validatedData['name'] ?? $user->name;
            $user->phone    = $validatedData['phone'] ?? $user->phone;
            $user->status   = $validatedData['status'] ?? $user->status;
            $user->balance = $validatedData['balance'] ?? $user->balance;
    
            $user->save();
    
            return $user;
        } catch (\Throwable $e) {
            report($e);
            return $this->sendError(
                'Something went wrong',
                500
            );
        }
    }
}
