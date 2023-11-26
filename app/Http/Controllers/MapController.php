<?php

namespace App\Http\Controllers;

use App\Helpers\Map\MapImageHelper;
use App\Models\EarthQuake;
use DantSu\OpenStreetMapStaticAPI\LatLng;
use DantSu\OpenStreetMapStaticAPI\OpenStreetMap;

class MapController extends Controller
{

    public function imageShowId(string $id)
    {
        $this->overrideRequestScheme();
        \header('Content-type: image/png');
        $quake = EarthQuake::query()->findOrFail($id);
        (new OpenStreetMap(new LatLng($quake->getLat(), $quake->getLng()), 8, 1024, 768))
            ->getImage()
            ->displayPNG();
    }

    protected function overrideRequestScheme()
    {
        $_SERVER["REQUEST_SCHEME"] = "https";
    }

    public function imageShowDate(string $year, string $month, string $day = null)
    {
        $this->overrideRequestScheme();
        $query = EarthQuake::query()->whereYear('date', $year)->whereMonth('date', $month);
        if (!is_null($day)) {
            $query->whereDay('date', $day);
        }
        $quakes = collect($query->get());
        \header('Content-type: image/png');

        $image = (new MapImageHelper($quakes))->getImage();

        $image->displayPNG();
    }

}
