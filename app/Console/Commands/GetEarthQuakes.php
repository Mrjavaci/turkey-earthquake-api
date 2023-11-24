<?php

namespace App\Console\Commands;

use App\Jobs\ProcessEarthQuakeData;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GetEarthQuakes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-earth-quakes';

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
        $data = Http::get('http://udim.koeri.boun.edu.tr/zeqmap/');
        $this->getMonthsFromData($data)->each(function (string $month) {
            ProcessEarthQuakeData::dispatch($month);
        });

    }

    protected function getMonthsFromData(Response $data): Collection
    {
        return $this->getOptionTagsFromMonthSelect($this->getSelectTagFromXml($data->body()));

    }

    protected function getOptionTagsFromMonthSelect(string $data): Collection
    {
        $re = '/<OPTION VALUE="(.*?)">(.*?)</ms';
        preg_match_all($re, $data, $matches, PREG_SET_ORDER, 0);
        unset($matches[0]);
        return collect($matches)->map(function (array $item) {
            return $item[2];
        });
    }

    protected function getSelectTagFromXml(string $body): string
    {
        $re = '/<Select NAME="LBTEST"(.*?)<\/Select>/ms';

        preg_match_all($re, $body, $matches, PREG_SET_ORDER, 0);
        return $matches[0][0];
    }


}
