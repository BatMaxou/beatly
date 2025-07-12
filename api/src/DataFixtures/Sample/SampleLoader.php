<?php

namespace App\DataFixtures\Sample;

use App\DataFixtures\Sample\Artist\AngeleSample;
use App\DataFixtures\Sample\Artist\ArcticMonkeysSample;
use App\DataFixtures\Sample\Artist\ArianaGrandeSample;
use App\DataFixtures\Sample\Artist\AyaNakamuraSample;
use App\DataFixtures\Sample\Artist\BillieEilishSample;
use App\DataFixtures\Sample\Artist\BrunoMarsSample;
use App\DataFixtures\Sample\Artist\CalvinHarrisSample;
use App\DataFixtures\Sample\Artist\ColdplaySample;
use App\DataFixtures\Sample\Artist\DrakeSample;
use App\DataFixtures\Sample\Artist\DuaLipaSample;
use App\DataFixtures\Sample\Artist\EdSheeranSample;
use App\DataFixtures\Sample\Artist\ImagineDragonsSample;
use App\DataFixtures\Sample\Artist\KendrickLamarSample;
use App\DataFixtures\Sample\Artist\OrelsanSample;
use App\DataFixtures\Sample\Artist\PNLSample;
use App\DataFixtures\Sample\Artist\PostMaloneSample;
use App\DataFixtures\Sample\Artist\RihannaSample;
use App\DataFixtures\Sample\Artist\StromaeSample;
use App\DataFixtures\Sample\Artist\TaylorSwiftSample;
use App\DataFixtures\Sample\Artist\TheWeekndSample;
use App\DataFixtures\Sample\Category\CategorySample;

class SampleLoader
{
    /**
     * @return SampleInterface[]
     */
    public function load(SampleType $type): array
    {
        return match ($type) {
            SampleType::ARTIST => $this->loadArtistSamples(),
            SampleType::CATEGORY => $this->loadCategorySamples(),
        };
    }

    private function loadCategorySamples(): array
    {
        return new CategorySample()->getData();
    }

    private function loadArtistSamples(): array
    {
        return [
            new AngeleSample()->getData(),
            new ArianaGrandeSample()->getData(),
            new ArcticMonkeysSample()->getData(),
            new AyaNakamuraSample()->getData(),
            new BillieEilishSample()->getData(),
            new BrunoMarsSample()->getData(),
            new CalvinHarrisSample()->getData(),
            new ColdplaySample()->getData(),
            new DrakeSample()->getData(),
            new DuaLipaSample()->getData(),
            new EdSheeranSample()->getData(),
            new ImagineDragonsSample()->getData(),
            new KendrickLamarSample()->getData(),
            new OrelsanSample()->getData(),
            new PNLSample()->getData(),
            new PostMaloneSample()->getData(),
            new RihannaSample()->getData(),
            new StromaeSample()->getData(),
            new TaylorSwiftSample()->getData(),
            new TheWeekndSample()->getData(),
        ];
    }
}
