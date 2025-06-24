<x-mail::message>
# Вітаємо, {{ $name }}!

Дякуємо, що приєдналися до {{ config('app.name') }}.

    @if ($report)
        {{ $report }}
    @endif

З повагою,<br>
{{ config('app.name') }}
</x-mail::message>
