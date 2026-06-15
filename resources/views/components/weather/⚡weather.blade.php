<?php

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;

new class extends Component
{
    public $weather;
    public $city;

    public function mount(){
        $response = Http::get('https://api.openweathermap.org/data/2.5/weather?q=Bhavnagar&units=metric&appid=3499ef9fb52f2b3270e5238cf055cbc1');
        $this->weather = $response->json(); 
    }

    public function updatedCity(){
        $response = Http::get('https://api.openweathermap.org/data/2.5/weather?q='.$this->city.'&units=metric&appid=3499ef9fb52f2b3270e5238cf055cbc1');
        if($response->successful() && isset($response->json()['weather']))
            $this->weather = $response->json();
    }
};
?>
<div class="min-h-screen bg-gray-100 dark:bg-gray-900 py-6">

    <!-- Search -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-5">
            <x-text-input
                wire:model.live="city"
                placeholder="🔍 Enter City Name..."
                class="w-full"
            />
        </div>
    </div>

    <!-- City Header -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div
            class="bg-gradient-to-r dark:bg-gray-800 from-blue-500 to-cyan-500 dark:from-blue-700 dark:to-cyan-700 text-black dark:text-white rounded-2xl shadow-lg p-6"
        >
            <h1 class="text-3xl font-bold">
                {{ $weather['name'] ?? 'Loading...' }}
            </h1>

            <p class="text-lg mt-2 capitalize">
                {{ $weather['weather'][0]['description'] ?? '' }}
            </p>

            <div class="mt-4">
                <span class="text-5xl font-bold">
                    {{ round($weather['main']['temp'] ?? 0) }}°C
                </span>
            </div>
        </div>
    </div>

    <!-- Weather Stats -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">🌤 Weather</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ $weather['weather'][0]['main'] ?? '-' }}
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">📝 Description</p>
                <p class="font-bold text-lg capitalize dark:text-white">
                    {{ $weather['weather'][0]['description'] ?? '-' }}
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">🌡 Temperature</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ $weather['main']['temp'] ?? '-' }} °C
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">⬇ Min Temp</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ $weather['main']['temp_min'] ?? '-' }} °C
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">⬆ Max Temp</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ $weather['main']['temp_max'] ?? '-' }} °C
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">🌬 Wind Speed</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ $weather['wind']['speed'] ?? '-' }} m/s
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">🧭 Wind Degree</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ $weather['wind']['deg'] ?? '-' }}°
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">💨 Wind Gust</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ $weather['wind']['gust'] ?? '-' }}
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">📈 Pressure</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ $weather['main']['pressure'] ?? '-' }} hPa
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">🌅 Sunrise</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ isset($weather['sys']['sunrise']) ? date('h:i A', $weather['sys']['sunrise']) : '-' }}
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">🌇 Sunset</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ isset($weather['sys']['sunset']) ? date('h:i A', $weather['sys']['sunset']) : '-' }}
                </p>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5">
                <p class="text-gray-500 dark:text-gray-400">🌊 Sea Level</p>
                <p class="font-bold text-lg dark:text-white">
                    {{ $weather['main']['sea_level'] ?? 'N/A' }}
                </p>
            </div>

        </div>
    </div>

</div>