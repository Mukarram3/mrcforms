<strong>{{ localize('Full Name') }}: </strong> {{ $address->full_name }}
<br>
<strong>{{ localize('Phone') }}: </strong> {{ $address->phone }}
<br>

<address class="fs-sm mb-0">
    <strong>{{ $address->address }}</strong>
</address>

<strong> {{ localize('City') }}: </strong>{{ $address->city ? $address->city->name : '' }}
<br>

{{--<strong>{{ localize('State') }}: </strong>{{ $address->state->name }}--}}

<br>
<strong>{{ localize('Country') }}: </strong> {{  $address->country->name }}

