<?php


namespace App\Model\Spotify;


use App\Model\ApiModel;

class User implements ApiModel
{
    protected $displayName;
    protected $email;
    protected $id;
    protected $href;
    protected $images;
    protected $birthdate;
    protected $country;
    protected $followers;

    public static function createFromApi(array $apiResponse): self
    {
        return new self(
            $apiResponse['birthdate'],
            $apiResponse['country'],
            $apiResponse['display_name'],
            $apiResponse['email'],
            UserFollowers::createFromApi($apiResponse['followers']),
            $apiResponse['href'],
            $apiResponse['id'],
            array_map([UserImage::class, 'createFromApi'], $apiResponse['images'] ?? [])
        );
    }

    private function __construct(
        string $birthdate,
        string $country,
        string $displayName,
        string $email,
        UserFollowers $followers,
        string $href,
        string $id,
        array $images
    )
    {
        $this->displayName = $displayName;
        $this->email = $email;
        $this->id = $id;
        $this->href = $href;
        $this->images = $images;
        $this->birthdate = new \DateTimeImmutable($birthdate);
        $this->country = $country;
        $this->followers = $followers;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return UserImage[]
     */
    public function getImages(): array
    {
        return $this->images;
    }

    public function getBirthdate(): \DateTimeInterface
    {
        return $this->birthdate;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getFollowers(): UserFollowers
    {
        return $this->followers;
    }

}
