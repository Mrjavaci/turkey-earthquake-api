<?php

namespace App\Console\Commands;

use App\Jobs\XmlParser;
use App\Models\EarthQuake;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GetEarthQuakesSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-earth-quakes-daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $data = Http::get(sprintf('http://udim.koeri.boun.edu.tr/zeqmap/xmlt/%s.xml', Carbon::now()->format('Ym')));
        $lastData = Storage::disk('cache')->get('lastData');
        if ($lastData == $data->body()) {
            return;
        }
        Storage::disk('cache')->put('lastData', $data->body());
        $lastData = explode("\n", $lastData);
        $lastData = collect($lastData);
        $data = explode("\n", $data->body());
        $data = collect($data);
        $data = $data->diff($lastData);
        if ($data->count() == 0) {
            return;
        }
        XmlParser::parseXml($this->createXml($data->implode('\n')))->each(function (array $data) {
            if (!EarthQuake::query()->where('date', $data['date'])->where('time', $data['time'])->exists()) {
                $this->info('eklendi ' . $data['date'] . ' ' . $data['time']);
                EarthQuake::query()->create($data);
            }
        });


    }

    protected function createXml(string $implode)
    {
        return '<eqlist>' . $implode . '</eqlist>';
    }
}
