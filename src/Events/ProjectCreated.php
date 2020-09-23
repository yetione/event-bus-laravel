<?php


namespace Yetione\EventBus\Events;


use Yetione\EventBus\Event;
use App\Models\Project;


class ProjectCreated extends Event
{
    protected string $source = 'auth';

    protected string $name = 'project_created';

    protected ?string $scope = 'users';

    public function __construct(Project $project)
    {
        $project = clone $project;
        $project->makeVisible('shopify_token')->load('owner');
        $this->setPayload($project->toArray());

    }
}
