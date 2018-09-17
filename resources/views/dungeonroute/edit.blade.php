@extends('layouts.app', ['wide' => true])
@section('header-title', $headerTitle)
@section('head')
    @parent

    <style>
        #settings_wrapper {
            border: #d3e0e9 solid 1px;

            -webkit-border-radius: 3px;
            -moz-border-radius: 3px;
            border-radius: 3px;
        }

        #settings_toggle {
            cursor: pointer;
        }
    </style>
@endsection

@section('scripts')
    @parent

    <script>
        $(function () {
            let $settings = $('#settings');
            $settings.on('hide.bs.collapse', function (e) {
                let $caret = $('#settings_caret');
                $caret.removeClass('fa-caret-up');
                $caret.addClass('fa-caret-down');
            });

            $settings.on('show.bs.collapse', function (e) {
                let $caret = $('#settings_caret');
                $caret.removeClass('fa-caret-down');
                $caret.addClass('fa-caret-up');
            });

            $('#save_settings').bind('click', _saveSettings);

            $('.selectpicker').selectpicker({
                showIcon: true
            });
        });

        function _saveSettings() {
            $.ajax({
                type: 'POST',
                url: '{{ route('api.dungeonroute.update', $model->public_key) }}',
                dataType: 'json',
                data: {
                    faction_id: $('#faction_id').val(),
                    race:
                        $('.raceselect select').map(function () {
                            return $(this).val();
                        }).get()
                    ,
                    class:
                        $('.classselect select').map(function () {
                            return $(this).val();
                        }).get()
                    ,
                    teeming: $('#teeming').val(),
                    unlisted: $('#unlisted').val(),
                    @if(Auth::user()->hasRole('admin'))
                    demo: $('#demo').val(),
                    @endif
                    affixes: $('#affixes').val(),
                    _method: 'PATCH'
                },
                beforeSend: function () {
                    $('#save_settings').hide();
                    $('#save_settings_saving').show();
                },
                success: function (json) {
                    $('#save_settings_success').show().delay(5000).hide(200);
                    $('#save_settings_error').hide();

                    $('#save_settings_success').html('{{__('Settings saved successfully')}}');
                },
                error: function (response) {
                    $('#save_settings_success').hide();
                    $('#save_settings_error').show().delay(5000).hide(200);

                    $('#save_settings_error').html('{{__('An error occurred saving your settings. Please try again.')}}');
                },
                complete: function () {
                    $('#save_settings').show();
                    $('#save_settings_saving').hide();
                }
            });
        }
    </script>
@endsection

@section('content')
    @isset($model)
        {{ Form::model($model, ['route' => ['dungeonroute.update', $model->public_key], 'method' => 'patch']) }}
    @else
        {{ Form::open(['route' => 'dungeonroute.savenew']) }}
    @endisset

    @isset($model)
        <div class='col-lg-12'>
            <div id='map_container'>
                @include('common.maps.map', [
                    'dungeon' => \App\Models\Dungeon::findOrFail($model->dungeon_id),
                    'dungeonroute' => $model,
                    'edit' => true
                ])
            </div>


            <div id='settings_wrapper' class='col-lg-12'>
                <div id='settings_toggle' class='col-lg-12 text-center btn btn-default' data-toggle='collapse'
                     data-target='#settings'>
                    <h4 class='mb-0'>
                        <i class='fas fa-cog'></i> {{ __('Settings') }} <i id='settings_caret'
                                                                           class='fas fa-caret-down'></i>
                    </h4>
                </div>

                <div id='settings' class='col-lg-12 collapse'>
                    {!! Form::checkbox('teeming', 1, $model->teeming, ['id' => 'teeming', 'class' => 'form-control left_checkbox d-none']) !!}

                    <h3>
                        {{ __('Group composition') }}
                    </h3>

                    @php($factions = $model->dungeon->isSiegeOfBoralus() ? \App\Models\Faction::where('name', '<>', 'Unspecified')->get() : null)
                    @include('common.group.composition', ['dungeonroute' => $model, 'factions' => $factions])

                    <h3 class='mt-1'>
                        {{ __('Affixes (optional)') }}
                    </h3>

                    <div class='container mt-1'>
                        @include('common.group.affixes', ['dungeonroute' => $model, 'teemingselector' => '#teeming'])
                    </div>

                    <h3>
                        {{ __('Sharing') }}
                    </h3>
                    <div class='form-group'>
                        {!! Form::label('unlisted', __('Private (when checked, only people with the link can view your route)')) !!}
                        {!! Form::checkbox('unlisted', 1, $model->unlisted, ['class' => 'form-control left_checkbox']) !!}
                    </div>

                    @if(Auth::user()->hasRole('admin'))
                        <h3>
                            {{ __('Admin') }}
                        </h3>
                        <div class='form-group'>
                            {!! Form::label('demo', __('Mark as demo route')) !!}
                            {!! Form::checkbox('demo', 1, $model->demo, ['class' => 'form-control left_checkbox']) !!}
                        </div>
                    @endif

                    <div class='form-group'>
                        <div id='save_settings' class='offset-lg-5 col-lg-2 btn btn-success'>
                            <i class='fas fa-save'></i> {{ __('Save settings') }}
                        </div>
                        <div id='save_settings_saving' class='offset-lg-5 col-lg-2 btn btn-success disabled'
                             style='display: none;'>
                            <i class='fas fa-circle-notch fa-spin'></i>
                        </div>
                    </div>

                    <div class='container'>
                        <div id='save_settings_success'
                             class='alert alert-success alert-dismissible fade show text-center'
                             style='display: none;' role='alert'>
                        </div>
                        <div id='save_settings_error' class='alert alert-danger alert-dismissible fade show text-center'
                             style='display: none;' role='alert'>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endisset

    {!! Form::close() !!}
@endsection

