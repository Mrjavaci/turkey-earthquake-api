<?php

namespace App\Jobs;

use Illuminate\Support\Collection;
use SimpleXMLElement;

class XmlParser
{
    public static function parseXml(string $body): Collection
    {
        $result = [];
        $data = new SimpleXMLElement($body);
        foreach ($data as $item) {
            $attributes = $item->attributes();
            $name = (string)$attributes->name;
            [$date, $time] = explode(" ", $name);
            $location = self::findLocation(explode(" ", (string)$attributes->lokasyon), true);
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
    public static function findLocation(array $parts, $isInner = false): string
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
