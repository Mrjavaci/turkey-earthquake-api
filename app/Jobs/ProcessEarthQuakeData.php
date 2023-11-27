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
        XmlParser::parseXml($data->body())->each(function (array $data) {
            EarthQuake::query()->create($data);
        });
    }

    /**
     * @throws Exception
     */

}
