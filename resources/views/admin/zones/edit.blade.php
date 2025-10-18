@extends('layouts.app')

@section('title', 'Nova Zona - Projeto Edificar')
@section('page-title', 'Editar Zona' . $zone->name)

@section('content')
    <div class="bg-white rounded-lg shadow p-8">
        <form action="{{ route('zones.update', $zone) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <x-input-label for="name" :value="__('Name')" />

                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                        value="{{ $zone->name }}" required />

                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                    <x-input-label for="description" :value="__('Description')" />

                    <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                        value="{{ $zone->description }}" />

                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <x-primary-button>
                    {{ __('Update') }}
                </x-primary-button>
            </div>
        </form>
    </div>
@endsection
