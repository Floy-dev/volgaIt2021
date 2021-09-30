@for ($i = 0; $i < $height; $i++)
    @for ($j = 0; $j < $width; $j++, $inc++)
        <div class="block {{ $i % 2 == 0 ? 'odd' : 'even' }} {{ $cells[$inc]['playerId'] == $currentPlayerId ? 'block__current' : ''}}" style="background-color: {{ $cells[$inc]['color'] }}"></div>
    @endfor
    <br>
@endfor
