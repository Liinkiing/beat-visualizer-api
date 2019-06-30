<?php


namespace App\Model\Spotify;


use App\Model\ApiModel;

class UserPlayerDevice implements ApiModel
{

    protected $id;
    protected $active;
    protected $privateSession;
    protected $restricted;
    protected $name;
    protected $type;
    protected $volume;

    public static function createFromApi(array $apiResponse): self
    {
        return new self(
            $apiResponse['id'],
            $apiResponse['is_active'],
            $apiResponse['is_private_session'],
            $apiResponse['is_restricted'],
            $apiResponse['name'],
            $apiResponse['type'],
            (int)$apiResponse['volume_percent']
        );
    }

    private function __construct(
        string $id,
        bool $active,
        bool $privateSession,
        bool $restricted,
        string $name,
        string $type,
        int $volume
    )
    {
        $this->id = $id;
        $this->active = $active;
        $this->privateSession = $privateSession;
        $this->restricted = $restricted;
        $this->name = $name;
        $this->type = $type;
        $this->volume = $volume;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function isPrivateSession(): bool
    {
        return $this->privateSession;
    }

    public function isRestricted(): bool
    {
        return $this->restricted;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVolume(): int
    {
        return $this->volume;
    }


}
