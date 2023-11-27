<?php

namespace App\Helpers\Map;

use App\Models\EarthQuake;
use DantSu\OpenStreetMapStaticAPI\LatLng;
use DantSu\OpenStreetMapStaticAPI\OpenStreetMap;
use Illuminate\Support\Collection;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

class MapImageHelper
{
    public const MAX_NORMALIZE_DEPTH = 25;

    public function __construct(public Collection $quakes)
    {
    }

    public function getImage(): Image
    {
        [[$maxLat, $maxLng], [$minLat, $minLng]] = $this->getMaxAndMinLatLng();

        $maxDepth = $this->getMaxDepth();
        $minDepth = $this->getMinDepth();

        $openStreetMap = OpenStreetMap::createFromBoundingBox(new LatLng($maxLat, $minLng), new LatLng($minLat, $maxLng), 1, 1024, 768);


        $manager = new ImageManager(['driver' => 'imagick']);
        $image = $manager->make($openStreetMap->getImage()->getDataPNG());
        $this->quakes->each(function (EarthQuake $quake) use ($openStreetMap, $maxDepth, $minDepth,$image) {
            $normalizedDepth = $this->normalizeDepth($quake->getDepth(), $maxDepth, $minDepth);
            $color = $this->getStrokeColor($normalizedDepth);
            $xy = $openStreetMap->getMapData()->convertLatLngToPxPosition(new LatLng($quake->getLat(),$quake->getLng()));
            $image->circle($normalizedDepth,$xy->getX(),$xy->getY(),function ($draw) use ($color, $normalizedDepth) {
                $draw->background($color);
            });
        });

        return $image;
        /*
        $this->quakes->each(function (EarthQuake $quake) use ($openStreetMap, $maxDepth, $minDepth) {
            $normalizedDepth = $this->normalizeDepth($quake->getDepth(), $maxDepth, $minDepth);
            $color = $this->getStrokeColor($normalizedDepth);
            $openStreetMap->addDraw(new Circle(new LatLng($quake->getLat(), $quake->getLng()), $color, $this->normalizeDepth($quake->getDepth(), $maxDepth, $minDepth), $color));
        });
        return $openStreetMap->getImage();
        */
    }

    protected function getMaxAndMinLatLng(): array
    {
        return [[$this->quakes->max('lat'), $this->quakes->max('lng')], [$this->quakes->min('lat'), $this->quakes->min('lng')]];
    }

    protected function getMaxDepth(): float
    {
        return (float)$this->quakes->max('depth');
    }

    protected function getMinDepth(): float
    {
        return (float)$this->quakes->min('depth');
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

    protected function normalizeDepth(float $depth, float $maxDepth, float $minDepth): float
    {
        $normalizedValue = ($depth - $minDepth) / ($maxDepth - $minDepth);
        return 1 + $normalizedValue * (self::MAX_NORMALIZE_DEPTH - 1);
    }

    protected function getStrokeColor(float $normalizedDepth): string
    {
        $colors = config('colors.groups');
        $colorIndex = floor($normalizedDepth / (self::MAX_NORMALIZE_DEPTH / count($colors)));
        $colorIndex = min($colorIndex, count($colors) - 1);
        $colorIndex = max($colorIndex, 0);
        return $colors[$colorIndex];
    }
}
