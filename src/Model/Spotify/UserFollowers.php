<?php


namespace App\Model\Spotify;


use App\Model\ApiModel;

class UserFollowers implements ApiModel
{
    protected $href;
    protected $total;

    public static function createFromApi(array $apiResponse): self
    {
        return new self(
            $apiResponse['total'],
            $apiResponse['href']
        );
    }

    private function __construct(
        int $total,
        ?string $href
    )
    {
        $this->total = $total;
        $this->href = $href;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

}
