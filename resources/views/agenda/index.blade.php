@extends('layouts.main')

@section('title', $congregacao->nome_curto . ' | ' . $appName)

@section('content')
@php
    $agenda = trans('agenda');
    $views = $agenda['views'];
    $schedule = $agenda['schedule'];
    $eventsLang = $agenda['events'];
    $localeJs = str_replace('_', '-', app()->getLocale());
@endphp

<div class="container">
    <h1>{{ $agenda['title'] }}</h1>
    <div class="info">
        <h3>{{ $agenda['overview'] }}</h3>
        <div class="control-btn">
            <div class="control-btn-group">
                <h4>{{ $views['change'] }}</h4>
                <div class="control-btn-wrapper">
                    <button class="btn" id="btnVisaoAnual">{{ $views['annual'] }}</button>
                    <button class="btn" id="btnVisaoMensal">{{ $views['monthly'] }}</button>
                </div>
            </div>

            <div class="control-btn-group">
                <h4>{{ $schedule['heading'] }}</h4>
                <div class="control-btn-wrapper">
                    <button onclick="abrirJanelaModal('{{ route('eventos.form_criar') }}')" class="btn" id="evento">
                        <i class="bi bi-calendar-event"></i> {{ $schedule['event'] }}
                    </button>
                    <button onclick="abrirJanelaModal('{{ route('cultos.form_criar') }}')" class="btn" id="culto">
                        <i class="bi bi-bell"></i> {{ $schedule['service'] }}
                    </button>
                    <button onclick="abrirJanelaModal('{{ route('reunioes.form_criar') }}')" class="btn" id="reuniao">
                        <i class="bi bi-people"></i> {{ $schedule['meeting'] }}
                    </button>
                </div>
            </div>
        </div>
        <div id="calendar"></div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const calendarEl = document.getElementById('calendar');
        if (!calendarEl) {
            return;
        }

        const locale = @json($localeJs);
        const buttonText = {
            today: @json($views['today']),
            month: @json($views['month_label']),
            week: @json($views['week_label']),
            day: @json($views['day_label']),
        };
        const eventLabels = {
            alertTitle: @json($eventsLang['alert_title']),
            alertStart: @json($eventsLang['alert_start']),
        };

        const calendar = new FullCalendar.Calendar(calendarEl, {
            locale: locale,
            timeZone: 'America/Sao_Paulo',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: buttonText,
            views: {
                multiMonthYear: {
                    type: 'multiMonth',
                    duration: { months: 12 },
                    buttonText: @json($views['year_label'])
                }
            },
            events: "{{ route('agenda.eventos.json') }}",
            eventDidMount: function(info) {
                const titleEl = info.el.querySelector('.fc-event-title');
                if (!titleEl) {
                    return;
                }

                const type = info.event.extendedProps ? info.event.extendedProps.type : null;
                const iconMap = {
                    culto: 'bi-bell',
                    evento: 'bi-calendar-event',
                    reuniao: 'bi-people-fill',
                    aniversario: 'bi-cake2',
                };

                const iconClass = iconMap[type];
                const safeTitle = titleEl.textContent?.trim() || info.event.title;
                const iconHtml = iconClass ? '<span class="event-icon event-icon-' + type + '"><i class="bi ' + iconClass + '"></i></span>' : '';
                titleEl.innerHTML = iconHtml + '<span class="event-title-text">' + safeTitle + '</span>';
                if (type) {
                    info.el.classList.add('event-type-' + type);
                }
            },
            eventClick: function(info) {
                info.jsEvent.preventDefault();

                const { editUrl } = info.event.extendedProps || {};

                if (editUrl) {
                    abrirJanelaModal(editUrl);
                    return;
                }

                const plainTitle = info.event.title.replace(/<[^>]+>/g, '');
                const start = info.event.start ? info.event.start.toLocaleString(locale) : '';
                alert(eventLabels.alertTitle + ': ' + plainTitle + '\n' + eventLabels.alertStart + ': ' + start);
            }
        });

        calendar.render();

        document.getElementById('btnVisaoMensal')?.addEventListener('click', function () {
            calendar.changeView('dayGridMonth');
        });

        document.getElementById('btnVisaoAnual')?.addEventListener('click', function () {
            calendar.changeView('multiMonthYear');
        });
    });
</script>
@endpush
@endsection
