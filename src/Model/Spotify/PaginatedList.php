<?php


namespace App\Model\Spotify;


use App\Model\Paginated;

class PaginatedList implements Paginated
{
    protected $href;
    protected $items;
    protected $limit;
    protected $offset;
    protected $total;
    protected $next;
    protected $previous;

    public static function createPaginatedFromApi(array $apiResponse, string $itemClassname): self
    {
        return new self(
            $apiResponse['href'],
            array_map([$itemClassname, 'createFromApi'], $apiResponse['items'] ?? []),
            $apiResponse['limit'],
            $apiResponse['offset'],
            $apiResponse['total'],
            $apiResponse['next'],
            $apiResponse['previous']
        );
    }

    private function __construct(
        string $href,
        array $items,
        int $limit,
        int $offset,
        int $total,
        ?string $next,
        ?string $previous
    )
    {
        $this->href = $href;
        $this->items = $items;
        $this->limit = $limit;
        $this->offset = $offset;
        $this->total = $total;
        $this->next = $next;
        $this->previous = $previous;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    /**
     * @return ApiModel[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getNext(): ?string
    {
        return $this->next;
    }

    public function getPrevious(): ?string
    {
        return $this->previous;
    }


}
