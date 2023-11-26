<?php

namespace App\Helpers\Map;

use App\Models\EarthQuake;
use DantSu\OpenStreetMapStaticAPI\Circle;
use DantSu\OpenStreetMapStaticAPI\LatLng;
use DantSu\OpenStreetMapStaticAPI\OpenStreetMap;
use Illuminate\Support\Collection;

class MapImageHelper
{

    public function __construct(public Collection $quakes)
    {
    }

    public function getImageOld()
    {
        $maxAndMinCoordinates = $this->getMaxAndMinLatLng();

        [$centerLat, $centerLng] = $this->getCenterLatLng($maxAndMinCoordinates);

        $zoom = $this->getZoomOfMap($maxAndMinCoordinates);
        $latlng = new LatLng($centerLat, $centerLng);
        $openStreetMap = new OpenStreetMap($latlng, $zoom, 1024, 768);
        $this->quakes->each(function (EarthQuake $quake) use ($openStreetMap) {
            $openStreetMap->addDraw(new Circle(new LatLng($quake->getLat(), $quake->getLng()), '#000000', 15, 15));
        });
        return $openStreetMap->getImage();
    }

    protected function getMaxAndMinLatLng(): array
    {
        return [[$this->quakes->max('lat'), $this->quakes->max('lng')], [$this->quakes->min('lat'), $this->quakes->min('lng')]];
    }

    protected function getCenterLatLng(array $maxAndMinCoordinates)
    {
        [$maxLat, $maxLng] = $maxAndMinCoordinates[0];
        [$minLat, $minLng] = $maxAndMinCoordinates[1];
        $centerLat = ($maxLat + $minLat) / 2;
        $centerLng = ($maxLng + $minLng) / 2;
        return [$centerLat, $centerLng];
    }

    protected function getZoomOfMap(array $maxAndMinCoordinates)
    {
        [$maxLat, $maxLng] = $maxAndMinCoordinates[0];
        [$minLat, $minLng] = $maxAndMinCoordinates[1];
        $zoom = 8;
        if ($maxLat - $minLat > 0.5) {
            $zoom++;
        }
        if ($maxLng - $minLng > 0.5) {
            $zoom++;
        }
        return $zoom;
    }

    public function getImage()
    {
        [[$maxLat, $maxLng], [$minLat, $minLng]] = $this->getMaxAndMinLatLng();

        $maxDepth = $this->getMaxDepth();
        $minDepth = $this->getMinDepth();

        $openStreetMap = OpenStreetMap::createFromBoundingBox(new LatLng($maxLat, $minLng), new LatLng($minLat, $maxLng), 1, 1024, 768);
        $this->quakes->each(function (EarthQuake $quake) use ($openStreetMap, $maxDepth, $minDepth) {
            $openStreetMap->addDraw(new Circle(new LatLng($quake->getLat(), $quake->getLng()), '#000000', $this->normalizeDepth($quake->getDepth(), $maxDepth, $minDepth), '#FFFFFF'));
        });
        return $openStreetMap->getImage();
    }

    protected function getMaxDepth(): float
    {
        return (float)$this->quakes->max('depth');
    }

    protected function getMinDepth(): float
    {
        return (float)$this->quakes->min('depth');
    }

    public function normalizeDepth(float $depth, float $maxDepth, float $minDepth): float
    {
        $normalizedValue = ($depth - $minDepth) / ($maxDepth - $minDepth);
        return 1 + $normalizedValue * (25 - 1);
    }
}
