<?php

namespace App\Console\Commands;

use App\Models\PhilippineBarangay;
use App\Models\PhilippineMunicipality;
use App\Models\PhilippineProvince;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncPhilippineLocations extends Command
{
    protected $signature = 'locations:sync {--endpoint=https://psgc.gitlab.io/api : PSGC API base URL}';

    protected $description = 'Synchronize Philippine province, municipality, and barangay reference data';

    public function handle(): int
    {
        $endpoint = rtrim($this->option('endpoint'), '/');
        $this->info('Fetching Philippine location reference data...');

        $provinces = $this->get($endpoint . '/provinces');
        $municipalities = $this->get($endpoint . '/cities-municipalities');
        $barangays = $this->get($endpoint . '/barangays');

        PhilippineProvince::upsert(
            collect($provinces)->map(fn(array $item) => [
                'code' => $item['code'],
                'name' => $item['name'],
                'region_code' => $item['regionCode'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all(),
            ['code'],
            ['name', 'region_code', 'updated_at']
        );

        PhilippineMunicipality::upsert(
            collect($municipalities)->map(fn(array $item) => [
                'code' => $item['code'],
                'name' => $item['name'],
                'province_code' => $item['provinceCode'] ?? null,
                'region_code' => $item['regionCode'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all(),
            ['code'],
            ['name', 'province_code', 'region_code', 'updated_at']
        );

        PhilippineBarangay::upsert(
            collect($barangays)->map(function (array $item) {
                $municipalityCode = $item['municipalityCode'] ?? null;

                if (!is_string($municipalityCode) || !preg_match('/^[0-9]+$/', $municipalityCode)) {
                    $municipalityCode = substr((string) $item['code'], 0, -3) . '000';
                }

                return [
                    'code' => $item['code'],
                    'name' => $item['name'],
                    'municipality_code' => $municipalityCode,
                    'province_code' => $item['provinceCode'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->all(),
            ['code'],
            ['name', 'municipality_code', 'province_code', 'updated_at']
        );

        $this->info(sprintf('Synchronized %d provinces, %d municipalities/cities, and %d barangays.', count($provinces), count($municipalities), count($barangays)));

        return self::SUCCESS;
    }

    private function get(string $url): array
    {
        $response = Http::acceptJson()->timeout(60)->get($url);

        if ($response->failed() || !is_array($response->json())) {
            $this->error('Unable to fetch location data from ' . $url);
            exit(self::FAILURE);
        }

        return $response->json();
    }
}