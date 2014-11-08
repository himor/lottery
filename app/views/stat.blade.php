@extends('layout.layout')

@section('cache')
@if ($fromCache)
    from cache
@else
    from remote
@endif
@stop

@section('content')

<h3>Frequency analysis</h3>

<table class="lines1">

@foreach ($set as $value_)
    <?php $set_ = [
        'set' => $value_['winning_numbers'],
        'mb' => $value_['mega_ball']
    ]?>

    <?php
        $firstLine = true;
    ?>

    @foreach ($analyzers as $an_key => $an)
        <tr>
        <td>
        @if ($firstLine)
            {{ $value_['draw_date'] }}
        @endif
        </td>

        <?php
        $err = $an->checkSet($set_);
        ?>

        <td>
        @if ($err)
            {{ $an_key }}: fail
        @else
            {{ $an_key }}: pass
        @endif
        </td>

        @foreach ($set_['set'] as $pos => $value)
            <td>
            @if (in_array($pos, $err))
                <span style='color:red'>{{ $value }}</span>
            @else
                <span style='color:green'>{{ $value }}</span>
            @endif

            @if ($an->requireReason)
            {{ number_format($aResults[$an_key]['numbers'][$value][1], 1) }}
            {{ number_format($aResults[$an_key]['numbers'][$value][2], 1) }}
            {{ number_format($aResults[$an_key]['numbers'][$value][3], 1) }}
            {{ number_format($aResults[$an_key]['numbers'][$value][4], 1) }}
            {{ number_format($aResults[$an_key]['numbers'][$value][5], 1) }}
            @endif

            </td>
        @endforeach

        <td>{{ $set_['mb'] }}
            @if ($an->requireReason)
            ({{ number_format($aResults[$an_key]['mb'][$set_['mb']], 2) }})
            @endif
        </td>

        </tr>
        <?php
            $firstLine = false;
        ?>
    @endforeach

@endforeach


</table>

@stop