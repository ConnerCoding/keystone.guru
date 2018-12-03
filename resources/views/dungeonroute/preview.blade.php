@extends('layouts.app', ['custom' => true, 'footer' => false, 'header' => false, 'cookieConsent' => false, 'title' => $model->title])
<?php
/** @var $model \App\Models\DungeonRoute */

/** @var \App\Models\Dungeon $dungeon */
$dungeon = \App\Models\Dungeon::findOrFail($model->dungeon_id);
$dungeon->load('floors');
?>
@section('content')
    @include('common.maps.map', [
        'dungeon' => $dungeon,
        'dungeonroute' => $model,
        'edit' => false,
        'noUI' => true,
        'defaultZoom' => 1,
        'hiddenMapObjectGroups' => [
            'enemy',
            'enemypatrol',
            'enemypack',
            'mapcomment',
            'dungeonfloorswitchmarker',
        ]
    ])
@endsection

