<?php


namespace App\Model\Spotify;


use App\Model\ApiModel;

class UserPlayer implements ApiModel
{

    protected $device;
    protected $shuffling;
    protected $repeat;
    protected $timestamp;
    protected $progression;
    protected $playing;

    public static function createFromApi(array $apiResponse): self
    {
        return new self(
            UserPlayerDevice::createFromApi($apiResponse['device']),
            $apiResponse['shuffle_state'],
            $apiResponse['repeat_state'],
            (int)$apiResponse['timestamp'],
            (int)$apiResponse['progress_ms'],
            $apiResponse['is_playing']
        );
    }

    private function __construct(
        UserPlayerDevice $device,
        bool $shuffling,
        string $repeat,
        int $timestamp,
        int $progression,
        bool $playing
    )
    {
        $this->device = $device;
        $this->shuffling = $shuffling;
        $this->repeat = $repeat;
        $this->timestamp = $timestamp;
        $this->progression = $progression;
        $this->playing = $playing;
    }

    public function getDevice(): UserPlayerDevice
    {
        return $this->device;
    }

    public function getShuffling(): string
    {
        return $this->shuffling;
    }

    public function getRepeat(): string
    {
        return $this->repeat;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function getProgression(): int
    {
        return $this->progression;
    }

    public function getPlaying(): string
    {
        return $this->playing;
    }


}
