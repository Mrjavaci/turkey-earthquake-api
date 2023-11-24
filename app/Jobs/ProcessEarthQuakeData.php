<?php

namespace App\Jobs;

use App\Models\EarthQuake;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class ProcessEarthQuakeData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public string $month)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $data = Http::get(sprintf('http://udim.koeri.boun.edu.tr/zeqmap/xmlt/%s.xml', $this->month));
        $this->parseXml($data->body())->each(function (array $data) {
            EarthQuake::query()->create($data);
        });
    }

    /**
     * @throws Exception
     */
    protected function parseXml(string $body): Collection
    {
        $result = [];
        $data = new SimpleXMLElement($body);
        foreach ($data as $item) {
            $attributes = $item->attributes();
            $name = (string)$attributes->name;
            [$date, $time] = explode(" ", $name);
            $location = $this->findLocation(explode(" ", (string)$attributes->lokasyon), true);
            if (is_null($attributes)) {
                continue;
            }
            $result[] = [
                'date' => $date,
                'time' => $time,
                'lat' => (float)$attributes->lat,
                'lng' => (float)$attributes->lng,
                'depth' => (float)$attributes->Depth,
                'scale_MD' => 0,
                'scale_ML' => (string)$attributes->mag,
                'scale_Mw' => 0,
                'location' => str($location)->squish()->toString(),
            ];
        }
        return collect($result);

    }

    public function findLocation(array $parts, $isInner = false): string
    {
        if (!$isInner) {
            $parts = array_slice($parts, 8, count($parts));
        }
        $location = "";
        foreach ($parts as $newPart) {
            if (str_contains($newPart, "İlksel") || str_contains($newPart, "REVIZE")) {
                return $location;
            }
            $location .= " " . $newPart;
        }
        return $location;
    }
}
