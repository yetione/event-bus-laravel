<?php


namespace Yetione\EventBus;

use Yetione\EventBus\Contracts\EventContract;
use InvalidArgumentException;

class Event implements EventContract
{
    protected string $source;

    protected string $name;

    protected ?string $scope = null;

    protected array $params = [];

    protected array $payload;

    public function __construct()
    {
        $this->params['content_type'] = 'application/json';
    }

    public function setPayload(array $payload): EventContract
    {
        $this->payload = $payload;
        return $this;
    }

    public function source(): string
    {
        return $this->source;
    }

    public function scope(): ?string
    {
        return $this->scope;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function params(): array
    {
        return $this->params;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    /**
     * @param string $source
     * @return Event
     */
    public function setSource(string $source): Event
    {
        $this->source = $source;
        return $this;
    }

    /**
     * @param string $name
     * @return Event
     */
    public function setName(string $name): Event
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string|null $scope
     * @return Event
     */
    public function setScope(?string $scope): Event
    {
        $this->scope = $scope;
        return $this;
    }

    /**
     * @param mixed[] $params
     * @return Event
     */
    public function setParams(array $params): Event
    {
        $this->params = $params;
        return $this;
    }
}
