<?php


namespace Yetione\EventBus\Events;


use Yetione\EventBus\Event;
use App\User;


class UserCreated extends Event
{
    protected string $source = 'auth';

    protected string $name = 'user_created';

    protected ?string $scope = 'users';

    public function __construct(User $user)
    {
        $user = clone $user;
        $user->makeVisible('password');
        $this->setPayload($user->toArray());
    }
}
