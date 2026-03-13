{{-- Single Room Card --}}
<div class="room-card"
     id="room-card-{{ $room->id }}"
     data-room-id="{{ $room->id }}"
     data-price="{{ $room->price }}"
     data-room-name="{{ $room->title }}">
    <div class="room-card__info">
        <div class="room-card__meta">
            <h6 class="room-card__name">{{ $room->title }}</h6>
            <div class="room-card__specs">
                @if($room->size)
                    <span>{{ $room->size }} m²</span>
                    <span class="room-card__dot">•</span>
                @endif
                @if($room->beds)
                    <span>{{ $room->beds }} {{ Str::plural('Bed', $room->beds) }}</span>
                @endif
            </div>
            <div class="room-card__tags">
                @foreach($room->facilities as $item)
                    <span class="room-card__tag">
                        <i class="{{ $item->icon ? (str_starts_with($item->icon, 'bi-') ? 'bi '.$item->icon : $item->icon) : 'bi bi-check-circle' }}"></i>
                        {{ $item->name }}
                    </span>
                @endforeach
                @foreach($room->services as $item)
                    <span class="room-card__tag">
                        <i class="{{ $item->icon ? (str_starts_with($item->icon, 'bi-') ? 'bi '.$item->icon : $item->icon) : 'bi bi-check-circle' }}"></i>
                        {{ $item->name }}
                    </span>
                @endforeach
                @if($room->adults)
                    <span class="room-card__tag">
                        <i class="bi bi-people"></i>
                        Up to {{ $room->adults }} {{ Str::plural('Adult', $room->adults) }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="room-card__action">
        <div class="room-card__price">
            <span class="room-card__price-label">Per night</span>
            <span class="room-card__price-amount">${{ number_format($room->price) }}</span>
        </div>
        <button type="button"
                class="btn btn-select room-select-btn">
            Select
        </button>
    </div>
</div>
