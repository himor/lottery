@extends('layout.layout')

@section('cache')
@if ($fromCache)
    from cache
@else
    from remote
@endif
@stop

@section('content')

<h3>Best random set</h3>

<table class="lines3">

@foreach ($set as $value_)
    <?php
        $firstLine = true;
    ?>
     @foreach ($analyzers as $an_key => $an)
            <tr>
            <td>
            @if ($firstLine)
                &lt;
            @endif
            </td>

            <?php
            $err = $predefined[md5(json_encode($value_))];
            ?>

            <td>
            @if ($err && $an->errorOnNonemptyCheckSet)
                {{ $an_key }}: fail
            @else
                {{ $an_key }}: @if (isset($err['total'])) {{ $err['total'] }}@else pass @endif
            @endif
            </td>

            @foreach ($value_['set'] as $pos => $value)
                <td>

                @if (in_array($pos, $err) && $an->errorOnNonemptyCheckSet)
                    <span style='color:red'>{{ $value }}</span>
                @elseif ($an->errorOnNonemptyCheckSet)
                    <span style='color:green'>{{ $value }}</span>
                @endif

                @if ($an->requireReason && $an->errorOnNonemptyCheckSet)
                {{ number_format($aResults[$an_key]['numbers'][$value][1], 1) }}
                {{ number_format($aResults[$an_key]['numbers'][$value][2], 1) }}
                {{ number_format($aResults[$an_key]['numbers'][$value][3], 1) }}
                {{ number_format($aResults[$an_key]['numbers'][$value][4], 1) }}
                {{ number_format($aResults[$an_key]['numbers'][$value][5], 1) }}
                @endif

                @if ($an->requireReason && !$an->errorOnNonemptyCheckSet)
                {{ $err[$pos + 1] }}
                @endif

                </td>
            @endforeach

            <td>{{ $value_['mb'] }}
            @if ($an->requireReason && $an->errorOnNonemptyCheckSet)
            ({{ number_format($aResults[$an_key]['mb'][$value_['mb']], 2) }})
            @endif
            @if ($an->requireReason && !$an->errorOnNonemptyCheckSet)
            ({{ $err['mb'] }})
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